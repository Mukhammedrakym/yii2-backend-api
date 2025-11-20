<?php

namespace app\helpers;

use Yii;

class ApiResponse
{
    public static function success($data, int $statusCode = 200): array
    {
        Yii::$app->response->statusCode = $statusCode;
        return [
            'success' => true,
            'data' => $data,
        ];
    }

    public static function error(array $errors, int $statusCode = 422, string $message = null): array
    {
        Yii::$app->response->statusCode = $statusCode;
        $response = [
            'success' => false,
            'errors' => $errors,
        ];
        
        if ($message !== null) {
            $response['message'] = $message;
        }
        
        return $response;
    }
}