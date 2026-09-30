<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi input dari frontend
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'nullable|string|max:30',
            'service'        => 'nullable|string|max:100',
            'subject'        => 'nullable|string|max:255',
            'message'        => 'required|string',
            'source_website' => 'required|string|max:100', // misal: "bromo-initiative.com"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // 2. Simpan pesan ke database
        $contact = ContactMessage::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'service'        => $request->service ?? 'Tidak Memilih',
            'subject'        => $request->subject,
            'message'        => $request->message,
            'source_website' => $request->source_website,
            'status'         => 'unread',
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih telah menghubungi kami. Kami akan segera merespons pesan Anda.',
            'data'    => $contact
        ], 201);
    }
}
