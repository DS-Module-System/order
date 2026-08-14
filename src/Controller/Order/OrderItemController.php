<?php

namespace App\Controller\Order;

use App\Controller\Core\CoreBaseController;
use App\Entity\Order\Order;
use App\Entity\Order\OrderItem;
use App\Form\Order\OrderItemForm;
use App\Form\Order\OrderItemSearchForm;
use App\Service\Order\OrderStockService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/order-items/{orderId}', name: 'order_item_')]
class OrderItemController extends CoreBaseController
{

    protected string $entityClass = OrderItem::class;
    protected string $formClass = OrderItemForm::class;
    protected string $searchFormClass = OrderItemSearchForm::class;
    protected string $moduleTemplateName = 'order_item';

    #[Route(path: '', name: 'list')]
    #[IsGranted('ROLE_ORDER_VIEW')]
    public function list(Request $request, int $orderId): Response
    {
        $order = $this->em->getRepository(Order::class)->find($orderId);
        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        $this->additionalData['order'] = $order;
        $this->appendSearchFormData['orderId'] = $order->getId();

        return $this->baseList($request, $request->query->getInt('page', 1));
    }

    #[Route(path: '/create', name: 'create')]
    #[IsGranted('ROLE_ORDER_CREATE')]
    public function create(Request $request, int $orderId, OrderStockService $orderStockService): Response
    {
        $this->isModalRequest = true;

        $order = $this->em->getRepository(Order::class)->find($orderId);
        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        $this->additionalData['order'] = $order;
        $this->callbacks['setDefaultEntityData'] = function (OrderItem $item, array $additionalData) {
            $item->setOrder($additionalData['order']);
            $item->setCreatedBy($this->getUser());
            return $item;
        };

        $this->callbacks['postCreateFlush'] = function ($entity) use ($orderStockService) {
            // Изписваме продуктите от склада
            $orderStockService->removeProductsFromWarehouse($entity);

            // Преизчисляваме общата цена на поръчката
            $order = $entity->getOrder();
            $order->calculateTotalPrice();
            $this->em->persist($order);
            $this->em->flush();
        };

        $this->callbacks['postCreateResponse'] = function ($response, $entity) {
            // Добавяме JavaScript за обновяване на общата цена
            if ($response instanceof \Symfony\Component\HttpFoundation\JsonResponse) {
                $data = json_decode($response->getContent(), true);
                if ($data && isset($data['success']) && $data['success']) {
                    $data['updateOrderTotal'] = true;
                    $data['orderId'] = $entity->getOrder()->getId();
                    $response->setData($data);
                }
            }
            return $response;
        };

        $this->em->getConnection()->beginTransaction();
        try {
            $response = $this->baseCreate($request);
            $this->em->getConnection()->commit();
            return $response;
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    #[Route(path: '/{id}/edit', name: 'edit')]
    #[IsGranted('ROLE_ORDER_EDIT')]
    public function edit($id, Request $request, int $orderId): Response
    {
        $this->isModalRequest = true;

        $order = $this->em->getRepository(Order::class)->find($orderId);
        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        $this->additionalData['order'] = $order;

        $item = $this->em->getRepository(OrderItem::class)->find($id);
        if (!$item || $item->getOrder()->getId() !== $order->getId()) {
            throw $this->createNotFoundException();
        }

        $this->callbacks['preEditPersist'] = function ($entity) {
            $entity->setUpdatedAt(new \DateTimeImmutable());
            return $entity;
        };

        $this->callbacks['postEditFlush'] = function ($entity) {
            // Преизчисляваме общата цена на поръчката
            $order = $entity->getOrder();
            $order->calculateTotalPrice();
            $this->em->persist($order);
            $this->em->flush();
        };

        $this->callbacks['postEditResponse'] = function ($response, $entity) {
            // Добавяме JavaScript за обновяване на общата цена
            if ($response instanceof \Symfony\Component\HttpFoundation\JsonResponse) {
                $data = json_decode($response->getContent(), true);
                if ($data && isset($data['success']) && $data['success']) {
                    $data['updateOrderTotal'] = true;
                    $data['orderId'] = $entity->getOrder()->getId();
                    $response->setData($data);
                }
            }
            return $response;
        };

        $this->em->getConnection()->beginTransaction();
        try {
            $response = $this->baseEdit($request, $id);
            $this->em->getConnection()->commit();
            return $response;
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    #[Route(path: '/deletes', name: 'deletes')]
    #[IsGranted('ROLE_ORDER_DELETE')]
    public function deletes(Request $request, int $orderId): Response
    {
        $order = $this->em->getRepository(Order::class)->find($orderId);
        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        $this->callbacks['postDeletesFlush'] = function () use ($order) {
            // Преизчисляваме общата цена на поръчката след изтриване
            $order->calculateTotalPrice();
            $this->em->persist($order);
            $this->em->flush();
        };

        $this->callbacks['postDeletesResponse'] = function ($response) use ($order) {
            // Добавяме JavaScript за обновяване на общата цена
            if ($response instanceof \Symfony\Component\HttpFoundation\JsonResponse) {
                $data = json_decode($response->getContent(), true);
                if ($data && isset($data['success']) && $data['success']) {
                    $data['updateOrderTotal'] = true;
                    $data['orderId'] = $order->getId();
                    $response->setData($data);
                }
            }
            return $response;
        };

        $this->em->getConnection()->beginTransaction();
        try {
            $response = $this->baseDeletes($request);
            $this->em->getConnection()->commit();
            return $response;
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
