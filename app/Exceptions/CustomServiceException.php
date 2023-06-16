<?php

namespace App\Exceptions;

use App\Traits\ResponseApiTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CustomServiceException extends Exception
{
    use ResponseApiTrait;

    /**
     * The status code to use for the response.
     *
     * @var int
     */
    public $status;

    /**
     * The message to use for the response.
     *
     * @var string
     */
    public $message;

    /**
     * The data to use for the response.
     *
     * @var array
     */
    public $data;

    /**
     * Create a new exception instance.
     *
     * @param  int  $status
     * @param  string  $message
     * @param  array  $data
     * @return void
     */
    public function __construct($status = 500, $message = "", $data = [])
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(): JsonResponse
    {
        if ($this->status == Response::HTTP_OK) {
            return $this->sendResponse($this->data, $this->message, $this->status);
        }

        return $this->sendError($this->message, $this->data, $this->status);
    }
}
