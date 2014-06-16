<?php

namespace PQstudio\RestUtilityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use PQstudio\RestUtilityBundle\DependencyInjection\PQstudioRestUtilityExtension;

class PQstudioRestUtilityBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new PQstudioRestUtilityExtension();
    }
}
