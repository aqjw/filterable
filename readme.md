
# Filterable 📝  
The package for creating and managing filters for eloquent models.
Its allows you to easily scale the filters and easily apply them to filter records.

## Get Started 🚀  
... 


## Usage

Add the trait `HasFilters` to your model;

<hr>

Crate a filter using the command:
```
php partisan filter:make ByName

// specify a column
php partisan filter:make ByName full_name

// specify a model
php partisan filter:make ByName --model=User
```

<hr>


```php
User::filters([
    \App\Filters\ByName::class,
    \App\Filters\ByEmail::class,
])->get();
```