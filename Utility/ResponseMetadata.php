<?php
namespace PQstudio\RestUtilityBundle\Utility;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("utility.response_metadata")
 */
class ResponseMetadata
{
    protected $code;

    protected $errorMessage;

    protected $moreInfo;

    protected $error;

    protected $count;

    protected $notifications;

    public function __construct()
    {
        $this->code = null;
        $this->errorMessage = null;
        $this->moreInfo = null;
        $this->error = null;
        $this->count = null;
        $this->notifications = null;
    }

    public function setStatusCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function setMoreInfo($moreInfo)
    {
        $this->moreInfo = $moreInfo;
        return $this;
    }

    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    public function setNotifications($notifications)
    {
        $this->notifications = $notifications;
        return $this;
    }

    public function build()
    {
        $meta = [];

        if($this->code !== null) {
            $meta = array_merge(['code' => $this->code], $meta);
        }
        if($this->errorMessage !== null) {
            $meta = array_merge(['errorMessage' => $this->errorMessage], $meta);
        }
        if($this->moreInfo !== null) {
            $meta = array_merge(['moreInfo' => $this->moreInfo], $meta);
        }
        if($this->error !== null) {
            $meta = array_merge(['error' => $this->error], $meta);
        }
        if($this->count !== null) {
            $meta = array_merge(['count' => $this->count], $meta);
        }
        if($this->notifications !== null) {
            $meta = array_merge(['notifications' => $this->notifications], $meta);
        }

        return $meta;
    }
}


