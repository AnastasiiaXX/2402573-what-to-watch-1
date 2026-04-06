<?php

namespace App\Http\Responses;

use Override;
use Symfony\Component\HttpFoundation\Response;

class ErrorResponse extends BaseResponse
{
    public int $responseCode;
    public string $message;
    public mixed $errors;

    /**
     * @param int $responseCode
     * @param string $message
     * @param mixed|null $errors
     */
    public function __construct(int $responseCode, string $message, mixed $errors = null)
    {
        $this->responseCode = $responseCode;
        $this->message = $message;
        $this->errors = $errors;
    }

    /**
     * @param $request
     * @return Response
     */
    #[Override]
    public function toResponse($request): Response
    {
        $response = ['message' => $this->message];

        if ($this->errors) {
            $response['errors'] = $this->errors;
        }

        return response()->json($response, $this->responseCode);
    }
}
