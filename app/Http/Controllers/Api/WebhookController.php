<?php

namespace App\Http\Controllers\Api;

use App\WebhookFPJS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class WebhookController extends Controller
{
    public function storefpjs(Request $request){

        WebhookFPJS::updateOrCreate([
            'visitor_id' => $request->visitorId
        ],[
            'object_data' => json_encode((array) $request->all()),
        ]);

        return response()->json('success', 200);
    }
}
