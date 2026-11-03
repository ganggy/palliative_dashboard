const express = require('express');
const router = express.Router();
const palliativeService = require('../services/palliativeService');
const { cacheMiddleware } = require('../middleware/cache');

// Apply caching to heavy read endpoints
// Overview & charts: 120s TTL
router.use('/charts', cacheMiddleware(120));
// Static metadata: 300s TTL
router.use('/rpst-list', cacheMiddleware(300));
router.use('/categories', cacheMiddleware(300));
// Patient list: 60s TTL
router.use('/patients', cacheMiddleware(60));
// FDH paid summary: 60s TTL
router.use('/fdh-paid', cacheMiddleware(60));
router.use('/eclaim-paid', cacheMiddleware(60));

// รายการหน่วยบริการ
router.get('/rpst-list', (req, res) => {
    const list = Object.entries(palliativeService.RPST_MAP).map(([code, name]) => ({
        code,
        name
    }));
    res.json({ success: true, units: list });
});

// รายการหมวดหมู่โรคทั้งหมด
router.get('/categories', (req, res) => {
    const list = Object.entries(palliativeService.CATEGORY_DIAGS).map(([key, val]) => ({
        key,
        name: val.name,
        icon: val.icon,
        color: val.color
    }));
    res.json({ success: true, categories: list });
});

