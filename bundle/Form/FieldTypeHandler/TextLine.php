<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class TextLine extends FieldTypeHandler
{

    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): string
    {
        return $value->text;
    }

    public function convertFieldValueFromForm($data): TextLineValue
    {
        if (empty($data)) {
            $data = '';
        }

        return new TextLineValue($data);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        if (!empty($fieldDefinition->validatorConfiguration['StringLengthValidator'])) {
            $lengthConstraints = [];

            $minStringLength = $fieldDefinition->validatorConfiguration['StringLengthValidator']['minStringLength'];
            $maxStringLength = $fieldDefinition->validatorConfiguration['StringLengthValidator']['maxStringLength'];

            if (!empty($minStringLength)) {
                $lengthConstraints['min'] = $minStringLength;
            }

            if (!empty($maxStringLength)) {
                $lengthConstraints['max'] = $maxStringLength;
            }

            if (!empty($lengthConstraints)) {
                $options['constraints'][] = new Constraints\Length($lengthConstraints);
            }
        }

        $formBuilder->add($fieldDefinition->identifier, TextType::class, $options);
    }
}
