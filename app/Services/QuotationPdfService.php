<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\SiteSetting;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Symfony\Component\HttpFoundation\Response;

class QuotationPdfService
{
    public function generatePdfString(Quotation $quotation): string
    {
        $quotation->loadMissing(['customer', 'items.product', 'items.variant']);
        $settings = SiteSetting::current();

        $html = view('quotations.document', compact('quotation', 'settings'))->render();

        $mpdf = $this->makeMpdf();
        $mpdf->SetDirectionality('rtl');

        return $this->withSuppressedMpdfNotices(function () use ($mpdf, $html) {
            $mpdf->WriteHTML($html);

            return $mpdf->Output('', Destination::STRING_RETURN);
        });
    }

    public function download(Quotation $quotation): Response
    {
        $filename = ($quotation->reference_code ?: "quotation-{$quotation->id}") . '.pdf';

        return response()->streamDownload(function () use ($quotation) {
            echo $this->generatePdfString($quotation);
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Builds an mPDF instance configured for RTL Persian documents: UTF-8
     * encoding, A4 portrait, embedded Vazirmatn font, and mPDF's built-in
     * script/language-aware shaping so Persian text renders fully connected
     * (no reversed or disjointed glyphs) without any manual pre-shaping.
     */
    private function makeMpdf(): Mpdf
    {
        $tempDir = storage_path('app/mpdf-temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        return new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'useSubstitutions' => true,
            'default_font' => 'vazirmatn',
            'tempDir' => $tempDir,
            'fontDir' => array_merge($fontDirs, [public_path('fonts')]),
            'fontdata' => $fontData + [
                'vazirmatn' => [
                    'R' => 'Vazirmatn-Regular.ttf',
                    'B' => 'Vazirmatn-Bold.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'margin_top' => 12,
            'margin_bottom' => 15,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);
    }
    /**
     * mPDF's bidi/glyph-shaping engine emits harmless PHP notices/warnings
     * (undefined array keys) for certain RTL text layouts even when the
     * resulting PDF renders correctly. Laravel's error handler normally
     * escalates these into exceptions, so we scope down error reporting to
     * just the mPDF library files for the duration of the render call,
     * without hiding any genuine error raised by our own application code.
     */
    private function withSuppressedMpdfNotices(callable $callback): string
    {
        $previousHandler = set_error_handler(function (int $errno, string $errstr, string $errfile = '') {
            if (in_array($errno, [E_WARNING, E_NOTICE, E_DEPRECATED], true) && str_contains($errfile, 'mpdf')) {
                return true;
            }

            return false;
        });

        try {
            return $callback();
        } finally {
            restore_error_handler();
        }
    }
}

