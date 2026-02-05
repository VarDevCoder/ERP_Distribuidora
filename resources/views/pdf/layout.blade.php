<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page {
            margin: 25mm 20mm 20mm 20mm;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.5;
        }

        /* Header */
        .header {
            border-bottom: 3px solid #1e40af;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header table { width: 100%; }
        .company-name { font-size: 18px; font-weight: bold; color: #1e3a5f; }
        .header-subtitle { font-size: 9px; color: #6b7280; margin-top: 2px; }

        .doc-number { font-size: 14px; font-weight: bold; color: #1e40af; text-align: right; }
        .doc-date { font-size: 10px; color: #6b7280; text-align: right; margin-top: 2px; }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-blue { background-color: #dbeafe; color: #1e40af; }
        .badge-green { background-color: #d1fae5; color: #065f46; }
        .badge-yellow { background-color: #fef3c7; color: #92400e; }
        .badge-red { background-color: #fee2e2; color: #991b1b; }

        /* Sections */
        .section { margin-bottom: 16px; }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #374151;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 4px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Info grid */
        .info-grid { width: 100%; border-collapse: collapse; }
        .info-grid td { padding: 3px 8px 3px 0; vertical-align: top; }
        .info-label { font-size: 9px; color: #6b7280; font-weight: bold; text-transform: uppercase; }
        .info-value { font-size: 11px; color: #111827; }

        /* Items table */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .items-table thead th {
            background-color: #1e40af;
            color: white;
            padding: 7px 10px;
            font-size: 9px;
            text-transform: uppercase;
            text-align: left;
            letter-spacing: 0.3px;
        }
        .items-table thead th.right { text-align: right; }
        .items-table tbody td {
            padding: 6px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        .items-table tbody td.right { text-align: right; }
        .items-table tbody tr:nth-child(even) { background-color: #f9fafb; }

        /* Totals */
        .totals { margin-top: 12px; float: right; width: 240px; }
        .totals table { width: 100%; border-collapse: collapse; }
        .totals td { padding: 4px 0; font-size: 11px; }
        .totals .label { text-align: left; color: #6b7280; }
        .totals .value { text-align: right; font-weight: bold; }
        .totals .total-row { border-top: 2px solid #1e40af; }
        .totals .total-row td { padding-top: 8px; font-size: 14px; color: #1e40af; font-weight: bold; }

        /* Notes */
        .notes {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 10px;
            margin-top: 15px;
            font-size: 10px;
        }

        .clearfix::after { content: ""; display: table; clear: both; }

        /* Footer */
        .footer {
            position: fixed;
            bottom: -10mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td width="55%" style="vertical-align: top;">
                    <div class="company-name">Ankhor Distribuidora</div>
                    <div class="header-subtitle">Sistema de Gestión Empresarial</div>
                </td>
                <td width="45%" style="vertical-align: top;">
                    <div class="doc-number">@yield('doc-number')</div>
                    <div class="doc-date">@yield('doc-date')</div>
                    <div style="text-align: right; margin-top: 4px;">@yield('doc-badge')</div>
                </td>
            </tr>
        </table>
    </div>

    @yield('content')

    <div class="footer">
        Ankhor Distribuidora &mdash; Documento generado el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
