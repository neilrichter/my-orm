# getById

```php
public function getById(int $id): self
```

Retrieves an object in the database with a given id.

Example:
```php
use App\Entities\Kebab;

$kebab = $phpstORM->new(Kebab::class);
var_dump($kebab->getById(3));
```

This will output:

```php
object(App\Entities\Kebab)[13]
  public 'id' => int 3
  public 'name' => string 'La tortilla' (length=11)
  public 'salade' => boolean false
  public 'tomate' => boolean false
  public 'oignon' => boolean true
```