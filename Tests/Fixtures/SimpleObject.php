<?php
namespace PQstudio\RestUtilityBundle\Tests\Fixtures;


class SimpleObject
{
    public $variable = 0;

    public function __construct() {
        $this->variable = 5;
    }
}
