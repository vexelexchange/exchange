# Usage example

## Ping
```php
$api = new Vexel;
var_dump($api->query('ping'));
```
# Create order
```php
$api = new Vexel;
var_dump($api->query('order', ['pair' => 'btc_usd', 'amount' => 1, 'type' => 'buy', 'method' => 'limit', 'price' => 10000], 'POST', true));
```
