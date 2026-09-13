<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>پیش‌فاکتور {{ $quotation->reference_code }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; direction: rtl; color: #1f2937; font-size: 12px; }
        .header { border-bottom: 2px solid #9e1b32; padding-bottom: 14px; margin-bottom: 18px; }
        h1 { color: #9e1b32; margin: 0 0 8px; font-size: 22px; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #d1d5db; padding: 7px; text-align: right; }
        th { background: #f3f4f6; }
        .summary { width: 45%; margin-right: auto; margin-top: 18px; }
        .summary td { border: 0; border-bottom: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="header">
        <h1>پیش‌فاکتور تجاری</h1>
        <div>کد پیگیری: {{ $quotation->reference_code }}</div>
        <div class="muted">تاریخ: {{ $quotation->created_at?->format('Y/m/d') }} | اعتبار تا: {{ $quotation->valid_until?->format('Y/m/d') ?? '—' }}</div>
    </div>
    <p><strong>خریدار:</strong> {{ $quotation->customer?->name ?? '—' }}</p>
    <table>
        <thead><tr><th>ردیف</th><th>کالا</th><th>شرح</th><th>تعداد</th><th>قیمت خرید واحد ({{ $quotation->base_currency?->value ?? $quotation->base_currency }})</th><th>جمع خرید ({{ $quotation->base_currency?->value ?? $quotation->base_currency }})</th><th>جمع فروش (ریال)</th></tr></thead>
        <tbody>
        @foreach($quotation->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td><td>{{ $item->item_title }}</td><td>{{ $item->technical_description ?: '—' }}</td>
                <td>{{ number_format($item->quantity) }}</td>
                <td>{{ number_format($item->unit_cost_currency, 4) }} {{ $quotation->base_currency?->value ?? $quotation->base_currency }}</td>
                <td>{{ number_format($item->total_cost_currency, 4) }} {{ $quotation->base_currency?->value ?? $quotation->base_currency }}</td>
                <td>{{ number_format($item->total_price_irr) }} ریال</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <table class="summary">
        <tr><td>جمع ارزش کالا ({{ $quotation->base_currency?->value ?? $quotation->base_currency }})</td><td>{{ number_format($quotation->subtotal_base_currency, 4) }} {{ $quotation->base_currency?->value ?? $quotation->base_currency }}</td></tr>
        <tr><td>حمل بین‌الملل ({{ $quotation->shipping_currency?->value ?? $quotation->shipping_currency }})</td><td>{{ number_format($quotation->shipping_cost_base_currency, 4) }} {{ $quotation->shipping_currency?->value ?? $quotation->shipping_currency }}</td></tr>
        <tr><td>مجموع ارزی</td><td>{{ number_format($quotation->total_foreign_currency, 4) }} (ارز مبنا)</td></tr>
        <tr><td>معادل ریالی ارزی</td><td>{{ number_format($quotation->total_foreign_currency * $quotation->exchange_rate) }} ریال</td></tr>
        <tr><td>ترخیص و گمرک</td><td>{{ number_format($quotation->customs_duty_irr) }} ریال</td></tr>
        <tr><td>حمل داخلی ایران</td><td>{{ number_format($quotation->inland_shipping_irr) }} ریال</td></tr>
        <tr><td>هزینه پیش‌بینی‌نشده</td><td>{{ number_format($quotation->unforeseen_cost_irr) }} ریال</td></tr>
        <tr><td>کارمزد / سود</td><td>{{ number_format($quotation->total_profit_irr) }} ریال</td></tr>
        <tr><td><strong>مبلغ نهایی</strong></td><td><strong>{{ number_format($quotation->final_total_irr) }} ریال</strong></td></tr>
    </table>
    @if($quotation->payment_terms)<p><strong>شرایط پرداخت:</strong> {{ $quotation->payment_terms }}</p>@endif
    @if($quotation->customer_notes)<p><strong>توضیحات:</strong> {{ $quotation->customer_notes }}</p>@endif
</body>
</html>
