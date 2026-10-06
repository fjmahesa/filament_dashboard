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

        // Link langsung ke halaman utama daftar pesan admin
        $dashboardUrl = "https://dashboard.inmarco.id/admin/contact-messages";

        // Format Pesan dengan Markdown
        $message = "📩 *PESAN MASUK BARU!*\n\n"
            . "👤 *Nama:* {$inbox->name}\n"
            . "📧 *Email:* {$inbox->email}\n"
            . "📱 *Telepon:* " . ($inbox->phone ?? '-') . "\n"
            . "📌 *Subjek:* " . ($inbox->subject ?? 'Tanpa Subjek') . "\n\n"
            . "💬 *Pesan:*\n"
            . "_{$inbox->message}_\n\n"
            . "📅 *Waktu:* " . now()->timezone('Asia/Jakarta')->format('d M Y, H:i') . " WIB\n\n"
            . "🔗 [Buka Dashboard Pesan Admin]({$dashboardUrl})";

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