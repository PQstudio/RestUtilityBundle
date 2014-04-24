<?php
namespace PQstudio\RestUtilityBundle\Tests\Controller;

use PQstudio\RestUtilityBundle\Controller\PQRestController;
use PQstudio\RestUtilityBundle\Utility\ResponseMetadata;
use PQstudio\RestUtilityBundle\Tests\Fixtures\SimpleObject;

class PQRestControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateObject()
    {
        $ctrl = new PQRestController();

        $this->assertTrue($ctrl->meta instanceof ResponseMetadata);
    }
}

