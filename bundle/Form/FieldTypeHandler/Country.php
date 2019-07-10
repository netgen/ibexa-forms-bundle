<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Country\Value as CountryValue;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class Country extends FieldTypeHandler
{
    /**
     * Country codes.
     *
     * @var array
     */
    protected $countryData;

    /**
     * Removed redundant data from array.
     *
     * @var array
     */
    protected $filteredCountryData;

    public function __construct(array $countryData)
    {
        $this->countryData = $countryData;

        foreach ($countryData as $countryCode => $country) {
            $this->filteredCountryData[$countryCode] = $country['Name'];
        }
    }

    /**
     * @return array|string
     */
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null)
    {
        $isMultiple = true;
        if ($fieldDefinition !== null) {
            $fieldSettings = $fieldDefinition->getFieldSettings();
            $isMultiple = $fieldSettings['isMultiple'];
        }

        if (!$isMultiple) {
            if (empty($value->countries)) {
                return '';
            }

            $keys = array_keys($value->countries);

            return reset($keys);
        }

        return array_keys($value->countries);
    }

    public function convertFieldValueFromForm($data): CountryValue
    {
        $country = [];

        // case if multiple is true
        if (is_array($data)) {
            foreach ($data as $countryCode) {
                if (array_key_exists($countryCode, $this->countryData)) {
                    $country[$countryCode] = $this->countryData[$countryCode];
                }
            }
        } elseif (array_key_exists($data, $this->countryData)) {
            $country[$data] = $this->countryData[$data];
        }

        return new CountryValue($country);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $options['expanded'] = false;
        $options['multiple'] = $fieldDefinition->getFieldSettings()['isMultiple'];

        $options['choices'] = array_flip($this->filteredCountryData);

        $formBuilder->add($fieldDefinition->identifier, ChoiceType::class, $options);
    }
}
