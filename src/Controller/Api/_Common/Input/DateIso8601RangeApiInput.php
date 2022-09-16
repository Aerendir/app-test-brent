<?php

namespace App\Controller\Api\_Common\Input;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;

class DateIso8601RangeApiInput extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDateISO8601', DateType::class)
            ->add('endDateISO8601', DateType::class);
    }
}
