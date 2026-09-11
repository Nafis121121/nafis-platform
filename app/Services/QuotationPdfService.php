<?php

namespace App\Services;

use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class QuotationPdfService
{
    public function download(Quotation $quotation): Response
    {
        $quotation->loadMissing(['customer', 'items.product', 'items.variant']);

        return Pdf::loadView('quotations.document', compact('quotation'))
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', true)
            ->download($quotation->reference_code . '.pdf');
    }
}
