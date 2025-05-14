<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugController extends Controller
{
    public function testAjax(Request $request)
    {
        Log::info('AJAX test request received', [
            'method' => $request->method(),
            'ajax' => $request->ajax(),
            'wantsJson' => $request->wantsJson(),
            'content' => $request->all(),
            'headers' => $request->header()
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => 9999,
                'content' => $request->input('content', 'Test content'),
                'sender_id' => auth()->id(),
                'receiver_id' => $request->input('receiver_id', 1),
                'created_at' => now()->toISOString(),
                'is_read' => false
            ],
            'debug' => [
                'is_ajax' => $request->ajax(),
                'method' => $request->method(),
                'content_type' => $request->header('Content-Type')
            ]
        ]);
    }
}
