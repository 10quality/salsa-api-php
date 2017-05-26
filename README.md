# Salsa API (PHP)

[![Latest Stable Version](https://poser.pugx.org/10quality/salsa-api-php/v/stable)](https://packagist.org/packages/10quality/salsa-api-php)
[![Total Downloads](https://poser.pugx.org/10quality/salsa-api-php/downloads)](https://packagist.org/packages/10quality/salsa-api-php)
[![License](https://poser.pugx.org/10quality/salsa-api-php/license)](https://packagist.org/packages/10quality/salsa-api-php)

[Salsa](https://help.salsalabs.com/hc/en-us) API wrapper/handler for PHP.

## Scope

Current supporting end-points:
* Metrics
* Supporters

## Installation

With composer, make the dependecy required in your project:
```bash
composer require 10quality/salsa-api-php
```

## Usage

Initialize the API instance:
```php
use Salsa\Api;

$api = Api::instance([
    'token'     => '..YOUR-ACCESS-TOKEN..',
    'env'       => 'live', // For development change to 'sandbox'
    'sandbox'   => 'https://sandbox.salsalabs.com/' // Sandbox environment custom base URL.
]);
```

### Metrics endpoint

Next shows how to get metrics from Salsa:
```php
use Salsa\Metrics;

$endpoint = new Metrics($api);

// Retrieve metrics response
$response = $endpoint->get();

// A specific metric
echo $endpoint->get()->payload->totalAPICalls;
```

### Supporters endpoint

Next sample shows how to search for supporters:
```php
use Salsa\Supporters;

$endpoint = new Supporters($api);

// Search for supporters
$response = $endpoint->searchByEmail('an-email@domain.com');
$response = $endpoint->searchByEmails(['an-email@domain.com', 'second-email@domain.com']);

// Retrieve supporters from response
print_r($response->supporters);
```

Next sample shows how to create/update a new supporter:
```php
use Salsa\Supporters;
use Salsa\Models\Supporter;

// Define the endpoint
$endpoint = new Supporters($api);

// Create supporter using model
$supporter = new Supporter;
$supporter->email = 'an-email@domain.com';
$supporter->title = 'Mr';
$supporter->firstName = 'Alejandro';
$supporter->lastName = 'Mostajo';
$supporter->address = [
    'line1' => 'Alice in wonderland',
    'line2' => 'In the books',
    'city'  => 'San Diego',
    'state' => 'CA',
    'postalCode' => '99999',
    'county' => 'CA',
    'country' => 'US',
];
$supporter->dateOfBirth = '2017-01-01';
// Phones
$supporter->cellphone = '1234567890';
$supporter->workphone = '1234567890';
$supporter->homephone = '1234567890';
// Custom fields
$supporter->addCustomField(
    null, // Field ID
    'Nickname', // Field Name
    'Piru', // Value
    null // Type
);

// Add supporter
$response = $endpoint->update($supporter);

// Get updated supporters with their Salsa Supporter ID
$response->supporters;

// Add multiple supporters
$response = $endpoint->updateBatch([$supporter, $supporter2]);
```

**NOTE:** Supporter models must include property `supporterId` in order to be updated.

**NOTE:** Custom fields types: STRING, NUMBER, DATE, TIMESTAMP, BOOL.

Next sample shows how to delete an existint supporter:
```php
use Salsa\Supporters;
use Salsa\Models\Supporter;

// Define the endpoint
$endpoint = new Supporters($api);

// Create supporter using model
$supporter = new Supporter;
$supporter->supporterId = '-an-id-';

// Delete supporter
$response = $endpoint->delete($supporter);

// Delete multiple supporters
$response = $endpoint->deleteBatch([$supporter, $supporter2]);
```

## Coding guidelines

PSR-4.

## LICENSE

The MIT License (MIT)

Copyright (c) 2017 [10Quality](http://www.10quality.com).