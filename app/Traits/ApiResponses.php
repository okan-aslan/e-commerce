<?php

namespace App\Traits;

trait ApiResponses
{
    protected function success($data, $message = null, $statusCode = 200)
    {
        return response()->json([
            'status' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function error($data, $message = null, $statusCode)
    {
        return response()->json([
            'status' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}
