# existsWith

```php
/**
 * @param QueryBuilder|Array $mixed Querybuilder created by the user or an associative array colum => value
 */
public function existsWith($mixed): Bool
```

Checks if a value exists with the given data. Parameter can either be an associative array where keys are columns and value are the strictly equal value we are looking for in the database, either a DBAL query builder.

Example:
```php
use App\Entities\Kebab;

$kebab = $phpstORM->new(Kebab::class);
$kebabQB = $kebab->getQueryBuilder();

/* Checks if a kebab without tomatoes and without oignons exists in the database (Does it even exist ?) */
$kebabQB
    ->select('*')
    ->from($kebab->getClassName())
    ->where('tomate = :tomate')
    ->setParameter(':tomate', false)
    ->andWhere('oignon = :oignon')
    ->setParameter(':oignon', false);

var_dump($kebab->existsWith($kebabQB));
```

This will output:

```php
false // Of course it doesn't
```

Now with an associative Array:

```php
var_dump($kebab->existsWith([
    'tomate' => !true,
    'salade' => true,
    'oignon' => true,
]));
```

```php
true
```

