<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Columns\TextColumn;

use Filament\Forms\Components\Select;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('customer_name')
                ->label('Nama Pelanggan')
                ->required(),

            TextInput::make('customer_email')
                ->label('Email Pelanggan')
                ->email()
                ->required(),

            TextInput::make('amount')
                    ->label('Nominal')
                    ->numeric()
                    ->required()
                    ->disabled(fn ($livewire) => $livewire instanceof EditRecord),

            Select::make('bank')
                    ->label('Bank')
                    ->options([
                        'bca'           => 'BCA',
                        'bri'           => 'BRI',
                        'bni'           => 'BNI',
                        'bank_transfer' => 'Permata',
                        'echannel'      => 'Mandiri'
                    ])
                    ->native(false)
                    ->searchable()
                    ->required()
                    ->disabled(fn ($livewire) => $livewire instanceof EditRecord),

            TextInput::make('virtual_account')
                ->label('Virtual Account')
                ->placeholder('Terisi Otomatis')
                ->disabled()
                ->dehydrated(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable(),

                TextColumn::make('customer_name')
                    ->label('Nama Pelanggan')
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Nominal')
                    ->sortable(),

                TextColumn::make('virtual_account')
                    ->label('Virtual Account')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d-m-Y H:i'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
            'view' => Pages\ViewInvoice::route('/{record}'),
        ];
    }
}
