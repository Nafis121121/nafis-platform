<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>پیش‌فاکتور {{ $quotation->reference_code }}</title>
    <style>
        body {
            direction: rtl;
            text-align: right;
            color: #1f2937;
            font-size: 10px;
            line-height: 1.4;
            background: #ffffff;
            margin: 0;
            padding: 0;
            font-family: 'vazirmatn', sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table {
            margin-bottom: 10px;
            border-bottom: 2px solid #9e1b32;
            padding-bottom: 6px;
        }

        .brand-title {
            color: #9e1b32;
            font-size: 18px;
            font-weight: bold;
        }

        .brand-tagline {
            color: #c9a84c;
            font-size: 10px;
            font-weight: bold;
            margin: 2px 0;
        }

        .brand-desc {
            color: #6b7280;
            font-size: 8px;
        }

        .doc-title-box {
            background-color: #fcf6ea;
            border: 1px solid #c9a84c;
            padding: 6px 10px;
            text-align: center;
        }

        .doc-main-title {
            color: #9e1b32;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .doc-meta {
            font-size: 9px;
            color: #374151;
            line-height: 1.5;
        }

        .party-card {
            border: 1px solid #e5e7eb;
            background-color: #fafafa;
            padding: 6px 8px;
            vertical-align: top;
            font-size: 9.5px;
        }

        .party-card-title {
            color: #9e1b32;
            font-weight: bold;
            font-size: 10px;
            border-bottom: 1px dashed #d1d5db;
            padding-bottom: 3px;
            margin-bottom: 4px;
        }

        .items-table {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .items-table th {
            background-color: #9e1b32;
            color: #ffffff;
            font-weight: bold;
            font-size: 9.5px;
            padding: 5px 6px;
            border: 1px solid #9e1b32;
            text-align: center;
        }

        .items-table td {
            border: 1px solid #e5e7eb;
            padding: 5px 6px;
            font-size: 9px;
            text-align: center;
        }

        .items-table tr.even {
            background-color: #f9fafb;
        }

        .totals-table {
            border: 1px solid #e5e7eb;
        }

        .totals-table td {
            padding: 4px 8px;
            font-size: 9.5px;
            border-bottom: 1px solid #e5e7eb;
        }

        .final-row td {
            background-color: #9e1b32;
            color: #ffffff !important;
            font-weight: bold;
            font-size: 10px;
            border-bottom: none;
        }

        .signatures-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            border: 1px dashed #d1d5db;
            background-color: #fafafa;
            padding: 8px;
            font-size: 9px;
            height: 60px;
        }

        .footer-note {
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 4px;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    @php
        $brandSettings = $settings->brand ?? [];
        $contactSettings = $settings->contact ?? [];
        $sellerName = $brandSettings['name'] ?? 'بازرگانی نفیس تجارت';
        $sellerTagline = $brandSettings['tagline'] ?? 'واردات مستقیم · ترخیص تخصصی · پخش عمده کالا';
        $sellerPhone = $contactSettings['phoneDisplay'] ?? '۰۹۹۹۱۲۲۲۲۶۱';
        $sellerEmail = $contactSettings['email'] ?? 'info@nafiskala.co';
        $sellerAddress = $contactSettings['address'] ?? 'تهران، میدان دوم صادقیه، برج گلدیس، طبقه ۵، واحد ۵۰۸';
    @endphp

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <div class="brand-title">{{ $sellerName }}</div>
                <div class="brand-tagline">{{ $sellerTagline }}</div>
                <div class="brand-desc">عضو رسمی اتاق بازرگانی · دارنده کارت بازرگانی معتبر · نماد اعتماد الکترونیکی</div>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <div class="doc-title-box">
                    <div class="doc-main-title">پیش‌فاکتور رسمی فروش کالا</div>
                    <div class="doc-meta">
                        <div><strong>شماره پیش‌فاکتور:</strong> {{ $quotation->reference_code }}</div>
                        <div><strong>تاریخ صدور:</strong> {{ $quotation->created_at ? $quotation->created_at->format('Y/m/d') : date('Y/m/d') }}</div>
                        <div><strong>اعتبار تا تاریخ:</strong> {{ $quotation->valid_until ? $quotation->valid_until->format('Y/m/d') : '—' }}</div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Parties Info -->
    <table>
        <tr>
            <td class="party-card" style="width: 48%;">
                <div class="party-card-title">مشخصات فروشنده (تامین‌کننده)</div>
                <div><span style="color: #6b7280;">نام شرکت:</span> <strong>{{ $sellerName }}</strong></div>
                <div><span style="color: #6b7280;">تلفن:</span> {{ $sellerPhone }}</div>
                <div><span style="color: #6b7280;">ایمیل:</span> {{ $sellerEmail }}</div>
                <div><span style="color: #6b7280;">نشانی:</span> {{ $sellerAddress }}</div>
            </td>
            <td style="width: 4%;"></td>
            <td class="party-card" style="width: 48%;">
                <div class="party-card-title">مشخصات خریدار (مشتری محترم)</div>
                <div><span style="color: #6b7280;">خریدار:</span> <strong>{{ $quotation->customer?->name ?? 'مشتری محترم' }}</strong></div>
                <div><span style="color: #6b7280;">تلفن همراه:</span> {{ $quotation->customer?->phone ?? '—' }}</div>
                <div><span style="color: #6b7280;">پست الکترونیک:</span> {{ $quotation->customer?->email ?? '—' }}</div>
                <div><span style="color: #6b7280;">شناسه مشتری:</span> {{ $quotation->customer?->id ? substr($quotation->customer->id, 0, 8) : '—' }}</div>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 6%;">ردیف</th>
                <th style="width: 46%;">شرح و مشخصات فنی کالا</th>
                <th style="width: 12%;">تعداد</th>
                <th style="width: 18%;">قیمت واحد (ریال)</th>
                <th style="width: 18%;">مبلغ کل (ریال)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quotation->items as $index => $item)
                <tr class="{{ $index % 2 === 1 ? 'even' : '' }}">
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: right;">
                        <strong>{{ $item->item_title }}</strong>
                        @if($item->technical_description)
                            <div style="font-size: 8px; color: #4b5563;">{{ $item->technical_description }}</div>
                        @endif
                    </td>
                    <td>{{ number_format($item->quantity) }}</td>
                    <td style="text-align: left; direction: ltr;">{{ number_format((float) $item->unit_price_irr) }}</td>
                    <td style="text-align: left; direction: ltr;">{{ number_format((float) $item->total_price_irr) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding: 10px; color: #9ca3af;">اقلامی یافت نشد</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Financial & Terms -->
    <table>
        <tr>
            <td style="width: 52%; vertical-align: top; padding-left: 8px;">
                <div style="border: 1px solid #e5e7eb; background-color: #fcfcfc; padding: 6px 8px; font-size: 9px;">
                    <div style="color: #9e1b32; font-weight: bold; margin-bottom: 4px;">شرایط و توضیحات قرارداد</div>
                    @if($quotation->payment_terms)
                        <div><strong>شرایط پرداخت:</strong> {{ $quotation->payment_terms }}</div>
                    @endif
                    @if($quotation->customer_notes)
                        <div><strong>ملاحظات تحویل:</strong> {{ $quotation->customer_notes }}</div>
                    @endif
                    <div style="color: #6b7280; font-size: 8px; margin-top: 4px;">
                        * قیمت‌های مندرج تا تاریخ اعتبار پیش‌فاکتور معتبر بوده و پس از آن نیاز به استعلام مجدد نرخ تسعیر و هزینه حمل دارد.
                    </div>
                </div>
            </td>
            <td style="width: 48%; vertical-align: top;">
                <table class="totals-table">
                    <tr>
                        <td style="color: #4b5563;">جمع کل اقلام (ریال):</td>
                        <td style="text-align: left; direction: ltr; font-weight: bold;">{{ number_format((float) $quotation->subtotal_irr) }}</td>
                    </tr>
                    @if($quotation->shipping_and_customs_irr > 0)
                        <tr>
                            <td style="color: #4b5563;">هزینه حمل و بازرسی (ریال):</td>
                            <td style="text-align: left; direction: ltr;">{{ number_format((float) $quotation->shipping_and_customs_irr) }}</td>
                        </tr>
                    @endif
                    @if((float) $quotation->customs_duty_irr > 0)
                        <tr>
                            <td style="color: #4b5563;">حقوق و عوارض گمرکی (ریال):</td>
                            <td style="text-align: left; direction: ltr;">{{ number_format((float) $quotation->customs_duty_irr) }}</td>
                        </tr>
                    @endif
                    @if((float) $quotation->handling_fee_irr > 0)
                        <tr>
                            <td style="color: #4b5563;">هزینه‌های خدمات و ترخیص (ریال):</td>
                            <td style="text-align: left; direction: ltr;">{{ number_format((float) $quotation->handling_fee_irr) }}</td>
                        </tr>
                    @endif
                    @if((float) $quotation->tax_irr > 0)
                        <tr>
                            <td style="color: #4b5563;">مالیات و عوارض قانونی (ریال):</td>
                            <td style="text-align: left; direction: ltr;">{{ number_format((float) $quotation->tax_irr) }}</td>
                        </tr>
                    @endif
                    <tr class="final-row">
                        <td>مبلغ نهایی قابل پرداخت (ریال):</td>
                        <td style="text-align: left; direction: ltr; font-size: 11px;">{{ number_format((float) $quotation->final_total_irr) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Signatures -->
    <table style="margin-top: 10px;">
        <tr>
            <td class="signatures-table" style="width: 48%;">
                <div style="font-weight: bold; color: #9e1b32; margin-bottom: 25px;">مهر و امضای شرکت بازرگانی نفیس تجارت</div>
                <div style="font-size: 8px; color: #9ca3af;">(واحد فروش و بازرگانی خارجی)</div>
            </td>
            <td style="width: 4%;"></td>
            <td class="signatures-table" style="width: 48%;">
                <div style="font-weight: bold; color: #374151; margin-bottom: 25px;">تایید، مهر و امضای خریدار محترم</div>
                <div style="font-size: 8px; color: #9ca3af;">(صحت مشخصات و قیمت‌های فوق مورد تایید است)</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer-note">
        این سند به عنوان پیش‌فاکتور رسمی توسط سامانه بازرگانی {{ $sellerName }} صادر گردیده است. | تلفن: {{ $sellerPhone }} | وب‌سایت: www.nafiskala.co
    </div>
</body>
</html>