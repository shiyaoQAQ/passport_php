<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function json($code, $message = '', $data = [], $meta = [])
    {
        return response()->json([
            'code' => $code,
            'msg' => $message,
            'data' => $data,
            'meta' => $meta,
        ]);
    }
}
