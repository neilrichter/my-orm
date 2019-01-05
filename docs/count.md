# Count

```php
public function count(): Int
```

Counts the number of Objects of a class in the database.

Example:
```php
use App\Entities\Kebab;

$kebab = $phpstORM->new(Kebab::class);
var_dump($kebab->count());
```

This will output:

```php
5
```

