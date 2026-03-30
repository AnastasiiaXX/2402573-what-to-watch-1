final <?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;

class SuccessResponse extends BaseResponse
{
    public int $responseCode;
    public mixed $data;

    public function __construct(mixed $data, int $responseCode)
    {
        $this->data = $data;
        $this->responseCode = $responseCode;
    }
    #[\Override]
    public function toResponse($request): Response
    {
        return response()->json(['data' => $this->data], $this->responseCode);
    }
}
