# Delete

Object deletion.

Deletes the current object from the database. If the object has not been inserted yet, this will throw an Exception.

Example:
```php
use App\Entities\Kebab;

$kebab = $phpstORM->new(Kebab::class);

$kebab->name = 'The Creation';
$kebab->oignon = true; // Hey, of course, onions !
$kebab->tomate = false;
$kebab->salade = true;
$kebab->save();

// Delete the same object
$kebab->delete(); // Created and deleted, won't appear in DB
```

This will output:

```php
// Nothing, but don't worry it is deleted
```

