// Palliative Care Main Application Controller
const App = {
    state: {
        currentUser: null,
        currentView: 'dashboard',
        currentCategory: 'palliative',
        currentReport: 'new_patients',
        units: [],
        categories: [],
        patients: [],
        filters: {
            dt1: '2019-01-01',
            dt2: '2099-12-31',
            unit: '99999',
            status: '99999',
            search: ''
        }
    },

    async init() {
        this.initDefaultDates();
        this.setupClock();
        this.setupEventListeners();
        await this.checkAuth();
        await this.loadInitialMetadata();
        await this.navigate('dashboard');
    },

    initDefaultDates() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        // ตั้งค่าเริ่มต้นของวันที่
        this.state.filters.dt1 = '2019-01-01';
        this.state.filters.dt2 = `${year}-12-31`;
    },

    setupClock() {
        const clockEl = document.getElementById('live-clock');
        if (!clockEl) return;
        const update = () => {
            const now = new Date();
            const dateStr = now.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            const timeStr = now.toLocaleTimeString('th-TH');
            clockEl.innerHTML = `<i class="fa fa-clock"></i> ${dateStr} ${timeStr}`;
        };
        update();
        setInterval(update, 1000);
    },

    setupEventListeners() {
        // Toggle mobile sidebar
        const toggleBtn = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                sidebar.classList.toggle('open');
            });

            document.addEventListener('click', (e) => {
                if (sidebar.classList.contains('open') && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            });
        }

        // Dark mode toggle
        const themeBtn = document.getElementById('theme-toggle');
        if (themeBtn) {
            themeBtn.addEventListener('click', () => {
                const current = document.documentElement.getAttribute('data-theme');
                const next = current === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', next);
                themeBtn.innerHTML = next === 'dark' ? '<i class="fa fa-sun"></i> โหมดสว่าง' : '<i class="fa fa-moon"></i> โหมดมืด';
            });
        }
    },

    async checkAuth() {
        try {
            const res = await fetch('/api/auth/me');
            const data = await res.json();
            if (data.authenticated) {
                this.state.currentUser = data.user;
                this.renderUserBadge();
            } else {
                // Default guest / doctor view
                this.state.currentUser = {
                    name: 'เจ้าหน้าที่คลินิก Palliative',
                    position: 'พยาบาล / แพทย์',
                    groupname: 'งานการดูแลแบบประคับประคอง'
                };
                this.renderUserBadge();
            }
        } catch (e) {
            console.error('Check auth error:', e);
        }
    },

    renderUserBadge() {
        const userEl = document.getElementById('user-profile-display');
        if (!userEl) return;
        const user = this.state.currentUser;
        userEl.innerHTML = `
            <div class="user-avatar"><i class="fa fa-user-md"></i></div>
            <div>
                <div style="font-size: 12.5px; font-weight: 600;">${user.name}</div>
                <div style="font-size: 10px; color: var(--text-muted);">${user.position || user.groupname || ''}</div>
            </div>
        `;
    },

    async loadInitialMetadata() {
        try {
            const [uRes, cRes] = await Promise.all([
                fetch('/api/rpst-list'),
                fetch('/api/categories')
            ]);
            const uData = await uRes.json();
            const cData = await cRes.json();
            this.state.units = uData.units || [];
            this.state.categories = cData.categories || [];
            this.renderCategorySidebar();
        } catch (e) {
            console.error('Load metadata error:', e);
        }
    },

    renderCategorySidebar() {
        const catContainer = document.getElementById('sidebar-categories');
        if (!catContainer) return;

        let html = '';
        this.state.categories.forEach(cat => {
            html += `
                <div class="nav-item" id="nav-cat-${cat.key}" onclick="App.navigateCategory('${cat.key}')">
                    <i class="fa ${cat.icon}" style="color: ${cat.color}"></i>
                    <span>${cat.name.replace('ทะเบียน ', '')}</span>
                </div>
            `;
        });
        catContainer.innerHTML = html;
    },

    // Navigation Router
    async navigate(view, params = {}) {
        this.state.currentView = view;
        document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));

        const targetNav = document.getElementById(`nav-${view}`);
        if (targetNav) targetNav.classList.add('active');

        // Close mobile sidebar on navigate
        const sidebar = document.getElementById('sidebar');
        if (sidebar) sidebar.classList.remove('open');

        const contentEl = document.getElementById('page-content');
        if (!contentEl) return;

        contentEl.innerHTML = `<div style="text-align:center; padding: 60px;"><i class="fa fa-spinner fa-spin fa-2x" style="color: var(--primary);"></i><div style="margin-top: 10px;">กำลังโหลดข้อมูล...</div></div>`;

        switch (view) {
            case 'dashboard':
                await this.renderDashboardView();
                break;
            case 'category':
                await this.renderCategoryView();
                break;
            case 'reports':
                await this.renderReportsView();
                break;
            case 'all-charts':
                await this.renderAllChartsView();
                break;
            case 'medical':
                await this.renderMedicalView();
                break;
            case 'ctscan':
                await this.renderCTScanView();
                break;
            case 'fdh-paid':
            case 'eclaim-paid':
                await this.renderFDHPaidView();
                break;
            case 'cctiem':
                await this.renderCCTiemView();
                break;
            case 'acp':
                await this.renderACPView();
                break;
            case 'map':
                await this.renderMapView();
                break;
            default:
                await this.renderDashboardView();
        }
    },

    async navigateCategory(catKey) {
        this.state.currentCategory = catKey;
        document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
        const navEl = document.getElementById(`nav-cat-${catKey}`);
        if (navEl) navEl.classList.add('active');
        await this.navigate('category');
    },

    // 1. Dashboard View (Overview)
    async renderDashboardView() {
        const contentEl = document.getElementById('page-content');
        document.getElementById('page-title-badge').innerText = 'ทะเบียนผู้ป่วยระยะสุดท้ายแบบประคับประคอง และข้อมูลการเยี่ยมบ้าน';

        contentEl.innerHTML = `
            ${this.renderFilterBar('palliative')}

            <div id="metrics-container" class="metrics-grid">
                <div class="metric-card"><div class="metric-info"><h4>ผู้ป่วยทั้งหมด</h4><div class="value" id="m-total">-</div></div><div class="metric-icon"><i class="fa fa-users"></i></div></div>
                <div class="metric-card danger"><div class="metric-info"><h4>เสียชีวิตแล้ว</h4><div class="value" id="m-deceased">-</div></div><div class="metric-icon"><i class="fa fa-cross"></i></div></div>
                <div class="metric-card success"><div class="metric-info"><h4>ยังมีชีวิต</h4><div class="value" id="m-living">-</div></div><div class="metric-icon"><i class="fa fa-heart"></i></div></div>
                <div class="metric-card info"><div class="metric-info"><h4>ได้รับการเยี่ยมบ้าน</h4><div class="value" id="m-visited">-</div></div><div class="metric-icon"><i class="fa fa-house-user"></i></div></div>
            </div>

            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title"><i class="fa fa-chart-column" style="color: #ef4444;"></i> จำนวนผู้ป่วยที่เสียชีวิตใน Palliative แยกตาม Diag</div>
                    </div>
                    <div class="chart-container"><canvas id="chart-deceased-diag"></canvas></div>
                </div>
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title"><i class="fa fa-chart-pie" style="color: #0284c7;"></i> สัดส่วนผู้ป่วยแยกตาม รพ.สต. ในเครือข่าย</div>
                    </div>
                    <div class="chart-container"><canvas id="chart-rpst"></canvas></div>
                </div>
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title"><i class="fa fa-chart-pie" style="color: #10b981;"></i> ผู้ป่วยที่ยังมีชีวิต แยกตาม Diag ปัจจุบัน</div>
                    </div>
                    <div class="chart-container"><canvas id="chart-living-diag"></canvas></div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header-tools">
                    <div style="font-weight: 700; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                        <i class="fa fa-table-list" style="color: var(--primary);"></i>
                        ตารางทะเบียน Palliative Care พร้อมสถานะการเยี่ยมบ้าน
                        <span id="patient-count-badge" class="badge-status badge-primary">0 ราย</span>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button onclick="App.exportTableCSV()" class="btn-success" style="font-size: 12.5px; padding: 6px 12px;">
                            <i class="fa fa-file-excel"></i> ส่งออก Excel (CSV)
                        </button>
                        <button onclick="window.print()" class="btn-primary" style="font-size: 12.5px; padding: 6px 12px; height: auto;">
                            <i class="fa fa-print"></i> พิมพ์
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="data-table" id="patients-data-table">
                        <thead>
                            <tr>
                                <th style="text-align: center; width: 50px;">ที่</th>
                                <th style="text-align: center;">วันที่ (ล่าสุด)</th>
                                <th style="text-align: center;">HN</th>
                                <th>สิทธิ</th>
                                <th style="text-align: center;">เลขบัตรประชาชน</th>
                                <th>ชื่อ - สกุล</th>
                                <th style="text-align: center;">วันเกิด</th>
                                <th style="text-align: center;">อายุ</th>
                                <th>ที่อยู่</th>
                                <th>รพ.สต.</th>
                                <th style="text-align: center;">PDX</th>
                                <th style="text-align: center;">เยี่ยม Z718</th>
                                <th style="text-align: center;">เยี่ยม รพ.</th>
                                <th style="text-align: center;">FDH / STM</th>
                                <th style="text-align: center;">การเช็คการเยี่ยมผู้ป่วย</th>
                            </tr>
                        </thead>
                        <tbody id="patients-table-body">
                            <tr><td colspan="15" style="text-align: center; padding: 30px;">กำลังโหลดตารางข้อมูล...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        await this.loadDashboardData();
    },

    async loadDashboardData() {
        const { dt1, dt2, unit, status, search } = this.state.filters;
        try {
            // Load charts & summary
            const chartPromise = fetch(`/api/charts/overview?dt1=${dt1}&dt2=${dt2}`).then(r => r.json());
            const patientPromise = fetch(`/api/patients?category=palliative&dt1=${dt1}&dt2=${dt2}&unit=${unit}&status=${status}&search=${encodeURIComponent(search)}`).then(r => r.json());

            const [cRes, pRes] = await Promise.all([chartPromise, patientPromise]);

            if (cRes.success && cRes.data) {
                const s = cRes.data.summary || {};
                document.getElementById('m-total').innerText = (s.total_patients || 0).toLocaleString();
                document.getElementById('m-deceased').innerText = (s.deceased_patients || 0).toLocaleString();
                document.getElementById('m-living').innerText = (s.living_patients || 0).toLocaleString();
                document.getElementById('m-visited').innerText = (s.visited_patients || 0).toLocaleString();

                ChartManager.renderDeceasedDiagChart('chart-deceased-diag', cRes.data.deceasedDiag);
                ChartManager.renderRpstPieChart('chart-rpst', cRes.data.rpstDistribution);
                ChartManager.renderLivingDiagChart('chart-living-diag', cRes.data.livingDiag);
            }

            if (pRes.success && pRes.patients) {
                this.state.patients = pRes.patients;
                document.getElementById('patient-count-badge').innerText = `${pRes.patients.length.toLocaleString()} ราย`;
                this.renderPatientRows(pRes.patients);
            }

        } catch (e) {
            console.error('Load dashboard error:', e);
        }
    },

    // 2. Category View (Every category with dedicated complete charts & table)
    async renderCategoryView() {
        const catKey = this.state.currentCategory;
        const catObj = this.state.categories.find(c => c.key === catKey) || { name: catKey, color: '#0284c7', icon: 'fa-folder' };

        document.getElementById('page-title-badge').innerHTML = `
            <i class="fa ${catObj.icon}" style="color: ${catObj.color}"></i> ${catObj.name}
        `;

        const contentEl = document.getElementById('page-content');
        contentEl.innerHTML = `
            ${this.renderFilterBar(catKey)}

            <!-- กราฟเฉพาะหมวดหมู่อย่างครบถ้วนตามความต้องการของผู้ใช้ -->
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title"><i class="fa fa-chart-column" style="color: ${catObj.color}"></i> จำนวนผู้ป่วย ${catObj.name} แยกตาม รพ.สต.</div>
                    </div>
                    <div class="chart-container"><canvas id="cat-chart-rpst"></canvas></div>
                </div>
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title"><i class="fa fa-pie-chart" style="color: #10b981;"></i> สัดส่วนการมีชีวิต / เสียชีวิต</div>
                    </div>
                    <div class="chart-container"><canvas id="cat-chart-status"></canvas></div>
                </div>
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title"><i class="fa fa-house-medical-circle-check" style="color: #0284c7;"></i> ความครอบคลุมการเยี่ยมบ้าน</div>
                    </div>
                    <div class="chart-container"><canvas id="cat-chart-visit"></canvas></div>
                </div>
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title"><i class="fa fa-stethoscope" style="color: #6366f1;"></i> การจำแนกตามรหัสโรคย่อย (PDX)</div>
                    </div>
                    <div class="chart-container"><canvas id="cat-chart-diags"></canvas></div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header-tools">
                    <div style="font-weight: 700; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                        <i class="fa fa-list-check" style="color: ${catObj.color};"></i>
                        รายชื่อผู้ป่วย ${catObj.name}
                        <span id="patient-count-badge" class="badge-status badge-primary">0 ราย</span>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button onclick="App.exportTableCSV()" class="btn-success" style="font-size: 12.5px; padding: 6px 12px;">
                            <i class="fa fa-file-excel"></i> ส่งออก Excel (CSV)
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="data-table" id="patients-data-table">
                        <thead>
                            <tr>
                                <th style="text-align: center; width: 50px;">ที่</th>
                                <th style="text-align: center;">วันที่ (ล่าสุด)</th>
                                <th style="text-align: center;">HN</th>
                                <th>สิทธิ</th>
                                <th style="text-align: center;">เลขบัตรประชาชน</th>
                                <th>ชื่อ - สกุล</th>
                                <th style="text-align: center;">วันเกิด</th>
                                <th style="text-align: center;">อายุ</th>
                                <th>ที่อยู่</th>
                                <th>รพ.สต.</th>
                                <th style="text-align: center;">PDX</th>
                                <th style="text-align: center;">เยี่ยม Z718</th>
                                <th style="text-align: center;">เยี่ยม รพ.</th>
                                <th style="text-align: center;">FDH / STM</th>
                                <th style="text-align: center;">การเช็คการเยี่ยมผู้ป่วย</th>
                            </tr>
                        </thead>
                        <tbody id="patients-table-body">
                            <tr><td colspan="15" style="text-align: center; padding: 30px;">กำลังโหลดตารางข้อมูล...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        await this.loadCategoryData(catKey);
    },

    async loadCategoryData(catKey) {
        const { dt1, dt2, unit, status, search } = this.state.filters;
        try {
            const chartP = fetch(`/api/charts/category/${catKey}?dt1=${dt1}&dt2=${dt2}`).then(r => r.json());
            const patP = fetch(`/api/patients?category=${catKey}&dt1=${dt1}&dt2=${dt2}&unit=${unit}&status=${status}&search=${encodeURIComponent(search)}`).then(r => r.json());

            const [cRes, pRes] = await Promise.all([chartP, patP]);

            if (cRes.success && cRes.data) {
                ChartManager.renderCategoryCharts(cRes.data);
            }

            if (pRes.success && pRes.patients) {
                this.state.patients = pRes.patients;
                document.getElementById('patient-count-badge').innerText = `${pRes.patients.length.toLocaleString()} ราย`;
                this.renderPatientRows(pRes.patients);
            }
        } catch (e) {
            console.error('Load category data error:', e);
        }
    },

    // 3. Palliative Reports View (9 Sub-reports from getdata)
    async renderReportsView() {
        const reportsList = [
            { id: 'new_patients', name: '1. จำนวนผู้ป่วยรายใหม่ (New Patients)', icon: 'fa-user-plus' },
            { id: 'new_vs_old', name: '2. เปรียบเทียบ รายใหม่ / รายเก่า', icon: 'fa-arrows-rotate' },
            { id: 'pain_morphine', name: '3. ผู้ป่วยที่ได้รับยาแก้ปวด/Morphine', icon: 'fa-pills' },
            { id: 'oxygen', name: '4. ผู้ป่วยที่ได้รับ Oxygen', icon: 'fa-lungs' },
            { id: 'unvisited', name: '5. ผู้ป่วยที่ไม่ได้รับการเยี่ยมบ้าน', icon: 'fa-circle-xmark' },
            { id: 'visited', name: '6. ผู้ป่วยที่ได้รับการเยี่ยมบ้านแล้ว', icon: 'fa-circle-check' },
            { id: 'disease_summary', name: '7. สรุปกลุ่มโรค Palliative', icon: 'fa-chart-pie' },
            { id: 'deceased_report', name: '8. ผู้ป่วยที่เสียชีวิตแล้ว', icon: 'fa-skull' },
            { id: 'missing_z718', name: '9. ผู้ป่วยที่เยี่ยมแต่ไม่มี Z718', icon: 'fa-triangle-exclamation' }
        ];

        document.getElementById('page-title-badge').innerText = 'ระบบรายงาน Palliative Care (9 หมวดรายงาน)';

        const contentEl = document.getElementById('page-content');
        contentEl.innerHTML = `
            <div class="filter-card">
                <!-- 9 Report Tabs Pill Bar -->
                <div class="report-tabs-bar">
                    ${reportsList.map(r => `
                        <button class="report-tab-btn ${this.state.currentReport === r.id ? 'active' : ''}" onclick="App.changeReport('${r.id}')">
                            <i class="fa ${r.icon}"></i> <span>${r.name}</span>
                        </button>
                    `).join('')}
                </div>
                <!-- Compact Balanced Filters -->
                <div class="filter-grid" style="margin-top: 10px;">
                    <div class="form-group short-group">
                        <label><i class="fa fa-calendar-day"></i> ช่วงวันที่เริ่มต้น</label>
                        <input type="date" class="form-control" id="rep_dt1" value="${this.state.filters.dt1}">
                    </div>
                    <div class="form-group short-group">
                        <label><i class="fa fa-calendar-check"></i> ช่วงวันที่สิ้นสุด</label>
                        <input type="date" class="form-control" id="rep_dt2" value="${this.state.filters.dt2}">
                    </div>
                    <div class="form-actions">
                        <button class="btn-primary" onclick="App.applyReportFilter()">
                            <i class="fa fa-search"></i> ประมวลผลรายงาน
                        </button>
                    </div>
                </div>
            </div>

            <div class="metrics-grid">
                <div class="metric-card success">
                    <div class="metric-info">
                        <h4 id="rep-total-title">จำนวนทั้งหมด</h4>
                        <div class="value" id="rep-total-val">-</div>
                    </div>
                    <div class="metric-icon"><i class="fa fa-clipboard-check"></i></div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header-tools">
                    <div style="font-weight: 700; font-size: 15px;" id="rep-table-title">ตารางข้อมูลรายงาน</div>
                    <button onclick="App.exportTableCSV()" class="btn-success" style="font-size: 12.5px; padding: 6px 12px;">
                        <i class="fa fa-file-excel"></i> ส่งออก Excel (CSV)
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="data-table" id="patients-data-table">
                        <thead id="rep-table-head">
                            <tr>
                                <th>ลำดับ</th>
                                <th>HN</th>
                                <th>ชื่อ-สกุล</th>
                                <th>PDx</th>
                                <th>วันที่</th>
                                <th>ที่อยู่</th>
                            </tr>
                        </thead>
                        <tbody id="rep-table-body">
                            <tr><td colspan="6" style="text-align: center; padding: 20px;">กำลังประมวลผลรายงาน...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        await this.loadReportData();
    },

    async changeReport(repId) {
        this.state.currentReport = repId;
        await this.renderReportsView();
    },

    async applyReportFilter() {
        this.state.filters.dt1 = document.getElementById('rep_dt1').value;
        this.state.filters.dt2 = document.getElementById('rep_dt2').value;
        await this.loadReportData();
    },

    async loadReportData() {
        const { dt1, dt2 } = this.state.filters;
        const repType = this.state.currentReport;
        try {
            const res = await fetch(`/api/reports/${repType}?dt1=${dt1}&dt2=${dt2}`);
            const data = await res.json();
            if (!data.success || !data.data) return;

            const rep = data.data;
            document.getElementById('rep-total-title').innerText = rep.title;
            document.getElementById('rep-total-val').innerText = `${(rep.totalCount || 0).toLocaleString()} ราย`;
            document.getElementById('rep-table-title').innerText = rep.title;

            const tbody = document.getElementById('rep-table-body');
            if (rep.rows.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding: 30px;">ไม่พบข้อมูลในช่วงวันที่นี้</td></tr>`;
                return;
            }

            tbody.innerHTML = rep.rows.map(r => `
                <tr>
                    <td style="text-align: center;">${r.no}</td>
                    <td style="text-align: center; font-weight: 600; color: var(--primary);">${r.hn}</td>
                    <td><b>${r.ptname || r.ชื่อ || '-'}</b></td>
                    <td style="text-align: center;"><span style="background: var(--primary-light); padding: 2px 6px; border-radius: 4px; font-weight: 600;">${r.pdx || '-'}</span></td>
                    <td style="text-align: center;">${r.vstdateThai || r.deathDateThai || r.entryDateThai || '-'}</td>
                    <td>${r.addr || r.ที่อยู่ || '-'}</td>
                    ${r.patient_type ? `<td><span class="badge-status ${r.patient_type === 'รายใหม่' ? 'badge-success' : 'badge-secondary'}">${r.patient_type}</span></td>` : ''}
                    ${r.death_place_text ? `<td>${r.death_place_text}</td>` : ''}
                </tr>
            `).join('');

        } catch (e) {
            console.error('Load report error:', e);
        }
    },

    // 4. All Charts Hub (รวมกราฟทุกหมวดหมู่)
    async renderAllChartsView() {
        document.getElementById('page-title-badge').innerText = 'รวมกราฟสถิติ Palliative Care ครบทุกหมวดหมู่';
        const contentEl = document.getElementById('page-content');

        contentEl.innerHTML = `
            ${this.renderFilterBar('palliative')}
            <div style="font-weight: 700; font-size: 16px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-chart-line" style="color: var(--primary);"></i> กราฟสถิติภาพรวมผู้ป่วยระยะสุดท้าย
            </div>
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-header"><div class="chart-title">ผู้ป่วยที่เสียชีวิตแยกตาม Diag</div></div>
                    <div class="chart-container"><canvas id="all-chart-deceased"></canvas></div>
                </div>
                <div class="chart-card">
                    <div class="chart-header"><div class="chart-title">สัดส่วนผู้ป่วยแยกตาม รพ.สต.</div></div>
                    <div class="chart-container"><canvas id="all-chart-rpst"></canvas></div>
                </div>
                <div class="chart-card">
                    <div class="chart-header"><div class="chart-title">ผู้ป่วยที่ยังมีชีวิตแยกตาม Diag</div></div>
                    <div class="chart-container"><canvas id="all-chart-living"></canvas></div>
                </div>
            </div>

            <div style="font-weight: 700; font-size: 16px; margin: 24px 0 12px; display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-disease" style="color: #ec4899;"></i> กราฟสถิติแยกตามหมวดหมู่โรคเฉพาะ
            </div>
            <div id="category-charts-hub" class="charts-grid">
                <!-- Dynamically populated -->
            </div>
        `;

        const { dt1, dt2 } = this.state.filters;
        const res = await fetch(`/api/charts/overview?dt1=${dt1}&dt2=${dt2}`).then(r => r.json());
        if (res.success && res.data) {
            ChartManager.renderDeceasedDiagChart('all-chart-deceased', res.data.deceasedDiag);
            ChartManager.renderRpstPieChart('all-chart-rpst', res.data.rpstDistribution);
            ChartManager.renderLivingDiagChart('all-chart-living', res.data.livingDiag);
        }

        // Load cancer & stroke charts side by side
        const hubEl = document.getElementById('category-charts-hub');
        hubEl.innerHTML = `
            <div class="chart-card">
                <div class="chart-header"><div class="chart-title"><i class="fa fa-ribbon" style="color: #f72585;"></i> ผู้ป่วยโรคมะเร็ง (Cancer) แยก รพ.สต.</div></div>
                <div class="chart-container"><canvas id="hub-chart-cancer"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-header"><div class="chart-title"><i class="fa fa-brain" style="color: #7209b7;"></i> ผู้ป่วยหลอดเลือดสมอง (Stroke) แยก รพ.สต.</div></div>
                <div class="chart-container"><canvas id="hub-chart-stroke"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-header"><div class="chart-title"><i class="fa fa-lungs" style="color: #3a0ca3;"></i> ผู้ป่วยไตวายเรื้อรัง (CKD 5) แยก รพ.สต.</div></div>
                <div class="chart-container"><canvas id="hub-chart-ckd"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-header"><div class="chart-title"><i class="fa fa-head-side-cough" style="color: #4361ee;"></i> ผู้ป่วยปอดอุดกั้นเรื้อรัง (COPD) แยก รพ.สต.</div></div>
                <div class="chart-container"><canvas id="hub-chart-copd"></canvas></div>
            </div>
        `;

        const [canRes, strRes, ckdRes, copdRes] = await Promise.all([
            fetch(`/api/charts/category/cancer?dt1=${dt1}&dt2=${dt2}`).then(r => r.json()),
            fetch(`/api/charts/category/stroke?dt1=${dt1}&dt2=${dt2}`).then(r => r.json()),
            fetch(`/api/charts/category/ckd?dt1=${dt1}&dt2=${dt2}`).then(r => r.json()),
            fetch(`/api/charts/category/copd?dt1=${dt1}&dt2=${dt2}`).then(r => r.json())
        ]);

        if (canRes.data && canRes.data.rpstData) {
            new Chart(document.getElementById('hub-chart-cancer'), {
                type: 'bar',
                data: {
                    labels: canRes.data.rpstData.map(d => d.rpst_name),
                    datasets: [{ label: 'มะเร็ง (ราย)', data: canRes.data.rpstData.map(d => d.count), backgroundColor: '#f72585', borderRadius: 4 }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        if (strRes.data && strRes.data.rpstData) {
            new Chart(document.getElementById('hub-chart-stroke'), {
                type: 'bar',
                data: {
                    labels: strRes.data.rpstData.map(d => d.rpst_name),
                    datasets: [{ label: 'Stroke (ราย)', data: strRes.data.rpstData.map(d => d.count), backgroundColor: '#7209b7', borderRadius: 4 }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        if (ckdRes.data && ckdRes.data.rpstData) {
            new Chart(document.getElementById('hub-chart-ckd'), {
                type: 'bar',
                data: {
                    labels: ckdRes.data.rpstData.map(d => d.rpst_name),
                    datasets: [{ label: 'CKD 5 (ราย)', data: ckdRes.data.rpstData.map(d => d.count), backgroundColor: '#3a0ca3', borderRadius: 4 }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        if (copdRes.data && copdRes.data.rpstData) {
            new Chart(document.getElementById('hub-chart-copd'), {
                type: 'bar',
                data: {
                    labels: copdRes.data.rpstData.map(d => d.rpst_name),
                    datasets: [{ label: 'COPD (ราย)', data: copdRes.data.rpstData.map(d => d.count), backgroundColor: '#4361ee', borderRadius: 4 }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    },

    // 5. Medical E-Claim View (medical.php)
    async renderMedicalView() {
        document.getElementById('page-title-badge').innerText = 'รายงาน รายการเบิก E-Claim สำหรับผู้ป่วยระยะสุดท้าย';
        const contentEl = document.getElementById('page-content');

        contentEl.innerHTML = `
            <div class="filter-card">
                <div class="filter-grid">
                    <div class="form-group short-group">
                        <label><i class="fa fa-calendar-day"></i> ช่วงวันที่เริ่มต้น</label>
                        <input type="date" class="form-control" id="med_dt1" value="${this.state.filters.dt1}">
                    </div>
                    <div class="form-group short-group">
                        <label><i class="fa fa-calendar-check"></i> ช่วงวันที่สิ้นสุด</label>
                        <input type="date" class="form-control" id="med_dt2" value="${this.state.filters.dt2}">
                    </div>
                    <div class="form-actions">
                        <button class="btn-primary" onclick="App.applyMedicalFilter()">
                            <i class="fa fa-search"></i> ประมวลผล
                        </button>
                    </div>
                </div>
            </div>

            <div class="metrics-grid">
                <div class="metric-card info">
                    <div class="metric-info">
                        <h4>มูลค่ารวมรายการเบิก</h4>
                        <div class="value" id="med-total-val">-</div>
                    </div>
                    <div class="metric-icon"><i class="fa fa-coins"></i></div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header-tools">
                    <div style="font-weight: 700; font-size: 15px;">รายการเวชภัณฑ์และยาที่ส่งเบิก E-Claim</div>
                    <button onclick="App.exportTableCSV()" class="btn-success" style="font-size: 12.5px; padding: 6px 12px;">
                        <i class="fa fa-file-excel"></i> ส่งออก Excel (CSV)
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="data-table" id="patients-data-table">
                        <thead>
                            <tr>
                                <th style="text-align: center;">ลำดับ</th>
                                <th style="text-align: center;">วันที่รับบริการ</th>
                                <th style="text-align: center;">HN</th>
                                <th>ชื่อ - สกุล</th>
                                <th style="text-align: center;">PDX</th>
                                <th>รายการเวชภัณฑ์ / ยา</th>
                                <th style="text-align: right;">ราคาต่อหน่วย</th>
                                <th style="text-align: center;">จำนวน</th>
                                <th style="text-align: right;">ราคารวม (บาท)</th>
                                <th>วิธีการใช้ยา</th>
                            </tr>
                        </thead>
                        <tbody id="med-table-body">
                            <tr><td colspan="10" style="text-align: center; padding: 20px;">กำลังโหลดข้อมูล...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        await this.loadMedicalData();
    },

    async applyMedicalFilter() {
        this.state.filters.dt1 = document.getElementById('med_dt1').value;
        this.state.filters.dt2 = document.getElementById('med_dt2').value;
        await this.loadMedicalData();
    },

    async loadMedicalData() {
        const { dt1, dt2 } = this.state.filters;
        try {
            const res = await fetch(`/api/medical?dt1=${dt1}&dt2=${dt2}`);
            const data = await res.json();
            if (!data.success || !data.data) return;

            document.getElementById('med-total-val').innerText = `${(data.data.grandTotal || 0).toLocaleString()} บาท`;
            const tbody = document.getElementById('med-table-body');

            if (data.data.rows.length === 0) {
                tbody.innerHTML = `<tr><td colspan="10" style="text-align: center; padding: 30px;">ไม่พบรายการเบิกในช่วงวันที่นี้</td></tr>`;
                return;
            }

            tbody.innerHTML = data.data.rows.map(r => `
                <tr>
                    <td style="text-align: center;">${r.no}</td>
                    <td style="text-align: center;">${r.vstdateThai}</td>
                    <td style="text-align: center; font-weight: 600; color: var(--primary);">${r.hn}</td>
                    <td><b>${r.ptname}</b></td>
                    <td style="text-align: center;"><span style="background: var(--primary-light); padding: 2px 6px; border-radius: 4px; font-weight: 600;">${r.pdx}</span></td>
                    <td>${r.itemName || '-'}</td>
                    <td style="text-align: right;">${parseFloat(r.unitprice || 0).toLocaleString()}</td>
                    <td style="text-align: center;">${r.qty}</td>
                    <td style="text-align: right; font-weight: 600; color: #0284c7;">${parseFloat(r.sum_price || 0).toLocaleString()}</td>
                    <td><small>${r.drug_usage || '-'}</small></td>
                </tr>
            `).join('');

        } catch (e) {
            console.error('Load medical error:', e);
        }
    },

    // 6. CT-Scan Report View (ctscan.php)
    async renderCTScanView() {
        document.getElementById('page-title-badge').innerText = 'รายการตรวจ CT-Scan';
        const contentEl = document.getElementById('page-content');

        contentEl.innerHTML = `
            <div class="filter-card">
                <div class="filter-grid">
                    <div class="form-group short-group">
                        <label><i class="fa fa-calendar-day"></i> ช่วงวันที่เริ่มต้น</label>
                        <input type="date" class="form-control" id="ct_dt1" value="${this.state.filters.dt1}">
                    </div>
                    <div class="form-group short-group">
                        <label><i class="fa fa-calendar-check"></i> ช่วงวันที่สิ้นสุด</label>
                        <input type="date" class="form-control" id="ct_dt2" value="${this.state.filters.dt2}">
                    </div>
                    <div class="form-group short-group">
                        <label><i class="fa fa-id-card-clip"></i> สิทธิการรักษา</label>
                        <select class="form-control" id="ct_hipdata">
                            <option value="0">ทั้งหมด</option>
                            <option value="1">บัตรทอง (UCS)</option>
                            <option value="2">ข้าราชการ/อปท. (OFC/LGO)</option>
                            <option value="3">ประกันสังคม (SSS)</option>
                            <option value="4">สิทธิอื่นๆ</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button class="btn-primary" onclick="App.applyCTFilter()">
                            <i class="fa fa-search"></i> ประมวลผล
                        </button>
                    </div>
                </div>
            </div>

            <div class="metrics-grid">
                <div class="metric-card warning">
                    <div class="metric-info">
                        <h4>ยอดรวมค่าตรวจ CT-Scan</h4>
                        <div class="value" id="ct-total-val">-</div>
                    </div>
                    <div class="metric-icon"><i class="fa fa-x-ray"></i></div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header-tools">
                    <div style="font-weight: 700; font-size: 15px;">ตารางรายการตรวจ CT-Scan</div>
                    <button onclick="App.exportTableCSV()" class="btn-success" style="font-size: 12.5px; padding: 6px 12px;">
                        <i class="fa fa-file-excel"></i> ส่งออก Excel (CSV)
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="data-table" id="patients-data-table">
                        <thead>
                            <tr>
                                <th style="text-align: center;">ลำดับ</th>
                                <th style="text-align: center;">วันที่</th>
                                <th style="text-align: center;">HN</th>
                                <th style="text-align: center;">เลขบัตรประชาชน</th>
                                <th>ชื่อ - สกุล</th>
                                <th>สิทธิการรักษา</th>
                                <th style="text-align: center;">แผนก</th>
                                <th>รายการ CT-Scan</th>
                                <th style="text-align: right;">ราคาต่อหน่วย</th>
                                <th style="text-align: center;">จำนวน</th>
                                <th style="text-align: right;">ราคารวม (บาท)</th>
                            </tr>
                        </thead>
                        <tbody id="ct-table-body">
                            <tr><td colspan="11" style="text-align: center; padding: 20px;">กำลังโหลดข้อมูล...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        await this.loadCTScanData();
    },

    async applyCTFilter() {
        this.state.filters.dt1 = document.getElementById('ct_dt1').value;
        this.state.filters.dt2 = document.getElementById('ct_dt2').value;
        await this.loadCTScanData();
    },

    async loadCTScanData() {
        const { dt1, dt2 } = this.state.filters;
        const hipdata = document.getElementById('ct_hipdata') ? document.getElementById('ct_hipdata').value : '0';
        try {
            const res = await fetch(`/api/ctscan?dt1=${dt1}&dt2=${dt2}&hipdataCode=${hipdata}`);
            const data = await res.json();
            if (!data.success || !data.data) return;

            document.getElementById('ct-total-val').innerText = `${(data.data.grandTotal || 0).toLocaleString()} บาท`;
            const tbody = document.getElementById('ct-table-body');

            if (data.data.rows.length === 0) {
                tbody.innerHTML = `<tr><td colspan="11" style="text-align: center; padding: 30px;">ไม่พบรายการตรวจ CT-Scan ในช่วงวันที่นี้</td></tr>`;
                return;
            }

            tbody.innerHTML = data.data.rows.map(r => `
                <tr>
                    <td style="text-align: center;">${r.no}</td>
                    <td style="text-align: center;">${r.vstdateThai}</td>
                    <td style="text-align: center; font-weight: 600; color: var(--primary);">${r.hn}</td>
                    <td style="text-align: center;">${r.cid}</td>
                    <td><b>${r.paname}</b></td>
                    <td>${r.ptname}</td>
                    <td style="text-align: center;"><span class="badge-status badge-secondary">${r.depname}</span></td>
                    <td>${r.ctname || '-'}</td>
                    <td style="text-align: right;">${parseFloat(r.unitprice || 0).toLocaleString()}</td>
                    <td style="text-align: center;">${r.qty}</td>
                    <td style="text-align: right; font-weight: 600; color: #f59e0b;">${parseFloat(r.sum_price || 0).toLocaleString()}</td>
                </tr>
            `).join('');

        } catch (e) {
            console.error('Load CTScan error:', e);
        }
    },

    // 7. FDH Checker & STM Statement Paid List (แทน RCM/E-Claim เดิม)
    async renderFDHPaidView() {
        document.getElementById('page-title-badge').innerText = 'ทะเบียนผู้ป่วยชดเชยเงิน FDH Checker (Statement)';
        const contentEl = document.getElementById('page-content');

        contentEl.innerHTML = `
            <!-- Filters & Search Toolbar -->
            <div class="filter-card">
                <div class="filter-grid">
                    <div class="form-group short-group">
                        <label><i class="fa fa-calendar"></i> ปีงบประมาณ</label>
                        <select id="fdh-year-filter" class="form-control" onchange="App.fetchFDHPaidData()">
                            <option value="">-- ทั้งหมดทุกปี --</option>
                        </select>
                    </div>
                    <div class="form-group search-group">
                        <label><i class="fa fa-magnifying-glass"></i> ค้นหา (HN, CID, ชื่อ-สกุล, หรือเลข REP)</label>
                        <input type="text" id="fdh-search-input" class="form-control" placeholder="พิมพ์คำค้นหาแล้วกด Enter..." onkeypress="if(event.key === 'Enter') App.fetchFDHPaidData()">
                    </div>
                    <div class="form-actions">
                        <button class="btn-primary" onclick="App.fetchFDHPaidData()">
                            <i class="fa fa-search"></i> ค้นหา
                        </button>
                        <button class="btn-secondary" onclick="document.getElementById('fdh-search-input').value = ''; document.getElementById('fdh-year-filter').value = ''; App.fetchFDHPaidData();">
                            <i class="fa fa-rotate"></i> ล้าง
                        </button>
                    </div>
                </div>
            </div>

            <!-- Metrics Summary Cards -->
            <div class="metrics-grid">
                <div class="metric-card success">
                    <div class="metric-info">
                        <h4>ยอดเงินชดเชย FDH รวม</h4>
                        <div class="value" id="fdh-total-paid">-</div>
                    </div>
                    <div class="metric-icon"><i class="fa fa-hand-holding-dollar"></i></div>
                </div>
                <div class="metric-card info">
                    <div class="metric-info">
                        <h4>จำนวนผู้ป่วยที่ได้รับการชดเชย</h4>
                        <div class="value" id="fdh-total-patients">-</div>
                    </div>
                    <div class="metric-icon"><i class="fa fa-users"></i></div>
                </div>
                <div class="metric-card warning">
                    <div class="metric-info">
                        <h4>จำนวนรอบ Statement</h4>
                        <div class="value" id="fdh-total-count">-</div>
                    </div>
                    <div class="metric-icon"><i class="fa fa-receipt"></i></div>
                </div>
            </div>

            <!-- Main Data Table -->
            <div class="table-card">
                <div class="table-header-tools">
                    <div style="font-weight: 700; font-size: 15px;">
                        <i class="fa fa-file-invoice-dollar" style="color: var(--primary);"></i> รายชื่อผู้ป่วยและยอดชดเชยเงินจาก FDH Checker (กองทุน Palliative Care)
                    </div>
                    <button onclick="App.exportTableCSV()" class="btn-success" style="font-size: 12.5px; padding: 6px 12px;">
                        <i class="fa fa-file-excel"></i> ส่งออก Excel (CSV)
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="data-table" id="patients-data-table">
                        <thead>
                            <tr>
                                <th style="text-align: center; width: 50px;">ลำดับ</th>
                                <th style="text-align: center;">วันที่รับบริการ</th>
                                <th style="text-align: center;">HN</th>
                                <th style="text-align: center;">เลขบัตรประชาชน</th>
                                <th>ชื่อ - สกุล</th>
                                <th style="text-align: right;">เงินชดเชย (บาท)</th>
                                <th style="text-align: center;">เลขที่ REP</th>
                                <th style="text-align: center;">ปีงบประมาณ</th>
                                <th style="text-align: center;">รายละเอียด FDH</th>
                            </tr>
                        </thead>
                        <tbody id="fdh-table-body">
                            <tr><td colspan="9" style="text-align: center; padding: 30px;">กำลังโหลดข้อมูล FDH Checker...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        await this.fetchFDHPaidData(true);
    },

    async fetchFDHPaidData(initYearSelect = false) {
        const yearSelect = document.getElementById('fdh-year-filter');
        const searchInput = document.getElementById('fdh-search-input');
        const year = yearSelect ? yearSelect.value : '';
        const search = searchInput ? searchInput.value.trim() : '';

        const tbody = document.getElementById('fdh-table-body');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; padding: 30px;"><i class="fa fa-spinner fa-spin"></i> กำลังโหลดข้อมูล FDH Checker...</td></tr>`;
        }

        try {
            const url = `/api/fdh-paid?yearbudget=${encodeURIComponent(year)}&search=${encodeURIComponent(search)}`;
            const res = await fetch(url);
            const data = await res.json();
            if (!data.success || !data.data) {
                if (tbody) tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; padding: 30px; color: #ef4444;">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>`;
                return;
            }

            const { rows, totalPaid, totalPatients, totalCount, years } = data.data;

            // Update Metrics
            const totalPaidEl = document.getElementById('fdh-total-paid');
            if (totalPaidEl) totalPaidEl.innerText = `${(totalPaid || 0).toLocaleString()} ฿`;
            const totalPatientsEl = document.getElementById('fdh-total-patients');
            if (totalPatientsEl) totalPatientsEl.innerText = `${(totalPatients || 0).toLocaleString()} คน`;
            const totalCountEl = document.getElementById('fdh-total-count');
            if (totalCountEl) totalCountEl.innerText = `${(totalCount || 0).toLocaleString()} รอบ`;

            // Populate year dropdown if needed
            if (initYearSelect && yearSelect && years && years.length) {
                const currentVal = yearSelect.value;
                yearSelect.innerHTML = `<option value="">-- ทั้งหมดทุกปี --</option>` +
                    years.map(y => `<option value="${y}" ${y === currentVal ? 'selected' : ''}>ปีงบประมาณ ${y}</option>`).join('');
            }

            if (!tbody) return;

            if (!rows || rows.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">ไม่พบข้อมูลการชดเชยเงินตามเงื่อนไขที่ค้นหา</td></tr>`;
                return;
            }

            tbody.innerHTML = rows.map(r => `
                <tr>
                    <td style="text-align: center;">${r.no}</td>
                    <td style="text-align: center;">${r.admDateThai}</td>
                    <td style="text-align: center; font-weight: 600; color: var(--primary);">${r.HN}</td>
                    <td style="text-align: center;">${r.PID}</td>
                    <td><b>${r.PtName}</b></td>
                    <td style="text-align: right; font-weight: 700; color: #10b981;">
                        +${(r.PallativeCare || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                    </td>
                    <td style="text-align: center;"><span class="badge-status badge-success">${r.Rep || '-'}</span></td>
                    <td style="text-align: center;"><span class="badge-status badge-secondary">${r.YearBudget || '-'}</span></td>
                    <td style="text-align: center;">
                        <button class="btn-primary" style="font-size: 11px; padding: 4px 8px;" onclick="App.openFDHModal('${r.HN}')" title="ดูประวัติการส่งเคลม FDH MOPH และ Statement ทั้งหมด">
                            <i class="fa fa-file-invoice-dollar"></i> ดู FDH
                        </button>
                    </td>
                </tr>
            `).join('');

        } catch (e) {
            console.error('fetchFDHPaidData error:', e);
            if (tbody) tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; padding: 30px; color: #ef4444;">โหลดข้อมูลไม่สำเร็จ: ${e.message}</td></tr>`;
        }
    },

    // Backward compatibility
    async renderEClaimPaidView() {
        return this.renderFDHPaidView();
    },

    // 8. Patient History Tracking (cctiem.php)
    async renderCCTiemView() {
        document.getElementById('page-title-badge').innerText = 'ติดตามประวัติการรักษาผู้ป่วย (OPD / IPD / Lab / ยา)';
        const contentEl = document.getElementById('page-content');

        contentEl.innerHTML = `
            <div class="filter-card">
                <div style="font-weight: 700; font-size: 15px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-id-card" style="color: var(--primary);"></i> ค้นหาประวัติการรักษาผู้ป่วย
                </div>
                <div style="display: flex; gap: 12px; max-width: 500px;">
                    <input type="text" class="form-control" id="cctiem_key" placeholder="กรอกเลขบัตรประชาชน 13 หลัก หรือ HN 7-9 หลัก">
                    <button class="btn-primary" onclick="App.searchPatientHistory()">
                        <i class="fa fa-search"></i> ค้นหา
                    </button>
                </div>
            </div>

            <div id="cctiem-patient-profile" style="display: none; margin-bottom: 20px;">
                <!-- Profile card populated by search -->
            </div>

            <div id="cctiem-visits-card" class="table-card" style="display: none;">
                <div class="table-header-tools">
                    <div style="font-weight: 700; font-size: 15px;">ประวัติการมารับบริการย้อนหลัง</div>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="text-align: center;">VN / AN</th>
                                <th style="text-align: center;">วันที่รับบริการ</th>
                                <th style="text-align: center;">ประเภท</th>
                                <th style="text-align: center;">PDX</th>
                                <th style="text-align: center;">DX0-DX5</th>
                                <th style="text-align: center;">BW (กก.)</th>
                                <th style="text-align: center;">ส่วนสูง</th>
                                <th style="text-align: center;">ความดัน (BP)</th>
                                <th style="text-align: center;">ชีพจร (PR)</th>
                                <th style="text-align: center;">การหายใจ (RR)</th>
                                <th>อาการสำคัญ (CC)</th>
                                <th style="text-align: center;">รายการยา</th>
                            </tr>
                        </thead>
                        <tbody id="cctiem-visits-body"></tbody>
                    </table>
                </div>
            </div>
        `;
    },

    async searchPatientHistory() {
        const key = document.getElementById('cctiem_key').value;
        if (!key) {
            alert('กรุณากรอกเลขบัตรประชาชน หรือ HN');
            return;
        }

        try {
            const res = await fetch(`/api/patient-history?key=${encodeURIComponent(key)}`);
            const data = await res.json();
            if (!data.success || !data.data || !data.data.patient) {
                alert('ไม่พบข้อมูลผู้ป่วยที่มีเลขนี้');
                return;
            }

            const pt = data.data.patient;
            const visits = data.data.visits || [];

            const profEl = document.getElementById('cctiem-patient-profile');
            profEl.style.display = 'block';
            profEl.innerHTML = `
                <div class="filter-card" style="background: linear-gradient(135deg, rgba(2, 132, 199, 0.05) 0%, rgba(6, 182, 212, 0.05) 100%);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
                        <div>
                            <h3 style="font-size: 18px; font-weight: 700; color: var(--primary);">${pt.pname}</h3>
                            <div style="font-size: 13.5px; margin-top: 4px;">
                                <b>HN:</b> ${pt.hn} | <b>CID:</b> ${pt.cid} | <b>อายุ:</b> ${pt.cnt_year} ปี | <b>วันเกิด:</b> ${pt.birthdayThai}
                            </div>
                            <div style="font-size: 13.5px; margin-top: 4px;">
                                <b>ที่อยู่:</b> ${pt.addr || '-'}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="display: inline-block; padding: 6px 12px; border-radius: 8px; background: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 13px; font-weight: 600;">
                                <i class="fa fa-triangle-exclamation"></i> ประวัติแพ้ยา: ${pt.drugallergy || 'ไม่มีประวัติแพ้ยา'}
                            </div>
                            <div style="margin-top: 6px; font-size: 12.5px; color: var(--text-muted);">
                                <b>คลินิก:</b> ${pt.clinic || 'Palliative Care'}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const vCard = document.getElementById('cctiem-visits-card');
            const tbody = document.getElementById('cctiem-visits-body');
            vCard.style.display = 'block';

            if (visits.length === 0) {
                tbody.innerHTML = `<tr><td colspan="12" style="text-align:center; padding: 20px;">ไม่พบประวัติการรับบริการ</td></tr>`;
                return;
            }

            tbody.innerHTML = visits.map(v => `
                <tr>
                    <td style="text-align: center; font-weight: 600; color: var(--primary);">${v.vn}</td>
                    <td style="text-align: center;">${v.vstThai}</td>
                    <td style="text-align: center;"><span class="badge-status ${v.tp === 'IPD' ? 'badge-danger' : 'badge-primary'}">${v.tp}</span></td>
                    <td style="text-align: center;"><span style="background: var(--primary-light); padding: 2px 6px; border-radius: 4px; font-weight: 600;">${v.pdx}</span></td>
                    <td style="text-align: center;"><small>${[v.dx0, v.dx1, v.dx2, v.dx3, v.dx4, v.dx5].filter(Boolean).join(', ') || '-'}</small></td>
                    <td style="text-align: center;">${v.bw || '-'}</td>
                    <td style="text-align: center;">${v.height || '-'}</td>
                    <td style="text-align: center; font-weight: 600;">${v.bp || '-'}</td>
                    <td style="text-align: center;">${v.pulse || '-'}</td>
                    <td style="text-align: center;">${v.rr || '-'}</td>
                    <td><small>${v.cc || '-'}</small></td>
                    <td style="text-align: center;">
                        <button onclick="App.openDrugModal('${v.vn}')" class="btn-primary" style="padding: 4px 8px; font-size: 11.5px; height: auto;">
                            <i class="fa fa-pills"></i> ดูยา
                        </button>
                    </td>
                </tr>
            `).join('');

        } catch (e) {
            console.error('Search patient history error:', e);
        }
    },

    // 9. Advance Care Plan View (add.php)
    async renderACPView() {
        document.getElementById('page-title-badge').innerText = 'แผนการดูแลล่วงหน้า (Advance Care Plan - ACP)';
        const contentEl = document.getElementById('page-content');

        contentEl.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h3 style="font-size: 18px; font-weight: 700; color: var(--primary);">ทะเบียนแผนการดูแลล่วงหน้า (ACP)</h3>
                    <p style="font-size: 13px; color: var(--text-muted);">การแสดงเจตนาและวางแผนการรักษาในวาระสุดท้ายของผู้ป่วย</p>
                </div>
                <button onclick="App.openNewACPModal()" class="btn-primary">
                    <i class="fa fa-plus"></i> บันทึกแผน ACP ฉบับใหม่
                </button>
            </div>

            <div class="table-card">
                <div class="table-header-tools">
                    <div style="font-weight: 700; font-size: 15px;">รายชื่อผู้ป่วยที่ได้จัดทำแผน ACP</div>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="text-align: center;">ลำดับ</th>
                                <th style="text-align: center;">วันที่ทำแผน</th>
                                <th style="text-align: center;">HN</th>
                                <th>ชื่อ - สกุล</th>
                                <th style="text-align: center;">อายุ</th>
                                <th>การรักษาที่ได้รับ</th>
                                <th>แพทย์เจ้าของไข้</th>
                                <th>การเจาะคอ/เครื่องช่วยหายใจ</th>
                                <th>สถานที่ที่ประสงค์เสียชีวิต</th>
                            </tr>
                        </thead>
                        <tbody id="acp-table-body">
                            <tr><td colspan="9" style="text-align: center; padding: 20px;">กำลังโหลดข้อมูล ACP...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        try {
            const res = await fetch('/api/acp');
            const data = await res.json();
            const tbody = document.getElementById('acp-table-body');
            if (!data.success || !data.list || data.list.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; padding: 30px;">ยังไม่มีการบันทึกแผน ACP</td></tr>`;
                return;
            }

            tbody.innerHTML = data.list.map((r, i) => `
                <tr>
                    <td style="text-align: center;">${i + 1}</td>
                    <td style="text-align: center;">${r.dayttThai || '-'}</td>
                    <td style="text-align: center; font-weight: 600; color: var(--primary);">${r.hn}</td>
                    <td><b>${r.mr}${r.name} ${r.lastname}</b></td>
                    <td style="text-align: center;">${r.age}</td>
                    <td>${r.disease || '-'}</td>
                    <td>${r.physician || '-'}</td>
                    <td><span class="badge-status ${r.cc3 === 'ไม่ต้องการ' ? 'badge-danger' : 'badge-primary'}">${r.cc3 || '-'}</span></td>
                    <td>${r.ee1 || r.ee2 || r.ee3 || '-'}</td>
                </tr>
            `).join('');

        } catch (e) {
            console.error('Load ACP error:', e);
        }
    },

    // 10. Patient Map View (map/index.php)
    async renderMapView() {
        document.getElementById('page-title-badge').innerText = 'แผนที่และพิกัดที่อยู่ผู้ป่วย Palliative Care (GIS Map)';
        const contentEl = document.getElementById('page-content');

        contentEl.innerHTML = `
            <div class="filter-card">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <h4 style="font-size: 15px; font-weight: 700; color: var(--primary);">แผนที่พิกัดบ้านผู้ป่วยในเขตรับผิดชอบ</h4>
                        <p style="font-size: 12.5px; color: var(--text-muted);">แสดงหมุดระบุตำแหน่งสีตามสถานะการเยี่ยมบ้าน (คลิกที่หมุดเพื่อดูข้อมูลและเปิดบันทึกการเยี่ยม)</p>
                    </div>
                    <div style="display: flex; gap: 8px; font-size: 12px;">
                        <span class="badge-status badge-success">● เยี่ยม < 1 เดือน</span>
                        <span class="badge-status badge-primary">● เยี่ยม 1-2 เดือน</span>
                        <span class="badge-status badge-warning">● เยี่ยม 3-6 เดือน</span>
                        <span class="badge-status badge-danger">● เยี่ยม > 1 ปี</span>
                        <span class="badge-status badge-secondary">● เสียชีวิต</span>
                    </div>
                </div>
            </div>

            <div class="table-card" style="padding: 12px;">
                <div id="map-canvas"></div>
            </div>
        `;

        setTimeout(() => {
            MapController.init('map-canvas');
        }, 100);
    },

    // Helper: Render Filter Bar
    renderFilterBar(category) {
        return `
            <div class="filter-card">
                <div class="filter-grid">
                    <div class="form-group short-group">
                        <label><i class="fa fa-calendar-day"></i> วันที่เริ่มต้น</label>
                        <input type="date" class="form-control" id="f_dt1" value="${this.state.filters.dt1}">
                    </div>
                    <div class="form-group short-group">
                        <label><i class="fa fa-calendar-check"></i> วันที่สิ้นสุด</label>
                        <input type="date" class="form-control" id="f_dt2" value="${this.state.filters.dt2}">
                    </div>
                    <div class="form-group">
                        <label><i class="fa fa-hospital-user"></i> รพ.สต. ในเครือข่าย</label>
                        <select class="form-control" id="f_unit">
                            <option value="99999">ทั้งหมดทุกหน่วย</option>
                            ${this.state.units.filter(u => u.code !== '99999').map(u => `
                                <option value="${u.code}" ${this.state.filters.unit === u.code ? 'selected' : ''}>${u.name}</option>
                            `).join('')}
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fa fa-heart-pulse"></i> สถานะผู้ป่วย</label>
                        <select class="form-control" id="f_status">
                            <option value="99999" ${this.state.filters.status === '99999' ? 'selected' : ''}>ทั้งหมด</option>
                            <option value="N" ${this.state.filters.status === 'N' ? 'selected' : ''}>ยังมีชีวิต</option>
                            <option value="Y" ${this.state.filters.status === 'Y' ? 'selected' : ''}>เสียชีวิต</option>
                        </select>
                    </div>
                    <div class="form-group search-group">
                        <label><i class="fa fa-magnifying-glass"></i> ค้นหา (HN, ชื่อ, เลขบัตร)</label>
                        <input type="text" class="form-control" id="f_search" placeholder="พิมพ์คำค้นหา..." value="${this.state.filters.search}" onkeypress="if(event.key==='Enter') App.applyFilters('${category}')">
                    </div>
                    <div class="form-actions">
                        <button class="btn-primary" onclick="App.applyFilters('${category}')">
                            <i class="fa fa-search"></i> ค้นหา
                        </button>
                    </div>
                </div>
            </div>
        `;
    },

    applyFilters(category) {
        this.state.filters.dt1 = document.getElementById('f_dt1').value;
        this.state.filters.dt2 = document.getElementById('f_dt2').value;
        this.state.filters.unit = document.getElementById('f_unit').value;
        this.state.filters.status = document.getElementById('f_status').value;
        this.state.filters.search = document.getElementById('f_search').value;

        if (this.state.currentView === 'category') {
            this.loadCategoryData(this.state.currentCategory);
        } else {
            this.loadDashboardData();
        }
    },

    // Helper: Render Table Rows
    renderPatientRows(patients) {
        const tbody = document.getElementById('patients-table-body');
        if (!tbody) return;

        if (patients.length === 0) {
            tbody.innerHTML = `<tr><td colspan="15" style="text-align: center; padding: 40px; color: var(--text-muted);">ไม่พบข้อมูลผู้ป่วยตามเงื่อนไขที่เลือก</td></tr>`;
            return;
        }

        tbody.innerHTML = patients.map(p => `
            <tr class="${p.death === 'Y' ? 'danger-row' : ''}">
                <td style="text-align: center;">${p.no}</td>
                <td style="text-align: center; white-space: nowrap;">${p.vstdateThai}</td>
                <td style="text-align: center; font-weight: 700; color: var(--primary);">${p.hn}</td>
                <td><small>${p.pttype_name || '-'}</small></td>
                <td style="text-align: center; font-family: monospace;">${p.cid}</td>
                <td><b>${p.ptname}</b></td>
                <td style="text-align: center;">${p.birthdayThai}</td>
                <td style="text-align: center;">${p.age}</td>
                <td><small>${p.addr}</small></td>
                <td><span style="font-size: 12px; font-weight: 500;">${p.rpst_name}</span></td>
                <td style="text-align: center;"><span style="background: var(--primary-light); padding: 2px 6px; border-radius: 4px; font-weight: 600;">${p.pdx || '-'}</span></td>
                
                <!-- เยี่ยม Z718 -->
                <td style="text-align: center;">
                    ${p.dayc1 > 0 ? `
                        <span class="badge-status badge-primary clickable-badge" onclick="App.openZ718Modal('${p.hn}')" title="คลิกดูรายละเอียด Z718">
                            <i class="fa fa-users"></i> ${p.dayc1}
                        </span>
                    ` : '-'}
                </td>

                <!-- เยี่ยม รพ. -->
                <td style="text-align: center;">
                    ${p.dayc > 0 ? `
                        <span class="badge-status badge-info clickable-badge" onclick="App.openVisitModal('${p.hn}')" title="คลิกดูบันทึกการเยี่ยมบ้าน">
                            <i class="fa fa-house-user"></i> ${p.dayc}
                        </span>
                    ` : '-'}
                </td>

                <!-- FDH / STM ชดเชยเงิน -->
                <td style="text-align: center;">
                    ${p.fdhInfo && p.fdhInfo.compensated > 0 ? `
                        <span class="badge-status badge-success clickable-badge" onclick="App.openFDHModal('${p.hn}')" title="ชดเชยแล้ว ${(p.fdhInfo.compensated || 0).toLocaleString()} บาท (คลิกดู Statement)">
                            <i class="fa fa-hand-holding-dollar"></i> ${(p.fdhInfo.compensated || 0).toLocaleString()} บ.
                        </span>
                    ` : `
                        <span class="badge-status badge-secondary clickable-badge" onclick="App.openFDHModal('${p.hn}')" title="คลิกดูประวัติการส่งเบิก FDH และ STM">
                            <i class="fa fa-file-invoice-dollar"></i> FDH/STM
                        </span>
                    `}
                </td>

                <!-- การเช็คการเยี่ยมผู้ป่วย -->
                <td style="text-align: center;">
                    <span class="badge-status ${p.visitStatus.badgeClass}">
                        ${p.visitStatus.text}
                    </span>
                </td>
            </tr>
        `).join('');
    },

    // Modal: Hospital Visits Detail (palliative_view_home.php)
    async openVisitModal(hn) {
        try {
            const res = await fetch(`/api/patients/${hn}/visits`);
            const data = await res.json();
            if (!data.success) return;

            const pt = data.patient || {};
            const visits = data.visits || [];

            document.getElementById('modal-title').innerHTML = `
                <i class="fa fa-house-medical" style="color: var(--primary);"></i> บันทึกการเยี่ยมบ้านของโรงพยาบาล (HN: ${pt.hn})
            `;

            let bodyHtml = `
                <div style="background: var(--bg-main); padding: 14px 18px; border-radius: 10px; margin-bottom: 16px; border: 1px solid var(--border-color);">
                    <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                        <div><b>ผู้ป่วย:</b> <span style="color: var(--primary); font-weight: 600;">${pt.ptname}</span> (CID: ${pt.cid})</div>
                        <div><b>รพ.สต.:</b> ${pt.rpst_name}</div>
                    </div>
                    <div style="margin-top: 4px; font-size: 13px;"><b>ที่อยู่:</b> ${pt.addr}</div>
                </div>
            `;

            if (visits.length === 0) {
                bodyHtml += `<div style="text-align:center; padding: 40px; color: var(--text-muted);">ยังไม่มีบันทึกการออกเยี่ยมในระบบ</div>`;
            } else {
                bodyHtml += visits.map(v => `
                    <div class="visit-item">
                        <div class="visit-header">
                            <div><span style="font-weight: 700; color: #7c3aed;">วันที่ออกเยี่ยม:</span> ${v.vstdateThai}</div>
                            <div><span style="font-weight: 600; color: var(--text-muted);">เจ้าหน้าที่:</span> ${v.dtname}</div>
                        </div>

                        <!-- ตัวชี้วัดสำคัญ PPS, Pain Score, Bed level, Barthel Index, Vital signs -->
                        <div class="indicators-grid">
                            <div class="indicator-pill" style="border-left: 3px solid #0284c7;">${v.pps}</div>
                            <div class="indicator-pill" style="border-left: 3px solid #ef4444;">${v.ps}</div>
                            <div class="indicator-pill" style="border-left: 3px solid #f59e0b;">${v.bed}</div>
                            <div class="indicator-pill" style="border-left: 3px solid #10b981;">${v.bi}</div>
                            <div class="indicator-pill">PR: ${v.pulse || '-'} /นาที</div>
                            <div class="indicator-pill">RR: ${v.rr || '-'} /นาที</div>
                            <div class="indicator-pill">BP: ${v.bp || '-'} mmHg</div>
                        </div>

                        <div style="margin-top: 10px; font-size: 13.5px;">
                            <div style="color: #b91c1c; font-weight: 600;">อาการสำคัญ (CC):</div>
                            <div style="margin-bottom: 6px;">${v.cc}</div>
                            <div style="color: #b91c1c; font-weight: 600;">ประวัติอาการปัจจุบัน (HPI):</div>
                            <div style="line-height: 1.6;">${v.hpi}</div>
                        </div>

                        ${v.services.length > 0 ? `
                            <div style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 6px;">
                                ${v.services.map(s => `<span class="badge-status badge-secondary" style="font-size: 11px;">● ${s}</span>`).join('')}
                            </div>
                        ` : ''}
                    </div>
                `).join('');
            }

            document.getElementById('modal-body').innerHTML = bodyHtml;
            this.showModal();

        } catch (e) {
            console.error('Open visit modal error:', e);
        }
    },

    // Modal: Z718 Visits
    async openZ718Modal(hn) {
        try {
            const res = await fetch(`/api/patients/${hn}/z718`);
            const data = await res.json();
            if (!data.success) return;

            const pt = data.patient || {};
            const visits = data.visits || [];

            document.getElementById('modal-title').innerHTML = `
                <i class="fa fa-users" style="color: var(--primary);"></i> การบันทึกเยี่ยมบ้าน Z718 (HN: ${pt.hn})
            `;

            let bodyHtml = `
                <div style="background: var(--bg-main); padding: 14px 18px; border-radius: 10px; margin-bottom: 16px; border: 1px solid var(--border-color);">
                    <div><b>ผู้ป่วย:</b> <span style="color: var(--primary); font-weight: 600;">${pt.ptname}</span> (CID: ${pt.cid})</div>
                    <div style="margin-top: 4px; font-size: 13px;"><b>ที่อยู่:</b> ${pt.addr}</div>
                </div>
            `;

            if (visits.length === 0) {
                bodyHtml += `<div style="text-align:center; padding: 40px; color: var(--text-muted);">ไม่พบข้อมูลการเยี่ยมบ้าน Z718</div>`;
            } else {
                bodyHtml += visits.map(v => `
                    <div class="visit-item">
                        <div class="visit-header">
                            <div><span style="font-weight: 700; color: var(--primary);">วันที่:</span> ${v.vstdateThai}</div>
                            <div><span style="font-weight: 600; color: var(--text-muted);">เจ้าหน้าที่:</span> ${v.dtname}</div>
                        </div>
                        <div class="indicators-grid">
                            <div class="indicator-pill">${v.pps}</div>
                            <div class="indicator-pill">${v.ps}</div>
                            <div class="indicator-pill">${v.bed}</div>
                        </div>
                        <div style="margin-top: 8px;">
                            <div><b>อาการสำคัญ:</b> ${v.cc}</div>
                            <div><b>HPI:</b> ${v.hpi}</div>
                        </div>
                    </div>
                `).join('');
            }

            document.getElementById('modal-body').innerHTML = bodyHtml;
            this.showModal();

        } catch (e) {
            console.error('Open Z718 modal error:', e);
        }
    },

    // Modal: FDH Checker & STM Statements
    async openFDHModal(hn) {
        try {
            const res = await fetch(`/api/patients/${hn}/fdh`);
            const data = await res.json();
            if (!data.success) return;

            const pt = data.patient || {};
            const stm = data.stmRecords || [];
            const fdh = data.fdhStatusLogs || [];
            const audit = data.fdhAuditRows || [];

            let totalComp = 0;
            stm.forEach(s => { totalComp += parseFloat(s.compensated || 0); });

            document.getElementById('modal-title').innerHTML = `
                <i class="fa fa-file-invoice-dollar" style="color: var(--primary);"></i> ข้อมูล FDH Checker & เงินชดเชย STM (HN: ${pt.hn || hn})
            `;

            let bodyHtml = `
                <!-- Patient Info Header -->
                <div style="background: var(--bg-main); padding: 14px 18px; border-radius: 10px; margin-bottom: 16px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <div style="font-size: 15px;"><b>ผู้ป่วย:</b> <span style="color: var(--primary); font-weight: 700;">${pt.ptname || '-'}</span> (CID: ${pt.cid || '-'})</div>
                        <div style="margin-top: 4px; font-size: 13px; color: var(--text-muted);"><b>ที่อยู่:</b> ${pt.addr || '-'} | <b>รพ.สต.:</b> ${pt.rpst_name || '-'}</div>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 12px; color: var(--text-muted);">ยอดชดเชยรวม STM:</span>
                        <div style="font-size: 18px; font-weight: 800; color: #10b981;">+${totalComp.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ฿</div>
                    </div>
                </div>

                <!-- Tab Headers -->
                <div style="display: flex; gap: 6px; margin-bottom: 14px; border-bottom: 2px solid var(--border-color); padding-bottom: 6px;">
                    <button id="tab-btn-stm" class="btn-primary" style="padding: 6px 14px; font-size: 12.5px;" onclick="App.switchFDHTab('stm')">
                        <i class="fa fa-money-bill-transfer"></i> ชดเชย STM (${stm.length})
                    </button>
                    <button id="tab-btn-fdh" class="btn-secondary" style="padding: 6px 14px; font-size: 12.5px;" onclick="App.switchFDHTab('fdh')">
                        <i class="fa fa-tower-broadcast"></i> สถานะ FDH MOPH (${fdh.length})
                    </button>
                    ${audit.length > 0 ? `
                        <button id="tab-btn-audit" class="btn-secondary" style="padding: 6px 14px; font-size: 12.5px;" onclick="App.switchFDHTab('audit')">
                            <i class="fa fa-clipboard-check"></i> Audit Details (${audit.length})
                        </button>
                    ` : ''}
                </div>

                <!-- Tab 1: STM Statement -->
                <div id="fdh-tab-stm" style="display: block;">
                    ${stm.length === 0 ? `
                        <div style="text-align:center; padding: 35px; color: var(--text-muted);">
                            <i class="fa fa-receipt" style="font-size: 32px; opacity: 0.3; margin-bottom: 8px;"></i>
                            <div>ยังไม่มีข้อมูลเงินชดเชยจาก Statement (STM) ในฐาน FDH Checker</div>
                        </div>
                    ` : `
                        <div class="table-responsive">
                            <table class="data-table" style="font-size: 12.5px;">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">วันที่รับบริการ</th>
                                        <th style="text-align: center;">เลขที่ REP</th>
                                        <th style="text-align: right;">เงินชดเชย (บาท)</th>
                                        <th style="text-align: center;">ปีงบ</th>
                                        <th>โครงการ / กองทุน</th>
                                        <th>ชื่อไฟล์ STM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${stm.map(s => `
                                        <tr>
                                            <td style="text-align: center;">${s.admDateThai}</td>
                                            <td style="text-align: center;"><span class="badge-status badge-success">${s.rep_no || '-'}</span></td>
                                            <td style="text-align: right; font-weight: 700; color: #10b981;">
                                                +${parseFloat(s.compensated || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                            </td>
                                            <td style="text-align: center;"><span class="badge-status badge-secondary">${s.yearbudget || '-'}</span></td>
                                            <td><small>${s.projectcode || 'Palliative Care'}</small></td>
                                            <td><small style="color: var(--text-muted); word-break: break-all;">${s.filename || '-'}</small></td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `}
                </div>

                <!-- Tab 2: FDH MOPH Status -->
                <div id="fdh-tab-fdh" style="display: none;">
                    ${fdh.length === 0 ? `
                        <div style="text-align:center; padding: 35px; color: var(--text-muted);">
                            <i class="fa fa-cloud-arrow-up" style="font-size: 32px; opacity: 0.3; margin-bottom: 8px;"></i>
                            <div>ยังไม่มีประวัติการส่งข้อมูลเคลมเข้าสู่ระบบ FDH MOPH สำหรับ HN นี้</div>
                        </div>
                    ` : `
                        <div class="table-responsive">
                            <table class="data-table" style="font-size: 12.5px;">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">VN</th>
                                        <th style="text-align: center;">วันเวลาที่ส่งเคลม</th>
                                        <th style="text-align: center;">สถานะ FDH</th>
                                        <th style="text-align: center;">รหัสข้อผิดพลาด</th>
                                        <th>ข้อความตอบกลับจากระบบ FDH</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${fdh.map(f => {
                                        const isPass = (f.fdh_claim_status_message || '').toLowerCase().includes('pass') || (f.fdh_claim_status_message || '').toLowerCase().includes('received');
                                        const badgeClass = isPass ? 'badge-success' : (f.error_code ? 'badge-danger' : 'badge-info');
                                        return `
                                            <tr>
                                                <td style="text-align: center; font-weight: 600;">${f.vn || '-'}</td>
                                                <td style="text-align: center;">${f.statusDateThai}</td>
                                                <td style="text-align: center;"><span class="badge-status ${badgeClass}">${f.fdh_claim_status_message || 'รอผล'}</span></td>
                                                <td style="text-align: center;"><span class="badge-status badge-secondary">${f.error_code || '-'}</span></td>
                                                <td><small style="word-break: break-all;">${f.fdh_claim_status_response || '-'}</small></td>
                                            </tr>
                                        `;
                                    }).join('')}
                                </tbody>
                            </table>
                        </div>
                    `}
                </div>

                <!-- Tab 3: FDH Audit Details -->
                <div id="fdh-tab-audit" style="display: none;">
                    ${audit.length === 0 ? `
                        <div style="text-align:center; padding: 35px; color: var(--text-muted);">ไม่มีรายการ Audit Detail</div>
                    ` : `
                        <div class="table-responsive">
                            <table class="data-table" style="font-size: 12.5px;">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">วันเวลา</th>
                                        <th style="text-align: center;">Claim Code</th>
                                        <th style="text-align: center;">ประเภทผู้ป่วย</th>
                                        <th style="text-align: center;">สิทธิ (Inscl)</th>
                                        <th style="text-align: center;">สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${audit.map(a => `
                                        <tr>
                                            <td style="text-align: center;">${a.serviceDateThai}</td>
                                            <td style="text-align: center;"><b>${a.claim_code || '-'}</b></td>
                                            <td style="text-align: center;">${a.patient_type || '-'}</td>
                                            <td style="text-align: center;"><span class="badge-status badge-info">${a.maininscl || '-'}</span></td>
                                            <td style="text-align: center;"><span class="badge-status badge-primary">${a.claim_status || '-'}</span></td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `}
                </div>
            `;

            document.getElementById('modal-body').innerHTML = bodyHtml;
            this.showModal();

        } catch (e) {
            console.error('Open FDH modal error:', e);
        }
    },

    switchFDHTab(tabKey) {
        ['stm', 'fdh', 'audit'].forEach(t => {
            const tabEl = document.getElementById(`fdh-tab-${t}`);
            const btnEl = document.getElementById(`tab-btn-${t}`);
            if (tabEl) tabEl.style.display = t === tabKey ? 'block' : 'none';
            if (btnEl) {
                btnEl.className = t === tabKey ? 'btn-primary' : 'btn-secondary';
            }
        });
    },

    // Backward compatibility
    async openEClaimModal(hn) {
        return this.openFDHModal(hn);
    },

    // Modal: Visit Prescriptions / Drugs
    async openDrugModal(vn) {
        try {
            const res = await fetch(`/api/visit-drugs/${vn}`);
            const data = await res.json();
            if (!data.success) return;

            document.getElementById('modal-title').innerHTML = `
                <i class="fa fa-pills" style="color: var(--primary);"></i> รายการยาที่ได้รับใน Visit: ${vn}
            `;

            let bodyHtml = '';
            if (data.drugs.length === 0) {
                bodyHtml = `<div style="text-align:center; padding: 40px; color: var(--text-muted);">ไม่มีรายการสั่งยาใน Visit นี้</div>`;
            } else {
                bodyHtml = `
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ชื่อยาและความแรง</th>
                                <th style="text-align: center;">จำนวน</th>
                                <th>วิธีการใช้ยา</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.drugs.map(d => `
                                <tr>
                                    <td><b>${d.drugname}</b></td>
                                    <td style="text-align: center; font-weight: 600;">${d.qty}</td>
                                    <td>${d.drugu}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
            }

            document.getElementById('modal-body').innerHTML = bodyHtml;
            this.showModal();

        } catch (e) {
            console.error('Open drug modal error:', e);
        }
    },

    // Modal: New ACP Form (add.php)
    openNewACPModal() {
        document.getElementById('modal-title').innerHTML = `
            <i class="fa fa-file-signature" style="color: var(--primary);"></i> จัดทำแผนการดูแลล่วงหน้า (Advance Care Plan - ACP)
        `;

        const now = new Date().toISOString().split('T')[0];

        document.getElementById('modal-body').innerHTML = `
            <form id="acp-form" onsubmit="App.submitACP(event)">
                <div style="display: grid; grid-template-columns: 1fr 2fr 2fr 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                    <div class="form-group">
                        <label>คำนำหน้า</label>
                        <select class="form-control" name="mr">
                            <option value="นาย">นาย</option>
                            <option value="นาง">นาง</option>
                            <option value="นางสาว">นางสาว</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ชื่อ</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>สกุล</label>
                        <input type="text" class="form-control" name="lastname" required>
                    </div>
                    <div class="form-group">
                        <label>อายุ</label>
                        <input type="number" class="form-control" name="age">
                    </div>
                    <div class="form-group">
                        <label>HN</label>
                        <input type="text" class="form-control" name="hn" placeholder="เช่น 0000123" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                    <div class="form-group">
                        <label>วันที่จัดทำแผน</label>
                        <input type="date" class="form-control" name="daytt" value="${now}">
                    </div>
                    <div class="form-group">
                        <label>การรักษาที่ได้รับ</label>
                        <input type="text" class="form-control" name="disease" placeholder="การวินิจฉัย/การรักษาปัจจุบัน">
                    </div>
                    <div class="form-group">
                        <label>แพทย์เจ้าของไข้</label>
                        <input type="text" class="form-control" name="physician" placeholder="ระบุชื่อแพทย์">
                    </div>
                </div>

                <div style="background: var(--bg-main); padding: 14px; border-radius: 8px; margin-bottom: 14px; border: 1px solid var(--border-color);">
                    <div style="font-weight: 700; color: var(--primary); margin-bottom: 8px;">การตัดสินใจเกี่ยวกับการกู้ชีพและการใช้เครื่องพยุงชีพ</div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="form-group">
                            <label>การเจาะคอ / ใช้เครื่องช่วยหายใจ (CPR/Intubation)</label>
                            <select class="form-control" name="cc3">
                                <option value="ไม่ต้องการใส่ท่อช่วยหายใจ">ไม่ต้องการใส่ท่อช่วยหายใจ (DNR)</option>
                                <option value="ต้องการใส่ท่อช่วยหายใจ">ต้องการใส่ท่อช่วยหายใจ</option>
                                <option value="ให้แพทย์และครอบครัวตัดสินใจตามสถานการณ์">ให้แพทย์และครอบครัวตัดสินใจ</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>สถานที่ที่ประสงค์จะได้รับการดูแลในวาระสุดท้าย</label>
                            <input type="text" class="form-control" name="ee1" placeholder="เช่น ที่บ้าน, โรงพยาบาล">
                        </div>
                    </div>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" class="btn-primary" style="background: var(--secondary); margin-right: 8px;" onclick="App.hideModal()">ยกเลิก</button>
                    <button type="submit" class="btn-primary" style="display: inline-flex;"><i class="fa fa-save"></i> บันทึกข้อมูล ACP</button>
                </div>
            </form>
        `;

        this.showModal();
    },

    async submitACP(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        try {
            const res = await fetch('/api/acp', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                alert('บันทึกแผนการดูแลล่วงหน้า (ACP) เรียบร้อยแล้ว');
                this.hideModal();
                if (this.state.currentView === 'acp') {
                    await this.renderACPView();
                }
            } else {
                alert('เกิดข้อผิดพลาด: ' + data.message);
            }
        } catch (e) {
            alert('เกิดข้อผิดพลาดในการบันทึก: ' + e.message);
        }
    },

    // Modal Control
    showModal() {
        const overlay = document.getElementById('main-modal');
        if (overlay) overlay.classList.add('active');
    },

    hideModal() {
        const overlay = document.getElementById('main-modal');
        if (overlay) overlay.classList.remove('active');
    },

    // Export Table to CSV
    exportTableCSV() {
        const table = document.getElementById('patients-data-table');
        if (!table) return;

        let csv = [];
        const rows = table.querySelectorAll('tr');
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll('td, th');
            for (let j = 0; j < cols.length; j++) {
                let text = cols[j].innerText.replace(/"/g, '""').trim();
                row.push('"' + text + '"');
            }
            csv.push(row.join(','));
        }

        const csvContent = '\uFEFF' + csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `palliative_export_${new Date().toISOString().split('T')[0]}.csv`;
        a.click();
        URL.revokeObjectURL(url);
    }
};

window.App = App;

// Bootstrap on DOM loaded
document.addEventListener('DOMContentLoaded', () => {
    App.init();
});
