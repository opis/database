<?php

namespace Opis\Database\Test;

use Opis\Database\Model as BaseModel;

class Model extends BaseModel
{
    protected function getQueryBuilder(bool $clean = false)
    {
        if($this->queryBuilder === null){
            $this->queryBuilder = new QueryModel($this);
        }

        $r = $this->queryBuilder;
        if($clean){
            $this->queryBuilder = null;
        }
        return $r;
    }
}