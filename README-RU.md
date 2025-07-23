# Doctrine DBAL Hydrator

\[ [English](./README.md) | Русский \]

**`kenny1911/doctrine-dbal-hydrator`** — это PHP-библиотека для удобного преобразования результатов SQL-запросов (Doctrine DBAL) в объекты DTO (Data Transfer Object) с поддержкой типизации, кастомных маппингов и работы с enum.

## 🔥 Возможности

- **Гибкий маппинг** через PHP-атрибуты (`#[Column]`)
- **Поддержка типов Doctrine DBAL** (`Types::DATETIME_IMMUTABLE`, `Types::JSON` и др.)
- **Автоматическое приведение типов** (включая enum)
- **Кастомизация имен полей** (`name: 'login' → $username`)
- **Простое подключение** к существующему `Doctrine\DBAL\Connection`

## 📦 Установка

```bash
composer require kenny1911/doctrine-dbal-hydrator
```  

## 🚀 Пример использования

### 1. Определяем DTO

```php
use Doctrine\DBAL\Types\Types;
use Kenny1911\DoctrineDbalHydrator\Mapping\Attribute\Column;

final readonly class User
{
    public function __construct(
        #[Column]
        public string $id,
        #[Column(name: 'login')]  // Маппинг SQL-поля `login` в свойство `username`
        public string $username,
        #[Column]
        public Role $role,       // Поддержка enum
        #[Column(type: Types::DATETIME_IMMUTABLE)]
        public \DateTimeImmutable $registeredAt,
    ) {}
}
```  

### 2. Настраиваем гидратор

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

### 3. Преобразуем данные

```php
$data = $conn->fetchAssociative(
    query: 'SELECT * FROM users WHERE id = :id',
    params: [
        'id' => 'f4117ad3-5914-493d-90e1-4371832e82f4'
    ],
);

$user = $hydrator->hydrate(User::class, $data);
```  

## 📌 Особенности

- **Не требует ORM** — работает напрямую с DBAL.
- **Иммутабельность** — DTO можно сделать `readonly`.
- **Лёгковесность** — минимум зависимостей.

## 📜 Лицензия

MIT
