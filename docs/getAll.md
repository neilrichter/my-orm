# getAll

```php
public function getAll(): Array
```

Retrieves all objects from a class in the database.

Example:
```php
use App\Entities\Kebab;

$kebab = $phpstORM->new(Kebab::class);
var_dump($kebab->getAll());
```

This will output:

```php
array (size=x)
  0 => 
    object(App\Entities\Kebab)[12]
      public 'id' => int 3
      public 'name' => string 'La tortilla' (length=11)
      public 'salade' => boolean false
      public 'tomate' => boolean false
      public 'oignon' => boolean true
  1 => 
    object(App\Entities\Kebab)[14]
      public 'id' => int 59
      public 'name' => string 'Le test' (length=7)
      public 'salade' => boolean false
      public 'tomate' => boolean false
      public 'oignon' => boolean true
  2 => 
    object(App\Entities\Kebab)[16]
      public 'id' => int 62
      public 'name' => string 'L\'original' (length=10)
      public 'salade' => boolean true
      public 'tomate' => boolean true
      public 'oignon' => boolean true
  3 => 
    object(App\Entities\Kebab)[18]
      public 'id' => int 63
      public 'name' => string 'Le bun' (length=6)
      public 'salade' => boolean false
      public 'tomate' => boolean true
      public 'oignon' => boolean true
  4 => 
    object(App\Entities\Kebab)[20]
      public 'id' => int 64
      public 'name' => string 'Chicken tandoori' (length=16)
      public 'salade' => boolean true
      public 'tomate' => boolean false
      public 'oignon' => boolean true
  ... => 
    object(App\Entities\Kebab)[...]
```

