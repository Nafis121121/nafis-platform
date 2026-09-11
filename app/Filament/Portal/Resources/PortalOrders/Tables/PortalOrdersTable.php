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
                TextColumn::make('reference_code')->label('کد سفارش')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('payment_status')->badge(),
                TextColumn::make('balance_irr')->label('مانده')->numeric(),
                TextColumn::make('incoterms')->label('اینکوترمز'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('submitPayment')
                    ->label('ثبت فیش پرداخت')
                    ->form([
                        TextInput::make('amount_irr')->label('مبلغ ریالی')->numeric()->required(),
                        Select::make('type')->options([
                            'deposit' => 'پیش‌پرداخت',
                            'milestone' => 'مرحله‌ای',
                            'final_balance' => 'تسویه نهایی',
                        ])->required(),
                        Select::make('method')->options([
                            'bank_transfer' => 'واریز بانکی',
                            'gateway' => 'درگاه',
                            'cash' => 'نقدی',
                            'cheque' => 'چک',
                        ])->required(),
                        FileUpload::make('proof_path')->disk('public')->directory('payment-proofs')->required(),
                    ])
                    ->action(fn (\App\Models\Order $record, array $data) => app(\App\Services\PaymentService::class)->record($record, $data)),
            ])
            ->toolbarActions([]);
    }
}
