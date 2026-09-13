<?php

namespace App\Filament\Portal\Resources\PortalOrders\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Forms\Components\{TextInput,FileUpload,Select};

class PortalOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_code')->label('شماره سفارش')->searchable()->sortable(),
                TextColumn::make('status')->label('وضعیت سفارش')->badge(),
                TextColumn::make('payment_status')->label('وضعیت پرداخت')->badge(),
                TextColumn::make('balance_irr')->label('مانده تسویه (ریال)')->numeric()->sortable(),
                TextColumn::make('incoterms')->label('شرایط تحویل (Incoterms)'),
            ])
            ->emptyStateHeading('هیچ سفارشی ثبت نشده است')
            ->emptyStateDescription('پس از تأیید پیش‌فاکتور و آغاز فرایند خرید، سفارش‌های شما در این قسمت قابل پیگیری خواهند بود.')
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('submitPayment')
                    ->label('ثبت فیش واریزی')
                    ->icon('heroicon-o-credit-card')
                    ->form([
                        TextInput::make('amount_irr')->label('مبلغ ریالی پرداختی')->numeric()->required(),
                        Select::make('type')->label('نوع پرداخت')->options([
                            'deposit' => 'پیش‌پرداخت',
                            'milestone' => 'مرحله‌ای',
                            'final_balance' => 'تسویه نهایی',
                        ])->required(),
                        Select::make('method')->label('روش پرداخت')->options([
                            'bank_transfer' => 'واریز به حساب / فیش بانکی',
                            'gateway' => 'درگاه پرداخت آنلاین',
                            'cash' => 'نقدی',
                            'cheque' => 'چک صیادی',
                        ])->required(),
                        FileUpload::make('proof_path')->label('تصویر فیش / رسید واریزی')->disk('public')->directory('payment-proofs')->required(),
                    ])
                    ->action(fn (\App\Models\Order $record, array $data) => app(\App\Services\PaymentService::class)->record($record, $data)),
            ])
            ->toolbarActions([]);
    }
}
