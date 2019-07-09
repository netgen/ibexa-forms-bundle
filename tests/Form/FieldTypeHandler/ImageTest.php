<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\Image\Value as ImageValue;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Image;
use Netgen\Bundle\EzFormsBundle\Tests\Form\Mock\FileMock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class ImageTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $image = new Image();

        self::assertInstanceOf(FieldTypeHandler::class, $image);
    }

    public function testConvertFieldValueToForm()
    {
        $image = new Image();
        $imageValue = new ImageValue(['fileName' => 'picture.jpeg']);

        $returnedValue = $image->convertFieldValueToForm($imageValue);

        self::assertNull($returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $image = new Image();
        $data = new FileMock();

        $returnedData = $image->convertFieldValueFromForm($data);

        self::assertInstanceOf(ImageValue::class, $returnedData);
    }

    public function testConvertFieldValueFromFormWhenDataIsNull()
    {
        $image = new Image();
        $returnedData = $image->convertFieldValueFromForm(null);

        self::assertNull($returnedData);
    }

    public function testBuildFieldCreateForm()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

        $formBuilder->expects(self::exactly(2))
            ->method('add');

        $fieldDefinition = new FieldDefinition(
            [
                'id' => 'id',
                'identifier' => 'identifier',
                'isRequired' => true,
                'descriptions' => ['fre-FR' => 'fre-FR'],
                'names' => ['fre-FR' => 'fre-FR'],
            ]
        );

        $languageCode = 'eng-GB';

        $image = new Image();
        $image->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
