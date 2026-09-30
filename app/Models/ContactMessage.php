<?php

namespace App\Models;

use App\Services\TelegramService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'service',
        'subject',
        'message',
        'source_website',
        'status',
        'ip_address',
        'user_agent',
    ];

    /**
     * Memicu event otomatis saat model dipanggil
     */
    protected static function booted(): void
    {
        static::created(function ($contactMessage) {
            // Otomatis kirim notifikasi ke Telegram saat ada pesan baru disimpan
            TelegramService::sendInboxNotification($contactMessage);
        });
    }
}