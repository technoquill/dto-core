# Type-safe, test-friendly, and extendable DTO

![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue?style=flat-square&logo=php)
![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)
![Repo size](https://img.shields.io/github/repo-size/technoquill/dto-core?style=flat-square)

**dto-core** is a lightweight, typed, and flexible base layer for working with Data Transfer Objects (DTOs) in PHP 8.1+.  
It supports strict/lenient modes, nested DTOs, lazy evaluation via `Closure`, and full debug output.

---

## 🚀 Features

- 🔒 Strict or lenient validation modes
- 🧱 Full support for nested DTOs (recursive)
- 🧪 Property type validation and class-level error tracking
- ⚡ `make()` method (recommended) for flexible and different modes using, or `new DTOClass()` for strict using
- 📚 `toArray()` method converts the current object instance into an array representation
- 🧩 `Closure` support for lazy-loaded properties
- 🔧 `debug()` method with full structural output
- 📤 Framework-agnostic — works with pure PHP classes
- 🔩 Optimized for PHP 8.1+
- ⚙️ Compatible with Symfony 6.4, Laravel 10+, and modern typed PHP codebases.

---

## 🧬 Requirements

- PHP **8.1 or higher**
- [symfony/var-dumper](https://packagist.org/packages/symfony/var-dumper)

---

## 🛠 Installation

Add to composer.json
```json
"repositories": [
  {
    "type": "vcs",
    "url": "https://github.com/technoquill/dto-core"
  }
]
```
```bash
composer require technoquill/dto-core
```

---

## ⚙️ Quick Example

```php

final class UserDTO extends AbstractDTO
{
    public function __construct(
        public int    $id,
        public string $type,
        public string $first_name,
        public string $last_name,
        public string $email,
        public string $phone,
        public array  $address = [], // for ex. relation
        public string $annotation,
        public bool   $blocked,
        public string $created_at
    )
    {}
}

final class UserAddressDTO extends AbstractDTO
{
    public function __construct(
        public string  $street,
        public string  $city,
        public string  $postalCode,
        public string  $country,
        public ?string $state = null,
        public ?string $houseNumber = null,
        public ?string $apartment = null
    )
    {}
}

// Create via array
$user = UserDTO::make([
    'id' => 435,
    'type' => 'manager',
    'first_name' => 'John',
    'last_name' => 'Smith',
    'email' => 'john@example.com',
    'phone' => '123456789',
    'address' => UserAddressDTO::make([
        'street' => '123 Main St',
        'city' => 'New York',
        'postalCode' => '10001',
        'country' => 'USA',
        //'state' => 'NY', // optional; dto makes it nullable
        'houseNumber' => '123',
        'apartment' => '123A',
    ])->toArray(),
    'annotation' => static fn() => strip_tags('<p>Some annotation</p>'),
    'blocked' => false,
    'created_at' => '2022-10-03 22:59:52'
])->toArray();

dd($user);

// or
$dtoData = PaymentDTO::make($data, false);
if (!$dtoData->isValid()) {
    dump($dtoData->getErrors());
}

// Data output to an array is supported
$payment = PaymentDTO::make($data)->toArray();

```

---

## 🧩 Nested DTO Support

```php
final class OrderDTO extends AbstractDTO {
    public function __construct(
        public int $id,
        public PaymentDTO $payment
    ) {}
}
final class PaymentDTO extends AbstractDTO {
    public function __construct(
        public int $id,
        public float $amount
    ) {}
}

$order = OrderDTO::make([
    'id' => 10,
    'payment' => PaymentDTO::make([
        'id' => 435,
        'amount' => 765.35,
    ])
]);

```

---
## ✒️ Constructor-Based Instantiation

DTOs can also be instantiated directly via the constructor (including nested DTOs), but all values must be fully typed and pre-resolved.

> **Note:**  
> Always works in strict mode
> 
```php
$payment = (new PaymentDTO(435, 765.35))->toArray();
```

---

## 📚 Project Structure

```
src/
├── AbstractDTO.php
├── Contracts/
│   └── DTOInterface.php
├── Support/
│   └── LoggerContext.php
├── Traits/
│   ├── DebuggableTrait.php
│   └── DTOTrait.php
```

---

## 🔖 License

This package is released under the MIT license.  
© [technoquill](https://github.com/technoquill)

