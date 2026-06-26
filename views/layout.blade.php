<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('failed-jobs-manager::failed-jobs-manager.pages.index_title'))</title>
    <style>
        :root {
            color-scheme: light;
            --fjm-bg: #f8fafc;
            --fjm-panel: #ffffff;
            --fjm-border: #e2e8f0;
            --fjm-border-strong: #cbd5e1;
            --fjm-text: #0f172a;
            --fjm-muted: #64748b;
            --fjm-soft: #f9fafb;
            --fjm-primary: #d97706;
            --fjm-primary-soft: #fffbeb;
            --fjm-blue: #2563eb;
            --fjm-green: #16a34a;
            --fjm-red: #dc2626;
            --fjm-orange: #d97706;
            --fjm-shadow: 0 1px 2px rgba(15, 23, 42, .06);
            --fjm-ring: 0 0 0 1px rgba(15, 23, 42, .05), 0 8px 24px rgba(15, 23, 42, .06);
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
            background: #ffffff;
            color: var(--fjm-text);
            border-bottom: 1px solid var(--fjm-border);
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
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--fjm-primary-soft);
            color: #92400e;
            font-weight: 800;
            border: 1px solid #fde68a;
        }

        .fjm-brand-title {
            font-size: 15px;
            font-weight: 700;
        }

        .fjm-brand-subtitle {
            color: var(--fjm-muted);
            font-size: 12px;
            margin-top: 1px;
        }

        .fjm-topbar-meta {
            color: var(--fjm-muted);
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
            font-size: 24px;
            font-weight: 650;
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
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: var(--fjm-ring);
        }

        .fjm-message {
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 16px;
            background: #eef6ff;
            color: #1e3a8a;
            white-space: pre-wrap;
        }

        .fjm-message-success {
            background: #ecfdf3;
            border-color: #abefc6;
            color: #065f46;
        }

        .fjm-message-error {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .fjm-message-warning {
            background: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
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
            border-radius: 12px;
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
            border-radius: 8px;
            padding: 7px 9px;
            background: #ffffff;
            color: var(--fjm-text);
            font: inherit;
        }

        .fjm-control:focus {
            border-color: var(--fjm-primary);
            outline: 3px solid rgba(217, 119, 6, .14);
        }

        textarea.fjm-control {
            min-width: 260px;
            min-height: 88px;
            resize: vertical;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
            font-size: 12px;
            background: #fbfdff;
        }

        .fjm-preview-title {
            color: var(--fjm-text);
            font-weight: 700;
        }

        .fjm-preview-muted {
            margin-top: 4px;
            color: var(--fjm-muted);
            font-size: 12px;
        }

        .fjm-preview-error {
            max-width: 420px;
            color: #991b1b;
            font-weight: 600;
        }

        .fjm-table-wrap {
            width: 100%;
            overflow-x: auto;
            background: var(--fjm-panel);
            border: 1px solid var(--fjm-border);
            border-radius: 12px;
            margin-bottom: 16px;
            box-shadow: var(--fjm-ring);
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
            background: #f8fafc;
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
            border: 1px solid var(--fjm-border-strong);
            border-radius: 8px;
            padding: 7px 10px;
            color: var(--fjm-text);
            background: #ffffff;
            font: inherit;
            font-weight: 600;
            line-height: 1;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
            box-shadow: var(--fjm-shadow);
            transition: background-color .12s ease, border-color .12s ease, color .12s ease;
        }

        .fjm-button:hover {
            background: #f8fafc;
        }

        .fjm-button-blue {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        .fjm-button-green {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #15803d;
        }

        .fjm-button-orange {
            background: #fffbeb;
            border-color: #fde68a;
            color: #b45309;
        }

        .fjm-button-red {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
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
            background: #f8fafc;
            color: #334155;
            border: 1px solid var(--fjm-border);
            border-radius: 10px;
            padding: 14px;
            font-size: 12px;
            line-height: 1.55;
        }

        .fjm-details {
            background: var(--fjm-panel);
            border: 1px solid var(--fjm-border);
            border-radius: 12px;
            margin-bottom: 16px;
            box-shadow: var(--fjm-shadow);
        }

        .fjm-details summary {
            cursor: pointer;
            padding: 14px 16px;
            color: var(--fjm-text);
            font-weight: 800;
        }

        .fjm-details-body {
            border-top: 1px solid var(--fjm-border);
            padding: 16px;
        }

        .fjm-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(220px, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }

        .fjm-detail-card {
            background: var(--fjm-panel);
            border: 1px solid var(--fjm-border);
            border-radius: 12px;
            padding: 16px;
            box-shadow: var(--fjm-ring);
        }

        .fjm-detail-card-danger {
            border-color: #fecaca;
            background: #fffafa;
        }

        .fjm-kv {
            display: grid;
            grid-template-columns: 140px minmax(0, 1fr);
            gap: 8px 14px;
            margin: 0;
        }

        .fjm-kv dt {
            color: var(--fjm-muted);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .fjm-kv dd {
            min-width: 0;
            margin: 0;
            overflow-wrap: anywhere;
        }

        .fjm-exception-message {
            color: #991b1b;
            font-size: 16px;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .fjm-section-title {
            margin: 0 0 12px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--fjm-muted);
        }

        .fjm-badge {
            display: inline-flex;
            align-items: center;
            min-height: 24px;
            border-radius: 999px;
            padding: 3px 9px;
            background: #f1f5f9;
            color: #475569;
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

            .fjm-detail-grid {
                grid-template-columns: 1fr;
            }

            .fjm-kv {
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
                <div class="fjm-brand-title">{{ __('failed-jobs-manager::failed-jobs-manager.brand') }}</div>
                <div class="fjm-brand-subtitle">{{ __('failed-jobs-manager::failed-jobs-manager.brand_subtitle') }}</div>
            </div>
        </div>
        <div class="fjm-topbar-meta">{{ __('failed-jobs-manager::failed-jobs-manager.topbar_meta') }}</div>
    </div>
</div>
<main class="fjm-page">
    <header class="fjm-header">
        <div>
            <h1 class="fjm-title">@yield('title', __('failed-jobs-manager::failed-jobs-manager.pages.index_title'))</h1>
            <div class="fjm-subtitle">@yield('subtitle', __('failed-jobs-manager::failed-jobs-manager.pages.default_subtitle'))</div>
        </div>
    </header>

    @yield('content')
</main>
</body>
</html>
