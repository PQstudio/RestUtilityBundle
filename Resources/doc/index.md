Setting up the bundle
=============================

### A) Install RestUtilityBundle

Add to your composer.json:

``` json
"pqstudio/rest-utility-bundle": "dev-master"
```

### B) Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new PQstudio\RestUtilityBundle\PQstudioRestUtilityBundle(),
    );
}
```
