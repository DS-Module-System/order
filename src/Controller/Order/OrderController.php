<?php

namespace App\Controller\Order;

use App\Controller\Core\CoreBaseController;
use App\Entity\Order\Order;
use App\Form\Order\OrderForm;
use App\Form\Order\OrderSearchForm;
use App\Service\Order\OrderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/order', name: 'order_')]
class OrderController extends CoreBaseController
{
    

    protected string $entityClass = Order::class;
    protected string $formClass = OrderForm::class;
    protected string $searchFormClass = OrderSearchForm::class;
    protected string $moduleTemplateName = 'order';

    #[Route(path: '', name: 'list')]
    #[IsGranted('ROLE_ORDER_VIEW')]
    public function list(Request $request): Response
    {
        return $this->baseList($request, $request->query->getInt('page', 1));
    }

    #[Route(path: '/create', name: 'create')]
    #[IsGranted('ROLE_ORDER_CREATE')]
    public function create(Request $request): Response
    {

        $this->callbacks['preCreatePersist'] = function ($entity) {
            $entity->setCreatedBy($this->getUser());
            return $entity;
        };

        $this->callbacks['redirectAfterCreate'] = function ($entity) {
            return $this->redirectToRoute('order_edit', ['id' => $entity->getId()]);
        };

        return $this->baseCreate($request);
    }

    #[Route(path: '/{id}/edit', name: 'edit')]
    #[IsGranted('ROLE_ORDER_EDIT')]
    public function edit($id, Request $request): Response
    {
        $this->callbacks['preEditPersist'] = function ($entity) {
            $entity->setUpdatedAt(new \DateTimeImmutable());
            return $entity;
        };

        return $this->baseEdit($request, $id);
    }

    #[Route(path: '/deletes', name: 'deletes')]
    #[IsGranted('ROLE_ORDER_DELETE')]
    public function deletes(Request $request): Response
    {
        return $this->baseDeletes($request);
    }

    #[Route(path: '/{id}/total-price', name: 'total_price')]
    #[IsGranted('ROLE_ORDER_VIEW')]
    public function getTotalPrice(int $id): Response
    {
        $order = $this->em->getRepository(Order::class)->find($id);
        if (!$order) {
            return $this->json(['success' => false, 'message' => 'Order not found']);
        }

        return $this->json([
            'success' => true,
            'totalPrice' => $order->getTotalPrice()
        ]);
    }
}
