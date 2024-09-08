<?php

namespace App\Traits;

trait ApiResponses
{
    /**
     * Build a success response
     * @param $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data, $statusCode = 200)
    {
        return response()->json(['data' => $data], $statusCode);
    }

    /**
     * Build an error response
     * @param $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($message, $statusCode = 404)
    {
        return response()->json(['error' => $message, 'code' => $statusCode], $statusCode);
    }
}