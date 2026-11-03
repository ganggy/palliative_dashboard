// Palliative Care Charts Controller using Chart.js
const ChartManager = {
    instances: {},

    // ทำลายกราฟเดิมเพื่อวาดใหม่
    destroy(id) {
        if (this.instances[id]) {
            this.instances[id].destroy();
            delete this.instances[id];
        }
    },

    destroyAll() {
        Object.keys(this.instances).forEach(id => {
            this.destroy(id);
        });
    },

    // 1. กราฟแท่งแสดงผู้เสียชีวิตแยกตาม Diag
    renderDeceasedDiagChart(canvasId, data = []) {
        this.destroy(canvasId);
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        const labels = data.map(d => d.pdx || 'ไม่ระบุ');
        const values = data.map(d => parseInt(d.count || 0));

        this.instances[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'จำนวนผู้เสียชีวิต (ราย)',
                    data: values,
                    backgroundColor: 'rgba(239, 68, 68, 0.75)',
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 1.5,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (c) => ` จำนวนผู้เสียชีวิต: ${c.raw.toLocaleString()} ราย`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    },

    // 2. กราฟวงกลมผู้ป่วยแยกตาม รพ.สต.
    renderRpstPieChart(canvasId, data = []) {
        this.destroy(canvasId);
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        const labels = data.map(d => d.rpst_name || 'ไม่ระบุ');
        const values = data.map(d => parseInt(d.count || 0));
        const colors = [
            '#0284c7', '#10b981', '#f59e0b', '#8b5cf6', 
            '#ec4899', '#06b6d4', '#64748b', '#14b8a6'
        ];

        this.instances[canvasId] = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12, font: { family: 'Prompt', size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: (c) => ` ${c.label}: ${c.raw.toLocaleString()} ราย`
                        }
                    }
                },
                cutout: '60%'
            }
        });
    },

    // 3. กราฟผู้ป่วยที่ยังมีชีวิตแยกตาม Diag
    renderLivingDiagChart(canvasId, data = []) {
        this.destroy(canvasId);
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        const labels = data.map(d => d.pdx || 'ไม่ระบุ');
        const values = data.map(d => parseInt(d.count || 0));
        const colors = [
            '#3b82f6', '#10b981', '#6366f1', '#f97316', 
            '#14b8a6', '#eab308', '#ec4899', '#8b5cf6'
        ];

        this.instances[canvasId] = new Chart(ctx, {
            type: 'pie',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12, font: { family: 'Prompt', size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: (c) => ` ${c.label}: ${c.raw.toLocaleString()} ราย`
                        }
                    }
                }
            }
        });
    },

    // 4. กราฟเฉพาะหมวดหมู่โรค (Category Hub Charts)
    renderCategoryCharts(catData) {
        if (!catData) return;

        // 4.1 กราฟ รพ.สต. ในหมวด
        if (catData.rpstData) {
            this.destroy('cat-chart-rpst');
            const ctx = document.getElementById('cat-chart-rpst');
            if (ctx) {
                const labels = catData.rpstData.map(d => d.rpst_name || 'ไม่ระบุ');
                const values = catData.rpstData.map(d => parseInt(d.count || 0));
                this.instances['cat-chart-rpst'] = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'จำนวนผู้ป่วย (ราย)',
                            data: values,
                            backgroundColor: catData.categoryInfo.color || '#0284c7',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }
        }

        // 4.2 กราฟ สถานะ มีชีวิต vs เสียชีวิต
        if (catData.statusData) {
            this.destroy('cat-chart-status');
            const ctx = document.getElementById('cat-chart-status');
            if (ctx) {
                const labels = catData.statusData.map(d => d.status_label);
                const values = catData.statusData.map(d => parseInt(d.count || 0));
                this.instances['cat-chart-status'] = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            backgroundColor: ['#ef4444', '#10b981'],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12 } }
                        },
                        cutout: '65%'
                    }
                });
            }
        }

        // 4.3 กราฟ การเยี่ยมบ้าน (ได้รับการเยี่ยม vs ไม่ได้รับการเยี่ยม)
        if (catData.visitCoverData) {
            this.destroy('cat-chart-visit');
            const ctx = document.getElementById('cat-chart-visit');
            if (ctx) {
                const labels = catData.visitCoverData.map(d => d.visit_label);
                const values = catData.visitCoverData.map(d => parseInt(d.count || 0));
                this.instances['cat-chart-visit'] = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            backgroundColor: ['#0284c7', '#f59e0b'],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12 } }
                        },
                        cutout: '65%'
                    }
                });
            }
        }

        // 4.4 กราฟ Top Diagnoses ในหมวด
        if (catData.topDiagData) {
            this.destroy('cat-chart-diags');
            const ctx = document.getElementById('cat-chart-diags');
            if (ctx) {
                const labels = catData.topDiagData.map(d => d.diag || 'ไม่ระบุ');
                const values = catData.topDiagData.map(d => parseInt(d.count || 0));
                this.instances['cat-chart-diags'] = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'จำนวน (ราย)',
                            data: values,
                            backgroundColor: 'rgba(99, 102, 241, 0.75)',
                            borderColor: '#6366f1',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }
        }
    }
};

window.ChartManager = ChartManager;
