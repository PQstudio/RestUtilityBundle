<?php

namespace PQstudio\RestUtilityBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use PQstudio\RestUtilityBundle\Utility\ResponseMetadata;
use JMS\Serializer\DeserializationContext;
use FOS\RestBundle\View\View;


class PQRestController extends FOSRestController
{
    public $meta;

    public function __construct() {
        $this->meta = new ResponseMetadata();
    }

    public function deserialize($content, $class, $deserializeGroups, $validationGroups, $id = null)
    {
        $format = 'json';
        $serializer = $this->get('jms_serializer');

        // add id to not deserialized json request to not repeat id in request
        if(null !== $id) {
            $content = "{\"id\": ".$id.",".substr($content, 1);
        }

        try {
            $entity = $serializer->deserialize(
                $content,
                $class,
                $format,
                DeserializationContext::create()->setGroups($deserializeGroups)
            );

        } catch(\Exception $e) {
            throw new PQHttpException(400, "json_malformed", $e->getMessage());
        }

        // check if class match after deserialization
        $this->isA($entity, $class);

        $errors = $this->validate($entity, $validationGroups);

        if(true !== $errors) {
            $this->showValidationErrors($errors, strtolower(get_class($entity)));
        }

        return $entity;
    }

    protected function validate($entity, $validationGroups)
    {
        $validator = $this->get('validator');

        if(count($errors = $validator->validate($entity, $validationGroups))) {
            return $errors;
        }

        return true;
    }

    public function showValidationErrors($errors, $className)
    {
        $this->meta->setError('validation_error')
                   ->setErrorMessage('Provided data is incorrect. Model did not passed validation.')
        ;

        $view = $this->makeView(
            422,
            ['meta' => $this->meta->build(), $className => $errors],
            [],
            false
        );

        return $this->handleView($view);
    }

    public function exist($object)
    {
        $this->meta = new ResponseMetadata();
        if($object === null || empty($object)) {
            $this->meta->setError('not_found')
                       ->setErrorMessage('Object not found')
            ;

            $view = $this->makeView(
                404,
                ['meta' => $this->meta->build()],
                [],
                false
            );

            return $this->handleView($view);
        }

        return true;
    }

    protected function isA($object, $type)
    {
        if(!is_a($object, $type, true)) {
            $this->meta->setError('wrong_type')
                       ->setErrorMessage('Wrong type of object')
            ;

            $view = $this->makeView(
                422,
                ['meta' => $this->meta->build()],
                [],
                false
            );

            return $this->handleView($view);
        }
    }

    protected function permissionDenied()
    {
        $this->meta->setError('permission_denied')
                   ->setErrorMessage('Access to the resource has been forbidden')
        ;

        $view = $this->makeView(
            403,
            ['meta' => $this->meta->build()],
            [],
            false
        );

        return $this->handleView($view);
    }

    protected function setOffsetAndLimit(Request $request)
    {
        $limit = $request->query->getInt('limit');
        $offset = $request->query->getInt('offset');

        $this->limit = ($limit > 0 && $limit <= $this->container->getParameter('pqstudio_rest_utility.limit')) ? $limit : 10;
        $this->offset = $offset >= 0 ? $offset : $this->container->getParameter('pqstudio_rest_utility.offset');
    }

    protected function makeView($code, $data, $serializationGroups, $enableMaxDepthChecks)
    {
        $view = $this->view();

        $view->setStatusCode($code)
            ->setData($data);
        ;
        if($enableMaxDepthChecks) {
            $view->getSerializationContext()->enableMaxDepthChecks();
        }

        if(!empty($serializationGroups)) {
            $view->getSerializationContext()->setGroups($serializationGroups);
        }

        return $view;
    }
}

