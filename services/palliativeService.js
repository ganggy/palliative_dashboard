const { query, queryPalliative } = require('../config/db');

// หน่วยบริการในเครือข่าย อ.โคกศรีสุพรรณ (มาตรฐานระบบสุขภาพอำเภอโคกศรีสุพรรณ)
const RPST_MAP = {
    'pcu-hospital': 'หน่วย PCU โรงพยาบาล',
    '05569': 'รพ.สต.บ้านห้วยหีบรุ่งอรุณ',
    '05570': 'รพ.สต.บ้านเหล่าโพนค้อ',
    '05571': 'รพ.สต.บ้านม่วงไข่น้อย',
    '05572': 'รพ.สต.บ้านโคกนาดี',
    '05573': 'รพ.สต.บ้านโพนทองวัฒนา',
    '00000': 'นอกเขตบริการ'
};

// SQL Expression สำหรับจำแนกและผูกหน่วยบริการ (รพ.สต./PCU) ให้เป็นมาตรฐานเดียวกัน ไม่ซ้ำซ้อน
const SQL_UNIT_EXPRESSION = `
    CASE 
        WHEN reg.assigned_unit_name LIKE '%ห้วยหีบ%' THEN 'รพ.สต.บ้านห้วยหีบรุ่งอรุณ'
        WHEN reg.assigned_unit_name LIKE '%โพนค้อ%' THEN 'รพ.สต.บ้านเหล่าโพนค้อ'
        WHEN reg.assigned_unit_name LIKE '%ม่วงไข%' THEN 'รพ.สต.บ้านม่วงไข่น้อย'
        WHEN reg.assigned_unit_name LIKE '%โคกนาดี%' THEN 'รพ.สต.บ้านโคกนาดี'
        WHEN reg.assigned_unit_name LIKE '%โพนทอง%' THEN 'รพ.สต.บ้านโพนทองวัฒนา'
        WHEN reg.assigned_unit_name LIKE '%PCU%' THEN 'หน่วย PCU โรงพยาบาล'
        WHEN pt.chwpart != '47' OR pt.amppart != '15' THEN 'นอกเขตบริการ'
        WHEN pt.tmbpart = '01' AND CAST(pt.moopart AS UNSIGNED) IN (3, 6, 11, 12, 17, 18) THEN 'รพ.สต.บ้านห้วยหีบรุ่งอรุณ'
        WHEN pt.tmbpart = '01' THEN 'หน่วย PCU โรงพยาบาล'
        WHEN pt.tmbpart = '02' THEN 'รพ.สต.บ้านเหล่าโพนค้อ'
        WHEN pt.tmbpart = '03' THEN 'รพ.สต.บ้านม่วงไข่น้อย'
        WHEN pt.tmbpart = '04' AND CAST(pt.moopart AS UNSIGNED) IN (1, 4, 5, 9, 10, 11, 12) THEN 'รพ.สต.บ้านโพนทองวัฒนา'
        WHEN pt.tmbpart = '04' THEN 'รพ.สต.บ้านโคกนาดี'
        ELSE 'หน่วย PCU โรงพยาบาล'
    END
`;

// Helper ฟังก์ชันสำหรับกำหนดชื่อหน่วยบริการในระดับ JavaScript
function resolveUnitName(regUnitName, chw, amp, tmb, moo) {
    if (regUnitName) {
        if (regUnitName.includes('ห้วยหีบ')) return 'รพ.สต.บ้านห้วยหีบรุ่งอรุณ';
        if (regUnitName.includes('โพนค้อ')) return 'รพ.สต.บ้านเหล่าโพนค้อ';
        if (regUnitName.includes('ม่วงไข')) return 'รพ.สต.บ้านม่วงไข่น้อย';
        if (regUnitName.includes('โคกนาดี')) return 'รพ.สต.บ้านโคกนาดี';
        if (regUnitName.includes('โพนทอง')) return 'รพ.สต.บ้านโพนทองวัฒนา';
        if (regUnitName.includes('PCU')) return 'หน่วย PCU โรงพยาบาล';
    }

    if (chw !== '47' || amp !== '15') return 'นอกเขตบริการ';
    const mooNum = parseInt(moo, 10);

    if (tmb === '01') {
        const hhbMoos = [3, 6, 11, 12, 17, 18];
        if (hhbMoos.includes(mooNum)) return 'รพ.สต.บ้านห้วยหีบรุ่งอรุณ';
        return 'หน่วย PCU โรงพยาบาล';
    }
    if (tmb === '02') return 'รพ.สต.บ้านเหล่าโพนค้อ';
    if (tmb === '03') return 'รพ.สต.บ้านม่วงไข่น้อย';
    if (tmb === '04') {
        const ptwMoos = [1, 4, 5, 9, 10, 11, 12];
        if (ptwMoos.includes(mooNum)) return 'รพ.สต.บ้านโพนทองวัฒนา';
        return 'รพ.สต.บ้านโคกนาดี';
    }

    return 'หน่วย PCU โรงพยาบาล';
}

