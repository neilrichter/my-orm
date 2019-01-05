# Update

Object edition.

The same way you insert an object in the database, phpstORM will detect if the Object has already been saved or not, if yes it will just update the object, otherwise it will insert the object in the Database.

Example:
```php
use App\Entities\Kebab;

$kebab = $phpstORM->new(Kebab::class);

$kebab->name = 'The Creation';
$kebab->oignon = true; // Hey, of course, onions !
$kebab->tomate = false;
$kebab->salade = true;
$kebab->save();

// Edit the same object
$kebab->salade = false;
$kebab->save();
```

We will still consider you intend to fill all the required fields for the object, otherwise, PDO will throw an Exception anyway.

This will output:

```php
// Nothing, but don't worry it is edited
```

