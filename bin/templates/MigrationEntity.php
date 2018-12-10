<?php

class Migration {
    public $name;

    public function __construct ($name) {
        $this->name = $name;
    }

    public function __call($method, $args)
    {
        if (isset($this->$method)) {
            $func = $this->$method;
            return call_user_func_array($func, $args);
        }
    }
}
