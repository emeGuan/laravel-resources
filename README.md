# Laravel API Resources
Adds extra functionality to the Laravel Eloquent: API Resources, allowing you to choose attributes and relationships to return via url

## Introduction
When we create a JSON API from Laravel we can do it in many ways, the most used way (I think) is to create a Rest API. We can use the [json:api specification](https://jsonapi.org/) if we want or use one of the great libraries already available for Laravel such as:

* [Laravel Orion](https://github.com/tailflow/laravel-orion)
* [Laravel Restify](https://github.com/binarcode/laravel-restify)
* [Laravel JSON:API](https://github.com/laravel-json-api/laravel)

But it is possible that our API with the standard methods of a Rest API is not enough and we prefer to have extra parameters with which to perform different queries on the data before returning it in JSON format.  
In this case we can generate our queries with Eloquent and return the data with the *toJson* method.  
With the use of the [Laravel Eloquent: API Resources](https://laravel.com/docs/9.x/eloquent-resources) we can control which attributes and relationships we want to return for each model in a static way. With this small library that we have here we can modify the API Resources dynamically from parameters in the url.

## Instalation
Install using [Composer](https://getcomposer.org)

```bash
composer require emeguan/laravel-resources
```

## Requirements
I have not tested it but it should work with any version of Laravel that includes Eloquent API Resources, that is, Laravel 5.5 and later.  
The code has been tested with Laravel 9

## Use
### Model
```php
class A extends Model
{
    use HasFactory;
    use \EmeGuan\Resources\ModelTrait;  //<--
```

### Controller
```php
Route::get('/as', function ()
{
    $as=A::all();
    return new \EmeGuan\Resources\Collection($as);
});
```

### Call
http://laravel-resource.local/as

```json
{
  "data": [
    {
      "id": 1,
      "name": "A1",
      "created_at": "2023-02-06 16:55:52",
      "updated_at": "2023-02-06 16:55:52"
    },
    {
      "id": 2,
      "name": "A2",
      "created_at": "2023-02-06 16:55:52",
      "updated_at": "2023-02-06 16:55:52"
    },
```
## Documentation
In the url we can specify the attributes we want from a model and the relationships to include. Let's see this with an example.  
We start from these 5 models:
```php
class A extends Model
{
    use HasFactory;
    use ModelTrait;

    protected $fillable = [ 'id', 'name'];

    public function bs()
    {
        return $this->hasMany(B::class);
    }
    
    public function ds()
    {
        return $this->hasMany(D::class);
    }
}

class B extends Model
{
    use HasFactory;
    use ModelTrait;

    protected $fillable = [ 'id', 'name', 'a_id'];

    public function cs()
    {
        return $this->hasMany(C::class);
    }

    public function a()
    {
        return $this->belongsTo(A::class);
    }
}

class C extends Model
{
    use HasFactory;
    use ModelTrait;

    protected $fillable = [ 'id', 'name', 'b_id'];

    public function b()
    {
        return $this->belongsTo(B::class);
    }
}

class D extends Model
{
    use HasFactory;
    use ModelTrait;

    protected $fillable = [ 'id', 'name', 'a_id'];

    public function es()
    {
        return $this->hasMany(E::class);
    }

    public function a()
    {
        return $this->belongsTo(A::class);
    }
}

class E extends Model
{
    use HasFactory;
    use ModelTrait;

    protected $fillable = [ 'id', 'name', 'd_id'];

    public function d()
    {
        return $this->belongsTo(D::class);
    }
}
```

* Get all As with onlly id and name attribute  
http://laravel-resources.local/as?fields[as]=id,name
```json
{
  "data": [
    {
      "id": 1,
      "name": "A1"
    },
    {
      "id": 2,
      "name": "A2"
    },
    {
      "id": 3,
      "name": "A3"
    },
```
* Get all As include all Bs with specific attributes  
http://laravel-resources.local/as?include=bs&fields[as]=id,name&fields[bs]=name
```json
{
  "data": [
    {
      "id": 1,
      "name": "A1",
      "bs": [
        {
          "name": "B1"
        },
        {
          "name": "B2"
        },
        {
          "name": "B3"
        }
      ]
    },
    {
      "id": 2,
      "name": "A2",
      "bs": [
        {
          "name": "B4"
        },
        {
          "name": "B5"
        },
        {
          "name": "B6"
        }
      ]
    },
    {
      "id": 3,
      "name": "A3",
      "bs": [
        {
          "name": "B7"
        },
        {
          "name": "B8"
        }
      ]
    },
```

* We can include multilevel relationships with the dot notation.  
http://laravel-resources.local/as?include=bs.cs&fields[as]=id,name&fields[bs]=name
```json
{
  "data": [
    {
      "id": 1,
      "name": "A1",
      "bs": [
        {
          "name": "B1",
          "cs": [
            {
              "id": 1,
              "name": "C1",
              "b_id": 1,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            },
            {
              "id": 2,
              "name": "C2",
              "b_id": 1,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            },
            {
              "id": 3,
              "name": "C3",
              "b_id": 1,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            }
          ]
        },
        {
          "name": "B2",
          "cs": [
            {
              "id": 4,
              "name": "C4",
              "b_id": 2,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            },
            {
              "id": 5,
              "name": "C5",
              "b_id": 2,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            },
            {
              "id": 6,
              "name": "C6",
              "b_id": 2,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            }
          ]
        },
        {
          "name": "B3",
          "cs": [
            {
              "id": 7,
              "name": "C7",
              "b_id": 3,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            },
            {
              "id": 8,
              "name": "C8",
              "b_id": 3,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            }
          ]
        }
      ]
    },
    {
      "id": 2,
      "name": "A2",
      "bs": [
```
If attributes are not specified all are returned.  
All relations are included with the dot notation, in the example it is not necessary to specify *include=bs,bs.cs*

* We can include *belongsTo* relationships  
http://laravel-resources.local/bs?fields[bs]=name,a&fields[a]=id,name
```json
{
  "data": [
    {
      "name": "B1",
      "a": {
        "id": 1,
        "name": "A1"
      }
    },
    {
      "name": "B2",
      "a": {
        "id": 1,
        "name": "A1"
      }
    },
    {
      "name": "B3",
      "a": {
        "id": 1,
        "name": "A1"
      }
    },
```
* We can include multiple relationships separated by commas  
  http://laravel-resources.local/as?include=bs,ds&fields[bs]=name&fields[ds]=name
```json
{
  "data": [
    {
      "id": 1,
      "name": "A1",
      "created_at": "2023-02-06 16:55:52",
      "updated_at": "2023-02-06 16:55:52",
      "bs": [
        {
          "name": "B1"
        },
        {
          "name": "B2"
        },
        {
          "name": "B3"
        }
      ],
      "ds": [
        {
          "name": "D1"
        },
        {
          "name": "D2"
        },
        {
          "name": "D3"
        }
      ]
    },
    {
      "id": 2,
      "name": "A2",
      "created_at": "2023-02-06 16:55:52",
      "updated_at": "2023-02-06 16:55:52",
      "bs": [
        {
          "name": "B4"
        },
        {
          "name": "B5"
        },
        {
          "name": "B6"
        }
      ],
      "ds": [
        {
          "name": "D4"
        },
        {
          "name": "D5"
        },
        {
          "name": "D6"
        }
      ]
    },
```

## TODO
* When we include a second level relationship and the first level does not specify any attribute with *fields[relationship]=* an output similar to this is returned  
  http://laravel-resources.local/as?include=bs.cs&fields[as]=id&fields[bs]=&fileds[cs]=name
```json
{
  "data": [
    {
      "id": 1,
      "bs": [
        {
          "cs": [
            {
              "id": 1,
              "name": "C1",
              "b_id": 1,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            },
            {
              "id": 2,
              "name": "C2",
              "b_id": 1,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            },
            {
              "id": 3,
              "name": "C3",
              "b_id": 1,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            }
          ]
        },
        {
          "cs": [
            {
              "id": 4,
              "name": "C4",
              "b_id": 2,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            },
            {
              "id": 5,
              "name": "C5",
              "b_id": 2,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            },
            {
              "id": 6,
              "name": "C6",
              "b_id": 2,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            }
          ]
        },
        {
          "cs": [
            {
              "id": 7,
              "name": "C7",
              "b_id": 3,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            },
            {
              "id": 8,
              "name": "C8",
              "b_id": 3,
              "created_at": "2023-02-06 16:57:18",
              "updated_at": "2023-02-06 16:57:18"
            }
          ]
        }
      ]
    },
```
Something similar to this could be returned where the 1st level relationship did not appear
```json
{
  "data": [
    {
      "id": 1,
      "cs": [
        {
          "id": 1,
          "name": "C1",
          "b_id": 1,
          "created_at": "2023-02-06 16:57:18",
          "updated_at": "2023-02-06 16:57:18"
        },
        {
          "id": 2,
          "name": "C2",
          "b_id": 1,
          "created_at": "2023-02-06 16:57:18",
          "updated_at": "2023-02-06 16:57:18"
        },
        {
          "id": 3,
          "name": "C3",
          "b_id": 1,
          "created_at": "2023-02-06 16:57:18",
          "updated_at": "2023-02-06 16:57:18"
        },
        {
          "id": 4,
          "name": "C4",
          "b_id": 2,
          "created_at": "2023-02-06 16:57:18",
          "updated_at": "2023-02-06 16:57:18"
        },
        {
          "id": 5,
          "name": "C5",
          "b_id": 2,
          "created_at": "2023-02-06 16:57:18",
          "updated_at": "2023-02-06 16:57:18"
        },
        {
          "id": 6,
          "name": "C6",
          "b_id": 2,
          "created_at": "2023-02-06 16:57:18",
          "updated_at": "2023-02-06 16:57:18"
        },
        {
          "id": 7,
          "name": "C7",
          "b_id": 3,
          "created_at": "2023-02-06 16:57:18",
          "updated_at": "2023-02-06 16:57:18"
        },
        {
          "id": 8,
          "name": "C8",
          "b_id": 3,
          "created_at": "2023-02-06 16:57:18",
          "updated_at": "2023-02-06 16:57:18"
        }
      ]
    },
```

* ...

## License
Laravel API Resources is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).