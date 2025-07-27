<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $asset->name }} - Real Estate Portal</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --grass-green: #4ade80;
            --grass-green-dark: #16a34a;
            --grass-green-light: #86efac;
            --grass-green-bg: #dcfce7;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .navbar {
            border-radius: 0 !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .map-container {
            height: 450px;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            border-radius: 25px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        .btn-grass {
            background: linear-gradient(135deg, var(--grass-green) 0%, var(--grass-green-dark) 100%);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 20px;
            padding: 12px 30px;
            transition: all 0.3s ease;
        }

        .btn-grass:hover {
            background: linear-gradient(135deg, var(--grass-green-dark) 0%, #15803d 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(74, 222, 128, 0.4);
            color: white;
        }

        .btn-outline-grass {
            border: 2px solid var(--grass-green);
            color: var(--grass-green-dark);
            background: transparent;
            font-weight: 600;
            border-radius: 20px;
            padding: 12px 30px;
            transition: all 0.3s ease;
        }

        .btn-outline-grass:hover {
            background: var(--grass-green);
            border-color: var(--grass-green);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(74, 222, 128, 0.4);
        }

        .content-card {
            border-radius: 25px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .status-badge {
            border-radius: 15px;
            padding: 8px 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .detail-container {
            border-radius: 15px;
            background: var(--grass-green-bg) !important;
            border: 1px solid var(--grass-green-light);
        }

        .contact-card {
            border-radius: 20px;
            background: linear-gradient(135deg, var(--grass-green-bg) 0%, #ffffff 100%);
            border: 1px solid var(--grass-green-light);
        }

        .table-borderless {
            background: transparent !important;
        }

        .table-borderless td {
            border: none;
            padding: 12px 0;
            background: transparent !important;
        }

        .detail-value {
            color: var(--grass-green-dark);
            font-weight: 600;
        }

        .price-text {
            background: linear-gradient(135deg, var(--grass-green) 0%, var(--grass-green-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg bg-white sticky-top">
        <div class="container">
            <div class="d-flex align-items-center">
                <a href="{{ route('public.landing') }}" class="btn btn-outline-grass me-3">
                    Back to Home
                </a>
                <h1 class="navbar-brand mb-0 fw-bold fs-4">Detail Aset</h1>
            </div>

            <a href="{{ route('login') }}" class="btn btn-grass">
                Staff Login
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <!-- Property Header -->
        <div class="content-card card mb-5">
            <div class="card-body p-5">
                <div class="row align-items-start">
                    <div class="col-lg-8">
                        <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                            <h1 class="display-5 fw-bold mb-0">{{ $asset->name }}</h1>
                            <span
                                class="status-badge
                                {{ $asset->status === 'tersedia'
                                    ? 'bg-success text-white'
                                    : ($asset->status === 'disewakan'
                                        ? 'bg-warning text-dark'
                                        : 'bg-danger text-white') }}">
                                {{ ucfirst($asset->status) }}
                            </span>
                        </div>

                        <p class="fs-5 text-muted mb-4">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                            {{ $asset->address }}
                        </p>

                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="stat-card card text-center p-4">
                                    <div class="card-body">
                                        <i class="bi bi-hash fs-1 text-primary mb-3"></i>
                                        <h6 class="text-muted mb-2">Asset Code</h6>
                                        <h4 class="fw-bold">{{ $asset->code }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="stat-card card text-center p-4">
                                    <div class="card-body">
                                        <i class="bi bi-currency-dollar fs-1 mb-3"
                                            style="color: var(--grass-green)"></i>
                                        <h6 class="text-muted mb-2">Property Value</h6>
                                        <h4 class="fw-bold price-text">Rp {{ number_format($asset->value) }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="stat-card card text-center p-4">
                                    <div class="card-body">
                                        <i class="bi bi-rulers fs-1 text-info mb-3"></i>
                                        <h6 class="text-muted mb-2">Land Area</h6>
                                        <h4 class="fw-bold text-info">{{ number_format($asset->area_sqm) }} m²</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="contact-card p-4 text-center">
                            <h5 class="fw-bold mb-3">Contact Information</h5>
                            <div class="mb-3">
                                <i class="bi bi-telephone-fill fs-4 text-success"></i>
                                <p class="mt-2 mb-1 fw-semibold">Phone</p>
                                <a href="tel:+6212345678" class="text-decoration-none">+62-123-4567-8900</a>
                            </div>
                            <div class="mb-4">
                                <i class="bi bi-envelope-fill fs-4 text-primary"></i>
                                <p class="mt-2 mb-1 fw-semibold">Email</p>
                                <a href="mailto:info@realestate.com"
                                    class="text-decoration-none">info@realestate.com</a>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="tel:+6212345678" class="btn btn-success rounded-pill">
                                    <i class="bi bi-telephone"></i> Call Us
                                </a>
                                <a href="mailto:info@realestate.com?subject=Inquiry about {{ $asset->name }}"
                                    class="btn btn-primary rounded-pill">
                                    <i class="bi bi-envelope"></i> Send Email
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Map -->
        <div class="content-card card mb-5">
            <div class="card-body p-5">
                <h2 class="fw-bold mb-4">
                    <i class="bi bi-map"></i> Property Location
                </h2>
                <div id="map" class="map-container"></div>
            </div>
        </div>

        <!-- Property Details -->
        <div class="content-card card">
            <div class="card-body p-5">
                <h2 class="fw-bold mb-4">
                    <i class="bi bi-info-circle"></i> Property Details
                </h2>

                @if ($asset->description)
                    <div class="mb-5">
                        <h3 class="h5 fw-semibold mb-3">Description</h3>
                        <div class="detail-container p-4">
                            <p class="mb-0 lh-lg">
                                {!! nl2br(e($asset->description)) !!}
                            </p>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-6">
                        <h3 class="h5 fw-semibold mb-3">Basic Information</h3>
                        <div class="detail-container p-4">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">Asset Code</td>
                                    <td class="text-end detail-value">{{ $asset->code }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status</td>
                                    <td class="text-end">
                                        <span
                                            class="status-badge
                                            {{ $asset->status === 'tersedia'
                                                ? 'bg-success text-white'
                                                : ($asset->status === 'disewakan'
                                                    ? 'bg-warning text-dark'
                                                    : 'bg-danger text-white') }}">
                                            {{ ucfirst($asset->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Land Area</td>
                                    <td class="text-end detail-value">{{ number_format($asset->area_sqm) }} m²</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <h3 class="h5 fw-semibold mb-3">Financial Information</h3>
                        <div class="detail-container p-4">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">Property Value</td>
                                    <td class="text-end price-text fw-bold">Rp {{ number_format($asset->value) }}</td>
                                </tr>
                                @if ($asset->owner)
                                    <tr>
                                        <td class="text-muted">Owner</td>
                                        <td class="text-end detail-value">{{ $asset->owner }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="text-muted">Listed Date</td>
                                    <td class="text-end detail-value">{{ $asset->created_at->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Map Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            const map = L.map('map').setView([-6.2088, 106.8456], 13);

            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add asset geometry if available
            @if ($asset->geometry)
                try {
                    const geometry = @json($asset->geometry);
                    const geoData = JSON.parse(geometry);

                    if (geoData.type === 'Polygon') {
                        const coordinates = geoData.coordinates[0];

                        // Calculate center point
                        const centerLat = coordinates.reduce((sum, coord) => sum + coord[1], 0) / coordinates
                            .length;
                        const centerLng = coordinates.reduce((sum, coord) => sum + coord[0], 0) / coordinates
                            .length;

                        // Set map view to center
                        map.setView([centerLat, centerLng], 16);

                        // Add polygon with grass green theme
                        const polygon = L.polygon(coordinates.map(coord => [coord[1], coord[0]]), {
                            color: '{{ $asset->status === 'tersedia' ? '#4ade80' : ($asset->status === 'disewakan' ? '#f59e0b' : '#ef4444') }}',
                            weight: 3,
                            opacity: 0.8,
                            fillOpacity: 0.2
                        }).addTo(map);

                        // Custom grass green marker icon
                        const grassIcon = L.divIcon({
                            className: 'custom-marker',
                            html: `<div style="background: linear-gradient(135deg, #4ade80 0%, #16a34a 100%); width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 4px 12px rgba(74, 222, 128, 0.4); display: flex; align-items: center; justify-content: center;">
                                <i style="color: white; font-size: 14px;" class="bi bi-house-fill"></i>
                            </div>`,
                            iconSize: [30, 30],
                            iconAnchor: [15, 15]
                        });

                        // Add marker at center with custom grass green icon
                        const marker = L.marker([centerLat, centerLng], {
                            icon: grassIcon
                        }).addTo(map);

                        // Enhanced popup content with Bootstrap styling
                        const popupContent = `
                            <div class="p-3" style="min-width: 250px;">
                                <h5 class="fw-bold mb-2" style="color: #16a34a;">{{ $asset->name }}</h5>
                                <p class="text-muted mb-3">
                                    <i class="bi bi-geo-alt-fill text-danger"></i>
                                    {{ $asset->address }}
                                </p>
                                <div class="row g-2 text-sm">
                                    <div class="col-12">
                                        <span class="badge {{ $asset->status === 'tersedia' ? 'bg-success' : ($asset->status === 'disewakan' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                            {{ ucfirst($asset->status) }}
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Area:</small><br>
                                        <strong>{{ number_format($asset->area_sqm) }} m²</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Value:</small><br>
                                        <strong style="color: #16a34a;">Rp {{ number_format($asset->value / 1000000, 1) }}M</strong>
                                    </div>
                                </div>
                            </div>
                        `;

                        marker.bindPopup(popupContent);
                        polygon.bindPopup(popupContent);

                        // Fit map to polygon bounds
                        map.fitBounds(polygon.getBounds().pad(0.1));
                    }
                } catch (e) {
                    console.error('Error parsing geometry:', e);
                }
            @endif
        });
    </script>
</body>

</html>
