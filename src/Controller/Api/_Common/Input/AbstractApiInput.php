<?php

namespace App\Controller\Api\_Common\Input;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractApiInput extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', IntegerType::class, [
                'constraints' => [
                    new Assert\Type('integer')
                ]
            ])
            ->add('jsonrpc', EnumType::class, ['class' => JsonRpcVersionsEnum::class]);
    }
}
