<?php

namespace App\Services;

use App\Api\GitApiInterface;
use App\Exception\UnknownApiServiceException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ApiFactory
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $service
     *
     * @return GitApiInterface
     *
     * @throws UnknownApiServiceException
     */
    public function createApi(?string $service) : GitApiInterface
    {
        switch ($service) {
            case 'github':
                return $this->container->get('api.github');
            default:
                throw new UnknownApiServiceException(sprintf('Unknown service \'%s\'', $service));
        }
    }
}