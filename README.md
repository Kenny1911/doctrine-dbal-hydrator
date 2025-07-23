# Doctrine DBAL Hydrator

\[ English | [Русский](./README-RU.md) \]

**`kenny1911/doctrine-dbal-hydrator`** is a PHP library for easy conversion of SQL query results (Doctrine DBAL) into typed DTOs (Data Transfer Objects) with support for custom mappings, enums, and Doctrine type handling.

## 🔥 Features

- **Flexible mapping** via PHP attributes (`#[Column]`)
- **Doctrine DBAL type support** (`Types::DATETIME_IMMUTABLE`, `Types::JSON`, etc.)
- **Automatic type conversion** (including enums)
- **Custom field name mapping** (`name: 'login' → $username`)
- **Seamless integration** with existing `Doctrine\DBAL\Connection`

## 📦 Installation

```bash
composer require kenny1911/doctrine-dbal-hydrator
```  

## 🚀 Basic Usage

### 1. Define a DTO

```php
use Doctrine\DBAL\Types\Types;
use Kenny1911\DoctrineDbalHydrator\Mapping\Attribute\Column;

final readonly class User
{
    public function __construct(
        #[Column]
        public string $id,
        #[Column(name: 'login')]  // Maps SQL column `login` to property `username`
        public string $username,
        #[Column]
        public Role $role,       // Enum support
        #[Column(type: Types::DATETIME_IMMUTABLE)]
        public \DateTimeImmutable $registeredAt,
    ) {}
}
```  

### 2. Configure the Hydrator

```php
use Kenny1911\DoctrineDbalHydrator\Hydrator;
use Kenny1911\DoctrineDbalHydrator\ObjectHydrator;
use Kenny1911\DoctrineDbalHydrator\Mapping\AttributeLoader;

/** @var \Doctrine\DBAL\Connection $conn */
$hydrator = new Hydrator(
    objectHydrator: ObjectHydrator::createByConnection($conn),
    loader: new AttributeLoader(),
);
```  

### 3. Hydrate Data

```php
$data = $conn->fetchAssociative(
    query: 'SELECT * FROM users WHERE id = :id',
    params: [
        'id' => 'f4117ad3-5914-493d-90e1-4371832e82f4'
    ],
);

$user = $hydrator->hydrate(User::class, $data);
```  

## 📌 Key Notes

- **No ORM required** — works directly with DBAL.
- **Immutability** — supports `readonly` DTOs.
- **Lightweight** — minimal dependencies.

## 📜 License

MIT
