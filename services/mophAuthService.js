const crypto = require('crypto');
const { query } = require('../config/db');

const MOPH_CONFIG = {
    healthId: {
        clientId: process.env.MOPH_HEALTH_ID_CLIENT_ID || '01a0faf5-2888-758a-a1c3-d885143cf2e4',
        secret: process.env.MOPH_HEALTH_ID_SECRET || 'a92c05aeec078f4cd9da04d34dd3e5ebe1ea0942',
        authUrl: 'https://moph.id.th/oauth/redirect',
        tokenUrl: 'https://moph.id.th/api/v1/token'
    },
    providerId: {
        clientId: process.env.MOPH_PROVIDER_ID_CLIENT_ID || '68fdef16-2121-451b-952c-07a9aaf4d1f0',
        secret: process.env.MOPH_PROVIDER_ID_SECRET || '6bFJzxrV1SnBwEwphUK9vcAPXiWFlzX8',
        tokenUrl: 'https://provider.id.th/api/v1/services/token',
        profileUrl: 'https://provider.id.th/api/v1/services/profile'
    }
};

/**
 * Determine dynamic callback URL based on request headers if not statically configured
 */
function getCallbackUrl(req) {
    if (process.env.MOPH_CALLBACK_URL && process.env.MOPH_CALLBACK_URL.trim()) {
        return process.env.MOPH_CALLBACK_URL.trim();
    }
    const host = req.get('x-forwarded-host') || req.get('host') || 'localhost:3000';
    const proto = req.get('x-forwarded-proto') || req.protocol || 'http';
    return `${proto}://${host}/api/auth/moph/callback`;
}

/**
 * Step 1: Generate Authorization Redirect URL
 */
function getLoginUrl(req) {
    const callbackUrl = getCallbackUrl(req);
    const params = new URLSearchParams({
        client_id: MOPH_CONFIG.healthId.clientId,
        redirect_uri: callbackUrl,
        response_type: 'code'
    });
    return `${MOPH_CONFIG.healthId.authUrl}?${params.toString()}`;
}

/**
 * Step 2: Exchange Authorization Code for Health ID Access Token
 */
async function exchangeHealthIdToken(code, callbackUrl, userAgent = '') {
    const headers = {
        'Content-Type': 'application/x-www-form-urlencoded',
        'User-Agent': userAgent || 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    };

    const bodyParams = new URLSearchParams({
        client_id: MOPH_CONFIG.healthId.clientId,
        client_secret: MOPH_CONFIG.healthId.secret,
        code: code,
        redirect_uri: callbackUrl,
        grant_type: 'authorization_code'
    });

    const response = await fetch(MOPH_CONFIG.healthId.tokenUrl, {
        method: 'POST',
        headers: headers,
        body: bodyParams.toString()
    });

    const resData = await response.json();
    if (!response.ok || !resData || (!resData.access_token && !resData.data?.access_token)) {
        const errorMsg = resData?.message || resData?.error_description || JSON.stringify(resData);
        throw new Error(`Health ID Token exchange failed: ${errorMsg}`);
    }

    return resData.access_token || resData.data.access_token;
}

/**
 * Step 3: Exchange Health ID Token for Provider ID Token
 */
async function exchangeProviderIdToken(healthAccessToken, userAgent = '') {
    const headers = {
        'Content-Type': 'application/json',
        'User-Agent': userAgent || 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    };

    const body = {
        client_id: MOPH_CONFIG.providerId.clientId,
        secret_key: MOPH_CONFIG.providerId.secret,
        token_by: 'Health ID',
        token: healthAccessToken
    };

    const response = await fetch(MOPH_CONFIG.providerId.tokenUrl, {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(body)
    });

    const resData = await response.json();
    if (!response.ok || !resData || (!resData.access_token && !resData.data?.access_token)) {
        const errorMsg = resData?.message || resData?.error || JSON.stringify(resData);
        throw new Error(`Provider ID Token exchange failed: ${errorMsg}`);
    }

    return resData.access_token || resData.data.access_token;
}

/**
 * Step 4: Get Provider Profile from Provider ID API
 */
