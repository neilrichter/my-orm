<?php

namespace App\Entities;

use ORM\baseEntity;

class Kebab extends baseEntity {
    public function __construct($data = null)
    {
        $this->entityName = __CLASS__;
        parent::__construct($data);
    }

    // public function __call($name, $arguments)
    // {
    //     // parent::__call($name, $arguments);
    // }
}