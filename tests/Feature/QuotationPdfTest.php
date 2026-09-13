<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use App\Services\QuotationPdfService;
use FontLib\Font;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_vazirmatn_font_files_exist(): void
    {
        $this->assertFileExists(public_path('fonts/Vazirmatn-Regular.ttf'));
        $this->assertFileExists(public_path('fonts/Vazirmatn-Bold.ttf'));
    }

    public function test_pdf_service_generates_valid_pdf(): void
    {
        $customer = User::factory()->create([
            'name' => 'شرکت توسعه تجارت پارس',
            'phone' => '09123456789',
        ]);
        $category = Category::create([
            'name_fa' => 'تجهیزات صنعتی',
            'slug' => 'industrial',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name_fa' => 'سنسور حرارتی صنعتی',
            'slug' => 'thermal-sensor',
            'base_currency' => 'USD',
            'moq' => 1,
            'is_active' => true,
        ]);

        $quotation = Quotation::create([
            'reference_code' => 'QT-2026-001',
            'user_id' => $customer->id,
            'base_currency' => 'USD',
            'exchange_rate' => 500000,
            'subtotal_irr' => 10000000,
            'final_total_irr' => 11000000,
            'tax_irr' => 1000000,
            'valid_until' => now()->addDays(10),
            'payment_terms' => '۵۰ درصد پیش‌پرداخت، مابقی هنگام تحویل',
            'customer_notes' => 'تحویل در انبار مرکزی تهران',
        ]);

        $quotation->items()->create([
            'product_id' => $product->id,
            'item_title' => 'سنسور حرارتی صنعتی مدل PT100',
            'technical_description' => 'دقت بالا با کابل نسوز سیلیکونی ۲ متری',
            'quantity' => 10,
            'unit_cost_currency' => 20,
            'unit_price_irr' => 1000000,
            'total_price_irr' => 10000000,
        ]);

        $pdfOutput = app(QuotationPdfService::class)->generatePdfString($quotation);
        $this->assertNotEmpty($pdfOutput);
        $this->assertStringStartsWith('%PDF', $pdfOutput);

        $response = app(QuotationPdfService::class)->download($quotation);
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class, $response);
    }

    public function test_vazirmatn_font_has_persian_glyphs(): void
    {
        $font = Font::load(public_path('fonts/Vazirmatn-Regular.ttf'));
        $font->parse();
        $subtables = $font->getData('cmap', 'subtables');
        $this->assertNotEmpty($subtables);

        // Check character mappings
        $unicodeTable = null;
        foreach ($subtables as $subtable) {
            if (isset($subtable['glyphIndexArray'])) {
                $unicodeTable = $subtable['glyphIndexArray'];
                break;
            }
        }
        $this->assertNotNull($unicodeTable);
        // Persian Pe: 0x067E, Che: 0x0686, Zhe: 0x0698, Gaf: 0x06AF, Yeh: 0x06CC
        $this->assertArrayHasKey(0x067E, $unicodeTable, 'Should have Pe (پ)');
        $this->assertArrayHasKey(0x0686, $unicodeTable, 'Should have Che (چ)');
        $this->assertArrayHasKey(0x0698, $unicodeTable, 'Should have Zhe (ژ)');
        $this->assertArrayHasKey(0x06AF, $unicodeTable, 'Should have Gaf (گ)');
        $this->assertArrayHasKey(0x06CC, $unicodeTable, 'Should have Farsi Yeh (ی)');

        // Check presentation forms
        $hasPePresentation = isset($unicodeTable[0xFB56]); // Pe isolated
        $hasBehInitial = isset($unicodeTable[0xFE91]); // Beh initial
        $this->assertTrue($hasPePresentation || $hasBehInitial);
    }

    public function test_persian_pdf_shaper_shapes_persian_words(): void
    {
        $shaped = \App\Support\PersianPdfShaper::shape('پیش‌فاکتور');
        $this->assertNotEmpty($shaped);
        $this->assertNotEquals('پیش‌فاکتور', $shaped);

        $shapedWithNumbers = \App\Support\PersianPdfShaper::shape('مبلغ: 10,000,000 ریال');
        $this->assertStringContainsString('10,000,000', $shapedWithNumbers);

        $html = '<div class="header"><h1>پیش‌فاکتور رسمی نفیس تجارت</h1><p>کد: QT-2026</p></div>';
        $shapedHtml = \App\Support\PersianPdfShaper::shapeHtml($html);
        $this->assertStringContainsString('class="header"', $shapedHtml);
        $this->assertStringContainsString('QT-2026', $shapedHtml);
    }
}
