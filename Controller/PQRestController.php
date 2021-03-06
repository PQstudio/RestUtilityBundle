<?php

namespace PQstudio\RestUtilityBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use PQstudio\RestUtilityBundle\Utility\ResponseMetadata;
use PQstudio\RestUtilityBundle\Exception\PQHttpException;
use PQstudio\RestUtilityBundle\Exception\PQValidationException;
use JMS\Serializer\DeserializationContext;
use FOS\RestBundle\View\View;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;

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

    /**
     * Function for sanitizing input for batch update
     * Uses JMSSerializer metadata
     */
    public function sanitizeInput($class, $content, $groups)
    {
        $json = json_decode($content, true);

        $serializer = $this->get('jms_serializer');
        $metadataFactory = $serializer->getMetadataFactory();

        $metadata = $metadataFactory->getMetadataForClass($class);

        $sanitizedFields = [];

        foreach($metadata->propertyMetadata as $property) {
            if(!empty(array_intersect($property->groups, $groups))) {
                $sanitizedFields[] = $property->name;
            }
        }

        $filtered = [];
        foreach($json as $key => $value) {
            if(in_array($key, $sanitizedFields)) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    /**
     * Returns ids of objects allowed by ACL for action
     */
    public function getAllowedIds($objects, $action) {
        $oids = [];

        foreach($objects as $object) {
            $oids[] = ObjectIdentity::fromDomainObject($object);
        }

        $securityContext = $this->get('security.context');
        $aclProvider = $this->get('security.acl.provider');

        $aclProvider->findAcls($oids);

        $ids = [];
        foreach ($objects as $object) {
            if ($securityContext->isGranted($action, $object)) {
                $ids[] = $object->getId();
            }
        }

        return $ids;
    }

    public function showValidationErrors($errors, $className)
    {
        throw new PQValidationException(
            422,
            "validation_error",
            'Provided data is incorrect. Model did not passed validation.',
            null,
            $className,
            $errors
        );
    }

    public function exist($object)
    {
        $this->meta = new ResponseMetadata();
        if($object === null || empty($object)) {
            throw new PQHttpException(404, "not_found", "Object not found");
        }

        return true;
    }

    protected function isA($object, $type)
    {
        if(!is_a($object, $type, true)) {
            throw new PQHttpException(422, "wrong_type", "Wrong type of object");
        }
    }

    protected function permissionDenied()
    {
        throw new PQHttpException(403, "permission_denied", "Access to the resource has been forbidden");
    }

    protected function setOffsetAndLimit(Request $request)
    {
        $limit = $request->query->getInt('limit');
        $offset = $request->query->getInt('offset');

        $this->limit = ($limit > 0 && $limit <= $this->container->getParameter('pq_rest_utility')['limit']) ? $limit : 10;
        $this->offset = $offset >= 0 ? $offset : $this->container->getParameter('pq_rest_utility')['offset'];
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