// แปลงวันที่เป็น พ.ศ.
function formatThaiDate(dateStr) {
    if (!dateStr) return '-';
    try {
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        const months = ["", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
        const day = d.getDate();
        const month = months[d.getMonth() + 1];
        let year = d.getFullYear();
        if (year < 2400) {
            year += 543;
        }
        return `${day} ${month} ${year}`;
    } catch {
        return dateStr;
    }
}

// คำนวณสถานะการเยี่ยมบ้าน (ตามสูตรเดิมของ รพ.)
function calculateVisitStatus(daym, death) {
    if (death === 'Y') {
        return {
            text: 'เสียชีวิตแล้ว',
            color: 'danger',
            badgeClass: 'badge-danger',
            days: null
        };
    }
    if (daym === null || daym === undefined || daym === '') {
        return {
            text: 'ยังไม่มีประวัติเยี่ยม',
            color: 'secondary',
            badgeClass: 'badge-secondary',
            days: null
        };
    }
    const days = parseInt(daym, 10);
    if (isNaN(days)) {
        return { text: 'ไม่ระบุ', color: 'secondary', badgeClass: 'badge-secondary', days: null };
    }
    if (days < 30) {
        return { text: `เยี่ยม ${days} วันที่แล้ว`, color: 'success', badgeClass: 'badge-success', days };
    } else if (days < 60) {
        return { text: 'เยี่ยม 1 เดือนที่แล้ว', color: 'primary', badgeClass: 'badge-primary', days };
    } else if (days < 90) {
        return { text: 'เยี่ยม 2 เดือนที่แล้ว', color: 'info', badgeClass: 'badge-info', days };
    } else if (days < 120) {
        return { text: 'เยี่ยม 3 เดือนที่แล้ว', color: 'warning', badgeClass: 'badge-warning', days };
    } else if (days < 210) {
        return { text: 'เยี่ยม 6 เดือนที่แล้ว', color: 'warning', badgeClass: 'badge-warning', days };
    } else if (days < 420) {
        return { text: 'เยี่ยม 1 ปีที่แล้ว', color: 'danger', badgeClass: 'badge-danger', days };
    } else {
        return { text: 'เยี่ยมมากกว่า 1 ปี', color: 'dark', badgeClass: 'badge-dark', days };
    }
}

// ฟังก์ชันสกัดข้อมูลจาก HPI เช่น PPS, Pain Score, ระดับติดเตียง, Barthel Index
function extractClinicalIndicators(hpi) {
    if (!hpi) return { pps: '-', ps: '-', bed: '-', bi: '-' };
    const text = hpi.toString();
    const lower = text.toLowerCase();

    // 1. PPS (Palliative Performance Scale)
    let pps = '-';
    for (let percent = 90; percent >= 10; percent -= 10) {
        if (text.includes(`PPS = ${percent} %`) || text.includes(`PPS=${percent}%`) || text.includes(`${percent}%`) || text.includes(`${percent} %`)) {
            pps = `PPS ${percent}%`;
            break;
        }
    }

    // 2. Pain Score (PS)
    let ps = '-';
    for (let s = 10; s >= 1; s--) {
        if (text.includes(`PS = ${s}/10`) || text.includes(`PS=${s}/10`) || text.includes(`${s}/10`)) {
            ps = `PS ${s}/10`;
            break;
        }
    }

    // 3. ประเภทเตียง (Bed 1 - 4)
    let bed = '-';
    for (let b = 1; b <= 4; b++) {
        const patterns = [`เตียง${b}`, `เตียง ${b}`, `เตียง=${b}`, `เตียง = ${b}`, `เตียง= ${b}`, `เตียง =${b}`];
        if (patterns.some(p => text.includes(p))) {
            bed = `เตียง ${b}`;
            break;
        }
    }

    // 4. Barthel Index (BI)
    let bi = '-';
    for (let i = 100; i >= 0; i--) {
        const patterns = [`bi${i}`, `bi ${i}`, `bi  ${i}`, `bi=${i}`, `bi = ${i}`, `bi= ${i}`, `bi =${i}`];
        if (patterns.some(p => lower.includes(p))) {
            bi = `BI ${i}`;
            break;
        }
    }

    return { pps, ps, bed, bi };
}

// เงื่อนไขหมวดหมู่โรค
const CATEGORY_DIAGS = {
    'palliative': {
        name: 'ทะเบียน Palliative (Z515)',
        icon: 'fa-heartbeat',
        color: '#e63946',
        sql: "(ov.pdx ='Z515' OR ov.dx0 ='Z515' OR ov.dx1 ='Z515' OR ov.dx2 ='Z515' OR ov.dx3 ='Z515' OR ov.dx4 ='Z515' OR ov.dx5 ='Z515')"
    },
    'cancer': {
        name: 'ทะเบียน โรคมะเร็ง (Cancer)',
        icon: 'fa-ribbon',
        color: '#f72585',
        sql: "ov.pdx LIKE 'C%'"
    },
    'stroke': {
        name: 'ทะเบียน โรคหลอดเลือดสมอง (Stroke)',
        icon: 'fa-brain',
        color: '#7209b7',
        sql: "(ov.pdx BETWEEN 'I60' AND 'I69')"
    },
    'ckd': {
        name: 'ทะเบียน โรคไตวายเรื้อรังระยะ 5 (CKD 5)',
        icon: 'fa-lungs',
        color: '#3a0ca3',
        sql: "ov.pdx LIKE 'N185%'"
    },
    'copd': {
        name: 'ทะเบียน โรคปอดอุดกั้นเรื้อรัง (COPD)',
        icon: 'fa-head-side-cough',
        color: '#4361ee',
        sql: "ov.pdx LIKE 'J44%'"
    },
    'tbi': {
        name: 'ทะเบียน บาดเจ็บทางสมอง (TBI)',
        icon: 'fa-user-injured',
        color: '#4cc9f0',
        sql: "(ov.pdx BETWEEN 'S061' AND 'S069')"
    },
    'sci': {
        name: 'ทะเบียน บาดเจ็บไขสันหลัง (SCI)',
        icon: 'fa-wheelchair',
        color: '#2a9d8f',
        sql: "(ov.pdx BETWEEN 'S140' AND 'S141' OR ov.pdx BETWEEN 'S240' AND 'S241' OR ov.pdx BETWEEN 'S340' AND 'S341' OR ov.pdx LIKE 'S343%')"
    },
    'senility': {
        name: 'ทะเบียน ภาวะชราภาพ (Senility)',
        icon: 'fa-blind',
        color: '#e76f51',
        sql: "ov.pdx LIKE '%R54'"
    },
    'dementia': {
        name: 'ทะเบียน โรคสมองเสื่อม (Dementia)',
        icon: 'fa-puzzle-piece',
        color: '#f4a261',
        sql: "(ov.pdx LIKE '%F03%' OR ov.pdx LIKE '%G30%')"
    },
    'acs': {
        name: 'ทะเบียน กล้ามเนื้อหัวใจเฉียบพลัน (ACS)',
        icon: 'fa-heart-pulse',
        color: '#d62828',
        sql: "(ov.pdx BETWEEN 'I20' AND 'I28' OR ov.pdx BETWEEN 'I30' AND 'I52')"
    },
    'stemi': {
        name: 'ทะเบียน STEMI',
        icon: 'fa-bolt',
        color: '#ffb703',
        sql: "(ov.pdx BETWEEN 'I210' AND 'I212' OR ov.pdx LIKE 'I214%')"
    },
    'all': {
        name: 'ทะเบียนผู้ป่วยทั้งหมดที่มีการดูแลในชุมชน',
        icon: 'fa-users',
        color: '#0077b6',
        sql: "ov.pdx BETWEEN 'A00' AND 'Z999' AND EXISTS (SELECT 1 FROM ovst_community_service oc2 WHERE oc2.vn = ov.vn)"
    },
    'deceased': {
        name: 'รายชื่อผู้เสียชีวิต',
        icon: 'fa-cross',
        color: '#6c757d',
        sql: "(ov.pdx ='Z515' OR ov.dx0 ='Z515' OR ov.dx1 ='Z515' OR ov.dx2 ='Z515' OR ov.dx3 ='Z515' OR ov.dx4 ='Z515' OR ov.dx5 ='Z515') AND pt.death='Y'"
    },
    'living': {
        name: 'ผู้ป่วยที่ยังมีชีวิต',
        icon: 'fa-smile',
        color: '#38b000',
        sql: "(ov.pdx ='Z515' OR ov.dx0 ='Z515' OR ov.dx1 ='Z515' OR ov.dx2 ='Z515' OR ov.dx3 ='Z515' OR ov.dx4 ='Z515' OR ov.dx5 ='Z515') AND (pt.death='N' OR pt.death IS NULL OR pt.death='')"
    }
};

/**
 * ดึงรายชื่อผู้ป่วยตามหมวดหมู่ พร้อมตัวกรอง วันที่, หน่วยบริการ, สถานะ
 */
async function getPatients(options = {}) {
    const {
        category = 'palliative',
        dt1 = '2018-01-01',
        dt2 = '2099-12-31',
        unit = '99999',
        status = '99999',
        search = ''
    } = options;

    const catObj = CATEGORY_DIAGS[category] || CATEGORY_DIAGS['palliative'];
    let whereClauses = [catObj.sql];
    let params = [];

    // วันที่
    whereClauses.push("ov.vstdate BETWEEN ? AND ?");
    params.push(dt1, dt2);

    // สถานะ เสียชีวิต / ยังมีชีวิต
    if (status === 'Y') {
        whereClauses.push("pt.death = 'Y'");
    } else if (status === 'N') {
        whereClauses.push("(pt.death = 'N' OR pt.death IS NULL OR pt.death = '')");
    }

    // คำค้นหา HN หรือ ชื่อ หรือ เลขบัตร
    if (search && search.trim()) {
        const term = `%${search.trim()}%`;
        whereClauses.push("(ov.hn LIKE ? OR pt.cid LIKE ? OR pt.fname LIKE ? OR pt.lname LIKE ?)");
        params.push(term, term, term, term);
    }

    const limitClause = (category === 'all') ? "LIMIT 300" : "LIMIT 1000";

    const coreSql = `
        SELECT 
            MAX(ov.vstdate) AS vstdate,
            ov.hn,
            ov.pttype,
            ov.pdx AS C
        FROM vn_stat ov
        ${(status === 'Y' || status === 'N' || (search && search.trim())) ? 'LEFT JOIN patient pt ON pt.hn = ov.hn' : ''}
        WHERE ${whereClauses.join(' AND ')}
        GROUP BY ov.hn
        ORDER BY vstdate DESC
        ${limitClause}
    `;

    const coreRows = await query(coreSql, params);
    if (!coreRows || coreRows.length === 0) return [];

    const hns = coreRows.map(r => r.hn);
    const placeholders = hns.map(() => '?').join(',');

    // Parallel high-performance batch fetching
    const [patientRows, pttypeRows, visitStats, fdhStats] = await Promise.all([
        query(`
            SELECT pt.hn, pt.cid, pt.pname, pt.fname, pt.lname, pt.birthday,
                   pt.addrpart, pt.moopart, pt.tmbpart, pt.chwpart, pt.amppart,
                   pt.death, th.full_name AS full_addr, reg.assigned_unit_name
            FROM patient pt
            LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart, pt.amppart, pt.tmbpart)
            LEFT JOIN palliative.palliative_registry reg ON reg.hn = pt.hn
            WHERE pt.hn IN (${placeholders})
        `, hns),
        query(`SELECT pttype, name FROM pttype`),
        query(`
            SELECT vn.hn,
                   COUNT(DISTINCT a1.ovst_community_service_id) AS dayc,
                   COUNT(DISTINCT CASE WHEN di.icd10 = 'Z718' THEN a1.ovst_community_service_id END) AS dayc1,
                   DATEDIFF(NOW(), MAX(a1.entry_datetime)) AS daym
            FROM ovst_community_service a1
            INNER JOIN vn_stat vn ON vn.vn = a1.vn
            LEFT JOIN ovstdiag di ON di.vn = vn.vn AND di.icd10 = 'Z718'
            WHERE vn.hn IN (${placeholders}) AND a1.ovst_community_service_type_id BETWEEN 1 AND 103
            GROUP BY vn.hn
        `, hns),
        query(`
            SELECT r.hn, ROUND(SUM(r.compensated), 2) AS fdh_compensated
            FROM repstminv.rep_data r
            WHERE r.hn IN (${placeholders}) AND r.compensated > 0
            GROUP BY r.hn
        `, hns)
    ]);

    // Build Fast Lookup Maps (O(1) dictionary lookups)
    const patientMap = new Map();
    patientRows.forEach(p => patientMap.set(p.hn, p));

    const pttypeMap = new Map();
    pttypeRows.forEach(pt => pttypeMap.set(pt.pttype, pt.name));

    const visitMap = new Map();
    visitStats.forEach(v => visitMap.set(v.hn, v));

    const fdhMap = new Map();
    fdhStats.forEach(f => fdhMap.set(f.hn, f.fdh_compensated));

    // Combine and build enriched result
    let enriched = coreRows.map((core) => {
        const pt = patientMap.get(core.hn) || {};
        const vs = visitMap.get(core.hn) || {};
        const fdhComp = fdhMap.get(core.hn) || 0;

        // Determine RPST Name (จำแนกแบบมาตรฐาน ไม่เกิดชื่อซ้ำ)
        const rpstName = resolveUnitName(pt.assigned_unit_name, pt.chwpart, pt.amppart, pt.tmbpart, pt.moopart);

        const age = pt.birthday ? (new Date().getFullYear() - new Date(pt.birthday).getFullYear()) : '-';
        const death = (pt.death === 'Y') ? 'Y' : 'N';
        const daym = vs.daym !== undefined && vs.daym !== null ? vs.daym : null;
        const visitStatus = calculateVisitStatus(daym, death);

        return {
            hn: core.hn,
            cid: pt.cid || '-',
            ptname: `${pt.pname || ''}${pt.fname || ''} ${pt.lname || ''}`.trim() || 'ไม่พบชื่อ',
            birthday: pt.birthday ? pt.birthday.toISOString().split('T')[0] : null,
            birthdayThai: formatThaiDate(pt.birthday),
            age,
            vstdate: core.vstdate ? core.vstdate.toISOString().split('T')[0] : null,
            vstdateThai: formatThaiDate(core.vstdate),
            addr: `${pt.addrpart || ''} หมู่ ${pt.moopart || ''} ${pt.full_addr || ''}`.trim(),
            rpst_name: rpstName,
            pttype_name: pttypeMap.get(core.pttype) || core.pttype || '-',
            pdx: core.C,
            dayc: vs.dayc || 0,
            dayc1: vs.dayc1 || 0,
            daym,
            death,
            fdhInfo: {
                compensated: parseFloat(fdhComp || 0),
                status: fdhComp > 0 ? 'ชดเชยแล้ว' : 'รอส่ง/ตรวจสอบ',
                auditStatus: '-'
            },
            visitStatus
        };
    });

    // กรองหน่วยบริการตาม user filter (หากไม่ได้เลือก "ทั้งหมด")
    if (unit && unit !== '99999' && unit !== '') {
        const targetUnitName = RPST_MAP[unit];
        if (targetUnitName) {
            const cleanTarget = targetUnitName.replace('รพ.สต.', '').replace('รพ.', '').trim();
            enriched = enriched.filter(r => (r.rpst_name || '').includes(cleanTarget));
        }
    }

    return enriched.map((row, index) => ({
        no: index + 1,
        ...row
    }));
}

/**
 * ดึงรายละเอียดการเยี่ยมบ้านของโรงพยาบาลสำหรับผู้ป่วยรายคน (Popup Modal)
 */
async function getHospitalVisits(hn) {
    // ข้อมูลผู้ป่วย
    const patientSql = `
        SELECT pt.hn, pt.cid, CONCAT(pt.pname, pt.fname, ' ', pt.lname) AS ptname,
               CONCAT(pt.addrpart, ' หมู่ ', pt.moopart, ' ', COALESCE(th.full_name, '')) AS addr,
               ${SQL_UNIT_EXPRESSION} AS rpst_name
        FROM patient pt
        LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart, pt.amppart, pt.tmbpart)
        LEFT JOIN palliative.palliative_registry reg ON reg.hn = pt.hn
        WHERE pt.hn = ? LIMIT 1
    `;
    const patients = await query(patientSql, [hn]);
    const patientInfo = patients[0] || {};

    // ประวัติการเยี่ยม
    const visitSql = `
        SELECT 
            DATE(oc.entry_datetime) AS vstdate,
            dt.name AS dtname,
            os.cc,
            os.hpi,
            GROUP_CONCAT(DISTINCT ot.ovst_community_service_type_name SEPARATOR ' | ') AS csname,
            os.temperature,
            os.pulse,
            os.rr,
            CONCAT(LEFT(os.bps, 3), '/', LEFT(os.bpd, 2)) AS bp
        FROM ovst_community_service oc
        INNER JOIN opdscreen os ON os.vn = oc.vn 
        INNER JOIN ovst_community_service_type ot ON ot.ovst_community_service_type_id = oc.ovst_community_service_type_id
        INNER JOIN (
            SELECT cs.vn 
            FROM ovst_community_service cs 
            INNER JOIN vn_stat vn ON vn.vn = cs.vn AND cs.ovst_community_service_type_id BETWEEN 1 AND 103 AND vn.hn = ?
        ) tb ON tb.vn = oc.vn
        LEFT JOIN doctor dt ON dt.code = oc.doctor
        WHERE os.hn = ? 
        GROUP BY oc.vn 
        ORDER BY oc.vn DESC
    `;
    const visits = await query(visitSql, [hn, hn]);

    const formattedVisits = visits.map(v => {
        const indicators = extractClinicalIndicators(v.hpi);
        return {
            vstdate: v.vstdate ? v.vstdate.toISOString().split('T')[0] : null,
            vstdateThai: formatThaiDate(v.vstdate),
            dtname: v.dtname || 'ไม่ระบุเจ้าหน้าที่',
            cc: v.cc || '-',
            hpi: v.hpi || '-',
            services: v.csname ? v.csname.split(' | ') : [],
            temperature: v.temperature,
            pulse: v.pulse,
            rr: v.rr,
            bp: v.bp,
            ...indicators
        };
    });

    return {
        patient: patientInfo,
        visits: formattedVisits
    };
}

/**
 * ดึงรายละเอียดการเยี่ยมบ้านพร้อมรหัส Z718
 */
async function getZ718Visits(hn) {
    const patientSql = `
        SELECT pt.hn, pt.cid, CONCAT(pt.pname, pt.fname, ' ', pt.lname) AS ptname,
               CONCAT(pt.addrpart, ' หมู่ ', pt.moopart, ' ', COALESCE(th.full_name, '')) AS addr,
               ${SQL_UNIT_EXPRESSION} AS rpst_name
        FROM patient pt
        LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart, pt.amppart, pt.tmbpart)
        LEFT JOIN palliative.palliative_registry reg ON reg.hn = pt.hn
        WHERE pt.hn = ? LIMIT 1
    `;
    const patients = await query(patientSql, [hn]);
    const patientInfo = patients[0] || {};

    const visitSql = `
        SELECT 
            DATE(oc.entry_datetime) AS vstdate,
            dt.name AS dtname,
            os.cc,
            os.hpi,
            GROUP_CONCAT(DISTINCT ot.ovst_community_service_type_name SEPARATOR ' | ') AS csname,
            di.icd10
        FROM ovst_community_service oc
        INNER JOIN opdscreen os ON os.vn = oc.vn 
        INNER JOIN ovst_community_service_type ot ON ot.ovst_community_service_type_id = oc.ovst_community_service_type_id
        INNER JOIN (
            SELECT cs.vn 
            FROM ovst_community_service cs 
            INNER JOIN vn_stat vn ON vn.vn = cs.vn AND cs.ovst_community_service_type_id BETWEEN 1 AND 103 AND vn.hn = ?
        ) tb ON tb.vn = oc.vn
        LEFT JOIN doctor dt ON dt.code = oc.doctor
        LEFT JOIN ovstdiag di ON oc.vn = di.vn
        LEFT JOIN vn_stat ov ON ov.vn = oc.vn
        WHERE (ov.pdx = 'Z515' OR ov.dx0 = 'Z515' OR ov.dx1 = 'Z515' OR ov.dx2 = 'Z515' OR ov.dx3 = 'Z515' OR ov.dx4 = 'Z515' OR ov.dx5 = 'Z515')
          AND (ov.pdx = 'Z718' OR ov.dx0 = 'Z718' OR ov.dx1 = 'Z718' OR ov.dx2 = 'Z718' OR ov.dx3 = 'Z718' OR ov.dx4 = 'Z718' OR ov.dx5 = 'Z718')
          AND os.hn = ?
        GROUP BY oc.vn
        ORDER BY oc.vn DESC
    `;
    const visits = await query(visitSql, [hn, hn]);

    const formattedVisits = visits.map(v => {
        const indicators = extractClinicalIndicators(v.hpi);
        return {
            vstdate: v.vstdate ? v.vstdate.toISOString().split('T')[0] : null,
            vstdateThai: formatThaiDate(v.vstdate),
            dtname: v.dtname || 'ไม่ระบุ',
            cc: v.cc || '-',
            hpi: v.hpi || '-',
            services: v.csname ? v.csname.split(' | ') : [],
            icd10: v.icd10,
            ...indicators
        };
    });

    return {
        patient: patientInfo,
        visits: formattedVisits
    };
}

/**
 * ดึงข้อมูล FDH Checker & การชดเชยเงิน STM ของผู้ป่วยรายคน (เชื่อมโยงแทน RCM)
 */
async function getPatientFDHClaim(hn) {
    const patientSql = `
        SELECT pt.hn, pt.cid, CONCAT(pt.pname, pt.fname, ' ', pt.lname) AS ptname,
               CONCAT(pt.addrpart, ' หมู่ ', pt.moopart, ' ', COALESCE(th.full_name, '')) AS addr,
               ${SQL_UNIT_EXPRESSION} AS rpst_name
        FROM patient pt
        LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart, pt.amppart, pt.tmbpart)
        LEFT JOIN palliative.palliative_registry reg ON reg.hn = pt.hn
        WHERE pt.hn = ? LIMIT 1
    `;
    const patients = await query(patientSql, [hn]);
    const patientInfo = patients[0] || {};

    let stmRecords = [];
    let fdhStatusLogs = [];
    let fdhAuditRows = [];

    try {
        // 1. รายการชดเชยเงิน STM จาก repstminv.rep_data
        const stmSql = `
            SELECT r.rep_no, r.admdate, r.compensated, r.income, r.yearbudget, r.projectcode, r.filename
            FROM repstminv.rep_data r
            WHERE r.hn = ?
            ORDER BY r.admdate DESC
            LIMIT 50
        `;
        stmRecords = await query(stmSql, [hn]);
    } catch (err) {
        console.error('FDH stmRecords query error:', err.message);
    }

    try {
        // 2. สถานะส่งเคลม FDH MOPH จาก hos.fdh_claim_status
        const fdhSql = `
            SELECT f.vn, f.fdh_claim_status_message, f.fdh_claim_status_datetime, f.error_code, f.fdh_claim_status_response
            FROM hos.fdh_claim_status f
            WHERE f.hn = ?
            ORDER BY f.fdh_claim_status_datetime DESC
            LIMIT 30
        `;
        fdhStatusLogs = await query(fdhSql, [hn]);
    } catch (err) {
        console.error('FDH status query error:', err.message);
    }

    try {
        // 3. ตรวจสอบ FDH Detail audit จาก repstminv.fdh_claim_detail_row
        const auditSql = `
            SELECT d.claim_code, d.claim_status, d.patient_type, d.service_datetime, d.maininscl, d.upload_uid
            FROM repstminv.fdh_claim_detail_row d
            WHERE d.hn = ?
            ORDER BY d.service_datetime DESC
            LIMIT 30
        `;
        fdhAuditRows = await query(auditSql, [hn]);
    } catch (err) {
        console.error('FDH audit query error:', err.message);
    }

    return {
        patient: patientInfo,
        stmRecords: stmRecords.map(r => ({
            ...r,
            admDateThai: formatThaiDate(r.admdate)
        })),
        fdhStatusLogs: fdhStatusLogs.map(f => ({
            ...f,
            statusDateThai: formatThaiDate(f.fdh_claim_status_datetime)
        })),
        fdhAuditRows: fdhAuditRows.map(a => ({
            ...a,
            serviceDateThai: formatThaiDate(a.service_datetime)
        }))
    };
}

// Backward compatibility alias
const getPatientEClaim = getPatientFDHClaim;

/**
 * สถิติ Dashboard และกราฟสรุปภาพรวม (Overview Charts)
 */
async function getOverviewCharts(dt1 = '2018-01-01', dt2 = '2099-12-31') {
    // 1. กราฟแท่ง: ผู้ป่วยเสียชีวิตแยกตาม Diag
    const deceasedDiagSql = `
        SELECT COUNT(DISTINCT ov.hn) AS count, ov.pdx 
        FROM vn_stat ov 
        LEFT JOIN patient pt ON pt.hn = ov.hn
        WHERE (ov.pdx = 'Z515' OR ov.dx0 = 'Z515' OR ov.dx1 = 'Z515' OR ov.dx2 = 'Z515' OR ov.dx3 = 'Z515' OR ov.dx4 = 'Z515' OR ov.dx5 = 'Z515')
          AND ov.vstdate BETWEEN ? AND ? 
          AND pt.death = 'Y' 
        GROUP BY ov.pdx 
        ORDER BY count DESC 
        LIMIT 10
    `;
    const deceasedDiag = await query(deceasedDiagSql, [dt1, dt2]);

    // 2. กราฟวงกลม: ผู้ป่วยแยกตาม รพ.สต. (ใช้ SQL_UNIT_EXPRESSION ป้องกันชื่อซ้ำ)
    const rpstSql = `
        SELECT 
            ${SQL_UNIT_EXPRESSION} AS rpst_name,
            COUNT(DISTINCT ov.hn) AS count
        FROM vn_stat ov 
        LEFT JOIN patient pt ON pt.hn = ov.hn
        LEFT JOIN palliative.palliative_registry reg ON reg.hn = ov.hn
        WHERE (ov.pdx = 'Z515' OR ov.dx0 = 'Z515' OR ov.dx1 = 'Z515' OR ov.dx2 = 'Z515' OR ov.dx3 = 'Z515' OR ov.dx4 = 'Z515' OR ov.dx5 = 'Z515')
          AND ov.vstdate BETWEEN ? AND ? 
        GROUP BY rpst_name 
        ORDER BY count DESC
    `;
    const rpstDistribution = await query(rpstSql, [dt1, dt2]);

    // 3. กราฟวงกลม: ผู้ป่วยที่ยังมีชีวิต แยกตาม Diag
    const livingDiagSql = `
        SELECT COUNT(DISTINCT ov.hn) AS count, ov.pdx 
        FROM vn_stat ov 
        LEFT JOIN patient pt ON pt.hn = ov.hn
        WHERE (ov.pdx = 'Z515' OR ov.dx0 = 'Z515' OR ov.dx1 = 'Z515' OR ov.dx2 = 'Z515' OR ov.dx3 = 'Z515' OR ov.dx4 = 'Z515' OR ov.dx5 = 'Z515')
          AND ov.vstdate BETWEEN ? AND ? 
          AND (pt.death = 'N' OR pt.death IS NULL OR pt.death = '') 
        GROUP BY ov.pdx 
        ORDER BY count DESC 
        LIMIT 10
    `;
    const livingDiag = await query(livingDiagSql, [dt1, dt2]);

    // 4. สรุปตัวเลขการเยี่ยมบ้าน (สถิติตรวจเช็คการเยี่ยมบ้าน)
    const visitStatsSql = `
        SELECT 
            COUNT(DISTINCT ov.hn) AS total_patients,
            COUNT(DISTINCT CASE WHEN pt.death = 'Y' THEN ov.hn END) AS deceased_patients,
            COUNT(DISTINCT CASE WHEN pt.death != 'Y' OR pt.death IS NULL THEN ov.hn END) AS living_patients,
            COUNT(DISTINCT CASE WHEN oc.entry_datetime IS NOT NULL THEN ov.hn END) AS visited_patients,
            COUNT(DISTINCT CASE WHEN oc.entry_datetime IS NULL THEN ov.hn END) AS unvisited_patients
        FROM vn_stat ov
        LEFT JOIN patient pt ON pt.hn = ov.hn
        LEFT JOIN ovst_community_service oc ON oc.vn = ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
        WHERE (ov.pdx = 'Z515' OR ov.dx0 = 'Z515' OR ov.dx1 = 'Z515' OR ov.dx2 = 'Z515' OR ov.dx3 = 'Z515' OR ov.dx4 = 'Z515' OR ov.dx5 = 'Z515')
          AND ov.vstdate BETWEEN ? AND ?
    `;
    const visitStats = await query(visitStatsSql, [dt1, dt2]);

    return {
        summary: visitStats[0] || {},
        deceasedDiag,
        rpstDistribution,
        livingDiag
    };
}

/**
 * กราฟเฉพาะหมวดหมู่โรค (Category Specific Charts)
 * เพื่อตอบโจทย์ผู้ใช้: "แต่ละหมวดกราฟครบถ้วน เพิ่มเติมได้แต่ห้ามขาด"
 */
async function getCategoryCharts(category = 'cancer', dt1 = '2018-01-01', dt2 = '2099-12-31') {
    const catObj = CATEGORY_DIAGS[category] || CATEGORY_DIAGS['cancer'];

    // 1. กราฟแยกตาม รพ.สต. ในหมวดนี้ (ใช้ SQL_UNIT_EXPRESSION ป้องกันชื่อซ้ำ)
    const rpstSql = `
        SELECT 
            ${SQL_UNIT_EXPRESSION} AS rpst_name,
            COUNT(DISTINCT ov.hn) AS count
        FROM vn_stat ov 
        LEFT JOIN patient pt ON pt.hn = ov.hn
        LEFT JOIN palliative.palliative_registry reg ON reg.hn = ov.hn
        WHERE ${catObj.sql} AND ov.vstdate BETWEEN ? AND ?
        GROUP BY rpst_name
        ORDER BY count DESC
    `;
    const rpstData = await query(rpstSql, [dt1, dt2]);

    // 2. กราฟสถานะการมีชีวิต / เสียชีวิต
    const statusSql = `
        SELECT 
            IF(pt.death = 'Y', 'Y', 'N') AS death_status,
            COUNT(DISTINCT ov.hn) AS count
        FROM vn_stat ov 
        LEFT JOIN patient pt ON pt.hn = ov.hn
        WHERE ${catObj.sql} AND ov.vstdate BETWEEN ? AND ?
        GROUP BY death_status
    `;
    const statusRaw = await query(statusSql, [dt1, dt2]);
    const statusData = statusRaw.map(r => ({
        status_label: r.death_status === 'Y' ? 'เสียชีวิตแล้ว' : 'ยังมีชีวิต',
        count: r.count
    }));

    // 3. กราฟความครอบคลุมการเยี่ยมบ้าน (ได้รับการเยี่ยม vs ยังไม่ได้เยี่ยม)
    const visitCoverSql = `
        SELECT 
            IF((SELECT COUNT(*) FROM ovst_community_service a1 INNER JOIN vn_stat vn ON vn.vn = a1.vn WHERE vn.hn = ov.hn AND a1.ovst_community_service_type_id BETWEEN 1 AND 103) > 0, 1, 0) AS has_visit,
            COUNT(DISTINCT ov.hn) AS count
        FROM vn_stat ov 
        LEFT JOIN patient pt ON pt.hn = ov.hn
        WHERE ${catObj.sql} AND ov.vstdate BETWEEN ? AND ?
        GROUP BY has_visit
    `;
    const visitCoverRaw = await query(visitCoverSql, [dt1, dt2]);
    const visitCoverData = visitCoverRaw.map(r => ({
        visit_label: r.has_visit === 1 ? 'ได้รับการเยี่ยมบ้านแล้ว' : 'ยังไม่ได้รับการเยี่ยม',
        count: r.count
    }));

    // 4. สรุป Top 5 Diag ย่อยในหมวด
    const topDiagSql = `
        SELECT ov.pdx AS diag, COUNT(DISTINCT ov.hn) AS count
        FROM vn_stat ov 
        LEFT JOIN patient pt ON pt.hn = ov.hn
        WHERE ${catObj.sql} AND ov.vstdate BETWEEN ? AND ?
        GROUP BY ov.pdx
        ORDER BY count DESC
        LIMIT 8
    `;
    const topDiagData = await query(topDiagSql, [dt1, dt2]);

    return {
        categoryInfo: { key: category, name: catObj.name, color: catObj.color, icon: catObj.icon },
        rpstData,
        statusData,
        visitCoverData,
        topDiagData
    };
}

/**
 * รายงาน 9 หมวดของระบบ Palliative Care (getdata)
 */
async function getPalliativeReports(reportType, dt1 = '2019-01-01', dt2 = '2099-12-31') {
    let countSql = '';
    let listSql = '';
    let reportTitle = '';

    switch (reportType) {
        case 'new_patients': // 1. ผู้ป่วยรายใหม่
            reportTitle = 'จำนวนผู้ป่วยรายใหม่ (Z515)';
            countSql = `
                SELECT COUNT(DISTINCT o.hn) AS total
                FROM ovstdiag o
                LEFT JOIN vn_stat v ON v.vn = o.vn
                WHERE o.icd10 = 'Z515' AND v.vstdate BETWEEN ? AND ?
                  AND o.hn NOT IN (
                      SELECT DISTINCT o2.hn 
                      FROM ovstdiag o2 
                      LEFT JOIN vn_stat v2 ON v2.vn = o2.vn 
                      WHERE o2.icd10 = 'Z515' AND v2.vstdate < ?
                  )
            `;
            listSql = `
                SELECT DISTINCT o.hn, pp.name AS pttype_name, (YEAR(NOW()) - YEAR(pt.birthday)) AS age,
                       v.pdx, o.vstdate AS day1, CONCAT(pt.pname, pt.fname, ' ', pt.lname) AS ptname, pt.informaddr AS addr
                FROM ovstdiag o
                LEFT JOIN vn_stat v ON v.vn = o.vn
                LEFT JOIN patient pt ON pt.hn = o.hn
                LEFT JOIN pttype pp ON pp.pttype = v.pttype
                WHERE o.icd10 = 'Z515' AND v.vstdate BETWEEN ? AND ?
                  AND o.hn NOT IN (
                      SELECT DISTINCT o2.hn 
                      FROM ovstdiag o2 
                      LEFT JOIN vn_stat v2 ON v2.vn = o2.vn 
                      WHERE o2.icd10 = 'Z515' AND v2.vstdate < ?
                  )
                GROUP BY o.hn ORDER BY o.vn DESC
            `;
            break;

        case 'new_vs_old': // 2. รายใหม่ / รายเก่า
            reportTitle = 'ผู้ป่วย Palliative Care ทั้งหมด (เปรียบเทียบรายใหม่และรายเก่า)';
            countSql = `
                SELECT COUNT(DISTINCT o.hn) AS total
                FROM ovstdiag o
                LEFT JOIN vn_stat v ON v.vn = o.vn
                WHERE o.icd10 = 'Z515' AND v.vstdate BETWEEN ? AND ?
            `;
            listSql = `
                SELECT DISTINCT o.hn, CONCAT(pt.pname, pt.fname, ' ', pt.lname) AS ptname,
                       pt.informaddr AS addr, v.main_pdx AS pdx,
                       IF(o.hn IN (
                           SELECT DISTINCT o2.hn FROM ovstdiag o2 LEFT JOIN vn_stat v2 ON v2.vn = o2.vn 
                           WHERE o2.icd10 = 'Z515' AND v2.vstdate < ?
                       ), 'รายเก่า', 'รายใหม่') AS patient_type
                FROM ovstdiag o
                LEFT JOIN vn_stat v ON v.vn = o.vn
                LEFT JOIN patient pt ON pt.hn = o.hn
                WHERE o.icd10 = 'Z515' AND v.vstdate BETWEEN ? AND ?
                GROUP BY o.hn ORDER BY o.vn DESC
            `;
            break;

        case 'pain_morphine': // 3. ได้รับ Pain & Morphine
            reportTitle = 'ผู้ป่วยที่ได้รับยาบรรเทาอาการปวด / Morphine';
            countSql = `
                SELECT COUNT(DISTINCT op.hn) AS total
                FROM opitemrece op 
                WHERE op.icode IN (1580001, 1590005, 1000156, 1000406, 1000412, 1000413, 1000430, 1550025)
                  AND op.vstdate BETWEEN ? AND ?
            `;
            listSql = `
                SELECT DISTINCT op.hn, CONCAT(pt.pname, pt.fname, ' ', pt.lname) AS ptname,
                       pt.informaddr AS addr, vn.main_pdx AS pdx, op.vstdate
                FROM opitemrece op 
                LEFT JOIN vn_stat vn ON vn.vn = op.vn
                LEFT JOIN patient pt ON pt.hn = op.hn
                WHERE op.icode IN (1580001, 1590005, 1000156, 1000406, 1000412, 1000413, 1000430, 1550025)
                  AND op.vstdate BETWEEN ? AND ?
                GROUP BY op.hn ORDER BY op.vstdate DESC
            `;
            break;

        case 'oxygen': // 4. ได้รับ Oxygen
            reportTitle = 'ผู้ป่วยที่ได้รับ Oxygen';
            countSql = `
                SELECT COUNT(DISTINCT op.hn) AS total
                FROM opitemrece op 
                WHERE op.icode IN (3003056, 3003409, 3003533, 3003631)
                  AND op.vstdate BETWEEN ? AND ?
            `;
            listSql = `
                SELECT DISTINCT op.hn, CONCAT(pt.pname, pt.fname, ' ', pt.lname) AS ptname,
                       pt.informaddr AS addr, vn.main_pdx AS pdx, op.vstdate
                FROM opitemrece op 
                LEFT JOIN vn_stat vn ON vn.vn = op.vn
                LEFT JOIN patient pt ON pt.hn = op.hn
                WHERE op.icode IN (3003056, 3003409, 3003533, 3003631)
                  AND op.vstdate BETWEEN ? AND ?
                GROUP BY op.hn ORDER BY op.vstdate DESC
            `;
            break;

        case 'unvisited': // 5. ไม่ได้รับการเยี่ยมบ้าน
            reportTitle = 'ผู้ป่วยที่ยังไม่ได้รับการเยี่ยมบ้าน';
            countSql = `
                SELECT COUNT(DISTINCT o.hn) AS total
                FROM ovstdiag o
                LEFT JOIN vn_stat v ON v.vn = o.vn
                WHERE o.icd10 = 'Z515' AND v.vstdate BETWEEN ? AND ?
                  AND (SELECT COUNT(*) FROM ovst_community_service a1 INNER JOIN vn_stat vn ON vn.vn = a1.vn WHERE vn.hn = v.hn AND a1.ovst_community_service_type_id BETWEEN 1 AND 103) = 0
            `;
            listSql = `
                SELECT DISTINCT o.hn, CONCAT(pt.pname, pt.fname, ' ', pt.lname) AS ptname,
                       pt.informaddr AS addr, v.main_pdx AS pdx, v.vstdate
                FROM ovstdiag o
                LEFT JOIN vn_stat v ON v.vn = o.vn
                LEFT JOIN patient pt ON pt.hn = o.hn
                WHERE o.icd10 = 'Z515' AND v.vstdate BETWEEN ? AND ?
                  AND (SELECT COUNT(*) FROM ovst_community_service a1 INNER JOIN vn_stat vn ON vn.vn = a1.vn WHERE vn.hn = v.hn AND a1.ovst_community_service_type_id BETWEEN 1 AND 103) = 0
                GROUP BY o.hn ORDER BY v.vstdate DESC
            `;
            break;

        case 'visited': // 6. ได้รับการเยี่ยมบ้าน
            reportTitle = 'ผู้ป่วยที่ได้รับการเยี่ยมบ้านแล้ว';
            countSql = `
                SELECT COUNT(DISTINCT o.hn) AS total
                FROM ovstdiag o
                LEFT JOIN vn_stat v ON v.vn = o.vn
                INNER JOIN ovst_community_service oc ON oc.vn = o.vn
                WHERE o.icd10 = 'Z515' AND v.vstdate BETWEEN ? AND ?
                  AND oc.entry_datetime IS NOT NULL
            `;
            listSql = `
                SELECT DISTINCT o.hn, CONCAT(pt.pname, pt.fname, ' ', pt.lname) AS ptname,
                       pt.informaddr AS addr, v.main_pdx AS pdx, oc.entry_datetime AS entry_date
                FROM ovstdiag o
                LEFT JOIN vn_stat v ON v.vn = o.vn
                LEFT JOIN patient pt ON pt.hn = o.hn
                INNER JOIN ovst_community_service oc ON oc.vn = o.vn
                WHERE o.icd10 = 'Z515' AND v.vstdate BETWEEN ? AND ?
                  AND oc.entry_datetime IS NOT NULL
                GROUP BY o.hn ORDER BY entry_date DESC
            `;
            break;

        case 'disease_summary': // 7. สรุปกลุ่มโรค
            reportTitle = 'สรุปผู้ป่วยแยกตามกลุ่มโรค (PDX)';
            countSql = `
                SELECT COUNT(DISTINCT o.hn) AS total
                FROM ovstdiag o
                LEFT JOIN vn_stat v ON v.vn = o.vn
                WHERE o.icd10 = 'Z515' AND v.vstdate BETWEEN ? AND ?
            `;
            listSql = `
                SELECT DISTINCT o.hn, CONCAT(pt.pname, pt.fname, ' ', pt.lname) AS ptname,
                       pt.informaddr AS addr, v.main_pdx AS pdx, v.age_y AS age1
                FROM ovstdiag o
                LEFT JOIN vn_stat v ON v.vn = o.vn
                LEFT JOIN patient pt ON pt.hn = o.hn
                WHERE o.icd10 = 'Z515' AND v.vstdate BETWEEN ? AND ?
                ORDER BY v.main_pdx
            `;
            break;

        case 'deceased_report': // 8. ผู้ป่วยที่เสียชีวิตแล้ว
            reportTitle = 'ผู้ป่วย Palliative Care ที่เสียชีวิตแล้ว';
            countSql = `
                SELECT COUNT(DISTINCT o.hn) AS total
                FROM ovstdiag o
                LEFT JOIN vn_stat v ON v.vn = o.vn
                LEFT JOIN patient pt ON pt.hn = o.hn
                WHERE o.icd10 = 'Z515' AND v.vstdate BETWEEN ? AND ? AND pt.death = 'Y'
            `;
            listSql = `
                SELECT DISTINCT o.hn, CONCAT(pt.pname, pt.fname, ' ', pt.lname) AS ptname,
                       pt.informaddr AS addr, v.main_pdx AS pdx,
                       CASE dt.death_place
                           WHEN '1' THEN 'ในโรงพยาบาล'
                           WHEN '2' THEN 'นอกสถานพยาบาล'
                           ELSE 'ไม่ระบุ'
                       END AS death_place_text,
                       dt.death_date, v.vstdate
                FROM ovstdiag o
                LEFT JOIN vn_stat v ON v.vn = o.vn
                LEFT JOIN patient pt ON pt.hn = o.hn
                LEFT JOIN death dt ON dt.hn = o.hn
                WHERE o.icd10 = 'Z515' AND v.vstdate BETWEEN ? AND ? AND pt.death = 'Y'
                GROUP BY o.hn ORDER BY dt.death_date DESC
            `;
            break;

        case 'missing_z718': // 9. ผู้ป่วยที่ไม่มีรหัส Z718
        default:
            reportTitle = 'ผู้ป่วยที่ได้รับการเยี่ยมแต่ไม่มีรหัส Z718';
            countSql = `
                SELECT COUNT(DISTINCT o.hn) AS total
                FROM ovstdiag o
                LEFT JOIN vn_stat v ON v.vn = o.vn
                INNER JOIN ovst_community_service oc ON oc.vn = o.vn
                WHERE o.icd10 = 'Z515' AND v.vstdate BETWEEN ? AND ?
                  AND o.icd10 != 'Z718' AND oc.entry_datetime IS NOT NULL
            `;
            listSql = `
                SELECT DISTINCT o.hn, CONCAT(pt.pname, pt.fname, ' ', pt.lname) AS ptname,
                       pt.informaddr AS addr, v.main_pdx AS pdx, oc.entry_datetime AS entry_date
                FROM ovstdiag o
                LEFT JOIN vn_stat v ON v.vn = o.vn
                LEFT JOIN patient pt ON pt.hn = o.hn
                INNER JOIN ovst_community_service oc ON oc.vn = o.vn
                WHERE o.icd10 = 'Z515' AND v.vstdate BETWEEN ? AND ?
                  AND o.icd10 != 'Z718' AND oc.entry_datetime IS NOT NULL
                GROUP BY o.hn ORDER BY entry_date DESC
            `;
            break;
    }

    // Parameters handling
    const countParams = (reportType === 'new_patients') ? [dt1, dt2, dt1] : [dt1, dt2];
    const listParams = (reportType === 'new_patients' || reportType === 'new_vs_old') ? [dt1, dt2, dt1] : [dt1, dt2];

    const countRes = await query(countSql, countParams);
    const rows = await query(listSql, listParams);

    return {
        title: reportTitle,
        totalCount: countRes[0] ? countRes[0].total : 0,
        rows: rows.map((r, i) => ({
            no: i + 1,
            ...r,
            vstdateThai: r.vstdate ? formatThaiDate(r.vstdate) : (r.day1 ? formatThaiDate(r.day1) : '-'),
            deathDateThai: r.death_date ? formatThaiDate(r.death_date) : '-',
            entryDateThai: r.entry_date ? formatThaiDate(r.entry_date) : '-'
        }))
    };
}

/**
 * รายการเบิก E-Claim (medical.php)
 */
async function getMedicalEClaimReport(dt1, dt2) {
    const sql = `
        SELECT 
            op.vn, op.vstdate, os.vsttime, pt.hn, CONCAT(pt.pname, pt.fname, ' ', pt.lname) AS ptname,
            vn.pdx, vn.dx0, vn.dx1, vn.dx2, vn.dx3, vn.dx4, vn.dx5, os.cc,
            IF(op.icode IN ('1000156','1000406','1000412','1000413','1000430','1550025','1580001'), CONCAT(dr.name, ' ', dr.strength),
            IF(op.icode IN ('3003603','3003604'), nd.name,
            IF(op.icode IN ('3003056','3003409','3003533','3003631'), nd.name, ''))) AS itemName,
            ROUND(op.unitprice, 2) AS unitprice,
            op.qty,
            ROUND(op.sum_price, 2) AS sum_price,
            CONCAT(COALESCE(u.name1, ''), ' ', COALESCE(u.name2, ''), ' ', COALESCE(u.name3, '')) AS drug_usage
        FROM vn_stat vn
        INNER JOIN patient pt ON pt.hn = vn.hn
        INNER JOIN opitemrece op ON op.vn = vn.vn
        INNER JOIN opdscreen os ON os.vn = vn.vn
        LEFT JOIN drugitems dr ON dr.icode = op.icode
        LEFT JOIN nondrugitems nd ON nd.icode = op.icode 
        LEFT JOIN sp_use u ON u.sp_use = op.sp_use
        WHERE op.vstdate BETWEEN ? AND ? 
          AND op.icode IN ('1000156','1000406','1000412','1000413','1000430','1550025','1580001','3003603','3003604','3003056','3003409','3003533','3003631')
          AND (vn.pdx = 'Z515' OR vn.dx0 = 'Z515' OR vn.dx1 = 'Z515' OR vn.dx2 = 'Z515' OR vn.dx3 = 'Z515' OR vn.dx4 = 'Z515' OR vn.dx5 = 'Z515')
        ORDER BY vn.vn DESC, vn.hn
    `;
    const rows = await query(sql, [dt1, dt2]);
    let grandTotal = 0;
    const formatted = rows.map((r, i) => {
        grandTotal += parseFloat(r.sum_price || 0);
        return {
            no: i + 1,
            ...r,
            vstdateThai: formatThaiDate(r.vstdate)
        };
    });

    return {
        rows: formatted,
        grandTotal: Math.round(grandTotal * 100) / 100
    };
}

/**
 * รายการ CT-Scan (ctscan.php)
 */
async function getCTScanReport(dt1, dt2, hipdataCode = '0') {
    let pttypeFilter = '';
    if (hipdataCode === '1') pttypeFilter = " AND pt.hipdata_code IN ('UCS') ";
    else if (hipdataCode === '2') pttypeFilter = " AND pt.hipdata_code IN ('OFC','LGO') ";
    else if (hipdataCode === '3') pttypeFilter = " AND pt.hipdata_code IN ('SSS') ";
    else if (hipdataCode === '4') pttypeFilter = " AND pt.hipdata_code NOT IN ('SSS','OFC','LGO','UCS') ";

    const sql = `
        SELECT 
            op.vstdate, pa.cid, pa.hn, CONCAT(pa.pname, pa.fname, ' ', pa.lname) AS paname,
            pt.pttype, pt.name AS ptname, pt.hipdata_code, dr.name AS ctname, op.qty,
            ROUND(op.unitprice, 0) AS unitprice,
            ROUND(op.sum_price, 0) AS sum_price,
            IF(wa.name IS NOT NULL, wa.name, IF(er.vn IS NOT NULL, 'ER', 'OPD')) AS depname,
            GROUP_CONCAT(DISTINCT d.icd10) AS icd9
        FROM opitemrece op
        LEFT JOIN nondrugitems dr ON dr.icode = op.icode
        LEFT JOIN patient pa ON pa.hn = op.hn
        LEFT JOIN pttype pt ON pt.pttype = op.pttype
        LEFT JOIN an_stat an ON an.an = op.an
        LEFT JOIN ward wa ON wa.ward = an.ward
        LEFT JOIN er_regist er ON er.vn = op.vn
        LEFT JOIN ovstdiag d ON d.vn = op.vn
        WHERE op.icode IN ('3003202','3003205','3003207','3003209','3003210','3003214','3003222','3003224','3003225','3003226','3003228','3003229','3003230','3003614','3003618')
          AND op.vstdate BETWEEN ? AND ? ${pttypeFilter}
        GROUP BY op.vstdate, op.hn, op.icode 
        ORDER BY op.vstdate DESC, op.hn
    `;
    const rows = await query(sql, [dt1, dt2]);
    let grandTotal = 0;
    const formatted = rows.map((r, i) => {
        grandTotal += parseFloat(r.sum_price || 0);
        return {
            no: i + 1,
            ...r,
            vstdateThai: formatThaiDate(r.vstdate)
        };
    });

    return {
        rows: formatted,
        grandTotal
    };
}

/**
 * รายชื่อส่งเบิกที่ได้รับการชดเชยเงินจาก FDH Checker (STM)
 * เชื่อมโยงข้อมูลผ่าน repstminv.rep_data แทนระบบ RCM เดิม
 */
async function getFDHPaidList(options = {}) {
    const { yearbudget = '', search = '' } = options;
    let whereClauses = [
        "(vn.pdx = 'Z515' OR vn.dx0 = 'Z515' OR vn.dx1 = 'Z515' OR vn.dx2 = 'Z515' OR vn.dx3 = 'Z515' OR vn.dx4 = 'Z515' OR vn.dx5 = 'Z515')",
        "r.compensated > 0"
    ];
    let params = [];

    if (yearbudget) {
        whereClauses.push("r.yearbudget = ?");
        params.push(yearbudget);
    }

    if (search && search.trim()) {
        const term = `%${search.trim()}%`;
        whereClauses.push("(r.hn LIKE ? OR r.pid LIKE ? OR r.patient_name LIKE ? OR r.rep_no LIKE ?)");
        params.push(term, term, term, term);
    }

    try {
        const sql = `
            SELECT 
                r.id, r.hn, r.pid, r.patient_name, r.rep_no, r.admdate, r.compensated, r.income, r.yearbudget, r.projectcode, r.filename
            FROM repstminv.rep_data r
            INNER JOIN hos.vn_stat vn ON vn.hn = r.hn
            WHERE ${whereClauses.join(' AND ')}
            ORDER BY r.admdate DESC
            LIMIT 500
        `;
        const rows = await query(sql, params);

        let totalPaid = 0;
        const patientHnSet = new Set();

        const formatted = rows.map((r, i) => {
            const comp = parseFloat(r.compensated || 0);
            totalPaid += comp;
            patientHnSet.add(r.hn);
            return {
                no: i + 1,
                id: r.id,
                HN: r.hn,
                PID: r.pid,
                PtName: r.patient_name,
                PallativeCare: comp,
                AdmDate: r.admdate ? r.admdate.toISOString().split('T')[0] : null,
                admDateThai: formatThaiDate(r.admdate),
                Rep: r.rep_no,
                YearBudget: r.yearbudget,
                ProjectCode: r.projectcode || 'Palliative Care',
                Filename: r.filename
            };
        });

        let years = ['2569', '2568', '2567', '2566'];
        try {
            const yearRows = await query("SELECT DISTINCT yearbudget FROM repstminv.rep_data WHERE yearbudget IS NOT NULL AND yearbudget != '' ORDER BY yearbudget DESC LIMIT 10");
            if (yearRows && yearRows.length) {
                years = yearRows.map(y => y.yearbudget).filter(Boolean);
            }
        } catch {}

        return {
            rows: formatted,
            totalPaid: Math.round(totalPaid * 100) / 100,
            totalPatients: patientHnSet.size,
            totalCount: rows.length,
            years
        };
    } catch (err) {
        console.error('getFDHPaidList error:', err.message);
        return { rows: [], totalPaid: 0, totalPatients: 0, totalCount: 0 };
    }
}

// Backward compatibility
const getEClaimPaidList = getFDHPaidList;

/**
 * ประวัติการรักษาผู้ป่วยฉบับเต็ม (cctiem.php)
 * ค้นหาด้วย CID หรือ HN
 */
async function getPatientFullHistory(searchKey) {
    const cleanKey = (searchKey || '').trim();
    if (!cleanKey) return { patient: null, visits: [] };

    // 1. ข้อมูลทั่วไปผู้ป่วย
    const patientSql = `
        SELECT 
            p.hn, p.cid, CONCAT(p.pname, p.fname, ' ', p.lname) AS pname,
            TIMESTAMPDIFF(YEAR, p.birthday, CURDATE()) AS cnt_year,
            p.birthday,
            p.drugallergy,
            p.clinic,
            CONCAT(p.addrpart, ' หมู่ ', p.moopart, ' ', COALESCE(t.full_name, '')) AS addr
        FROM patient p
        LEFT JOIN thaiaddress t ON t.addressid = CONCAT(p.chwpart, p.amppart, p.tmbpart)
        WHERE p.cid = ? OR p.hn = ?
        LIMIT 1
    `;
    const pRows = await query(patientSql, [cleanKey, cleanKey]);
    if (pRows.length === 0) return { patient: null, visits: [] };

    const patient = pRows[0];
    patient.birthdayThai = formatThaiDate(patient.birthday);

    // 2. ประวัติการมารับบริการ OPD + IPD
    const visitsSql = `
        SELECT * FROM (
            (SELECT 
                v.vn, v.vstdate AS vst, s.bw, s.height, 
                CONCAT(LEFT(s.bps, 3), '/', LEFT(s.bpd, 2)) AS bp,
                s.temperature, s.pulse, s.rr, 
                v.pdx, v.dx0, v.dx1, v.dx2, v.dx3, v.dx4, v.dx5, 
                s.cc, l.lab_order_number, l.form_name, 'OPD' AS tp
             FROM vn_stat v 
             LEFT OUTER JOIN lab_head l ON l.vn = v.vn 
             INNER JOIN patient p ON p.hn = v.hn
             INNER JOIN opdscreen s ON s.vn = v.vn
             WHERE p.hn = ?)
            UNION
            (SELECT 
                v.an AS vn, l.order_date AS vst, s.bw, s.height, 
                CONCAT(s.bpd, '/', s.bps) AS bp,
                s.temperature, s.pulse, s.rr, 
                v.pdx, v.dx0, v.dx1, v.dx2, v.dx3, v.dx4, v.dx5, 
                i.prediag AS cc, l.lab_order_number, l.form_name, 'IPD' AS tp
             FROM an_stat v 
             LEFT OUTER JOIN lab_head l ON l.vn = v.an 
             INNER JOIN patient p ON p.hn = v.hn 
             INNER JOIN ipt i ON i.an = v.an
             INNER JOIN opdscreen s ON s.vn = v.vn
             WHERE p.hn = ?)
        ) AS tb 
        ORDER BY vn DESC 
        LIMIT 50
    `;
    const visits = await query(visitsSql, [patient.hn, patient.hn]);

    const formattedVisits = visits.map(v => ({
        ...v,
        vstThai: formatThaiDate(v.vst)
    }));

    return {
        patient,
        visits: formattedVisits
    };
}

/**
 * ดึงรายการยาที่จ่ายใน visit นั้นๆ (cctiem drug modal)
 */
async function getVisitDrugs(vn) {
    const sql = `
        SELECT 
            rx.vstdate AS ovdate, rx.rxtime, 
            CONCAT(COALESCE(du.name1, ''), ' ', COALESCE(du.name2, ''), ' ', COALESCE(du.name3, '')) AS drugu, 
            CONCAT(d.name, ' ', COALESCE(d.strength, ''), ' ', COALESCE(d.units, '')) AS drugname,
            rx.qty
        FROM ovst ov 
        LEFT JOIN opitemrece rx ON rx.vn = ov.vn 
        INNER JOIN drugitems d ON d.icode = rx.icode 
        LEFT JOIN drugusage du ON du.drugusage = rx.drugusage 
        WHERE ov.vn = ?
        ORDER BY ovdate DESC, rx.rxtime DESC
    `;
    const rows = await query(sql, [vn]);
    return rows.map(r => ({
        ...r,
        ovdateThai: formatThaiDate(r.ovdate)
    }));
}

/**
 * แผนการดูแลล่วงหน้า (Advance Care Plan - ACP)
 * บันทึกและดึงข้อมูล
 */
async function ensureACPTable() {
    const createTableSql = `
        CREATE TABLE IF NOT EXISTS palliative.zpt_acp (
            id INT AUTO_INCREMENT PRIMARY KEY,
            mr VARCHAR(20),
            name VARCHAR(100),
            lastname VARCHAR(100),
            age VARCHAR(10),
            hn VARCHAR(20) NOT NULL,
            daytt DATE,
            disease TEXT,
            physician VARCHAR(100),
            pt1 VARCHAR(50), pt2 VARCHAR(50), pt3 VARCHAR(50),
            pd1 VARCHAR(50), pd2 VARCHAR(50), pd3 VARCHAR(50),
            pc1 VARCHAR(50), pc2 VARCHAR(50), pc3 VARCHAR(50),
            tt21 VARCHAR(50), tt22 VARCHAR(50), tt23 VARCHAR(50),
            cc3 VARCHAR(50),
            ee1 VARCHAR(100), ee2 VARCHAR(100), ee3 VARCHAR(100), ee4 VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    `;
    await queryPalliative(createTableSql);
}

async function saveACP(data) {
    await ensureACPTable();
    const sql = `
        INSERT INTO palliative.zpt_acp 
        (mr, name, lastname, age, hn, daytt, disease, physician, pt1, pt2, pt3, pd1, pd2, pd3, pc1, pc2, pc3, tt21, tt22, tt23, cc3, ee1, ee2, ee3, ee4)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `;
    const params = [
        data.mr || '',
        data.name || '',
        data.lastname || '',
        data.age || '',
        data.hn || '',
        data.daytt || new Date().toISOString().split('T')[0],
        data.disease || '',
        data.physician || '',
        data.pt1 || '', data.pt2 || '', data.pt3 || '',
        data.pd1 || '', data.pd2 || '', data.pd3 || '',
        data.pc1 || '', data.pc2 || '', data.pc3 || '',
        data.tt21 || '', data.tt22 || '', data.tt23 || '',
        data.cc3 || '',
        data.ee1 || '', data.ee2 || '', data.ee3 || '', data.ee4 || ''
    ];
    const res = await queryPalliative(sql, params);
    return res.insertId;
}

async function getACPList() {
    await ensureACPTable();
    const sql = `
        SELECT * FROM palliative.zpt_acp ORDER BY created_at DESC LIMIT 100
    `;
    const rows = await queryPalliative(sql);
    return rows.map(r => ({
        ...r,
        dayttThai: formatThaiDate(r.daytt),
        createdAtThai: formatThaiDate(r.created_at)
    }));
}

/**
 * ดึงพิกัดแผนที่ผู้ป่วย Palliative (Patient Map)
 */
async function getPatientMapLocations() {
    const sql = `
        SELECT 
            MAX(ov.vstdate) AS vstdate,
            ov.hn,
            pt.cid,
            CONCAT(pt.pname, pt.fname, ' ', pt.lname) AS ptname,
            (YEAR(NOW()) - YEAR(pt.birthday)) AS age,
            CONCAT(pt.addrpart, ' หมู่ ', pt.moopart, ' ', COALESCE(th.full_name, '')) AS addr,
            ov.pdx AS C,
            IF(ISNULL(pt.death) OR pt.death = '', 'N', pt.death) AS death,
            (SELECT COUNT(*) FROM ovst_community_service a1 INNER JOIN vn_stat vn ON vn.vn = a1.vn WHERE vn.hn = ov.hn AND a1.ovst_community_service_type_id BETWEEN 1 AND 103) AS dayc,
            DATEDIFF(NOW(), (SELECT MAX(a1.entry_datetime) FROM ovst_community_service a1 INNER JOIN vn_stat vn ON vn.vn = a1.vn WHERE vn.hn = ov.hn AND a1.ovst_community_service_type_id BETWEEN 1 AND 103)) AS daym,
            h.latitude,
            h.longitude
        FROM vn_stat ov 
        LEFT JOIN patient pt ON pt.hn = ov.hn
        LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart, pt.amppart, pt.tmbpart)
        LEFT JOIN person p ON p.cid = pt.cid
        LEFT JOIN house h ON h.house_id = p.house_id
        WHERE (ov.pdx = 'Z515' OR ov.dx0 = 'Z515' OR ov.dx1 = 'Z515' OR ov.dx2 = 'Z515' OR ov.dx3 = 'Z515' OR ov.dx4 = 'Z515' OR ov.dx5 = 'Z515')
          AND h.latitude IS NOT NULL AND h.longitude IS NOT NULL AND h.latitude != 0 AND h.longitude != 0
        GROUP BY ov.hn 
        ORDER BY vstdate DESC
        LIMIT 300
    `;
    const rows = await query(sql);

    // ถ้าไม่มีพิกัด house ให้สร้าง default center อ.โคกศรีสุพรรณ จ.สกลนคร
    return rows.map(r => {
        const visitStatus = calculateVisitStatus(r.daym, r.death);
        return {
            hn: r.hn,
            cid: r.cid,
            ptname: r.ptname,
            age: r.age,
            addr: r.addr,
            pdx: r.C,
            vstdateThai: formatThaiDate(r.vstdate),
            lat: parseFloat(r.latitude),
            lng: parseFloat(r.longitude),
            visitStatus
        };
    });
}

module.exports = {
    RPST_MAP,
    CATEGORY_DIAGS,
    formatThaiDate,
    calculateVisitStatus,
    getPatients,
    getHospitalVisits,
    getZ718Visits,
    getPatientEClaim,
    getPatientFDHClaim,
    getOverviewCharts,
    getCategoryCharts,
    getPalliativeReports,
    getMedicalEClaimReport,
    getCTScanReport,
    getEClaimPaidList,
    getFDHPaidList,
    getPatientFullHistory,
    getVisitDrugs,
    saveACP,
    getACPList,
    getPatientMapLocations
};
