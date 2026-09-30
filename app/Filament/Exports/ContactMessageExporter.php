<?php

namespace App\Filament\Exports;

use App\Models\ContactMessage;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Notifications\Notification;

class ContactMessageExporter extends Exporter
{
    protected static ?string $model = ContactMessage::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('created_at')->label('Tanggal'),
            ExportColumn::make('source_website')->label('Website'),
            ExportColumn::make('name')->label('Nama'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('phone')->label('No. WA'),
            ExportColumn::make('service')->label('Layanan'),
            ExportColumn::make('message')->label('Pesan'),
            ExportColumn::make('status')->label('Status'),
        ];
    }

    public static function getCompletedNotificationTitle(Export $export): string
    {
        return 'Ekspor Pesan Masuk Selesai';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor data pesan masuk telah selesai diproses dan ' . number_format($export->successful_rows) . ' baris berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diekspor.';
        }

        return $body;
    }
}