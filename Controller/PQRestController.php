<?php

namespace PQstudio\RestUtilityBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use OinpharmaMojLek\ApiBundle\Util\MetaResource;
use OinpharmaMojLek\AppBundle\Exception\PQHttpException;
use JMS\Serializer\DeserializationContext;
use JMS\DiExtraBundle\Annotation as DI;
use FOS\RestBundle\View\View;


class PQRestController extends FOSRestController
{
    public $meta;

    protected function deserialize($content, $class, $deserializeGroups, $validationGroups, $id = null)
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

    protected function showValidationErrors($errors, $className)
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

    protected function exist($object)
    {
        if($object === null || empty($object)) {
            throw new PQHttpException(
                404,
                'not_found',
                'Object not found'
            );
        }
    }

    protected function isA($object, $type)
    {
        if(!is_a($object, $type, true)) {
            throw new PQHttpException(
                422,
                'wrong_type',
                'Wrong type of object'
            );
        }
    }

    //protected function permissionDenied()
    //{
        //throw new PQHttpException(
            //403,
            //'permission_denied',
            //'Access to the resource has been forbidden'
        //);
    //}

    //protected function setOffsetAndLimit(Request $request)
    //{
        //$limit = $request->query->getInt('limit');
        //$offset = $request->query->getInt('offset');

        //$this->limit = ($limit > 0 && $limit < 25) ? $limit : 10;
        //$this->offset = $offset >= 0 ? $offset : 0;
    //}

    protected function makeView($code, $data, $serializationGroups, $enableMaxDepthChecks)
    {
        $view = $this->view();

        $this->meta->setStatusCode($code);
        if(isset($data['meta'])) {
            $data['meta'] = array_merge(['code' => $code], $data['meta']);
        }

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

