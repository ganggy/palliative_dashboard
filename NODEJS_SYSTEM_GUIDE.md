# ระบบสารสนเทศการดูแลผู้ป่วยแบบประคับประคอง (Palliative Care Information System)
## พัฒนาใหม่ด้วย Node.js + Express + MariaDB/MySQL (HOSxP) & FDH Checker

---

### 1. ภาพรวมการพัฒนา (System Overview)
ระบบได้รับการแปลงและยกระดับจาก PHP แบบดั้งเดิม สู่ **Modern Node.js Application** พร้อมดีไซน์ UI ที่สวยงาม ทันสมัย (Clean Medical Dashboard) รองรับ Responsive ทุกหน้าจอ และฟังก์ชันครบถ้วน 100% ไม่มีขาดตกบกพร่อง:

1. **การเช็คการเยี่ยมผู้ป่วย (Home Visit Tracking)**:
   - ตรวจสอบสถานะการเยี่ยมบ้านแบบเรียลไทม์:
     - 🟢 **ได้รับการเยี่ยมแล้ว** (มีประวัติการเยี่ยมบ้าน รพ. หรือ Z718)
     - 🟡 **เกินกำหนดเยี่ยม** (ผู้ป่วยที่มีชีวิต แต่ไม่มีการบันทึกเยี่ยมเกิน 90 วัน)
     - 🔴 **ยังไม่ได้รับการเยี่ยม**
     - ⚫ **เสียชีวิตแล้ว**
   - รองรับการแสดงผลตัวชี้วัดสำคัญทางคลินิกครบถ้วน:
     - **PPS** (Palliative Performance Scale 10% - 90%)
     - **Pain Score** (ระดับความปวด 0 - 10)
     - **Bed Level** (ประเภทเตียง 1 - 4)
     - **Barthel Index (BI)** (0 - 100)
     - **สัญญาณชีพ (Vital Signs)**: ความดัน (BP), ชีพจร (Pulse), การหายใจ (RR), อุณหภูมิ
     - **อาการสำคัญ (CC)** และ **ประวัติอาการปัจจุบัน (HPI)**

2. **เชื่อมโยงกับ FDH Checker แทน RCM ตามความต้องการ**:
   - เชื่อมต่อฐานข้อมูล **FDH Checker (`repstminv`)**:
     - ตรวจสอบยอดเงินชดเชย Statement จาก `repstminv.rep_data` และ `repstminv.repstm_statement_data`
     - แสดงสรุปยอดเงินชดเชยรวม (THB), จำนวนผู้ป่วยที่ได้รับเงิน, จำนวนรอบ Statement
     - สามารถค้นหาผู้ป่วย, กรองตามปีงบประมาณ, และส่งออกไฟล์ Excel/CSV ได้ทันที
   - ตรวจสอบสถานะการส่งเคลมเข้า **FDH MOPH** จาก `hos.fdh_claim_status` พร้อมรหัสตอบกลับ (Pass, Received, Error Code)
   - ป๊อปอัปตรวจสอบประวัติ FDH / STM รายบุคคลแบบแยกแท็บละเอียด

3. **กราฟสถิติครบถ้วนทุกหมวดหมู่โรค (Complete Charts for Every Category)**:
   - **Dashboard Overview**:
     - กราฟแท่ง: ผู้เสียชีวิตแยกตาม Diag (ICD-10)
     - กราฟโดนัท: การกระจายตัวของผู้ป่วยแยกตาม รพ.สต.
     - กราฟวงกลม: ผู้ป่วยที่ยังมีชีวิตแยกตาม Diag
   - **กราฟเฉพาะ 14 หมวดหมู่โรค**:
     - กราฟจำนวนผู้ป่วยแยกตาม รพ.สต.
     - กราฟสัดส่วนผู้ป่วยยังมีชีวิต vs เสียชีวิต
     - กราฟความครอบคลุมการออกเยี่ยมบ้าน (ได้รับการเยี่ยม vs ยังไม่ได้เยี่ยม)
     - กราฟสถิติการวินิจฉัยโรคย่อย (Top Diagnoses)
   - หน้า **"กราฟสถิติภาพรวมทุกหมวด"** รวมกราฟเปรียบเทียบทุกกลุ่มโรคไว้ในที่เดียว

4. **รายงานครบถ้วนตามระบบเดิม (Palliative 9 Reports)**:
   - ผู้ป่วยรายใหม่ (Z515)
   - ผู้ป่วยทั้งหมดที่ยังมีชีวิต
   - สรุปแยกรายกลุ่มโรคสำคัญ (Cancer, Stroke, CKD, COPD, etc.)
   - รายงานเวชภัณฑ์ยา / วัสดุการแพทย์สำหรับเบิก FDH/E-Claim
   - รายงานการตรวจ CT-Scan
   - ทะเบียนวางแผนการดูแลล่วงหน้า (ACP: Advance Care Plan)
   - แผนที่พิกัดบ้านผู้ป่วย (GIS Interactive Map ด้วย Leaflet)

---

### 2. สถาปัตยกรรมและโครงสร้างไฟล์ (Architecture)

```
d:\palliative\
├── .env                         # คอนฟิกการเชื่อมต่อฐานข้อมูล HOSxP & FDH
├── server.js                    # Express Application Entrypoint (Port 3000)
├── config/
│   └── db.js                    # Database Connection Pool (SET NAMES utf8)
├── services/
│   └── palliativeService.js    # Data Business Logic, SQL, Indicators, FDH
├── routes/
│   ├── auth.js                  # Login/Logout & User Session
│   └── api.js                   # RESTful API Endpoints
└── public/
    ├── index.html               # Single Page Application Shell
    ├── css/
    │   └── style.css            # Modern UI Style (Light/Dark Mode, Badges)
    └── js/
        ├── app.js               # Application Controller & Data Fetching
        ├── charts.js            # Chart.js Integration
        └── map.js               # Leaflet GIS Patient Map Integration
```

---

### 3. วิธีการเริ่มใช้งาน (How to Run)

1. ติดตั้ง Dependencies (ดำเนินการแล้ว):
   ```bash
   npm install
   ```

2. เริ่มต้นรันเซิร์ฟเวอร์:
   ```bash
   node server.js
   ```

3. เข้าใช้งานผ่านเว็บเบราว์เซอร์:
   - **URL**: `http://localhost:3000` หรือ `http://<IP-เครื่องเซิร์ฟเวอร์>:3000`
