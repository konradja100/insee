<?php

namespace App\Api;

use App\DTO\RepositoryDTO;
use App\Exception\NotFoundException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;

/**
 * Class GithubApi
 *
 * You need to create your personal access token for your github account (no special scope required)
 * @see https://github.com/settings/tokens
 */
class GithubApi implements GitApiInterface
{

    /**
     * @var ClientInterface
     */
    private $http;
    /**
     * @var string
     */
    private $accessToken;
    /**
     * @var string
     */
    private $apiUrl;

    public function __construct(string $apiUrl, string $accessToken, ClientInterface $http)
    {
        $this->apiUrl = $apiUrl;
        $this->accessToken = $accessToken;
        $this->http = $http;
    }

    /**
     * @param string $endpoint
     *
     * @return \stdClass
     *
     * @throws NotFoundException
     */
    private function getRequest(string $endpoint) : \stdClass
    {
        try {
            /** @var Response $response */
            $response = $this->http->get(sprintf('%s/%s?access_token=%s',
                $this->apiUrl,
                $endpoint,
                $this->accessToken
            ));
        } catch (RequestException $exception) {
            if ($exception->getCode() === 404) {
                throw new NotFoundException();
            }
            throw $exception;
        }

        return json_decode($response->getBody());
    }

    /**
     * @param RepositoryDTO $repositoryDTO
     *
     * @return string
     *
     * @throws NotFoundException
     */
    public function findSha(RepositoryDTO $repositoryDTO) : string
    {
        $endpoint = sprintf('repos/%s/%s/branches/%s',
            $repositoryDTO->getUsername(),
            $repositoryDTO->getRepository(),
            $repositoryDTO->getBranch()
        );
        $response = $this->getRequest($endpoint);

        try {
            return $response->commit->sha;
        } catch (\ErrorException $exception) {
            throw new NotFoundException();
        }
    }
}