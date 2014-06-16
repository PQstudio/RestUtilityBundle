<?php
namespace PQstudio\RestUtilityBundle\Exception;

class PQValidationException extends PQHttpException
{
    protected $className;

    protected $errors;

    public function __construct($code, $error = null, $errorMessage = null, $moreInfo = null, $className, $errors)
    {
        parent::__construct($code, $error, $errorMessage, $moreInfo);

        $this->className = $className;
        $this->errors = $errors;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getErrors()
    {
        return $this->errors;
    }

}
