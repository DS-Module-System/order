<?php

namespace App\Form\Order;

use App\Form\Core\DefaultForm\SearchForm;
use App\Entity\Product\Product;
use App\Entity\Warehouse\Warehouse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OrderItemSearchForm extends SearchForm
{
    private RequestStack $requestStack;
    private UrlGeneratorInterface $router;

    public function __construct(
        RequestStack $requestStack,
        UrlGeneratorInterface $router
    ) {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'required' => false,
                'label' => 'product',
                'placeholder' => 'allProducts',
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('warehouse', EntityType::class, [
                'class' => Warehouse::class,
                'choice_label' => 'name',
                'required' => false,
                'label' => 'warehouse',
                'placeholder' => 'allWarehouses',
                'attr' => [
                    'class' => 'select2',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $resolver->setDefault('action', $this->router->generate(
                $request->get('_route'),
                array_merge($request->get('_route_params'), ['page' => 1])
            ));
        }
        $resolver->setDefault('translation_domain', 'order_item');
    }
}
