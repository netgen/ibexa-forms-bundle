<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Tests\Form\Type\FieldType;

use Netgen\Bundle\IbexaFormsBundle\Form\Type\FieldType\UserUpdateType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints;

final class UserUpdateTypeTest extends TestCase
{
    public function testItExtendsAbstractType(): void
    {
        $updateUserType = new UserUpdateType();
        self::assertInstanceOf(AbstractType::class, $updateUserType);
    }

    public function testBuildForm(): void
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['add'])
            ->getMock();

        $emailOptions = [
            'label' => 'E-mail address',
            'constraints' => [
                new Constraints\NotBlank(),
                new Constraints\Email(),
            ],
        ];

        $passwordOptions = [
            'type' => PasswordType::class,
            'required' => false,
            'invalid_message' => 'Both passwords must match.',
            'options' => [
                'constraints' => [],
            ],
            'first_options' => [
                'label' => 'New password (leave empty to keep current password)',
            ],
            'second_options' => [
                'label' => 'Repeat new password',
            ],
        ];

        $formBuilder->expects(self::at(0))
            ->method('add')
            ->willReturn($formBuilder)
            ->with('email', EmailType::class, $emailOptions);

        $formBuilder->expects(self::at(1))
            ->method('add')
            ->with('password', RepeatedType::class, $passwordOptions);

        $userCreateType = new UserUpdateType();
        $userCreateType->buildForm($formBuilder, []);
    }
}
