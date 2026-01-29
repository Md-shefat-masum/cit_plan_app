<?php

if (!function_exists('api_response')) {
    /**
     * Generate a standardized API response
     *
     * @param array $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    function api_response($data = [], $message = '', $code = 200)
    {
        $responseConstants = [
            200 => true,
            201 => true,
            400 => false,
            401 => false,
            403 => false,
            404 => false,
            422 => false,
            500 => false,
        ];

        $success = $responseConstants[$code] ?? ($code < 400);

        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
