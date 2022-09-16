<?php

namespace App\Controller\Api\Brent\Input;

use App\Controller\Api\_Common\Input\AbstractApiInput;
use App\Controller\Api\_Common\Input\DateIso8601RangeApiInput;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;

class BrentInput extends AbstractApiInput
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('method', EnumType::class, ['class' => BrentApiMethodsEnum::class])
            ->add('params', DateIso8601RangeApiInput::class);
    }
}
