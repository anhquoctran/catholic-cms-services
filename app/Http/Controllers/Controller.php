<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Response;

class Controller extends BaseController
{
    /**
     * Check error code is app error
     *
     * @param int $errorCode
     *
     * @return bool
     */
    public function checkErrorCodeIsAppError($errorCode)
    {
        return $errorCode > 1000 ? true : false;
    }

    /**
     * Get header response status code
     *
     * @param int $statusCode
     *
     * @return int
     */
    public function getHeaderResponseStatusCode($statusCode)
    {
        return $this->checkErrorCodeIsAppError($statusCode)
            ? floor($statusCode / 100000)
            : $statusCode;
    }

    /**
     * Get error message
     *
     * @param int $code
     *
     * @return string
     */
    public function getMessage($code)
    {
        return isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '';
    }

    /**
     * Build json response for api request
     *
     * @param bool $status
     * @param array $result
     * @param array $extraResponse
     *
     * @return bool
     */
    public function buildResponse($status, $result = null, $extraResponse = [])
    {
        $response = [
            'status' => Response::HTTP_OK,
            'message' => $this->getMessage(Response::HTTP_OK),
            'success' => $status,
            'data' => []
        ];

        if (is_array($extraResponse)) {
            $response = array_merge($response, $extraResponse);
        }

        if (! is_null($result)) {
            $response['data'] = $result;
        }

        $headerStatus = $this->getHeaderResponseStatusCode($response['status']);

        return response()->json($response, $headerStatus);
    }

    /**
     * Build success response json
     *
     * @param array $data
     * @param array $extra
     *
     * @return bool
     */
    public function succeedResponse($data = [], $extra = null)
    {
        return $this->buildResponse(true, $data, $extra);
    }

    /**
     * Failure json response
     *
     * @param int $errorCode
     * @param array $errorMessage
     *
     * @return bool
     */
    public function failResponse($errorCode = null, $errorMessage = [])
    {
        if (empty($errorMessage)) {
            array_push($errorMessage,$this->getMessage($errorCode));
        }

        $response = [
            'status' => $errorCode,
            'message' => $errorMessage
        ];

        return $this->buildResponse(false, null, $response);
    }

    /**
     * Non validate json response
     *
     * @param array $errors
     *
     * @return bool
     *
     */
    public function notValidateResponse($errors = [])
    {
        if (!is_array($errors)) {
            $errors = $errors->toArray();
        }

        $messages = [];
        foreach ($errors as $err) {
            foreach ($err as $message) {
                array_push($messages, $message);
            }
        }

        return $this->failResponse(Response::HTTP_BAD_REQUEST, $messages);
    }
}
