require('dotenv').config();
const mysql = require('mysql2/promise');

const dbConfig = {
    host: process.env.DB_HOST || '192.168.2.254',
    port: parseInt(process.env.DB_PORT || '3306'),
    user: process.env.DB_USER || 'opd',
    password: process.env.DB_PASS || 'opd',
    database: process.env.DB_NAME || 'hos',
    waitForConnections: true,
    connectionLimit: 15,
    queueLimit: 0,
    connectTimeout: 10000,
    enableKeepAlive: true,
    keepAliveInitialDelay: 10000,
    charset: 'utf8'
};

const poolHos = mysql.createPool(dbConfig);

const poolPalliative = mysql.createPool({
    ...dbConfig,
    database: process.env.DB_PALLIATIVE || 'palliative'
});

async function query(sql, params = []) {
    let conn;
    try {
        conn = await poolHos.getConnection();
        await conn.query('SET NAMES utf8');
        const [rows] = await conn.query(sql, params);
        return rows;
    } catch (err) {
        console.error('Database query error:', err.message, 'SQL:', sql.substring(0, 100));
        throw err;
    } finally {
        if (conn) conn.release();
    }
}

async function queryPalliative(sql, params = []) {
    let conn;
    try {
        conn = await poolPalliative.getConnection();
        await conn.query('SET NAMES utf8');
        const [rows] = await conn.query(sql, params);
        return rows;
    } catch (err) {
        console.error('Palliative DB query error:', err.message);
        throw err;
    } finally {
        if (conn) conn.release();
    }
}

// Test connectivity on start
async function testConnection() {
    try {
        const conn = await poolHos.getConnection();
        console.log(`[DB] Connected successfully to MySQL (${dbConfig.host}:${dbConfig.port}/${dbConfig.database})`);
        conn.release();
        return true;
    } catch (err) {
        console.error('[DB] Connection error:', err.message);
        return false;
    }
}

module.exports = {
    poolHos,
    poolPalliative,
    query,
    queryPalliative,
    testConnection,
    dbConfig
};
