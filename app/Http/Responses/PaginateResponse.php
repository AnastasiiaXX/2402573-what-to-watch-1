<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;

class PaginateResponse extends BaseResponse
{
    public int $responseCode;
    public mixed $data;

    public function __construct(mixed $data, int $responseCode)
    {
        $this->data = $data;
        $this->responseCode = $responseCode;
    }

    public function toResponse($request): Response
    {
        return response()->json($this->data, $this->responseCode);
    }
}
