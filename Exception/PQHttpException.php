<?php
namespace PQstudio\RestUtilityBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class PQHttpException extends HttpException
{
    protected $moreInfo;

    protected $error;

    protected $errorMessage;

    public function __construct($code, $error = null, $errorMessage = null, $moreInfo = null)
    {
        parent::__construct($code);

        $this->statusCode = $code;
        $this->error = $error;
        $this->errorMessage = $errorMessage;
        $this->moreInfo = $moreInfo;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getMoreInfo()
    {
        return $this->moreInfo;
    }

    public function getError()
    {
        return $this->error;
    }

}
