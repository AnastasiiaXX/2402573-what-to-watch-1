<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Override;

abstract class BaseResponse implements Responsable
{
    public int $statusCode;

    /**
     * @param Request $request
     * @return Response
     */
    #[Override]
    abstract public function toResponse($request);
}