// รายชื่อผู้ป่วยตามหมวดหมู่และตัวกรอง
router.get('/patients', async (req, res) => {
    try {
        const {
            category = 'palliative',
            dt1 = '2018-01-01',
            dt2 = '2099-12-31',
            unit = '99999',
            status = '99999',
            search = ''
        } = req.query;

        const data = await palliativeService.getPatients({
            category,
            dt1,
            dt2,
            unit,
            status,
            search
        });

        res.json({
            success: true,
            count: data.length,
            patients: data
        });
    } catch (err) {
        console.error('API /patients error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// รายละเอียดการเยี่ยมบ้านรายบุคคล (Modal)
router.get('/patients/:hn/visits', async (req, res) => {
    try {
        const { hn } = req.params;
        const result = await palliativeService.getHospitalVisits(hn);
        res.json({ success: true, ...result });
    } catch (err) {
        console.error('API /patients/:hn/visits error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// รายละเอียดการเยี่ยมบ้าน Z718
router.get('/patients/:hn/z718', async (req, res) => {
    try {
        const { hn } = req.params;
        const result = await palliativeService.getZ718Visits(hn);
        res.json({ success: true, ...result });
    } catch (err) {
        console.error('API /patients/:hn/z718 error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// ข้อมูล FDH Checker & การชดเชยเงิน STM ของผู้ป่วย
router.get('/patients/:hn/fdh', async (req, res) => {
    try {
        const { hn } = req.params;
        const result = await palliativeService.getPatientFDHClaim(hn);
        res.json({ success: true, ...result });
    } catch (err) {
        console.error('API /patients/:hn/fdh error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// Alias เพื่อความเข้ากันได้
router.get('/patients/:hn/eclaim', async (req, res) => {
    try {
        const { hn } = req.params;
        const result = await palliativeService.getPatientFDHClaim(hn);
        res.json({ success: true, ...result });
    } catch (err) {
        console.error('API /patients/:hn/eclaim error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// กราฟสรุปภาพรวม (Overview Charts)
router.get('/charts/overview', async (req, res) => {
    try {
        const { dt1 = '2018-01-01', dt2 = '2099-12-31' } = req.query;
        const data = await palliativeService.getOverviewCharts(dt1, dt2);
        res.json({ success: true, data });
    } catch (err) {
        console.error('API /charts/overview error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// กราฟเฉพาะแต่ละหมวดหมู่ (Category Specific Charts)
router.get('/charts/category/:category', async (req, res) => {
    try {
        const { category } = req.params;
        const { dt1 = '2018-01-01', dt2 = '2099-12-31' } = req.query;
        const data = await palliativeService.getCategoryCharts(category, dt1, dt2);
        res.json({ success: true, data });
    } catch (err) {
        console.error('API /charts/category error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// รายงาน 9 หมวด Palliative Care
router.get('/reports/:reportType', async (req, res) => {
    try {
        const { reportType } = req.params;
        const { dt1 = '2019-01-01', dt2 = '2099-12-31' } = req.query;
        const data = await palliativeService.getPalliativeReports(reportType, dt1, dt2);
        res.json({ success: true, data });
    } catch (err) {
        console.error('API /reports error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// รายการเบิก E-Claim เวชภัณฑ์/ยา (medical.php)
router.get('/medical', async (req, res) => {
    try {
        const { dt1 = '2020-01-01', dt2 = '2099-12-31' } = req.query;
        const data = await palliativeService.getMedicalEClaimReport(dt1, dt2);
        res.json({ success: true, data });
    } catch (err) {
        console.error('API /medical error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// รายการตรวจ CT-Scan (ctscan.php)
router.get('/ctscan', async (req, res) => {
    try {
        const { dt1 = '2020-01-01', dt2 = '2099-12-31', hipdataCode = '0' } = req.query;
        const data = await palliativeService.getCTScanReport(dt1, dt2, hipdataCode);
        res.json({ success: true, data });
    } catch (err) {
        console.error('API /ctscan error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// ทะเบียนผู้ป่วยที่ได้รับเงินชดเชยจาก FDH Checker (STM)
router.get('/fdh-paid', async (req, res) => {
    try {
        const { yearbudget = '', search = '' } = req.query;
        const data = await palliativeService.getFDHPaidList({ yearbudget, search });
        res.json({ success: true, data });
    } catch (err) {
        console.error('API /fdh-paid error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// Alias
router.get('/eclaim-paid', async (req, res) => {
    try {
        const { yearbudget = '', search = '' } = req.query;
        const data = await palliativeService.getFDHPaidList({ yearbudget, search });
        res.json({ success: true, data });
    } catch (err) {
        console.error('API /eclaim-paid error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// ค้นหาประวัติการรักษาผู้ป่วยฉบับเต็ม (cctiem.php)
router.get('/patient-history', async (req, res) => {
    try {
        const { key } = req.query;
        if (!key) {
            return res.status(400).json({ success: false, message: 'กรุณากรอกเลขบัตรประชาชน หรือ HN' });
        }
        const data = await palliativeService.getPatientFullHistory(key);
        res.json({ success: true, data });
    } catch (err) {
        console.error('API /patient-history error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// รายการยาใน Visit (cctiem drug modal)
router.get('/visit-drugs/:vn', async (req, res) => {
    try {
        const { vn } = req.params;
        const drugs = await palliativeService.getVisitDrugs(vn);
        res.json({ success: true, drugs });
    } catch (err) {
        console.error('API /visit-drugs error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// ดึงรายการแผนการดูแลล่วงหน้า (ACP List)
router.get('/acp', async (req, res) => {
    try {
        const list = await palliativeService.getACPList();
        res.json({ success: true, list });
    } catch (err) {
        console.error('API /acp error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// บันทึกแผนการดูแลล่วงหน้า (Advance Care Plan - ACP)
router.post('/acp', async (req, res) => {
    try {
        const insertId = await palliativeService.saveACP(req.body);
        res.json({
            success: true,
            message: 'บันทึกข้อมูลแผนการดูแลล่วงหน้าเรียบร้อยแล้ว',
            insertId
        });
    } catch (err) {
        console.error('API POST /acp error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// พิกัดแผนที่ผู้ป่วย Palliative (Patient Map)
router.get('/map/patients', async (req, res) => {
    try {
        const locations = await palliativeService.getPatientMapLocations();
        res.json({ success: true, count: locations.length, locations });
    } catch (err) {
        console.error('API /map/patients error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});

module.exports = router;
