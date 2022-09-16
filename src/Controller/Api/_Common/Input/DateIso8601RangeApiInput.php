<?php

namespace App\Controller\Api\_Common\Input;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;

class DateIso8601RangeApiInput extends AbstractType
{
    public const START_DATE = 'startDateISO8601';
    public const END_DATE = 'endDateISO8601';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::START_DATE, DateType::class, ['widget' => 'single_text'])
            ->add(self::END_DATE, DateType::class, ['widget' => 'single_text']);
    }
}
