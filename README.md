
# Filterable 📝  

[![Latest Version on Packagist](https://img.shields.io/packagist/v/aqjw/filterable.svg?style=flat-square)](https://packagist.org/packages/aqjw/filterable)
[![Total Downloads](https://img.shields.io/packagist/dt/aqjw/filterable.svg?style=flat-square)](https://packagist.org/packages/aqjw/filterable)

The `Filterable` package is designed for Laravel 8.x+ and provides tools for creating and managing filters for Eloquent models.
It makes it easy to scale filters and apply them to filter records.


## Get Started 🚀  

### Installation

The package can be easily installed via Composer using the following command:

```bash
composer require aqjw/filterable
```

### Adding the `HasFilters` Trait
After installation, add the `HasFilters` trait to the model that you want to filter:

```php
use Aqjw\Filterable\HasFilters;

class Product
{
    use HasFilters;
```


## Usage

### Create a filter
To create a new filter, use the Artisan `make:filter` command:

```bash
php artisan make:filter ByPrice
```

You can also specify the column to be filtered:
```bash
php artisan make:filter ByPrice --column=retail_price
```

In addition, you can specify a group for the filter:
```bash
php artisan make:filter ByCategory --group=Product
```
The filter will then be created in the following group folder:
`\App\Filters\Product\ByCategory::class`

### Applying Filters
Once you have created your filters, you can apply them to your model:

```php
Product::filters([
    \App\Filters\ByPrice::class,
    \App\Filters\BySalePrice::class,
])->get();
```

You can use the `or` operator to apply multiple filters:
```php
Product::filters([
    \App\Filters\ByPrice::class,
    'or',
    \App\Filters\BySalePrice::class,
])->get();
```

If you need to group filters, you can wrap them in an array:
```php
Product::filters([
    [
        \App\Filters\ByCategory::class,
        \App\Filters\Product\ByPrice::class,
    ],
    'or',
    [
        \App\Filters\BySubCategory::class,
        \App\Filters\BySalePrice::class,
    ],
])->get();
```

### Checking Request Parameters with the `key` Method
The `key` method in the filter class is used to check if a certain key exists in the request parameters. If the key is not present, the filter will not be applied.

Here is an example of how the `key` method can be used:

```php
use Aqjw\Filterable\Filter;

class ByPrice extends Filter
{
    public function key()
    {
        // only apply the filter if 'price' is present in the request
        return 'price';
    }

    public function apply($query, $value)
    {
        $query->where('price', $value);
    }
}
```

In this example, the `ByPrice` filter will only be applied if the `price` key is present in the request parameters.

### Overriding the `isActive` Method
If you want to force a filter to be applied even if the corresponding key is not present in the request parameters, you can override the `isActive` method in the filter class.

Here is an example of how the `isActive` method can be used:

```php
use Aqjw\Filterable\Filter;

class ByCategory extends Filter
{
    public function key()
    {
        // only apply the filter if 'category' is present in the request
        return 'category';
    }

    public function isActive($request)
    {
        // always force the filter to be applied,
        //  even if 'category' is not present in the request.
        // this will be useful when we want to apply the filter by default,
        //  for example, when displaying all products in a certain category
        return true;
    }

    public function apply($query, $value)
    {
        if ($value) {
            // apply the filter based on the value of 'category' in the request
            $query->where('category', $value);
        } else {
            // if 'category' is not present in the request, default to showing products in the root category (category ID of 1)
            $query->where('category', 1);
        }
    }
}
```

In this example, the `ByCategory` filter will always be applied, even if the `category` key is not present in the request parameters. If the `category` key is present, the filter will be applied based on its value. If the `category` key is not present, the filter will default to showing products in the root category (category ID of 1).


## License

The `Filterable` package is open-sourced software licensed under the MIT License. Please see the [License File](/LICENSE) for more information.

## Contributing
Contributions are welcome! If you would like to contribute to this project, please follow these steps:

1. Fork the repository
2. Create a new branch (`git checkout -b feature/your-feature`)
3. Make your changes
4. Push your branch to your forked repository (`git push origin feature/your-feature`)
5. Open a pull request
