<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions;
use Filament\Notifications\Notification;
use Midtrans\Config;
use Midtrans\CoreApi;
use Illuminate\Support\Str;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
    
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        Config::$serverKey      = config('midtrans.server_key');
        Config::$isProduction   = config('midtrans.is_production');
        Config::$isSanitized    = config('midtrans.is_sanitized');
        Config::$is3ds          = config('midtrans.is_3ds');
        
        $orderId = Str::uuid()->toString();
        $payload = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => [
                'order_id'      => $orderId,
                'gross_amount'  => $data['amount'],
            ],
            'customer_details' => [
                'first_name'    => $data['customer_name'],
                'email'         => $data['customer_email'],
            ],
            'bank_transfer' => [
                'bank' => $data['bank'],
            ],
        ];

        try {
            $response = CoreApi::charge($payload);
                if ($data['bank'] == 'bank_transfer') {
                    $vaNumber = $response->permata_va_number ?? null;
                } else if ($data['bank'] == 'echannel') {
                    $vaNumber = $response->permata_va_number ?? null;
                } else {
                    $vaNumber = $response->va_numbers[0]->va_number ?? null;
                }
            
            $data['virtual_account'] = $vaNumber;
            $data['order_id'] = $orderId;

            $record = parent::handleRecordCreation($data);

            Notification::make()
                ->title('Virtual Account berhasil dibuat')
                ->body("Nomor VA: $vaNumber")
                ->success()
                ->send();
                    return $record;
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal membuat Virtual Account')
                ->body($e->getMessage())
                ->danger()
                ->send();
                    throw $e;
        }
    }
}
