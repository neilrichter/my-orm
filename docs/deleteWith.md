# deleteWith

```php
public function deleteWith(Array $values): void
```

Objects deletion.

Deletes all objects according to the associative array where keys are columns and value are the strictly equal value we wish to delete from the database

Example:
```php
use App\Entities\Kebab;

$kebab = $phpstORM->new(Kebab::class);

$kebab->deleteWith(['oignon' => false]); // Kebabs without oignons are not kebabs
```

This will output:

```php
// Nothing, but don't worry it is deleted
```

