# Type-safe, test-friendly, and extendable DTO

![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue?style=flat-square&logo=php)
![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)
![Repo size](https://img.shields.io/github/repo-size/technoquill/dto-core?style=flat-square)

**dto-core** is a lightweight, typed, and flexible base layer for working with Data Transfer Objects (DTOs) in PHP 8.2+.  
It supports strict/lenient modes, nested DTOs, lazy evaluation via `Closure`, and full debug output.

---

## 🚀 Features

- 🔒 Strict or lenient validation modes
- 🧱 Full support for nested DTOs (recursive)
- 🧪 Property type validation and class-level error tracking
- ⚡ `make()` method (recommended) for flexible and different modes using, or `new DTOClass()` for strict using
- 🛠️ `toArray()` method converts the current object instance into an array representation
- 🧩 `Closure` support for lazy-loaded properties
- 🔧 `debug()` method with full structural output
- 📤 Framework-agnostic — works with pure PHP classes
- 🔩 Optimized for PHP 8.2+
- ⚙️ Compatible with Symfony 6.4, Laravel 10+, and modern typed PHP codebases.

---

## 🧬 Requirements

- PHP **8.2 or higher**
- [symfony/var-dumper](https://packagist.org/packages/symfony/var-dumper)

---

## 🛠 Installation

```bash
composer require technoquill/dto-core
```

---

## ⚙️ Quick Example

```php
final class PaymentDTO extends AbstractDTO
{
    public function __construct(
        public int $id,
        public string $type,
        public float $amount,
        public string $name,
        public string $email,
        public string $phone,
        public string $annotation,
        public bool $is_paid
    ) {}
}

// Create via array
$data = [
    'id' => 1,
    'type' => 'paypal',
    'amount' => 100.55,
    'name' => 'John',
    'email' => 'john@example.com',
    'phone' => '123456789',
    'annotation' => fn() => strip_tags('<p>Note</p>'),
    'is_paid' => true
];

$payment = PaymentDTO::make($data, strict: true)->debug();

if (!$payment->isValid()) {
    print_r($payment->getErrors());
}
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

$order = OrderDTO::make([
    'id' => 10,
    'payment' => $data
], false);
```

---

## 📚 Project Structure

```
src/
├── AbstractDTO.php
├── Contracts/
│   └── DTOInterface.php
├── Traits/
│   ├── DTOTrait.php
│   └── DebuggableTrait.php
```

---

## 🔖 License

This package is released under the MIT license.  
© [technoquill](https://github.com/technoquill)

