<?php

require_once(__DIR__.'/../../src/baseEntity.php');

class Kebab extends baseEntity {
    public function __construct($data = null)
    {
        $this->entityName = __CLASS__;
        parent::__construct($data);
    }
}