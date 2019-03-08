<?php

namespace App\Api;

use App\DTO\RepositoryDTO;

interface GitApiInterface {
    public function findSha(RepositoryDTO $repositoryDTO) : string;
}