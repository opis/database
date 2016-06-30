<?php

namespace Opis\Database\Test;

use Opis\Database\ORM\Query;

class QueryModel extends Query
{
    public function first(array $columns = [])
    {
        return $this->query($columns);
    }

    public function all(array $columns = []): array
    {
        return [$this->query($columns)];
    }
}