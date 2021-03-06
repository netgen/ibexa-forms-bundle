<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Tests\Form\Mock;

final class FileMock
{
    public function getRealPath(): string
    {
        return '/some/path';
    }

    public function getClientOriginalName(): string
    {
        return 'file_name';
    }

    public function getSize(): int
    {
        return 123;
    }
}
