<?php
namespace PQstudio\RestUtilityBundle\Tests\Utility;

use PQstudio\RestUtilityBundle\Utility\ResponseMetadata;

class ResponseMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateObjectWithNullValues()
    {
        $meta = new ResponseMetadata();

        $this->assertEquals($meta->build(), []);
    }

    public function testSetValues()
    {
        $meta = new ResponseMetadata();
        $meta->setError("error");
        $meta->setErrorMessage("Error message");

        $this->assertEquals($meta->build(), ['error' => 'error', 'errorMessage' => 'Error message']);
    }
}

