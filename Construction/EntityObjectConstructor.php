<?php
namespace PQstudio\RestUtilityBundle\Construction;

use JMS\Serializer\VisitorInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Construction\ObjectConstructorInterface;

/**
 * Constructor used by JMS Serializer for deserialization.
 *
 * Intended to be used as fallback constructor for DoctrineObjectConstructor - as a replacement for UnserializeObjectConstructor.
 * It *does* call entity constructor.
 */
class EntityObjectConstructor implements ObjectConstructorInterface
{
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context)
    {
        return new $metadata->name;
    }
}
