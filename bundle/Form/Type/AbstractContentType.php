<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Form\Type;

use Netgen\Bundle\IbexaFormsBundle\Form\FieldTypeHandlerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;

abstract class AbstractContentType extends AbstractType
{
    /**
     * @var \Netgen\Bundle\IbexaFormsBundle\Form\FieldTypeHandlerRegistry
     */
    protected $fieldTypeHandlerRegistry;

    /**
     * @var \Symfony\Component\Form\DataMapperInterface
     */
    protected $dataMapper;

    public function __construct(FieldTypeHandlerRegistry $fieldTypeHandlerRegistry, DataMapperInterface $dataMapper)
    {
        $this->fieldTypeHandlerRegistry = $fieldTypeHandlerRegistry;
        $this->dataMapper = $dataMapper;
    }
}
