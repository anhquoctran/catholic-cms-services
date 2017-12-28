<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;

class Controller extends BaseController
{
    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->resolvePaginationCurrentPage();
    }

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
    public function buildResponse($status, $result = [], $extraResponse = [])
    {
        if (!is_array($result)) {
            $result = $result->toArray();
        }

        $response = [
            'status' => Response::HTTP_OK,
            'message' => [$this->getMessage(Response::HTTP_OK)],
            'success' => $status,
            'data' => empty($result) ? null : $result
        ];

        if (is_array($extraResponse)) {
            $response = array_merge($response, $extraResponse);
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
    public function succeedResponse($data = null, $extra = null)
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
    public function invalidateResponse($errors = [])
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

    /**
     * Get pagination per page
     */
    public function getPaginationPerPage()
    {
        $requestPerPage = (int) app()->make('request')->input('per_page');

        return $requestPerPage ? $requestPerPage : DEFAULT_PAGINATION_PER_PAGE;
    }

    /**
     * Auto resolve pagination current page by request param current_page
     */
    public function resolvePaginationCurrentPage()
    {
        Paginator::currentPageResolver(function () {
            $request = app('request');
            return $request->input('current_page', $request->input('page', 1));
        });
    }

    /**
     * Build success response json for pagination data
     *
     * @param object $pagination
     * @param string $dataKey
     * @param array $extra
     *
     * @return bool
     */
    public function succeedPaginationResponse($pagination, $dataKey = 'items', $extra = [])
    {
        $pagination->setPath('/' . app()->make('request')->path());

        $tmpResult = $pagination->toArray();

        $data = $tmpResult['data'];

        unset($tmpResult['data']);
        unset($tmpResult['next_page_url'],$tmpResult['prev_page_url'],$tmpResult['path']);

        $result['pagination'] = $tmpResult;

        $result[$dataKey] = $data;

        if (!empty($extra) && is_array($extra)) {
            $result = array_merge($result, $extra);
        }

        return $this->buildResponse(true, $result);
    }
}
