<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\SuccessResponse;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function show()
    {
        return new SuccessResponse([], 200);
    }

    public function update()
    {
        return new SuccessResponse([], 200);
    }
}
