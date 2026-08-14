<?php

namespace App\Form\Order;

use App\Entity\Order\OrderItem;
use App\Entity\Product\Product;
use App\Entity\Warehouse\Warehouse;
use App\Form\Core\DefaultForm\EditForm;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderItemForm extends EditForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'label' => 'product',
                'constraints' => [
                    new NotBlank(),
                ],
                'required' => true,
                'placeholder' => 'chooseProduct',
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('warehouse', EntityType::class, [
                'class' => Warehouse::class,
                'choice_label' => 'name',
                'label' => 'warehouse',
                'constraints' => [
                    new NotBlank(),
                ],
                'required' => true,
                'placeholder' => 'chooseWarehouse',
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'quantity',
                'scale' => 2,
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                ],
                'required' => true,
                'attr' => [
                    'placeholder' => '0.00',
                ],
            ])
            ->add('unitPrice', NumberType::class, [
                'label' => 'unitPrice',
                'scale' => 2,
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                ],
                'required' => true,
                'attr' => [
                    'placeholder' => '0.00',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrderItem::class,
            'translation_domain' => 'order_item',
        ]);
    }
}
