<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\DataMapper;

use eZ\Publish\Core\FieldType\TextLine\Value;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\DataMapper\CreateContentMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class CreateContentMapperTest extends TestCase
{
    /**
     * @var CreateContentMapper
     */
    private $mapper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dispatcher;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $propertyAccessor;

    protected function setUp(): void
    {
        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->propertyAccessor = $this->getMockBuilder('Symfony\Component\PropertyAccess\PropertyAccessorInterface')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->registry = $this->getMockBuilder('Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->handler = $this->getMockBuilder('Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerInterface')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->mapper = new CreateContentMapper($this->registry, $this->propertyAccessor);
    }

    public function testInstanceOfDataMapper()
    {
        self::assertInstanceOf('\Symfony\Component\Form\DataMapperInterface', $this->mapper);
    }

    public function testMapDataToFormsShouldThrowUnexpectedTypeException()
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->mapper->mapDataToForms('data', 'form');
    }

    public function testMapDataToFormsWithoutValidFieldDefinition()
    {
        $this->expectException(RuntimeException::class);

        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => [
                    new FieldDefinition(
                        [
                            'id' => 'id',
                            'identifier' => 'test',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new Value('Some name'),
                        ]
                    ),
                ],
            ]
        );

        $contentCreateStruct = new ContentCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($contentCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMockForAbstractClass();

        $propertyPath->expects(self::once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapDataToForms($data, [$form]);
    }

    public function testMapDataToForms()
    {
        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => [
                    new FieldDefinition(
                        [
                            'id' => 'id',
                            'identifier' => 'name',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new Value('Some name'),
                        ]
                    ),
                ],
            ]
        );

        $this->registry->expects(self::once())
            ->method('get')
            ->with('eztext')
            ->willReturn($this->handler);

        $this->handler->expects(self::once())
            ->method('convertFieldValueToForm')
            ->willReturn('Some name');

        $contentCreateStruct = new ContentCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($contentCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMockForAbstractClass();

        $propertyPath->expects(self::once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('setData');

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapDataToForms($data, [$form]);
    }

    public function testMapDataToFormsWithoutDataWrapper()
    {
        $data = new \stdClass();

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMockForAbstractClass();

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('setData');

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapDataToForms($data, [$form]);
    }

    public function testMapDataToFormsDefault()
    {
        $data = new \stdClass();

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMapped'])
            ->getMock();

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('setData');

        $form->expects(self::exactly(2))
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn(null);

        $this->mapper->mapDataToForms($data, [$form]);
    }

    public function testMapFormsToData()
    {
        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => [
                    new FieldDefinition(
                        [
                            'id' => 'id',
                            'identifier' => 'name',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new Value('Some name'),
                        ]
                    ),
                ],
            ]
        );

        $this->registry->expects(self::once())
            ->method('get')
            ->with('eztext')
            ->willReturn($this->handler);

        $this->handler->expects(self::once())
            ->method('convertFieldValueFromForm')
            ->willReturn(new TextLineValue('Some name'));

        $contentCreateStruct = new ContentCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($contentCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMockForAbstractClass();

        $propertyPath->expects(self::once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isSynchronized')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isDisabled')
            ->willReturn(false);

        $form->expects(self::once())
            ->method('getData')
            ->willReturn('Some name');

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData([$form], $data);
    }

    public function testMapFormsToDataWithInvalidFieldDefinition()
    {
        $this->expectException(RuntimeException::class);

        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => [
                    new FieldDefinition(
                        [
                            'id' => 'id',
                            'identifier' => 'test',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new Value('Some name'),
                        ]
                    ),
                ],
            ]
        );

        $contentCreateStruct = new ContentCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($contentCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMockForAbstractClass();

        $propertyPath->expects(self::once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isSynchronized')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isDisabled')
            ->willReturn(false);

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData([$form], $data);
    }

    public function testMapFormsToDataWhenDataIsNull()
    {
        $data = null;
        $this->mapper->mapFormsToData([], $data);
    }

    public function testMapFormsToDataWhenDataIsNotDataWrapper()
    {
        $data = new \stdClass();

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMockForAbstractClass();

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isSynchronized')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isDisabled')
            ->willReturn(false);

        $form->expects(self::exactly(3))
            ->method('getData')
            ->willReturn('Some name');

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData([$form], $data);
    }

    public function testMapFormsToDataWhenDataIsNotDataWrapperSecondCase()
    {
        $data = new \stdClass();
        $date = new \DateTime();

        $this->propertyAccessor->expects(self::once())
            ->method('getValue')
            ->willReturn($date);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMockForAbstractClass();

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isSynchronized')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isDisabled')
            ->willReturn(false);

        $form->expects(self::exactly(2))
            ->method('getData')
            ->willReturn($date);

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData([$form], $data);
    }

    public function testMapFormsToDataUnexpectedData()
    {
        $this->expectException(UnexpectedTypeException::class);

        $someNumber = 42;

        $this->mapper->mapFormsToData([], $someNumber);
    }

    public function testMapFormsToDataWhenFormIsNotSynchronized()
    {
        $data = new \stdClass();

        $this->propertyAccessor->expects(self::never())
            ->method('getValue');

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMockForAbstractClass();

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isSynchronized')
            ->willReturn(false);

        $form->expects(self::never())
            ->method('isDisabled');

        $form->expects(self::never())
            ->method('getData');

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData([$form], $data);
    }

    public function testMapFormsToDataWithDataSameAsValueInData()
    {
        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => [
                    new FieldDefinition(
                        [
                            'id' => 'id',
                            'identifier' => 'name',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new Value('Some name'),
                        ]
                    ),
                ],
            ]
        );

        $this->registry->expects(self::any())
            ->method('get')
            ->with('eztext')
            ->willReturn($this->handler);

        $this->handler->expects(self::never())
            ->method('convertFieldValueFromForm')
            ->willReturn(new TextLineValue('Some name'));

        $contentCreateStruct = new ContentCreateStruct(['contentType' => $contentType]);

        $data = new \stdClass();

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMapped', 'getByReference'])
            ->getMock();

        $config->expects(self::once())
            ->method('getByReference')
            ->willReturn(true);

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMockForAbstractClass();

        $propertyPath->expects(self::never())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isSynchronized')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isDisabled')
            ->willReturn(false);

        $form->expects(self::any())
            ->method('getData')
            ->willReturn($data);

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->propertyAccessor->expects(self::once())
            ->method('getValue')
            ->willReturn($data);

        $this->mapper->mapFormsToData([$form], $data);
    }

    private function getForm()
    {
        return $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods([
                'getData',
                'setData',
                'getPropertyPath',
                'getConfig',
                'isSubmitted',
                'isSynchronized',
                'isDisabled',
            ])
            ->getMock();
    }
}
