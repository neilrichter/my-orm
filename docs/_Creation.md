# Creation

Object creation and insertion in the database.

Example:
```php
use App\Entities\Kebab;

$kebab = $phpstORM->new(Kebab::class);

$kebab->name = 'The Creation';
$kebab->oignon = true; // Hey, of course, onions !
$kebab->tomate = false;
$kebab->salade = true;
$kebab->save();
```

We will consider you intend to fill all the required fields for the object, otherwise, PDO will throw an Exception anyway.

This will output:

```php
// Nothing, but don't worry it is saved
```