async function getProviderProfile(providerAccessToken, userAgent = '') {
    const headers = {
        'Authorization': `Bearer ${providerAccessToken}`,
        'client-id': MOPH_CONFIG.providerId.clientId,
        'secret-key': MOPH_CONFIG.providerId.secret,
        'User-Agent': userAgent || 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    };

    const response = await fetch(MOPH_CONFIG.providerId.profileUrl, {
        method: 'GET',
        headers: headers
    });

    const resData = await response.json();
    if (!response.ok || !resData) {
        const errorMsg = resData?.message || JSON.stringify(resData);
        throw new Error(`Get Provider Profile failed: ${errorMsg}`);
    }

    // Profile data is usually in resData.data or directly in resData
    const profile = resData.data || resData;
    return profile;
}

/**
 * Step 5: Match Provider Profile with HOSxP opduser & doctor tables
 */
async function matchUserWithHOSxP(profile) {
    const hashCid = profile.hash_cid || profile.cid_hash || profile.cid;
    const nameTh = profile.name_th || profile.name || '';
    const licenseId = profile.license_id || profile.license_no || '';

    let matchedUser = null;

    // 1. Primary Match: SHA256 of CID with opduser
    if (hashCid) {
        const sqlCid = `
            SELECT u.loginname, u.name, u.entryposition, u.groupname, u.doctorcode
            FROM opduser u
            WHERE SHA2(REPLACE(u.cid, '-', ''), 256) = ?
              AND (u.account_disable IS NULL OR u.account_disable != 'Y')
            ORDER BY LENGTH(u.loginname) ASC
            LIMIT 1
        `;
        const users = await query(sqlCid, [hashCid.toLowerCase()]);
        if (users && users.length > 0) {
            matchedUser = users[0];
            matchedUser.match_type = 'hash_cid';
        }
    }

    // 2. Secondary Match: Doctor license_id via doctor table
    if (!matchedUser && licenseId) {
        const cleanLic = licenseId.replace(/[^0-9a-zA-Z]/g, '');
        const sqlLic = `
            SELECT u.loginname, u.name, u.entryposition, u.groupname, u.doctorcode
            FROM opduser u
            INNER JOIN doctor d ON d.code = u.doctorcode
            WHERE (d.licenseno = ? OR d.licenseno LIKE ? OR REPLACE(d.licenseno, '-', '') = ?)
              AND (u.account_disable IS NULL OR u.account_disable != 'Y')
            LIMIT 1
        `;
        const doctors = await query(sqlLic, [licenseId, `%${cleanLic}%`, cleanLic]);
        if (doctors && doctors.length > 0) {
            matchedUser = doctors[0];
            matchedUser.match_type = 'license_id';
        }
    }

    // 3. Fallback Match: Full Name in opduser
    if (!matchedUser && nameTh) {
        // Strip prefixes (นาย, นาง, นางสาว, นพ., พญ., etc.)
        const cleanName = nameTh.replace(/^(นาย|นางสาว|นาง|นพ\.|พญ\.|ดร\.|ทพ\.|ทพญ\.)\s*/, '').trim();
        const sqlName = `
            SELECT u.loginname, u.name, u.entryposition, u.groupname, u.doctorcode
            FROM opduser u
            WHERE (u.name LIKE ? OR u.name LIKE ?)
              AND (u.account_disable IS NULL OR u.account_disable != 'Y')
            LIMIT 1
        `;
        const nameUsers = await query(sqlName, [`%${nameTh}%`, `%${cleanName}%`]);
        if (nameUsers && nameUsers.length > 0) {
            matchedUser = nameUsers[0];
            matchedUser.match_type = 'name_th';
        }
    }

    // 4. Fallback 4: If still not matched, synthesize authenticated profile from MOPH Provider ID
    if (!matchedUser) {
        matchedUser = {
            loginname: `moph_${(profile.license_id || profile.cid || 'user').slice(-6)}`,
            name: profile.name_th || profile.name || 'เจ้าหน้าที่ MOPH Provider ID',
            entryposition: profile.position || profile.job_title || 'บุคลากรสาธารณสุข',
            groupname: profile.organization || 'MOPH Provider ID',
            doctorcode: null,
            match_type: 'moph_external'
        };
    }

    return matchedUser;
}

module.exports = {
    MOPH_CONFIG,
    getCallbackUrl,
    getLoginUrl,
    exchangeHealthIdToken,
    exchangeProviderIdToken,
    getProviderProfile,
    matchUserWithHOSxP
};
