<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>پیش‌فاکتور {{ $quotation->reference_code }}</title>
    <style>
        @page {
            margin: 10mm 12mm 15mm 12mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            direction: rtl;
            color: #1f2937;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        /* سربرگ شرکتی */
        table.header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #9e1b32;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        table.header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .logo-img {
            max-height: 58px;
            max-width: 115px;
            width: auto;
        }
        .company-title {
            color: #9e1b32;
            font-size: 19px;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }
        .company-subtitle {
            color: #4b5563;
            font-size: 9px;
            margin-top: 3px;
            text-align: center;
        }
        .meta-box {
            text-align: right;
            font-size: 9.5px;
            line-height: 1.7;
            color: #374151;
        }

        /* کادر خریدار */
        .info-card {
            background-color: #fdf6f7;
            border: 1px solid #fad2d8;
            border-right: 4px solid #9e1b32;
            padding: 6px 10px;
            margin-bottom: 10px;
            font-size: 10.5px;
        }

        /* جدول اقلام */
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        table.items-table th {
            background-color: #9e1b32;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #9e1b32;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
        }
        table.items-table td {
            border: 1px solid #e5e7eb;
            padding: 6px 6px;
            text-align: right;
            vertical-align: middle;
            font-size: 10px;
        }
        table.items-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        /* چیدمان بخش پایینی */
        .bottom-section {
            width: 100%;
            margin-top: 14px;
        }
        .right-box {
            float: right;
            width: 48%;
            text-align: right;
            font-size: 9.5px;
            color: #374151;
            line-height: 1.7;
        }
        .left-box {
            float: left;
            width: 48%;
        }

        /* جدول خلاصه مبالغ */
        table.summary-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e5e7eb;
            background-color: #ffffff;
        }
        table.summary-table td {
            border: 1px solid #f3f4f6;
            padding: 5px 8px;
            font-size: 10px;
        }
        table.summary-table td.label-col {
            text-align: right;
            color: #4b5563;
            background-color: #fcfcfc;
        }
        table.summary-table td.val-col {
            text-align: left;
            direction: ltr;
            font-weight: 500;
        }
        table.summary-table tr.total-row td {
            border-top: 2px solid #9e1b32;
            border-bottom: 2px solid #9e1b32;
            background-color: #fdf2f4;
            font-weight: bold;
            color: #9e1b32;
            padding: 7px 8px;
            font-size: 11px;
        }

        /* کادرهای امضا */
        table.signatures-table {
            width: 100%;
            margin-top: 35px;
            border-collapse: collapse;
        }
        table.signatures-table td {
            border: none;
            width: 50%;
            text-align: center;
            font-size: 10px;
            color: #374151;
            vertical-align: top;
        }

        /* فوتر */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding-top: 4px;
            text-align: center;
            font-size: 8.5px;
            color: #4b5563;
            line-height: 1.4;
        }
        .footer-line {
            height: 2px;
            background-color: #9e1b32;
            width: 100%;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    @php
        $baseCur = $quotation->base_currency instanceof \BackedEnum ? $quotation->base_currency->value : (string)$quotation->base_currency;
        $shipCur = $quotation->shipping_currency instanceof \BackedEnum ? $quotation->shipping_currency->value : (string)$quotation->shipping_currency;

        $shippingIrr = $quotation->shipping_cost_irr ?? 0;
        if ($shippingIrr <= 0) {
            $shippingIrr = max(0, ((float)$quotation->final_total_irr - (float)$quotation->total_profit_irr - (float)$quotation->customs_duty_irr - (float)$quotation->inland_shipping_irr - (float)$quotation->unforeseen_cost_irr) - (float)$quotation->subtotal_irr);
        }

        $logoFilePath = null;
        $mediaDir = public_path('storage/site-media');
        if (is_dir($mediaDir)) {
            $files = glob($mediaDir . '/*logo*.*');
            if (empty($files)) {
                $files = glob($mediaDir . '/*.{png,jpg,jpeg,webp}', GLOB_BRACE);
            }
            if (!empty($files) && file_exists($files[0])) {
                $logoFilePath = $files[0];
            }
        }
        if (!$logoFilePath && file_exists(public_path('images/logo.png'))) {
            $logoFilePath = public_path('images/logo.png');
        }
    @endphp

    <!-- سربرگ -->
    <table class="header-table">
        <tr>
            <td style="width: 30%;" class="meta-box">
                <div>شماره پیش‌فاکتور: <strong>{{ $quotation->reference_code }}</strong></div>
                <div>تاریخ صدور: <strong>{{ $quotation->created_at ? $quotation->created_at->format('Y/m/d') : '—' }}</strong></div>
                <div>مهلت اعتبار: <strong>{{ $quotation->valid_until ? $quotation->valid_until->format('Y/m/d') : '—' }}</strong></div>
            </td>
            <td style="width: 45%; text-align: center;">
                <div class="company-title">پیش‌فاکتور رسمی تجاری</div>
                <div class="company-subtitle">خدمات بازرگانی بین‌المللی و زنجیره تأمین نفیس</div>
            </td>
            <td style="width: 25%; text-align: left;">
                @if($logoFilePath)
                    <img src="{{ $logoFilePath }}" class="logo-img" alt="NFS Logo">
                @else
                    <div style="font-size: 22px; font-weight: bold; color: #9e1b32;">NFS</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- مشخصات خریدار -->
    <div class="info-card">
        <strong>خریدار محترم:</strong> {{ $quotation->customer?->name ?? '—' }}
        @if($quotation->customer?->mobile)
            &nbsp; | &nbsp; <strong>شماره تماس:</strong> {{ $quotation->customer->mobile }}
        @endif
    </div>

    <!-- جدول اقلام (عرض بهینه ۵۴٪ برای شرح کالا تا هیچ متنی دوخطی نشود) -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 24px;">#</th>
                <th style="width: 54%;">شرح کالا</th>
                <th style="width: 5%;">تعداد</th>
                <th style="width: 12%;">واحد ({{ $baseCur }})</th>
                <th style="width: 12%;">کل ({{ $baseCur }})</th>
                <th style="width: 17%;">جمع فروش (ریال)</th>
            </tr>
        </thead>
        <tbody>
        @forelse($quotation->items as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->item_title }}</strong>
                    @if($item->technical_description)
                        <div style="color: #6b7280; font-size: 8.5px; margin-top: 2px;">{{ $item->technical_description }}</div>
                    @endif
                </td>
                <td style="text-align: center;">{{ number_format((float)$item->quantity) }}</td>
                <td style="text-align: left; direction: ltr;">{{ number_format((float)$item->unit_cost_currency, 4) }}</td>
                <td style="text-align: left; direction: ltr;">{{ number_format((float)$item->total_cost_currency, 4) }}</td>
                <td style="text-align: left; direction: ltr; font-weight: bold; color: #111827;">{{ number_format((float)$item->total_price_irr) }} ریال</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 12px;">موردی ثبت نشده است.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <!-- بخش پایانی -->
    <div class="bottom-section">
        <div class="right-box">
            @if($quotation->customer_notes)
                <div><strong>توضیحات:</strong> {{ $quotation->customer_notes }}</div>
            @endif
            @if($quotation->payment_terms)
                <div style="margin-top: 6px;"><strong>شرایط پرداخت:</strong> {{ $quotation->payment_terms }}</div>
            @endif
        </div>

        <div class="left-box">
            <table class="summary-table">
                <tr>
                    <td class="label-col">جمع ارزش کالا:</td>
                    <td class="val-col">{{ number_format((float)$quotation->subtotal_base_currency, 4) }} <span style="color:#6b7280;">{{ $baseCur }}</span></td>
                </tr>
                <tr>
                    <td class="label-col">حمل بین‌الملل:</td>
                    <td class="val-col">{{ number_format((float)$quotation->shipping_cost_base_currency, 4) }} <span style="color:#6b7280;">{{ $shipCur }}</span></td>
                </tr>
                @if(!empty($quotation->total_foreign_currency) && (float)$quotation->total_foreign_currency > 0)
                <tr>
                    <td class="label-col">مجموع ارزی:</td>
                    <td class="val-col">{{ number_format((float)$quotation->total_foreign_currency, 4) }} <span style="color:#6b7280;">{{ $baseCur }}</span></td>
                </tr>
                @endif
                <tr>
                    <td class="label-col">معادل ریالی ارزش کالا:</td>
                    <td class="val-col">{{ number_format((float)$quotation->subtotal_irr) }} ریال</td>
                </tr>
                <tr>
                    <td class="label-col">معادل ریالی حمل بین‌الملل:</td>
                    <td class="val-col">{{ number_format((float)$shippingIrr) }} ریال</td>
                </tr>
                <tr>
                    <td class="label-col">ترخیص و حقوق گمرکی:</td>
                    <td class="val-col">{{ number_format((float)$quotation->customs_duty_irr) }} ریال</td>
                </tr>
                <tr>
                    <td class="label-col">حمل داخلی در ایران:</td>
                    <td class="val-col">{{ number_format((float)$quotation->inland_shipping_irr) }} ریال</td>
                </tr>
                @if((float)$quotation->unforeseen_cost_irr > 0)
                <tr>
                    <td class="label-col">هزینه‌های پیش‌بینی‌نشده:</td>
                    <td class="val-col">{{ number_format((float)$quotation->unforeseen_cost_irr) }} ریال</td>
                </tr>
                @endif
                <tr>
                    <td class="label-col">کارمزد و خدمات بازرگانی:</td>
                    <td class="val-col">{{ number_format((float)$quotation->total_profit_irr) }} ریال</td>
                </tr>
                <tr class="total-row">
                    <td class="label-col" style="color: #9e1b32; font-weight: bold;">مبلغ نهایی قابل پرداخت:</td>
                    <td class="val-col" style="color: #9e1b32; font-weight: bold;">{{ number_format((float)$quotation->final_total_irr) }} ریال</td>
                </tr>
            </table>
        </div>
        <div style="clear: both;"></div>
    </div>

    <!-- بخش امضاها: در RTL سلول اول در سمت راست (شرکت نفیس) و سلول دوم در سمت چپ (خریدار) می‌نشیند -->
    <table class="signatures-table">
        <tr>
            <td style="padding-top: 10px;">
                <strong>مهر و امضای شرکت بازرگانی نفیس (NFS)</strong>
                <table style="width: 60%; margin: 38px auto 0; border-collapse: collapse;">
                    <tr>
                        <td style="border: none; border-top: 1px dashed #9ca3af; padding-top: 4px; font-size: 9px; color: #6b7280; text-align: center;">
                            واحد مالی و بازرگانی خارجی
                        </td>
                    </tr>
                </table>
            </td>
            <td style="padding-top: 10px;">
                <strong>مهر و امضای خریدار</strong>
                <table style="width: 60%; margin: 38px auto 0; border-collapse: collapse;">
                    <tr>
                        <td style="border: none; border-top: 1px dashed #9ca3af; padding-top: 4px; font-size: 9px; color: #6b7280; text-align: center;">
                            امضا و تاریخ تأیید پیش‌فاکتور
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- فوتر -->
    <div class="footer">
        <div class="footer-line"></div>
        <div>دفتر مرکزی: تهران، خیابان مطهری، خیابان فجر، پلاک ۲۴ | تلفن تماس: ۰۲۱-۸۸۸۸۸۸۸۸ | وب‌سایت: www.nafis-trade.com</div>
        <div style="color: #9ca3af; margin-top: 1px;">پیش‌فاکتور رسمی بازرگانی نفیس — ثبت‌شده طبق قوانین تجارت الکترونیک</div>
    </div>
</body>
</html>