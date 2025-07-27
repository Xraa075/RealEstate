@props(['id' => 'map', 'height' => '500px', 'assets' => []])

<div>
    <!-- Street View Modal -->
    <div id="street-view-modal" class="street-view-modal">
        <div class="street-view-modal-content">
            <div class="street-view-modal-header">
                <h3 class="street-view-modal-title">
                    <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm12 12V8l-4 4-4-4v8h8z" clip-rule="evenodd"></path>
                    </svg>
                    Street View
                </h3>
                <button id="street-view-close" class="street-view-close">&times;</button>
            </div>
            <div style="position: relative; height: calc(100% - 70px);">
                <div id="street-view-loading" class="street-view-loading">
                    <div class="street-view-spinner"></div>
                    <div>Loading Street View...</div>
                </div>
                <iframe id="street-view-iframe" class="street-view-iframe" style="display: none;"></iframe>
                <div id="street-view-error" class="street-view-error" style="display: none;">
                    <svg style="width: 48px; height: 48px; margin: 0 auto 16px; color: #ef4444;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <h4 style="margin: 0 0 8px 0; color: #374151;">Street View Not Available</h4>
                    <p style="margin: 0; color: #6b7280;">Street view imagery is not available for this location.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="{{ $id }}" style="height: {{ $height }}; width: 100%;" class="rounded-lg shadow-md"></div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
    <style>
        /* Custom popup styling */
        .custom-popup .leaflet-popup-content-wrapper {
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: none;
            padding: 0;
            overflow: hidden;
            background: white;
        }

        .custom-popup .leaflet-popup-content {
            margin: 0;
            padding: 12px;
            max-height: 500px;
            overflow-y: auto;
            width: auto !important;
        }

        .custom-popup .leaflet-popup-tip {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .custom-popup .leaflet-popup-close-button {
            color: #64748b;
            font-size: 18px;
            padding: 8px;
            top: 8px;
            right: 8px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.2s ease;
        }

        .custom-popup .leaflet-popup-close-button:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        /* Custom pin icon animation */
        .custom-pin-icon {
            transition: all 0.3s ease;
        }

        .custom-pin-icon:hover {
            transform: scale(1.15);
        }

        /* Asset popup specific styles */
        .asset-popup {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
        }

        .asset-popup a:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        /* Map legend styles */
        .map-legend {
            background: white;
            padding: 12px;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 6px;
            transition: all 0.2s ease;
        }

        .legend-item:hover {
            transform: translateX(2px);
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            margin-right: 10px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .custom-popup .leaflet-popup-content-wrapper {
                border-radius: 12px;
            }

            .asset-popup {
                min-width: 260px !important;
                max-width: 280px !important;
            }
        }

        /* Street View Modal Styles */
        .street-view-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(4px);
        }

        .street-view-modal-content {
            position: relative;
            background-color: white;
            margin: 2% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 1000px;
            height: 85%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .street-view-modal-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .street-view-modal-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .street-view-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 24px;
            font-weight: bold;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .street-view-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .street-view-iframe {
            width: 100%;
            height: calc(100% - 70px);
            border: none;
            background: #f3f4f6;
        }

        .street-view-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #6b7280;
        }

        .street-view-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e5e7eb;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .street-view-error {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }

        @media (max-width: 768px) {
            .street-view-modal-content {
                width: 95%;
                height: 90%;
                margin: 5% auto;
            }

            .street-view-modal-header {
                padding: 12px 16px;
            }

            .street-view-modal-title {
                font-size: 16px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <script src="https://unpkg.com/leaflet-geometryutil@0.10.1/src/leaflet.geometryutil.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            const map = L.map('{{ $id }}').setView([-6.2088, 106.8456], 12); // Default to Jakarta, Indonesia

            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add assets to map if provided
            @if(isset($assets) && count($assets) > 0)

                // Function to get color based on asset status
                function getAssetColor(status) {
                    const colors = {
                        'tersedia': '#22c55e',      // Green - Available
                        'disewakan': '#f59e0b',     // Orange - Rented
                        'terjual': '#ef4444',       // Red - Sold
                        'dalam_sengketa': '#8b5cf6' // Purple - In Dispute
                    };
                    return colors[status] || '#3388ff'; // Default blue
                }

                // Function to get status display name
                function getStatusDisplay(status) {
                    const statusNames = {
                        'tersedia': 'Available',
                        'disewakan': 'Rented',
                        'terjual': 'Sold',
                        'dalam_sengketa': 'In Dispute'
                    };
                    return statusNames[status] || status;
                }

                // Custom pin icon
                const customIcon = L.divIcon({
                    html: '<div style="background-color: #fff; border: 2px solid #333; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><div style="background-color: #ef4444; border-radius: 50%; width: 8px; height: 8px;"></div></div>',
                    className: 'custom-pin-icon',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });

                @foreach($assets as $asset)
                    @if($asset->geometry)
                        try {
                            const geometry = {!! json_encode($asset->geometry) !!};
                            const assetStatus = '{{ isset($asset->status) ? $asset->status : "tersedia" }}';
                            const assetColor = getAssetColor(assetStatus);

                            const polygon = L.geoJSON(JSON.parse(geometry), {
                                style: {
                                    color: assetColor,
                                    weight: 3,
                                    opacity: 0.8,
                                    fillColor: assetColor,
                                    fillOpacity: 0.2
                                }
                            }).addTo(map);

                            // Add hover effects
                            polygon.on('mouseover', function() {
                                this.setStyle({
                                    weight: 5,
                                    fillOpacity: 0.4
                                });
                            });

                            polygon.on('mouseout', function() {
                                this.setStyle({
                                    weight: 3,
                                    fillOpacity: 0.2
                                });
                            });

                            // Add pin marker at polygon center
                            const bounds = polygon.getBounds();
                            const center = bounds.getCenter();

                            // Create custom icon with status color
                            const statusIcon = L.divIcon({
                                html: `<div style="background-color: #fff; border: 2px solid ${assetColor}; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"><div style="background-color: ${assetColor}; border-radius: 50%; width: 10px; height: 10px;"></div></div>`,
                                className: 'custom-pin-icon',
                                iconSize: [24, 24],
                                iconAnchor: [12, 12]
                            });

                            const marker = L.marker(center, { icon: statusIcon }).addTo(map);

                            // Cek apakah asset memiliki properti name dan asset_code sebelum menggunakannya
                            @if(isset($asset->name) && isset($asset->asset_code))
                                const popupContent = `
                                    <div class="asset-popup" style="min-width: 280px; max-width: 320px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;">
                                        <!-- Header Section -->
                                        <div style="background: linear-gradient(135deg, ${assetColor} 0%, ${assetColor}dd 100%); padding: 16px; margin: -12px -12px 16px -12px; border-radius: 8px 8px 0 0; color: white; position: relative; overflow: hidden;">
                                            <div style="position: absolute; top: -20px; right: -20px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                                            <div style="position: relative; z-index: 1;">
                                                <h3 style="margin: 0 0 4px 0; font-size: 18px; font-weight: 600; text-shadow: 0 1px 2px rgba(0,0,0,0.1);">{{ $asset->name }}</h3>
                                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                                    <span style="font-size: 13px; opacity: 0.9; font-weight: 500;">{{ $asset->asset_code }}</span>
                                                    <div style="background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 16px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        ${getStatusDisplay(assetStatus)}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Content Section -->
                                        <div style="padding: 0 4px;">
                                            @if(isset($asset->area_sqm) || isset($asset->value))
                                                <!-- Key Metrics -->
                                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                                                    @if(isset($asset->area_sqm))
                                                        <div style="background: #f8fafc; padding: 12px; border-radius: 8px; border-left: 3px solid ${assetColor};">
                                                            <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px;">Area</div>
                                                            <div style="font-size: 16px; font-weight: 700; color: #1e293b;">{{ number_format($asset->area_sqm, 0, ',', '.') }}</div>
                                                            <div style="font-size: 12px; color: #64748b; font-weight: 500;">m²</div>
                                                        </div>
                                                    @endif
                                                    @if(isset($asset->value))
                                                        <div style="background: #f8fafc; padding: 12px; border-radius: 8px; border-left: 3px solid ${assetColor};">
                                                            <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px;">Value</div>
                                                            <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Rp {{ number_format($asset->value / 1000000, 0, ',', '.') }}M</div>
                                                            <div style="font-size: 11px; color: #64748b; font-weight: 500;">IDR</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            @if(isset($asset->address))
                                                <!-- Address Section -->
                                                <div style="margin-bottom: 16px;">
                                                    <div style="display: flex; align-items: center; margin-bottom: 6px;">
                                                        <svg style="width: 14px; height: 14px; color: ${assetColor}; margin-right: 6px;" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <span style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Location</span>
                                                    </div>
                                                    <p style="margin: 0; color: #475569; font-size: 13px; line-height: 1.5; padding-left: 20px;">{{ Str::limit($asset->address, 80, '...') }}</p>
                                                </div>
                                            @endif

                                            @if(isset($asset->id))
                                                <!-- Action Buttons -->
                                                <div style="margin-top: 16px;">
                                                    <div style="display: grid; grid-template-columns: 1fr auto; gap: 8px; align-items: center;">
                                                        @if(request()->routeIs('admin.*'))
                                                            <a href="{{ route("admin.assets.show", $asset) }}" style="display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, ${assetColor} 0%, ${assetColor}dd 100%); color: white; padding: 12px 16px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: none;">
                                                                <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                View Details
                                                            </a>
                                                        @elseif(request()->routeIs('manager.*'))
                                                            <a href="{{ route("manager.assets.show", $asset) }}" style="display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, ${assetColor} 0%, ${assetColor}dd 100%); color: white; padding: 12px 16px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: none;">
                                                                <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                View Details
                                                            </a>
                                                        @elseif(request()->routeIs('surveyor.*'))
                                                            <a href="{{ route("surveyor.assets.show", $asset) }}" style="display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, ${assetColor} 0%, ${assetColor}dd 100%); color: white; padding: 12px 16px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: none;">
                                                                <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                View Details
                                                            </a>
                                                        @else
                                                            <div style="display: flex; align-items: center; justify-content: center; background: #f1f5f9; color: #64748b; padding: 12px 16px; border-radius: 8px; font-size: 14px; font-weight: 500;">
                                                                <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                Asset Info
                                                            </div>
                                                        @endif

                                                        <!-- Street View Button -->
                                                        <button onclick="openStreetView(${center.lat}, ${center.lng}, '{{ $asset->name }}')" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); min-width: 48px; display: flex; align-items: center; justify-content: center;" title="View Street View">
                                                            <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm12 12V8l-4 4-4-4v8h8z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                `;

                                // Bind popup to both polygon and marker
                                polygon.bindPopup(popupContent, {
                                    maxWidth: 300,
                                    className: 'custom-popup'
                                });
                                marker.bindPopup(popupContent, {
                                    maxWidth: 300,
                                    className: 'custom-popup'
                                });
                            @else
                                polygon.bindPopup('Preview of geometry');
                                marker.bindPopup('Preview of geometry');
                            @endif
                        } catch (e) {
                            console.error('Error parsing geometry:', e);
                        }
                    @endif
                @endforeach

                // Fit map to bounds of all assets
                if (map._layers) {
                    const bounds = [];
                    Object.values(map._layers).forEach(layer => {
                        if (layer.getBounds) {
                            bounds.push(layer.getBounds());
                        }
                    });

                    if (bounds.length > 0) {
                        map.fitBounds(bounds);
                    }
                }

                // Add legend control
                const legend = L.control({ position: 'bottomright' });
                legend.onAdd = function(map) {
                    const div = L.DomUtil.create('div', 'map-legend');
                    div.innerHTML = `
                        <div style="font-weight: bold; margin-bottom: 8px; color: #1f2937;">Asset Status</div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #22c55e;"></div>
                            <span>Available</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #f59e0b;"></div>
                            <span>Rented</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #ef4444;"></div>
                            <span>Sold</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #8b5cf6;"></div>
                            <span>In Dispute</span>
                        </div>
                    `;
                    return div;
                };
                legend.addTo(map);
            @endif

            // Initialize draw controls
            const drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);

            const drawControl = new L.Control.Draw({
                draw: {
                    marker: false,
                    circle: false,
                    circlemarker: false,
                    rectangle: true,
                    polygon: true,
                    polyline: false
                },
                edit: {
                    featureGroup: drawnItems
                }
            });
            map.addControl(drawControl);

            // Handle draw events
            map.on('draw:created', function(e) {
                const layer = e.layer;
                drawnItems.addLayer(layer);

                // Get GeoJSON representation of the drawn shape
                const geoJson = layer.toGeoJSON();
                const geoJsonString = JSON.stringify(geoJson.geometry);

                // Update form field if it exists
                const geometryField = document.getElementById('geometry');
                if (geometryField) {
                    geometryField.value = geoJsonString;
                }

                // Calculate area in square meters
                let area = 0;
                if (e.layerType === 'polygon' || e.layerType === 'rectangle') {
                    area = L.GeometryUtil.geodesicArea(layer.getLatLngs()[0]);

                    // Update area field if it exists (force update even if readonly)
                    const areaField = document.getElementById('area_sqm');
                    if (areaField) {
                        areaField.value = Math.round(area);
                        // Trigger change event for readonly fields
                        areaField.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
            });

            // Handle edit events
            map.on('draw:edited', function(e) {
                const layers = e.layers;
                layers.eachLayer(function(layer) {
                    // Get updated GeoJSON
                    const geoJson = layer.toGeoJSON();
                    const geoJsonString = JSON.stringify(geoJson.geometry);

                    // Update form field
                    const geometryField = document.getElementById('geometry');
                    if (geometryField) {
                        geometryField.value = geoJsonString;
                    }

                    // Recalculate area (force update even if readonly)
                    const area = L.GeometryUtil.geodesicArea(layer.getLatLngs()[0]);
                    const areaField = document.getElementById('area_sqm');
                    if (areaField) {
                        areaField.value = Math.round(area);
                        // Trigger change event for readonly fields
                        areaField.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
            });

            // Handle delete events
            map.on('draw:deleted', function() {
                const geometryField = document.getElementById('geometry');
                if (geometryField) {
                    geometryField.value = '';
                }

                const areaField = document.getElementById('area_sqm');
                if (areaField) {
                    areaField.value = '';
                }
            });

            // Expose map instance globally for external access
            window.mapInstance = {
                map: map,
                drawnItems: drawnItems,
                loadExistingGeometry: function(geometry) {
                    if (geometry && typeof geometry === 'object') {
                        try {
                            // Clear existing drawn items first
                            drawnItems.clearLayers();

                            // Add the existing geometry to the map
                            const layer = L.geoJSON(geometry, {
                                style: {
                                    color: '#3388ff',
                                    weight: 3,
                                    opacity: 0.8,
                                    fillOpacity: 0.2
                                }
                            });

                            // Add to drawn items layer group for editing
                            layer.eachLayer(function(l) {
                                drawnItems.addLayer(l);
                            });

                            // Fit map to geometry bounds
                            map.fitBounds(layer.getBounds());

                            console.log('Existing geometry loaded successfully for editing');
                        } catch (e) {
                            console.error('Error loading existing geometry:', e);
                        }
                    }
                },
                isEditMode: function() {
                    // Check if we're in edit mode by looking for geometry inputs
                    return document.getElementById('geometry') && document.getElementById('original_geometry');
                }
            };

            // Auto-load existing geometry in edit mode
            document.addEventListener('DOMContentLoaded', function() {
                if (window.mapInstance && window.mapInstance.isEditMode()) {
                    const originalGeometryInput = document.getElementById('original_geometry');
                    if (originalGeometryInput && originalGeometryInput.value) {
                        try {
                            const geometry = JSON.parse(originalGeometryInput.value);
                            setTimeout(() => {
                                window.mapInstance.loadExistingGeometry(geometry);
                            }, 500); // Small delay to ensure map is fully loaded
                        } catch (e) {
                            console.log('Could not auto-load existing geometry:', e);
                        }
                    }
                }
            });

            // Street View functionality
            window.openStreetView = function(lat, lng, assetName) {
                const modal = document.getElementById('street-view-modal');
                const iframe = document.getElementById('street-view-iframe');
                const loading = document.getElementById('street-view-loading');
                const error = document.getElementById('street-view-error');
                const modalTitle = document.querySelector('.street-view-modal-title');

                // Update modal title
                modalTitle.innerHTML = `
                    <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm12 12V8l-4 4-4-4v8h8z" clip-rule="evenodd"></path>
                    </svg>
                    Street View - ${assetName}
                `;

                // Show modal and loading
                modal.style.display = 'block';
                loading.style.display = 'block';
                iframe.style.display = 'none';
                error.style.display = 'none';

                // Street View URL (using Google Street View Static API approach)
                const streetViewUrl = `https://www.google.com/maps/embed/v1/streetview?location=${lat},${lng}&heading=0&pitch=0&fov=90&key=YOUR_API_KEY`;

                // For demo purposes, we'll use a direct Google Maps Street View URL
                const demoUrl = `https://www.google.com/maps/@${lat},${lng},3a,75y,0h,90t/data=!3m1!1e3`;

                // Try to load Street View
                setTimeout(() => {
                    try {
                        // For production, you would use the proper Google Street View Embed API
                        // For now, we'll use a workaround that opens Google Maps in the iframe
                        iframe.src = `https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1000!2d${lng}!3d${lat}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zM${lat}%C2%B0${lat < 0 ? 'S' : 'N'}%20${lng}%C2%B0${lng < 0 ? 'W' : 'E'}!5e1!3m2!1sen!2sid!4v1234567890123!5m2!1sen!2sid`;

                        iframe.onload = function() {
                            loading.style.display = 'none';
                            iframe.style.display = 'block';
                        };

                        iframe.onerror = function() {
                            loading.style.display = 'none';
                            error.style.display = 'block';
                        };

                    } catch (e) {
                        loading.style.display = 'none';
                        error.style.display = 'block';
                    }
                }, 500);
            };

            // Street View modal close functionality
            const modal = document.getElementById('street-view-modal');
            const closeBtn = document.getElementById('street-view-close');

            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
                const iframe = document.getElementById('street-view-iframe');
                iframe.src = ''; // Stop loading
            });

            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    const iframe = document.getElementById('street-view-iframe');
                    iframe.src = ''; // Stop loading
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.style.display === 'block') {
                    modal.style.display = 'none';
                    const iframe = document.getElementById('street-view-iframe');
                    iframe.src = ''; // Stop loading
                }
            });
        });
    </script>
@endpush
