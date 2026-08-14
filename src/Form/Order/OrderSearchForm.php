<?php

namespace App\Form\Order;

use App\Form\Core\DefaultForm\SearchForm;
use App\Entity\Client\Client;
use App\Entity\Warehouse\Warehouse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OrderSearchForm extends SearchForm
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
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'name',
                'required' => false,
                'label' => 'client',
                'placeholder' => 'allClients',
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
            ])
            ->add('orderDateFrom', DateType::class, [
                'required' => false,
                'label' => 'orderDateFrom',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['data-datepicker' => '{}'],
                'input' => 'datetime_immutable',
            ])
            ->add('orderDateTo', DateType::class, [
                'required' => false,
                'label' => 'orderDateTo',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['data-datepicker' => '{}'],
                'input' => 'datetime_immutable',
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
        $resolver->setDefault('translation_domain', 'order');
    }
}
