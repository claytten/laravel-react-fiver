<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ResponseApiTrait
{
  /**
   * Core of response
   * @param array $result
   * @param string $message
   * @param int $code
   * 
   * @return \Illuminate\Http\JsonResponse
   */
  public function sendResponse($result = [], $message, $code = Response::HTTP_OK): JsonResponse
  {
    $response = [
      'success' => true,
      'data'    => $result,
      'message' => $message,
    ];
    return response()->json($response, $code);
  }

  /**
   * Core of error response
   * @param string $error
   * @param array $errorMessages
   * @param int $code
   * 
   * @return \Illuminate\Http\JsonResponse
   */
  public function sendError($error, $errorMessages = [], $code = 404): JsonResponse
  {
    $response = [
      'success' => false,
      'message' => $error,
    ];

    if (!empty($errorMessages)) {
      $response['data'] = $errorMessages;
    }

    return response()->json($response, $code);
  }
}