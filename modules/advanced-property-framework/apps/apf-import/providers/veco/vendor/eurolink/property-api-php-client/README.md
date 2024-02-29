# Property API Client

A PHP client for Eurolink's Property API.

## Setup

```
composer require eurolink/property-api-php-client
```

## Usage

#### GET Properties

```php
<?php
    require 'vendor/autoload.php';

    use PropertyAPI\Client;

    $client = new Client([
        'accessToken' => '~~INSERT_ACCESS_TOKEN~~',
    ]);

    // additional GET parameters can be passed in (see example below)
    $collection = $client->getProperties([
        'size' => 5,
    ]);

    // returns total number of properties matching search criteria
    var_dump($collection->getTotal());

    // returns number of properties returned in this collection
    var_dump($collection->getCount());

    // returns array of objects
    var_dump($collection->getRows());

    // returns array of '\PropertyAPI\Property'
    var_dump($collection->getParsedRows());
?>
```

You can pass any of the allowed parameters into the “getProperties()” method. Please refer to the main Property API documentation for the full list.

```php
<?php
    $collection = $client->getProperties([
        'size' => 5,
        'town' => 'London',
    ]);
?>
```

#### GET Property

```php
<?php
    require 'vendor/autoload.php';

    use PropertyAPI\Client;

    $client = new Client([
        'accessToken' => '~~INSERT_ACCESS_TOKEN~~',
    ]);

    // gets property by property ID
    $property = $client->getProperty(123456);

    // verify that property have successfully been returned
    var_dump($property->isValid());

    // test request by displaying Property ID (UUID)
    var_dump($property->getPropertyID());
?>
```

## Testing

#### Automatic
Paste your API Access Token into the root `.env` file. See `.env.example`.

```bash
./vendor/bin/phpunit tests/
```

#### Manual
Paste your API Access Token into the root `.env` file. See `.env.example`.

```php
php example/simple.php
// or
php example/complex.php
```
