<?php

namespace SmartCms\Core\Repositories;

interface RepositoryInterface
{
   public function find(int $id): object;

   public function findMultiple(array $ids): array;
}
