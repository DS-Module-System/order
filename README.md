# Order

Продажбени поръчки към клиент с редове от складова наличност. Количеството се валидира спрямо склада и се записват движения.

## Функционалност

- CRUD на поръчки (клиент, склад, дати, описание)
- CRUD на редове (продукт, наличност, количество, цена)
- Автоматично изчисляване на обща цена
- Проследяване на складови движения (`OrderStockMovement`)
- Търсене по клиент, склад и дати

## Интеграция в системата

Copy-in модул: файловете се копират в хоста под `App\`.

- Пътища: `src/Controller|Entity|Enum|Form|Repository|Service/Order/`, `templates/order/`, `templates/order_item/`, `translations/order*.yaml`, `config/roles/order.yaml`
- Меню: Поръчки (`order_list`) при `ROLE_ORDER_VIEW`
- Роли: `ROLE_ORDER_{VIEW,CREATE,EDIT,DELETE}`
- Маршрути: `/order`, `/order-items/{orderId}`

## Структура

- `OrderController`, `OrderItemController`
- Ентитети: `Order`, `OrderItem`, `OrderStockMovement`
- `OrderService`, `OrderStockService`
- Enum: `OrderMovementType`

## Зависимости

- **erp-core**
- **client**
- **product**
- **warehouse** (`Warehouse`, `WarehouseStock`)

## Документация

- [docs/order/README.md](docs/order/README.md)
