# Задача на postgres:

~~~
Есть таблица "orders", содержащая информацию о заказах в интернет-магазине, включая идентификатор заказа, дату заказа, идентификатор клиента, сумму заказа и статус заказа.

Требуется написать запрос, который выберет топ-3 клиентов по общей сумме всех их заказов, суммой заказов за последний месяц, с датой первого и последнего заказа.

Дополнительное требование к решению:
Используйте оконную функцию row_number для нумерации клиентов по общей сумме заказов.
~~~
### РЕШЕНИЕ
<!-- Топ-3 клиента по общей сумме всех их заказов: -->
```

WITH TOP_ORDERS as (SELECT id, created_at, client_id, status, SUM(amount_order) as amount_orders from orders GROUP BY client_id ORDER BY amount_orders DESC LIMIT 3) SELECT *, ROW_NUMBER() OVER () num FROM TOP_ORDERS;
//Топ-3 клиента с суммой заказов за последний месяц
WITH TOP_ORDERS as (SELECT id, created_at, client_id, status, SUM(amount_order) as amount_orders from orders where DATE(created_at) = CURRENT_DATE GROUP BY client_id ORDER BY amount_orders DESC LIMIT 3) SELECT *, ROW_NUMBER() OVER () num FROM TOP_ORDERS;
//Топ-3 клиента с датой первого и последнего заказа
WITH TOP_ORDERS as (SELECT id, created_at, client_id, status, SUM(amount_order) as amount_orders, MAX(created_at) as date_last_order, MIN(created_at) as date_first_order from orders GROUP BY client_id ORDER BY amount_orders DESC LIMIT 3) SELECT *, ROW_NUMBER() OVER () num FROM TOP_ORDERS;
```
