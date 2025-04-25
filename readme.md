# Laravel Stock Management – Technical Assessment

This Laravel project manages product orders and updates ingredient stock based on consumption. It also sends an email
alert when an ingredient's stock drops below 50% of its full capacity.

## Features

- Create orders that consume ingredients based on grams per product
- Update ingredient stock automatically after each order
- Send a one-time email alert when stock drops below 50%
- Fully tested with feature tests

## Setup Instructions (Docker)

1. run the containers & install project
```bash
docker compose up -d
docker compose exec -it cp .env.example .env 
docker compose exec -it isnad_backend composer install
docker compose exec -it isnad_backend php artisan key:generate 
```

2. Run migrations and seeders inside the app container
```bash
docker compose exec -it isnad_backend php artisan migrate --seed
```

3. Run tests
```bash
docker compose exec -it isnad_backend 
```

## Setup Instructions (Local Machine)

1. Install dependencies
```bash
composer install
```

2. Create .env and generate key

```bash
cp .env.example .env
php artisan key:generate
```

3. Configure .env for database and mail

4. Run migrations and seeders

```bash
php artisan migrate --seed
```

5. Run the test suite

```bash
 php artisan test
 ```

## Database Structure

Tables:

- ingredients
- ingredient_stocks (**used for stock history**)
- products
- orders

Pivot Tables:

- ingredient_product
- order_product

## Tests Coverage

- Order creation
- Ingredient stock updates
- Email alerts when stock drops below 50%
- Prevent duplicate email alerts
- Error handling (invalid product ID, insufficient stock)
- Pivot quantity validation
- Edge Cases

## Email Alerts

- Email is sent using a Mailable class [LowStockAlert](code/app/Mail/LowStockAlert.php).
- Triggered only once when an ingredient's stock falls below 50% of its maximum capacity.
- Email view located at: [emails.low_stock_alert_email](code/resources/views/emails/low_stock_alert_email.blade.php)

## Design Notes

- `max_capacity_in_grams` field is used to determine the 50% threshold.
- `alert_sent_at` is used to prevent sending multiple emails for the same ingredient.
- when implement increase stock logic we should reset `alert_sent_at` to null to allow sending alerts again.
- Stock logic and email triggering are handled with events and listeners inside the order placement process.

## Endpoint
postman collection available for import [here](
https://api.postman.com/collections/403705-37f54099-efe0-4273-83fd-777e44a5ec54?access_key=PMAT-01JSQ2ZP5AKQ3Z03WP2Z8XAW6N)

```http request
POST http://localhost/api/v1/orders
```
```json 
{
  "products": [
    {
    "product_id": 1,
    "quantity": 2
    }
  ]
}
```

## Technologies Used

- Laravel 12
- PHP 8.4
- PHPUnit for Testing
- Docker & Docker compose

## License
This project was built solely for technical assessment purposes.