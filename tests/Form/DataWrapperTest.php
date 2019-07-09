<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form;

use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use PHPUnit\Framework\TestCase;

class DataWrapperTest extends TestCase
{
    public function testSetValuesCorrectly()
    {
        $payload = new \stdClass();
        $payload->payload = 'payload';

        $definition = new \stdClass();
        $definition->definition = 'definition';

        $target = new \stdClass();
        $target->target = 'target';

        $dataWrapper = new DataWrapper($payload, $definition, $target);

        self::assertSame($payload, $dataWrapper->payload);
        self::assertSame($definition, $dataWrapper->definition);
        self::assertSame($target, $dataWrapper->target);
    }
}
