<?php

namespace App\Services\LoadCommentsService;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class CommentRepository implements LoadCommentsInterface
{
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory
    ) {}

    public function getComments(string $imdbId): ?array
    {
        $request = $this->requestFactory->createRequest(
            'GET', config("services.comments.url") . "?date=" . now()->subDay()->toDateString()
        );
        $response = $this->httpClient->sendRequest($request);
        $commentsData = json_decode($response->getBody()->getContents(), true);
        if (empty($commentsData)) {
            return null;
        }

        return $commentsData;
    }
}
