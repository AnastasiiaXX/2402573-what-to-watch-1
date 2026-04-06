<?php

namespace App\Http\Responses;

use Override;
use Symfony\Component\HttpFoundation\Response;

class PaginateResponse extends BaseResponse
{
    public int $responseCode;
    public mixed $data;

    /**
     * @param mixed $data
     * @param int $responseCode
     */
    public function __construct(mixed $data, int $responseCode)
    {
        $this->data = $data;
        $this->responseCode = $responseCode;
    }

    /**
     * @param $request
     * @return Response
     */
    #[Override]
    public function toResponse($request): Response
    {
        return response()->json($this->data, $this->responseCode);
    }
}
