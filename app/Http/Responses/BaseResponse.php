<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

abstract class BaseResponse implements Responsable
{
    public int $statusCode;
    #[\Override]
    abstract public function toResponse($request);
}
