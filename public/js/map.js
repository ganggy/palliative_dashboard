// Leaflet Map Controller for Palliative GIS
const MapController = {
    map: null,
    markersLayer: null,

    init(containerId = 'map-canvas') {
        const el = document.getElementById(containerId);
        if (!el) return;

        if (this.map) {
            this.map.remove();
            this.map = null;
        }

        // Center on Khoksrisuphan Hospital, Sakon Nakhon
        const defaultCenter = [16.9741, 104.2825]; // พิกัด อ.โคกศรีสุพรรณ

        this.map = L.map(containerId).setView(defaultCenter, 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors | Palliative Care Khoksrisuphan',
            maxZoom: 18
        }).addTo(this.map);

        this.markersLayer = L.layerGroup().addTo(this.map);
        this.loadLocations();
    },

    async loadLocations() {
        if (!this.map) return;
        try {
            const res = await fetch('/api/map/patients');
            const data = await res.json();
            if (!data.success || !data.locations) return;

            this.markersLayer.clearLayers();
            const bounds = [];

            data.locations.forEach(pt => {
                if (!pt.lat || !pt.lng) return;

                // Color code marker based on visit status
                let markerColor = '#10b981'; // Green
                if (pt.visitStatus.text.includes('เสียชีวิต')) markerColor = '#64748b'; // Gray
                else if (pt.visitStatus.text.includes('1 เดือน') || pt.visitStatus.text.includes('2 เดือน')) markerColor = '#0284c7'; // Blue
                else if (pt.visitStatus.text.includes('3 เดือน') || pt.visitStatus.text.includes('6 เดือน')) markerColor = '#f59e0b'; // Amber
                else if (pt.visitStatus.text.includes('1 ปี')) markerColor = '#ef4444'; // Red

                const icon = L.divIcon({
                    className: 'custom-map-pin',
                    html: `<div style="background-color: ${markerColor}; width: 26px; height: 26px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-size: 11px;"><i class="fa fa-user"></i></div>`,
                    iconSize: [26, 26],
                    iconAnchor: [13, 13]
                });

                const marker = L.marker([pt.lat, pt.lng], { icon });

                const popupHtml = `
                    <div style="font-family: 'Prompt', sans-serif; font-size: 12.5px; line-height: 1.5; min-width: 220px;">
                        <h4 style="font-size: 14px; font-weight: 700; color: #0284c7; margin-bottom: 4px;">${pt.ptname}</h4>
                        <div><b>HN:</b> ${pt.hn} | <b>อายุ:</b> ${pt.age} ปี</div>
                        <div><b>ที่อยู่:</b> ${pt.addr}</div>
                        <div><b>โรค (PDX):</b> <span style="background: #e0f2fe; padding: 2px 6px; border-radius: 4px; font-weight: 600;">${pt.pdx || '-'}</span></div>
                        <div style="margin-top: 6px;"><b>สถานะเยี่ยม:</b> <span class="badge-status ${pt.visitStatus.badgeClass}">${pt.visitStatus.text}</span></div>
                        <div style="margin-top: 8px;">
                            <button onclick="App.openVisitModal('${pt.hn}')" style="background: #0284c7; color: white; border: none; padding: 5px 10px; border-radius: 6px; font-size: 11.5px; cursor: pointer; width: 100%;">
                                <i class="fa fa-home"></i> ดูบันทึกการเยี่ยมบ้าน
                            </button>
                        </div>
                    </div>
                `;

                marker.bindPopup(popupHtml);
                this.markersLayer.addLayer(marker);
                bounds.push([pt.lat, pt.lng]);
            });

            if (bounds.length > 0) {
                this.map.fitBounds(bounds, { padding: [30, 30] });
            }
        } catch (err) {
            console.error('Failed to load map locations:', err);
        }
    }
};

window.MapController = MapController;
