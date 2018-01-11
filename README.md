# Eloquent Status Recorder
[![Latest Stable Version](https://poser.pugx.org/aventure-cloud/eloquent-status-recorder/v/stable)](https://packagist.org/packages/aventure-cloud/eloquent-status-recorder)
[![Total Downloads](https://poser.pugx.org/aventure-cloud/eloquent-status-recorder/downloads)](https://packagist.org/packages/aventure-cloud/eloquent-status-recorder)
[![License](https://poser.pugx.org/aventure-cloud/eloquent-status-recorder/license)](https://packagist.org/packages/aventure-cloud/eloquent-status-recorder)


Any eloquent model can have Status management with just one trait. 
Declare status statically but store status history four your models in the database.


- **Author:** Valerio Barbera - [valerio@aventuresrl.com](mailto:valerio@aventuresrl.com)
- **Author Website:** [www.aventuresrl.com](target="_blank":https://www.aventuresrl.com)

## Install
`composer require aventure-cloud/eloquent-status-recorder`


## Config
Publish configuration file in your project:

`php artisan vendor:publish --provider="AventureCloud\EloquentStatusRecorder\EloquentStatusRecorderServiceProvider" --tag="config"`


## Migration
We provide a migration script to create `statuses` table. 
To publish migration script execute:

`php artisan vendor:publish --provider="AventureCloud\EloquentStatusRecorder\EloquentStatusRecorderServiceProvider" --tag="migrations"`

And run with artisan command:

`php artisan migrate --path=/database/migrations`


## Use
Attach `HasStatus` trait to your models and configure statuses values 
for each models indipendently.

```php
class Order extends Model {
    use HasStatus;
    
    protected $statuses = [
        'opened'    => [],
        'paid'      => ['from'     => 'opened'],
        'approved'  => ['from'     => 'paid'],
        'shipped'   => ['from'     => ['paid', 'approved']],
        'arrived'   => ['from'     => 'shipped'],
        'cancelled' => ['not-from' => ['arrived']],
    ];
}
```

## Utility methods
```php
$order = Order::find(1);

/* 
 * Current status
 */
$order->status;

/* 
 * List of all available statuses
 */
$order->statuses();

/* 
 * Available statuses after current status value based on "from" and "not-from" rules
 */
$order->nextAvailableStatuses();

/* 
 * Change status
 */
$order->changeStatusTo('shipped');
```

## Auto-run callbacks
In some cases, you need to do something after a specific status is set. 
For example, send an mail after an order is "shipped". This package 
invokes a method after status change by the convention of 
`on + status_name (camel cased)`:

```php
class Order extends Model {
    use HasStatus;
    
    protected $statuses = [
        'opened'    => [],
        'paid'      => ['from'     => 'opened'],
        'approved'  => ['from'     => 'paid'],
        'shipped'   => ['from'     => ['paid', 'approved']],
        'arrived'   => ['from'     => 'shipped'],
        'cancelled' => ['not-from' => ['arrived']],
    ];
    
    public function onShipped()
    {
        // Send email to the user
    }
}
```


## Events
Every time a status change happen two event will fire with attached the current eloquent model instance
and the given status:
- StatusChanging (Before status is saved)
- StatusChanged (After status change is performed)