<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Real Estate Asset Management</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom Green Grass Theme */
        :root {
            --grass-green: #4ade80;
            --grass-green-dark: #16a34a;
            --grass-green-light: #86efac;
            --grass-green-lighter: #dcfce7;
        }

        body {
            font-family: 'Figtree', sans-serif;
            padding-top: 76px;
            /* Adjust for fixed navbar */
        }

        /* Navigation */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--grass-green-light);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-custom .navbar-brand {
            color: var(--grass-green-dark) !important;
        }

        /* Force navbar menu to be visible */
        .navbar-expand-lg .navbar-nav {
            flex-direction: row !important;
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .navbar-expand-lg .navbar-nav .nav-item {
            display: list-item !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .navbar-expand-lg .navbar-nav .nav-link {
            color: #6c757d !important;
            transition: color 0.3s ease;
            padding: 0.5rem 1rem !important;
            display: flex !important;
            align-items: center;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link:focus {
            color: var(--grass-green-dark) !important;
        }

        /* Override any hiding styles */
        .navbar-collapse.collapse.show .navbar-nav,
        .navbar-collapse.collapsing .navbar-nav,
        .navbar-collapse:not(.collapse) .navbar-nav {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Desktop view - force visibility */
        @media (min-width: 992px) {
            .navbar-expand-lg .navbar-collapse {
                display: flex !important;
                flex-basis: auto;
                visibility: visible !important;
                opacity: 1 !important;
            }

            .navbar-expand-lg .navbar-nav {
                flex-direction: row !important;
                display: flex !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            .navbar-expand-lg .navbar-nav .nav-item {
                display: list-item !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
        }

        .navbar-custom .btn-grass {
            display: inline-flex !important;
            align-items: center;
            white-space: nowrap;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Ensure navbar items are visible */
        .navbar-nav .nav-item {
            margin: 0 5px;
        }

        .navbar-collapse .d-flex {
            display: flex !important;
        }

        .navbar-collapse .d-flex .btn {
            display: inline-flex !important;
            visibility: visible !important;
        }

        /* Navbar toggler */
        .navbar-toggler {
            border: none;
            padding: 4px 8px;
            border-radius: 15px;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        @media (max-width: 991.98px) {
            .navbar-nav {
                text-align: center;
                display: flex !important;
                flex-direction: column;
            }

            .navbar-nav .nav-item {
                margin: 5px 0;
            }

            .navbar-collapse .d-flex {
                justify-content: center;
                margin-top: 15px;
            }
        }

        /* Hero Section */
        .hero-bg {
            background: linear-gradient(135deg, var(--grass-green) 0%, var(--grass-green-dark) 100%);
            min-height: 100vh;
            position: relative;
            margin-top: -76px;
            /* Offset fixed navbar */
            padding-top: 76px;
            border-radius: 0 !important;
        }

        .hero-overlay {
            background: rgba(0, 0, 0, 0.3);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 0 !important;
        }

        /* Custom Buttons */
        .btn-grass {
            background: linear-gradient(135deg, var(--grass-green) 0%, var(--grass-green-dark) 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
            border-radius: 20px;
        }

        .btn-grass:hover {
            background: linear-gradient(135deg, var(--grass-green-dark) 0%, #166f2a 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(74, 222, 128, 0.3);
            color: white;
        }

        /* Glass Effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
        }

        /* Map Container */
        .map-container {
            height: 500px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }

        /* Ensure map div has proper positioning */
        #map {
            position: relative;
            z-index: 1;
            height: 500px;
            width: 100%;
        }

        /* Leaflet map specific positioning fixes */
        .leaflet-container {
            position: relative !important;
            overflow: hidden;
            height: 100% !important;
            width: 100% !important;
        }

        .leaflet-map-pane {
            position: relative !important;
        }

        /* Custom Cards */
        .asset-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .asset-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(74, 222, 128, 0.2);
        }

        /* Card text overflow fixes */
        .asset-card .card-title {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100%;
        }

        .asset-card .text-muted {
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }

        .asset-card .fw-bold.small {
            font-size: 0.85rem !important;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .asset-card .p-2.bg-light {
            min-height: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 15px;
        }

        /* Stats container fix */
        .stat-card .stat-number {
            word-break: break-word;
            overflow-wrap: break-word;
            line-height: 1.1;
        }

        .stat-card .text-muted {
            font-size: 0.9rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Stat Cards */
        .stat-card {
            background: linear-gradient(135deg, white 0%, var(--grass-green-lighter) 100%);
            border: 2px solid var(--grass-green-light);
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(74, 222, 128, 0.3);
        }

        .stat-number {
            color: var(--grass-green-dark);
            font-weight: 700;
        }

        /* Search Form */
        .search-form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 2px solid var(--grass-green-light);
            border-radius: 25px;
        }

        /* Search form inputs and selects */
        .search-form .form-control,
        .search-form .form-select {
            border-radius: 20px;
            border: 2px solid grey;
            transition: all 0.3s ease;
        }

        .search-form .form-control:focus,
        .search-form .form-select:focus {
            border-color: var(--grass-green);
            box-shadow: 0 0 0 0.2rem rgba(74, 222, 128, 0.25);
        }

        .search-form .btn {
            border-radius: 20px;
        }

        /* All Bootstrap buttons - make them rounded */
        .btn {
            border-radius: 20px;
        }

        .btn-outline-secondary {
            border-radius: 20px;
        }

        /* Pagination links */
        .pagination .page-link {
            border-radius: 15px;
            margin: 0 2px;
        }

        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            border-radius: 15px;
        }

        /* Sections styling */
        section {
            border-radius: 25px;
            margin: 10px 0;
        }

        /* Override rounded for hero section */
        section#hero {
            border-radius: 0 !important;
            margin: 0 !important;
        }

        section.py-5.bg-light {
            border-radius: 25px;
        }

        section.py-5.bg-white {
            border-radius: 25px;
        }

        /* Map legend indicators - make them more rounded */
        .rounded-circle {
            border-radius: 50% !important;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Additional rounded styles for containers */
        .container {
            border-radius: 20px;
        }

        /* Text center areas */
        .text-center {
            padding: 20px;
            border-radius: 20px;
        }

        /* Asset card image areas */
        .position-relative {
            border-radius: 15px;
        }

        /* Status Badges */
        .status-tersedia {
            background-color: var(--grass-green) !important;
            border-radius: 20px;
        }

        .status-disewakan {
            background-color: #f59e0b !important;
            border-radius: 20px;
        }

        .status-terjual {
            background-color: #ef4444 !important;
            border-radius: 20px;
        }

        .status-dalam_sengketa {
            background-color: #6b7280 !important;
            border-radius: 20px;
        }

        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card {
            animation: slideInUp 0.6s ease-out;
        }

        .stat-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .stat-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .stat-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .stat-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        /* Custom popup styling */
        .custom-popup .leaflet-popup-content-wrapper {
            background: white;
            color: #0c0c0c;
            font-size: 13px;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: none;
            padding: 0;
        }

        .custom-popup .leaflet-popup-content {
            margin: 12px;
            line-height: 1.4;
            font-size: 13px;
            padding: 0;
        }

        .custom-popup .leaflet-popup-tip {
            background: white;
            color: #333;
            box-shadow: 0 3px 14px rgba(0, 0, 0, 0.1);
        }

        .custom-popup .leaflet-popup-close-button {
            color: #475569;
            font-size: 18px;
            background: rgba(255, 255, 255, 0.9);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            text-align: center;
            margin: 6px;
            font-weight: bold;
            text-decoration: none;
            line-height: 26px;
            transition: all 0.2s;
        }

        .custom-popup .leaflet-popup-close-button:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        /* Asset popup specific styles */
        .asset-popup {
            font-size: 13px;
        }

        .asset-popup a:hover {
            text-decoration: underline;
        }

        /* Stats cards animation */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card {
            animation: slideInUp 0.6s ease-out;
        }

        .stat-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .stat-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .stat-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .stat-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        /* Search form styling */
        .search-form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Asset status badges */
        .status-tersedia {
            @apply bg-green-100 text-green-800;
        }

        .status-disewakan {
            @apply bg-yellow-100 text-yellow-800;
        }

        .status-terjual {
            @apply bg-red-100 text-red-800;
        }

        /* Loading animation */
        .loading {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
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
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(4px);
        }

        .street-view-modal-content {
            background-color: #fefefe;
            margin: 3% auto;
            border-radius: 16px;
            width: 90%;
            max-width: 1000px;
            height: 85vh;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .street-view-modal-header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 16px 16px 0 0;
        }

        .street-view-modal-title {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .street-view-close {
            color: white;
            float: right;
            font-size: 28px;
            font-weight: bold;
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 50%;
            transition: all 0.3s;
        }

        .street-view-close:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .street-view-iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 0 0 16px 16px;
        }

        .street-view-loading {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            color: #6b7280;
            font-size: 16px;
        }

        .street-view-spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #059669;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin-bottom: 16px;
        }

        .street-view-error {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <!-- Street View Modal -->
    <div id="street-view-modal" class="street-view-modal">
        <div class="street-view-modal-content">
            <div class="street-view-modal-header">
                <h3 class="street-view-modal-title">
                    <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm12 12V8l-4 4-4-4v8h8z"
                            clip-rule="evenodd"></path>
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
                    <svg style="width: 48px; height: 48px; margin: 0 auto 16px; color: #ef4444;" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <h4 style="margin: 0 0 8px 0; color: #374151;">Street View Not Available</h4>
                    <p style="margin: 0; color: #6b7280;">Street view imagery is not available for this location.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="#" style="color: var(--grass-green-dark);">
                RealEstate
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse show" id="navbarNav" style="display: flex !important;">
                <ul class="navbar-nav mx-auto"
                    style="display: flex !important; visibility: visible !important; opacity: 1 !important; flex-direction: row !important;">
                    <li class="nav-item" style="display: list-item !important; visibility: visible !important;">
                        <a class="nav-link fw-medium" href="#hero"
                            style="display: flex !important; visibility: visible !important; color: #6c757d !important;">
                            Home
                        </a>
                    </li>
                    <li class="nav-item" style="display: list-item !important; visibility: visible !important;">
                        <a class="nav-link fw-medium" href="#map-section"
                            style="display: flex !important; visibility: visible !important; color: #6c757d !important;">
                            Map
                        </a>
                    </li>
                    <li class="nav-item" style="display: list-item !important; visibility: visible !important;">
                        <a class="nav-link fw-medium" href="#assets"
                            style="display: flex !important; visibility: visible !important; color: #6c757d !important;">
                            Assets
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                    <a href="{{ route('login') }}" class="btn btn-grass px-4 py-2">
                        Staff Login
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <!-- Hero Section -->
    <section id="hero" class="hero-bg d-flex align-items-center position-relative">
        <div class="hero-overlay"></div>
        <div class="container position-relative">
            <div class="row justify-content-center text-center">
                <div class="col-lg-10">
                    <h1 class="display-2 fw-bold text-white mb-4">
                        Find Your Perfect
                        <span class="text-warning">Land Asset</span>
                    </h1>
                    <p class="lead text-white-50 mb-5 fs-4">
                        Explore our comprehensive database of premium land assets with interactive maps,
                        detailed information, and real-time availability status.
                    </p>

                    <!-- Quick Stats -->
                    <div class="row g-4 mt-5">
                        <div class="col-6 col-md-3">
                            <div class="stat-card p-4 text-center h-100">
                                <div class="stat-number display-6 mb-2" style="font-size: 2.5rem; line-height: 1.1;">
                                    {{ number_format($statistics['total_assets']) }}</div>
                                <div class="text-muted fw-medium">
                                    Total Assets
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-card p-4 text-center h-100">
                                <div class="stat-number display-6 mb-2" style="font-size: 2.5rem; line-height: 1.1;">
                                    {{ number_format($statistics['available_assets']) }}</div>
                                <div class="text-muted fw-medium">
                                    Available
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-card p-4 text-center h-100">
                                <div class="stat-number display-6 mb-2" style="font-size: 2rem; line-height: 1.1;">
                                    {{ number_format($statistics['total_area']) }}</div>
                                <div class="text-muted fw-medium">
                                    Total sqm
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-card p-4 text-center h-100">
                                <div class="stat-number display-6 mb-2" style="font-size: 2rem; line-height: 1.1;">Rp
                                    {{ number_format($statistics['total_value'] / 1000000000, 1) }}B</div>
                                <div class="text-muted fw-medium">
                                    Total Value
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold" style="color: var(--grass-green-dark);">
                    <i class="bi bi-search me-2"></i>Search Land Assets
                </h2>
                <p class="text-muted fs-5">Use filters to find the perfect property for your needs</p>
            </div>

            <form method="GET" action="{{ route('public.landing') }}" class="search-form p-4">
                <div class="row g-3">
                    <div class="col-md-6 col-lg">
                        <label class="form-label fw-medium">
                            <i class="bi bi-search me-1"></i>Search
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Name, code, address..." class="form-control">
                    </div>

                    <div class="col-md-6 col-lg">
                        <label class="form-label fw-medium">
                            <i class="bi bi-tag me-1"></i>Status
                        </label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}"
                                    {{ request('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 col-lg">
                        <label class="form-label fw-medium">
                            <i class="bi bi-currency-dollar me-1"></i>Min Value
                        </label>
                        <input type="number" name="min_value" value="{{ request('min_value') }}" placeholder="0"
                            class="form-control">
                    </div>

                    <div class="col-md-6 col-lg">
                        <label class="form-label fw-medium">
                            <i class="bi bi-currency-dollar me-1"></i>Max Value
                        </label>
                        <input type="number" name="max_value" value="{{ request('max_value') }}"
                            placeholder="999999999" class="form-control">
                    </div>

                    <div class="col-12 col-lg-auto">
                        <label class="form-label d-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-grass px-4">
                                Search
                            </button>
                            <a href="{{ route('public.landing') }}" class="btn btn-outline-secondary">
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Map Section -->
    <section id="map-section" class="py-5 bg-white" style="margin-top: 0; clear: both;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold" style="color: var(--grass-green-dark);">
                    <i class="bi bi-map me-2"></i>Interactive Map
                </h2>
                <p class="text-muted fs-5">Explore land assets on our interactive map</p>
            </div>

            <div class="map-container mb-4">
                <div id="map" class="w-100 h-100"></div>
            </div>

            <div class="d-flex justify-content-center flex-wrap gap-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle me-2"
                        style="width: 16px; height: 16px; background-color: var(--grass-green);"></div>
                    <span class="fw-medium">Available</span>
                </div>
                <div class="d-flex align-items-center">
                    <div class="bg-warning rounded-circle me-2" style="width: 16px; height: 16px;"></div>
                    <span class="fw-medium">Rented</span>
                </div>
                <div class="d-flex align-items-center">
                    <div class="bg-danger rounded-circle me-2" style="width: 16px; height: 16px;"></div>
                    <span class="fw-medium">Sold</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Assets Grid Section -->
    <section id="assets" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold" style="color: var(--grass-green-dark);">
                    All Assets
                </h2>
                <p class="text-muted fs-5">Browse through our premium land assets</p>
            </div>

            @if ($assets->count() > 0)
                <div class="row g-4 mb-5">
                    @foreach ($assets as $asset)
                        <div class="col-lg-4 col-md-6">
                            <div class="asset-card h-100">
                                <div class="position-relative"
                                    style="height: 200px; background: linear-gradient(135deg, var(--grass-green) 0%, var(--grass-green-dark) 100%);">
                                    <div class="position-absolute top-0 start-0 p-3">
                                        <span class="badge status-{{ $asset->status }} fw-medium">
                                            {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                                        </span>
                                    </div>
                                    <div class="position-absolute bottom-0 start-0 p-3 text-white">
                                        <small class="opacity-75">{{ $asset->asset_code }}</small>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <h5 class="card-title fw-bold mb-2" style="color: var(--grass-green-dark);">
                                        {{ $asset->name }}</h5>
                                    <p class="text-muted small mb-3"
                                        style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $asset->address }}</p>

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="p-2 bg-light rounded">
                                                <small class="text-muted d-block">Area</small>
                                                <span class="fw-bold small">{{ number_format($asset->area_sqm) }}
                                                    m²</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 bg-light rounded">
                                                <small class="text-muted d-block">Value</small>
                                                <span class="fw-bold small">Rp
                                                    {{ number_format($asset->value / 1000000, 1) }}M</span>
                                            </div>
                                        </div>
                                    </div>

                                    <a href="{{ route('public.asset.show', $asset) }}" class="btn btn-grass w-100">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div> <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $assets->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="text-muted mb-4">
                        <i class="bi bi-search display-1"></i>
                    </div>
                    <h3 class="fs-4 fw-medium mb-2" style="color: var(--grass-green-dark);">No assets found</h3>
                    <p class="text-muted">Try adjusting your search criteria to find more assets.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Map Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map with error handling
            try {
                console.log('Initializing map...');
                const mapElement = document.getElementById('map');
                if (!mapElement) {
                    console.error('Map element not found!');
                    return;
                }

                const map = L.map('map').setView([-6.2088, 106.8456], 11); // Jakarta coordinates
                console.log('Map initialized successfully');

                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                console.log('Map tiles added');

                // Force map to resize after a small delay
                setTimeout(() => {
                    map.invalidateSize();
                    console.log('Map size invalidated');
                }, 100);

                // Asset data from Laravel
                const assets = @json($allAssets);

                // Status colors
                const statusColors = {
                    'tersedia': '#10b981', // green
                    'disewakan': '#f59e0b', // yellow
                    'terjual': '#ef4444', // red
                    'dalam_sengketa': '#6b7280' // gray
                };

                // Custom blue marker icon
                const blueIcon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="background-color: #3b82f6; width: 25px; height: 25px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
                    iconSize: [25, 25],
                    iconAnchor: [12, 12]
                });

                // Add markers for each asset
                assets.forEach(asset => {
                    if (asset.geometry) {
                        try {
                            const geometry = typeof asset.geometry === 'string' ?
                                JSON.parse(asset.geometry) :
                                asset.geometry;

                            if (geometry.type === 'Polygon' && geometry.coordinates && geometry.coordinates[
                                    0]) {
                                // Convert coordinates for Leaflet (swap lat/lng)
                                const coordinates = geometry.coordinates[0].map(coord => [coord[1], coord[
                                    0]]);

                                // Calculate center point for marker
                                const centerLat = coordinates.reduce((sum, coord) => sum + coord[0], 0) /
                                    coordinates.length;
                                const centerLng = coordinates.reduce((sum, coord) => sum + coord[1], 0) /
                                    coordinates.length;

                                // Create marker with blue icon
                                const marker = L.marker([centerLat, centerLng], {
                                    icon: blueIcon
                                }).addTo(map);

                                // Create polygon
                                const polygon = L.polygon(coordinates, {
                                    color: statusColors[asset.status] || '#6b7280',
                                    fillColor: statusColors[asset.status] || '#6b7280',
                                    fillOpacity: 0.3,
                                    weight: 2
                                }).addTo(map);

                                // Create modern popup content
                                const popupContent = `
                                <div style="font-family: system-ui, -apple-system, sans-serif; min-width: 280px; max-width: 320px;">
                                    <!-- Header -->
                                    <div style="background: linear-gradient(135deg, ${statusColors[asset.status] || '#6b7280'} 0%, ${statusColors[asset.status] || '#6b7280'}cc 100%);
                                                color: white; padding: 16px; margin: -12px -12px 16px -12px; border-radius: 8px 8px 0 0;">
                                        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600;">${asset.name}</h3>
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <span style="font-size: 13px; opacity: 0.9;">${asset.code}</span>
                                            <span style="background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 12px;
                                                         font-size: 11px; font-weight: 600; text-transform: uppercase;">
                                                ${asset.status}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div style="padding: 0 4px;">
                                        <!-- Stats Grid -->
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                                            <div style="background: #f8fafc; padding: 12px; border-radius: 8px; text-align: center;">
                                                <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Area</div>
                                                <div style="font-size: 16px; font-weight: 700; color: #1e293b;">${parseFloat(asset.area_sqm).toLocaleString()}</div>
                                                <div style="font-size: 11px; color: #64748b;">m²</div>
                                            </div>
                                            <div style="background: #f8fafc; padding: 12px; border-radius: 8px; text-align: center;">
                                                <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Value</div>
                                                <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Rp ${(parseFloat(asset.value) / 1000000).toFixed(1)}M</div>
                                                <div style="font-size: 11px; color: #64748b;">IDR</div>
                                            </div>
                                        </div>

                                        <!-- Address -->
                                        <div style="margin-bottom: 20px;">
                                            <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">
                                                Address
                                            </div>
                                            <div style="font-size: 13px; color: #475569; line-height: 1.4;">
                                                ${asset.address.length > 80 ? asset.address.substring(0, 80) + '...' : asset.address}
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div style="border-top: 1px solid #e2e8f0; padding: 16px 0 4px 0;">
                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                                <a href="${asset.url}"
                                                   style="display: flex; align-items: center; justify-content: center;
                                                          background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                                                          color: white; padding: 10px 12px; border-radius: 8px; text-decoration: none;
                                                          font-size: 12px; font-weight: 600; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                                   onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)'"
                                                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'">
                                                    <svg style="width: 14px; height: 14px; margin-right: 4px;" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    View Details
                                                </a>
                                                <button onclick="openStreetView(${centerLat}, ${centerLng}, '${asset.name}')"
                                                        style="background: linear-gradient(135deg, #059669 0%, #047857 100%);
                                                               color: white; border: none; padding: 10px 12px; border-radius: 8px;
                                                               cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                                               font-size: 12px; font-weight: 600; display: flex; align-items: center; justify-content: center;"
                                                        onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)'"
                                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'"
                                                        title="View Street View">
                                                    <svg style="width: 14px; height: 14px; margin-right: 4px;" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm12 12V8l-4 4-4-4v8h8z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Street View
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                                // Bind popup to both marker and polygon
                                const popupOptions = {
                                    maxWidth: 320,
                                    className: 'custom-popup'
                                };

                                marker.bindPopup(popupContent, popupOptions);
                                polygon.bindPopup(popupContent, popupOptions);
                            }
                        } catch (error) {
                            console.error('Error parsing geometry for asset:', asset.id, error);
                        }
                    }
                });

                // Fit map to show all assets if available
                if (assets.length > 0) {
                    const group = new L.featureGroup();
                    map.eachLayer(layer => {
                        if (layer instanceof L.Polygon || layer instanceof L.Marker) {
                            group.addLayer(layer);
                        }
                    });

                    if (group.getLayers().length > 0) {
                        map.fitBounds(group.getBounds(), {
                            padding: [20, 20]
                        });
                    }
                }

                // Smooth scrolling for navigation links
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function(e) {
                        e.preventDefault();
                        const target = document.querySelector(this.getAttribute('href'));
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    });
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

                    // Try to load Street View
                    setTimeout(() => {
                        try {
                            // Use Google Maps Embed API for Street View
                            iframe.src =
                                `https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1000!2d${lng}!3d${lat}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zM${lat}%C2%B0${lat < 0 ? 'S' : 'N'}%20${lng}%C2%B0${lng < 0 ? 'W' : 'E'}!5e1!3m2!1sen!2sid!4v1234567890123!5m2!1sen!2sid`;

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

                closeBtn.onclick = function() {
                    modal.style.display = 'none';
                    document.getElementById('street-view-iframe').src = '';
                };

                modal.onclick = function(event) {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                        document.getElementById('street-view-iframe').src = '';
                    }
                };

            } catch (error) {
                console.error('Error initializing map:', error);
                // Display error message to user
                const mapElement = document.getElementById('map');
                if (mapElement) {
                    mapElement.innerHTML =
                        '<div style="display: flex; align-items: center; justify-content: center; height: 500px; background: #f8f9fa; border-radius: 15px; color: #6c757d;"><div class="text-center"><h5>Map Loading Error</h5><p>Unable to load the interactive map. Please refresh the page.</p></div></div>';
                }
            }
        });
    </script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
