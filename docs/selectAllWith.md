# selectAllWith

```php
/**
 * @param QueryBuilder|Array $mixed Querybuilder created by the user or an associative array colum => value
 */
public function selectAllWith($mixed): Array
```

Retrieves all objects from a class where conditions matches the given data. Parameter can either be an associative array where keys are columns and value are the strictly equal value we are looking for in the database, either a DBAL query builder.

Example (Kebabs without onions):
```php
use App\Entities\Kebab;

$kebab = $phpstORM->new(Kebab::class);

$kebabQB = $kebab->getQueryBuilder();
$kebabQB
    ->select('*')
    ->where('oignon = :oignon')
    ->setParameter(':oignon', false);
var_dump($kebab->selectAllWith($kebabQB));

```

This will output:

```php
array (size=1)
  0 => 
    object(App\Entities\Kebab)[13]
      public 'id' => int 66
      public 'name' => string 'Le faux (sans oignons)' (length=22)
      public 'salade' => boolean true
      public 'tomate' => boolean true
      public 'oignon' => boolean false
```

Now with an associative Array (those with onions):

```php
var_dump($kebab->selectAllWith([
    'oignon' => true,
]));
```

```php
array (size=5)
  0 => 
    object(App\Entities\Kebab)[13]
      public 'id' => int 3
      public 'name' => string 'La tortilla' (length=11)
      public 'salade' => boolean false
      public 'tomate' => boolean false
      public 'oignon' => boolean true
  1 => 
    object(App\Entities\Kebab)[15]
      public 'id' => int 59
      public 'name' => string 'Le test' (length=7)
      public 'salade' => boolean false
      public 'tomate' => boolean false
      public 'oignon' => boolean true
  2 => 
    object(App\Entities\Kebab)[17]
      public 'id' => int 62
      public 'name' => string 'L\'original' (length=11)
      public 'salade' => boolean true
      public 'tomate' => boolean true
      public 'oignon' => boolean true
  3 => 
    object(App\Entities\Kebab)[19]
      public 'id' => int 63
      public 'name' => string 'Le bun' (length=6)
      public 'salade' => boolean false
      public 'tomate' => boolean true
      public 'oignon' => boolean true
  4 => 
    object(App\Entities\Kebab)[21]
      public 'id' => int 65
      public 'name' => string 'Chicken tandoori' (length=16)
      public 'salade' => boolean true
      public 'tomate' => boolean false
      public 'oignon' => boolean true
```

