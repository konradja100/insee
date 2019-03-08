<?php

namespace App\Command;

use App\Api\GitApiInterface;
use App\DTO\RepositoryDTO;
use App\Exception\AppExceptionInterface;
use App\Services\ApiFactory;
use App\Services\ArgumentParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FindLastCommitShaCommand extends Command
{
    const EXIT_CODE_OK = 1;
    const EXIT_CODE_EXCEPTION = 2;

    protected static $defaultName = 'find:sha';
    /**
     * @var ApiFactory
     */
    private $apiFactory;
    /**
     * @var ArgumentParser
     */
    private $argumentParser;

    public function __construct($name = null, ApiFactory $apiFactory, ArgumentParser $argumentParser)
    {
        parent::__construct($name);
        $this->apiFactory = $apiFactory;
        $this->argumentParser = $argumentParser;
    }

    protected function configure()
    {
        $this
            ->setDescription('Find sha of last commit by user, repository and branch.')
            ->addArgument(
                'repository',
                InputArgument::REQUIRED,
                'Repository'
            )
            ->addArgument(
                'branch',
                InputArgument::REQUIRED,
                'Branch'
            )
                ->addOption(
                '--service',
                null,
                InputOption::VALUE_OPTIONAL,
                'Option description',
                'github'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var SymfonyStyle $io */
        $io = new SymfonyStyle($input, $output);

        /** @var string $repository */
        $repository = $input->getArgument('repository');

        /** @var string $branch */
        $branch = $input->getArgument('branch');

        /** @var string $service */
        $service = $input->getOption('service');

        try {
            /** @var GitApiInterface $apiService */
            $apiService = $this->apiFactory->createApi($service);

            /** @var RepositoryDTO $repositoryDTO */
            $repositoryDTO = $this->argumentParser->parseArguments($repository, $branch);

            /** @var string $result */
            $result = $apiService->findSha($repositoryDTO);

            $io->text($result);

            return self::EXIT_CODE_OK;
        } catch (AppExceptionInterface $appException) {
            $io->error($appException->getMessage());

            return self::EXIT_CODE_EXCEPTION;
        } catch (\Throwable $throwable) {
            $io->error('Undefined error occurred.');

            return self::EXIT_CODE_EXCEPTION;
        }
    }
}
