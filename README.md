lbrs-rest
=========

In-progress library of REST-related helper classes for Yii1.

### ActiveResource

`ActiveResource << CActiveRecord` exposes methods that convert your records into
string representations in different formats (`MIME` types).

It implements the `SerializableResourceInterface` using `ActiveResourceTrait`.
If you don't want to extend from the base class, you can use the trait instead.

Conversion is handled by classes implementing the `ConverterInterface` and is
completely customizable. Out-of-the-box functionality includes:

```php
// Attributes, errors, and metadata as JSON
echo $activeResource->convertTo(MIME::JSON);
echo json_encode($activeResource);

// Attributes, errors, and metadata as XML
echo $activeResource->convertTo(MIME::XML);

// Renders the resource into 'views/{resourceClass}/show.php'
echo $activeResource->convertTo(MIME::HTML);

// Partially renders the resource into 'views/{resourceClass}/show.php'
echo $activeResource->convertTo(MIME::PARTIAL);
```
