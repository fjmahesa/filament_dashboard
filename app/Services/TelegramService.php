<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public static function sendInboxNotification($inbox): void
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$token || !$chatId) {
            return;
        }

        // Format Pesan dengan Markdown
        $message = "📩 *PESAN MASUK BARU!*\n\n"
            . "👤 *Nama:* {$inbox->name}\n"
            . "📧 *Email:* {$inbox->email}\n"
            . "📱 *Telepon:* " . ($inbox->phone ?? '-') . "\n"
            . "📌 *Layanan:* " . ($inbox->service ?? 'Tanpa Layanan') . "\n\n"
            . "💬 *Pesan:*\n"
            . "_{$inbox->message}_\n\n"
            . "📅 *Waktu:* " . now()->format('d M Y, H:i') . " WIB";

        try {
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi Telegram: ' . $e->getMessage());
        }
    }
}