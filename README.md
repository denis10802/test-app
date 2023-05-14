# Hа php + laravel:

1. создать новый проект. в нём создать контроллер с методом create (public function create(OfferCreateFormRequest $request): OfferDto), привязать к нему route "/offers/create", метод post. OfferCreateFormRequest расширяет класс FormRequest.

2. создать атрибут DtoRule, аргументы его конструктора:
```php
$min: int = null,
$max: int = null,
$date_format: string = null.
```
3. создать класс OfferDto:
```php
use Illuminate\Contracts\Support\Arrayable;

final readonly class OfferDto implements Arrayable {
    #[DtoRule(max: 255)]
    public string $title;
    #[DtoRule(min: 1000, max: 999999999)]
    public int $price;
    #[DtoRule(max: 4096)]
    public ?string $description;
    public bool $isActive;
    #[DtoRule(date_format: 'Y-m-d H:i:s')]
    public string $publishAt;

    ...
}
```
4. в методе rules класса OfferCreateFormRequest сгенерировать массив правил валидации на основе описания свойств класса OfferDto.
в итоге после генерации должен получиться массив:
```php
[
    'title' => ['required', 'string', 'max:255'],
    'price' => ['required', 'integer', 'min:1000', 'max:999999999'],
    'description' => ['nullable', 'string', 'max:4096'],
    'isActive' => ['required', 'boolean'],
    'publishAt' => ['required', 'date_format:Y-m-d H:i:s'],
]
```
5. в класс OfferCreateFormRequest добавить метод toDto(): OfferDto, в котором надо создать объект OfferDto из данных http-запроса. 

6. в методе create созданного контроллера просто возвращаем $request->toDto(). в итоге при запросе на созданный route будут возвращаться данные из запроса.

___

# Задача на postgres:


Есть таблица "orders", содержащая информацию о заказах в интернет-магазине, включая идентификатор заказа, дату заказа, идентификатор клиента, сумму заказа и статус заказа.

Требуется написать запрос, который выберет топ-3 клиентов по общей сумме всех их заказов, суммой заказов за последний месяц, с датой первого и последнего заказа.

Дополнительное требование к решению:
Используйте оконную функцию row_number для нумерации клиентов по общей сумме заказов.

### РЕШЕНИЕ
```sql
WITH customer_total_amount AS (
    SELECT customer_id, SUM(total_amount) as total_amount
    FROM orders
    GROUP BY customer_id
), last_month_orders AS (
    SELECT customer_id, SUM(total_amount) as total_amount_last_month
    FROM orders
    WHERE order_date >= NOW() - INTERVAL '1 MONTH'
    GROUP BY customer_id
), first_last_orders AS (
    SELECT customer_id, MIN(order_date) as first_order_date, MAX(order_date) as last_order_date
    FROM orders
    GROUP BY customer_id
)
SELECT customer_id, total_amount, total_amount_last_month, first_order_date, last_order_date
FROM (
         SELECT c.customer_id, c.total_amount, l.total_amount_last_month, fo.first_order_date, fo.last_order_date,
                ROW_NUMBER() OVER (ORDER BY c.total_amount DESC) as row_number
         FROM customer_total_amount c
                  JOIN last_month_orders l ON c.customer_id = l.customer_id
                  JOIN first_last_orders fo ON c.customer_id = fo.customer_id
     ) t
WHERE row_number <= 3
```

В этом запросе мы сначала находим общую сумму всех заказов для каждого клиента и сохраняем ее в CTE "customer_total_amount". Затем мы находим сумму заказов каждого из клиентов, сделанных за последний месяц, и сохраняем ее в CTE "last_month_orders". В третьем CTE "first_last_orders" мы находим дату первого и последнего заказа каждого из клиентов.

Затем мы объединяем все три CTE и используем оконную функцию row_number для нумерации клиентов по общей сумме заказов. Наконец, мы выбираем только топ-3 клиентов, используя условие WHERE row_number <= 3.

Этот запрос более компактен и более читаем, кроме того, мощность оконной функции row_number позволяет выводить дополнительную информацию, например номер топ-клиента в выборке, без доработки самого запроса.


