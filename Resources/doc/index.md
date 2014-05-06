Setting up the bundle
=====================

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

Basic configuration
===================

RestUtilityBundle is using JMSSerializerBundle and FOSRestBundle, so these bundles needs to be configured properly.
Minimum required configuration:

#### 1) Turn off SensioFrameworkExtraBundle view annotations:
``` yaml
sensio_framework_extra:
    router:  { annotations: true }
    request: { converters: true }
    view:    { annotations: false }
    cache:   { annotations: true }
```

#### 2) Configure FOSRestBundle (example configuration for json-only API):
``` yaml
fos_rest:
    format_listener:
        rules:
            - { priorities: ['json'], fallback_format: json, prefer_extension: true }
    routing_loader:
        default_format: json
    view:
        view_response_listener: force
        formats:
            json: true
```
