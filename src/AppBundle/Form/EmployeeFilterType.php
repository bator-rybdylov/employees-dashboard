<?php

namespace AppBundle\Form;

use AppBundle\ValueObject\EmployeeFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('departmentName', TextType::class, array(
                'required' => false,
            ))
            ->add('hireDate', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
            ))
            ->add('retireDate', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
            ))
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => EmployeeFilter::class,
        ));
    }

    public function getName()
    {
        return 'employee_filter';
    }
}