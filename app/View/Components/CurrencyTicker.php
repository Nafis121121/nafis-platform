<?php

namespace App\View\Components;

use App\Services\CurrencyExchangeService;
use Illuminate\View\Component;
use Illuminate\View\View;

class CurrencyTicker extends Component
{
    public array $rates;

    public function __construct(CurrencyExchangeService $exchangeService)
    {
        $this->rates = $exchangeService->getRates();
    }

    public function render(): View
    {
        return view('components.currency-ticker');
    }
}
