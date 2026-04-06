<?php

namespace App\Services\LoadCommentsService;

use Override;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class CommentRepository implements LoadCommentsInterface
{
    /**
     * @param ClientInterface $httpClient
     * @param RequestFactoryInterface $requestFactory
     */
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
    ) {
    }

    /**
     * Gets comments from an external source
     *
     * @param string $imdbId
     * @return array|null
     * @throws ClientExceptionInterface
     */
    #[Override]
    public function getComments(string $imdbId): ?array
    {
        $request = $this->requestFactory->createRequest(
            'GET',
            config("services.comments.url") . "?date=" . now()->subDay()->toDateString()
        );
        $response = $this->httpClient->sendRequest($request);
        $commentsData = json_decode($response->getBody()->getContents(), true);
        if (empty($commentsData)) {
            return null;
        }

        return $commentsData;
    }
}
