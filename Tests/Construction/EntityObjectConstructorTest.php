<?php
namespace PQstudio\RestUtilityBundle\Tests\Construction;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\PhpCollectionHandler;
use JMS\Serializer\DeserializationContext;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\IdentityTranslator;
use JMS\Serializer\EventDispatcher\Subscriber\DoctrineProxySubscriber;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Handler\ArrayCollectionHandler;
use JMS\Serializer\Handler\ConstraintViolationHandler;
use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\VisitorInterface;
use Metadata\MetadataFactory;
use PhpCollection\Map;
use JMS\Serializer\Metadata\ClassMetadata;
use PQstudio\RestUtilityBundle\Construction\EntityObjectConstructor;
use PQstudio\RestUtilityBundle\Tests\Fixtures\SimpleObject;

class EntityObjectConstructorTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $this->factory = new MetadataFactory(new AnnotationDriver(new AnnotationReader()));

        $this->handlerRegistry = new HandlerRegistry();
        //$this->handlerRegistry->registerHandler(GraphNavigator::DIRECTION_DESERIALIZATION, 'AuthorList', 'json',
            //function(VisitorInterface $visitor, $data, $type, Context $context) {
                //$type = array(
                    //'name' => 'array',
                    //'params' => array(
                        //array('name' => 'integer', 'params' => array()),
                        //array('name' => 'JMS\Serializer\Tests\Fixtures\Author', 'params' => array()),
                    //),
                //);

                //$elements = $visitor->getNavigator()->accept($data, $type, $context);
                //$list = new AuthorList();
                //foreach ($elements as $author) {
                    //$list->add($author);
                //}

                //return $list;
            //}
        //);

        $this->dispatcher = new EventDispatcher();

        $this->dispatcher->addSubscriber(new DoctrineProxySubscriber());

        $namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
        $objectConstructor = new EntityObjectConstructor();
        $this->serializationVisitors = new Map(array(
            'json' => new JsonSerializationVisitor($namingStrategy),
        ));
        $this->deserializationVisitors = new Map(array(
            'json' => new JsonDeserializationVisitor($namingStrategy),
        ));

        $this->serializer = new Serializer($this->factory, $this->handlerRegistry, $objectConstructor, $this->serializationVisitors, $this->deserializationVisitors, $this->dispatcher);
    }

    public function testCreateObject()
    {
        $entityObjectConstructor = new EntityObjectConstructor();
        $classMetadata = new ClassMetadata('PQstudio\RestUtilityBundle\Tests\Fixtures\SimpleObject');

        $namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
        $entity = $entityObjectConstructor->construct(new JsonDeserializationVisitor($namingStrategy), $classMetadata, null, [],  DeserializationContext::create());

        $this->assertTrue($entity instanceof SimpleObject);
    }

    public function testCallConstructor()
    {
        $entityObjectConstructor = new EntityObjectConstructor();
        $classMetadata = new ClassMetadata('PQstudio\RestUtilityBundle\Tests\Fixtures\SimpleObject');

        $namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
        $entity = $entityObjectConstructor->construct(new JsonDeserializationVisitor($namingStrategy), $classMetadata, null, [],  DeserializationContext::create());

        $this->assertEquals($entity->variable, 5);
    }
}
