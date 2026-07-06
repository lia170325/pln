<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard PLN</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* =========================================
           RESET & BASE STYLES
        ========================================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
        }

        /* =========================================
           LAYOUT UTAMA (SIDEBAR & CONTENT)
        ========================================= */
        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background-color: #005a8c;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.05);
        }

        .sidebar .logo {
            padding: 24px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .logo h2 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .nav-menu {
            list-style: none;
            margin-top: 20px;
        }

        .nav-menu li {
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: 500;
        }

        .nav-menu li:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-menu li.active {
            background-color: #0087d1;
            border-left: 4px solid #facc15;
        }

        /* Main Content */
        .content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .topbar h2 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 24px;
            color: #111827;
        }

        /* =========================================
           KOTAK DROPDOWN (KHS JASA, PEMBERSIHAN, DLL)
        ========================================= */
        .folder-list {
            display: flex;
            flex-direction: column;
            gap: 20px; /* Jarak pemisah antar kotak */
        }

        details.menu-folder {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        details.menu-folder[open] {
            border-color: #005a8c;
            box-shadow: 0 6px 15px rgba(0, 90, 140, 0.1);
        }

        summary.folder-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 24px;
            cursor: pointer;
            background-color: #ffffff;
            border-left: 5px solid #005a8c;
            list-style: none;
        }

        summary.folder-summary::-webkit-details-marker {
            display: none;
        }

        summary.folder-summary:hover {
            background-color: #f9fafb;
        }

        .folder-title {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }

        .expand-icon {
            color: #9ca3af;
            font-size: 28px;
            transition: transform 0.3s ease;
        }

        details.menu-folder[open] summary .expand-icon {
            transform: rotate(180deg);
            color: #005a8c;
        }

        .folder-content {
            padding: 16px 24px 24px 64px;
            background-color: #f9fafb;
            border-top: 1px solid #f3f4f6;
        }

        .folder-content ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .folder-content li a {
            text-decoration: none;
            color: #4b5563;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 8px;
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }

        .folder-content li a:hover {
            background-color: #f3f4f6;
            color: #005a8c;
            border-color: #d1d5db;
        }

        /* =========================================
           TOMBOL REKAP DATA KESELURUHAN
        ========================================= */
        .rekap-card {
            background-color: #10b981;
            border-radius: 12px;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            text-decoration: none;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .rekap-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px -3px rgba(16, 185, 129, 0.3);
        }

        .rekap-title {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 18px;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <aside class="sidebar">
        <div class="logo">
            <h2>PLN</h2>
        </div>
        <ul class="nav-menu">
            <li class="active">
                <span class="material-icons">dashboard</span>
                <span>Dashboard</span>
            </li>
            <li onclick="window.location.href='{{ url('/login') }}'">
                <span class="material-icons">logout</span>
                <span>Logout</span>
            </li>
        </ul>
    </aside>

    <main class="content">
        <div class="topbar">
            <h2>Daftar Sheet / Menu Monitoring</h2>
        </div>

        <div class="folder-list">
            <details class="menu-folder" open>
                <summary class="folder-summary">
                    <div class="folder-title">
                        <span class="material-icons" style="color: #fbbf24; font-size: 32px;">folder_special</span>
                        KHS JASA
                    </div>
                    <span class="material-icons expand-icon">expand_more</span>
                </summary>
                <div class="folder-content">
                    <ul>
                        <li>
                            <a href="{{ url('/khs-jasa-2024') }}">
                                <span class="material-icons" style="color: #9ca3af;">insert_drive_file</span> Tahun 2024
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/khs-jasa-2025') }}">
                                <span class="material-icons" style="color: #9ca3af;">insert_drive_file</span> Tahun 2025
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/khs-jasa-2026') }}">
                                <span class="material-icons" style="color: #9ca3af;">insert_drive_file</span> Tahun 2026
                            </a>
                        </li>
                    </ul>
                </div>
            </details>

            <details class="menu-folder">
                <summary class="folder-summary">
                    <div class="folder-title">
                        <span class="material-icons" style="color: #fbbf24; font-size: 32px;">folder_special</span>
                        KHS PEMBERSIHAN
                    </div>
                    <span class="material-icons expand-icon">expand_more</span>
                </summary>
                <div class="folder-content">
                    <ul>
                        <li>
                            <a href="{{ url('/khs-pembesian-2024') }}">
                                <span class="material-icons" style="color: #9ca3af;">insert_drive_file</span> Tahun 2024
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/khs-pembesian-2025') }}">
                                <span class="material-icons" style="color: #9ca3af;">insert_drive_file</span> Tahun 2025
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/khs-pembesian-2026') }}">
                                <span class="material-icons" style="color: #9ca3af;">insert_drive_file</span> Tahun 2026
                            </a>
                        </li>
                    </ul>
                </div>
            </details>

            <details class="menu-folder">
                <summary class="folder-summary">
                    <div class="folder-title">
                        <span class="material-icons" style="color: #fbbf24; font-size: 32px;">folder_special</span>
                        REGRESASI
                    </div>
                    <span class="material-icons expand-icon">expand_more</span>
                </summary>
                <div class="folder-content">
                    <ul>
                        <li>
                            <a href="{{ url('/regresasi-2025') }}">
                                <span class="material-icons" style="color: #9ca3af;">insert_drive_file</span> Tahun 2025
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/regresasi-2026') }}">
                                <span class="material-icons" style="color: #9ca3af;">insert_drive_file</span> Tahun 2026
                            </a>
                        </li>
                    </ul>
                </div>
            </details>

            <a href="{{ url('/rekap') }}" class="rekap-card">
                <div class="rekap-title">
                    <span class="material-icons" style="font-size: 32px;">analytics</span>
                    REKAP DATA KESELURUHAN
                </div>
                <span class="material-icons" style="font-size: 28px;">arrow_forward</span>
            </a>
        </div>
    </main>
</div>

</body>
</html>