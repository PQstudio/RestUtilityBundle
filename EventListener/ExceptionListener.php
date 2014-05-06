<?php
namespace PQstudio\RestUtilityBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use PQstudio\RestUtilityBundle\Exception\PQHttpException;
use PQstudio\RestUtilityBundle\Exception\PQValidationException;
use PQstudio\RestUtilityBundle\Utility\ResponseMetadata;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service
 */
class ExceptionListener
{
    protected $jmsSerializer;

    /**
     * @DI\InjectParams({
     *      "jmsSerializer" = @DI\Inject("jms_serializer")
     * })
     */
    public function __construct($jmsSerializer)
    {
        $this->jmsSerializer = $jmsSerializer;
    }

    /**
     * @DI\Observe("kernel.exception", priority = 255)
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        //You get the exception object from the received event
        $exception = $event->getException();

        $response = new JsonResponse();
        $meta = new ResponseMetadata();

        if ($exception instanceof PQHttpException && !($exception instanceof PQValidationException)) {
            $meta->setStatusCode($exception->getStatusCode());
            $meta->setError($exception->getError());
            $meta->setErrorMessage($exception->getErrorMessage());
            $meta->setMoreInfo($exception->getMoreInfo());

            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
            $response->setContent($this->jmsSerializer->serialize($meta->build(), 'json'));

            $event->setResponse($response);
        } elseif($exception instanceof PQValidationException) {
            $meta->setStatusCode($exception->getStatusCode());
            $meta->setError($exception->getError());
            $meta->setErrorMessage($exception->getErrorMessage());
            $meta->setMoreInfo($exception->getMoreInfo());

            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
            $content = [
                'meta' => $meta->build(),
                'errors' => $exception->getErrors()
            ];
            $response->setContent($this->jmsSerializer->serialize($content, 'json'));

            $event->setResponse($response);
        } else {
        }


    }
}
