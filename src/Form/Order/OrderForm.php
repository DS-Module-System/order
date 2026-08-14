<?php

namespace App\Form\Order;

use App\Entity\Order\Order;
use App\Entity\Client\Client;
use App\Entity\Warehouse\Warehouse;
use App\Form\Core\DefaultForm\EditForm;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderForm extends EditForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('orderDate', DateType::class, [
                'label' => 'orderDate',
                'required' => true,
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['data-datepicker' => '{}'],
                'input' => 'datetime_immutable',
                'empty_data' => '0000-00-00',
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'name',
                'label' => 'client',
                'constraints' => [
                    new NotBlank(),
                ],
                'required' => true,
                'placeholder' => 'customerSelect',
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'description',
                'constraints' => [
                    new Length([
                        'max' => 1000,
                    ]),
                ],
                'required' => false,
                'attr' => [
                    'rows' => 3,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'translation_domain' => 'order',
        ]);
    }
}
