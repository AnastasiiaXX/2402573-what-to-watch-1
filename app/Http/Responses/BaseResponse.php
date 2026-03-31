<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Override;

abstract class BaseResponse implements Responsable
{
    public int $statusCode;

    #[Override]
    abstract public function toResponse($request);
}
