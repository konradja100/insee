<?php

namespace App\Services;

use App\DTO\RepositoryDTO;
use App\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArgumentParser
{

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param string $repositoryName
     * @param string $branch
     *
     * @return RepositoryDTO
     *
     * @throws InvalidArgumentException
     */
    public function parseArguments(string $repositoryName, string $branch) : RepositoryDTO
    {
        $stringExploded = explode('/', $repositoryName);

        $violations = $this->validator->validate([$stringExploded, $branch], [
            new Collection([
                new Collection([
                    [new NotBlank()],
                    [new NotBlank()]
                ]),
                new NotBlank()
            ])
        ]);

        if ($violations->count() > 0) {
            throw new InvalidArgumentException(sprintf('%s - \'%s\'',
                $violations->get(0)->getMessage(),
                $violations->get(0)->getInvalidValue())
            );
        }

        $repositoryDTO = new RepositoryDTO();

        $repositoryDTO->setUsername($stringExploded[0]);
        $repositoryDTO->setRepository($stringExploded[1]);
        $repositoryDTO->setBranch($branch);

        return $repositoryDTO;
    }
}