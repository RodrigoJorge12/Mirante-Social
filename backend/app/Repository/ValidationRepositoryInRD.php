<?php

namespace App\Repository;
use App\Models\Validation;


class ValidationRepositoryInRD implements ValidationRepository{
    public function create($validation){
        return Validation::create($validation);
    }
}

?>