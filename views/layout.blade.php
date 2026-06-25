<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Failed jobs')</title>
    <style>
        :root {
            color-scheme: light;
            --fjm-bg: #f3f4f6;
            --fjm-panel: #ffffff;
            --fjm-header: #111827;
            --fjm-border: #e5e7eb;
            --fjm-border-strong: #d1d5db;
            --fjm-text: #111827;
            --fjm-muted: #6b7280;
            --fjm-soft: #f9fafb;
            --fjm-blue: #2563eb;
            --fjm-green: #059669;
            --fjm-red: #dc2626;
            --fjm-orange: #ea580c;
            --fjm-shadow: 0 10px 30px rgba(15, 23, 42, .08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--fjm-bg);
            color: var(--fjm-text);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 14px;
            line-height: 1.45;
        }

        a {
            color: var(--fjm-blue);
        }

        .fjm-topbar {
            background: var(--fjm-header);
            color: #ffffff;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }

        .fjm-topbar-inner {
            max-width: 1440px;
            margin: 0 auto;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .fjm-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .fjm-brand-mark {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ef4444, #f97316);
            color: #ffffff;
            font-weight: 800;
            box-shadow: 0 8px 18px rgba(239, 68, 68, .25);
        }

        .fjm-brand-title {
            font-size: 15px;
            font-weight: 700;
        }

        .fjm-brand-subtitle {
            color: #9ca3af;
            font-size: 12px;
            margin-top: 1px;
        }

        .fjm-topbar-meta {
            color: #d1d5db;
            font-size: 12px;
        }

        .fjm-page {
            max-width: 1440px;
            margin: 0 auto;
            padding: 26px 24px 34px;
        }

        .fjm-header {
            margin-bottom: 18px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
        }

        .fjm-title {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 0;
        }

        .fjm-subtitle {
            margin-top: 4px;
            color: var(--fjm-muted);
            font-size: 13px;
        }

        .fjm-panel {
            background: var(--fjm-panel);
            border: 1px solid var(--fjm-border);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: var(--fjm-shadow);
        }

        .fjm-message {
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 16px;
            background: #eef6ff;
            color: #1e3a8a;
        }

        .fjm-message-success {
            background: #ecfdf3;
            border-color: #abefc6;
            color: #065f46;
        }

        .fjm-metrics {
            display: grid;
            grid-template-columns: repeat(2, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }

        .fjm-metric {
            background: var(--fjm-panel);
            border: 1px solid var(--fjm-border);
            border-radius: 8px;
            padding: 16px;
            box-shadow: var(--fjm-shadow);
        }

        .fjm-metric-label {
            color: var(--fjm-muted);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .fjm-metric-value {
            margin-top: 8px;
            color: var(--fjm-text);
            font-size: 28px;
            font-weight: 800;
        }

        .fjm-metric-help {
            margin-top: 4px;
            color: var(--fjm-muted);
            font-size: 12px;
        }

        .fjm-form-grid {
            display: grid;
            grid-template-columns: minmax(160px, 1fr) minmax(240px, 2fr) minmax(150px, 1fr) minmax(150px, 1fr) auto auto;
            gap: 12px;
            align-items: end;
        }

        .fjm-field label {
            display: block;
            margin-bottom: 5px;
            color: var(--fjm-muted);
            font-size: 12px;
            font-weight: 600;
        }

        .fjm-control {
            width: 100%;
            min-height: 36px;
            border: 1px solid var(--fjm-border-strong);
            border-radius: 6px;
            padding: 7px 9px;
            background: #ffffff;
            color: var(--fjm-text);
            font: inherit;
        }

        .fjm-control:focus {
            border-color: var(--fjm-blue);
            outline: 3px solid rgba(37, 99, 235, .12);
        }

        textarea.fjm-control {
            min-width: 260px;
            min-height: 88px;
            resize: vertical;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
            font-size: 12px;
            background: #fbfdff;
        }

        .fjm-table-wrap {
            width: 100%;
            overflow-x: auto;
            background: var(--fjm-panel);
            border: 1px solid var(--fjm-border);
            border-radius: 8px;
            margin-bottom: 16px;
            box-shadow: var(--fjm-shadow);
        }

        .fjm-table {
            width: 100%;
            border-collapse: collapse;
        }

        .fjm-table th,
        .fjm-table td {
            border-bottom: 1px solid var(--fjm-border);
            padding: 12px;
            text-align: left;
            vertical-align: top;
        }

        .fjm-table tr:last-child td {
            border-bottom: 0;
        }

        .fjm-table tbody tr:hover {
            background: var(--fjm-soft);
        }

        .fjm-table th {
            color: var(--fjm-muted);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            white-space: nowrap;
            background: #fbfdff;
        }

        .fjm-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            min-width: 230px;
        }

        .fjm-inline-form {
            display: inline-block;
        }

        .fjm-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            border: 1px solid transparent;
            border-radius: 6px;
            padding: 7px 10px;
            color: #ffffff;
            font: inherit;
            font-weight: 600;
            line-height: 1;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
            transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
        }

        .fjm-button:hover {
            opacity: .92;
            transform: translateY(-1px);
        }

        .fjm-button-blue {
            background: var(--fjm-blue);
        }

        .fjm-button-green {
            background: var(--fjm-green);
        }

        .fjm-button-orange {
            background: var(--fjm-orange);
        }

        .fjm-button-red {
            background: var(--fjm-red);
        }

        .fjm-pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 16px;
            color: var(--fjm-muted);
        }

        .fjm-pagination-actions {
            display: flex;
            gap: 8px;
        }

        .fjm-pre {
            overflow-x: auto;
            white-space: pre-wrap;
            background: #0f172a;
            color: #e5e7eb;
            border-radius: 8px;
            padding: 14px;
            font-size: 12px;
            line-height: 1.55;
        }

        .fjm-section-title {
            margin: 22px 0 8px;
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--fjm-muted);
        }

        .fjm-badge {
            display: inline-flex;
            align-items: center;
            min-height: 24px;
            border-radius: 999px;
            padding: 3px 9px;
            background: #fee2e2;
            color: #991b1b;
            font-size: 12px;
            font-weight: 700;
        }

        .fjm-id {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
            font-weight: 700;
        }

        @media (max-width: 900px) {
            .fjm-topbar-inner {
                padding: 16px 14px;
                align-items: flex-start;
                flex-direction: column;
            }

            .fjm-page {
                padding: 14px;
            }

            .fjm-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .fjm-metrics {
                grid-template-columns: 1fr;
            }

            .fjm-form-grid {
                grid-template-columns: 1fr;
            }

            .fjm-pagination {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<div class="fjm-topbar">
    <div class="fjm-topbar-inner">
        <div class="fjm-brand">
            <span class="fjm-brand-mark">FJ</span>
            <div>
                <div class="fjm-brand-title">Failed Jobs Manager</div>
                <div class="fjm-brand-subtitle">Laravel queue failure control</div>
            </div>
        </div>
        <div class="fjm-topbar-meta">Standard failed_jobs table</div>
    </div>
</div>
<main class="fjm-page">
    <header class="fjm-header">
        <div>
            <h1 class="fjm-title">@yield('title', 'Failed jobs')</h1>
            <div class="fjm-subtitle">@yield('subtitle', 'Review, retry, and clean failed queue jobs')</div>
        </div>
    </header>

    @yield('content')
</main>
</body>
</html>
