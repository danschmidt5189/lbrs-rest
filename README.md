lbrs-rest
=========

In-progress library of REST-related classes for the Yii framework.

Currently, this is primarily a set of interfaces that define the concept of a
"Resource" and its representations in different formats.

The goal is:

1. Model classes that can easily convert themselves to different representations
(json, xml, html, etc.).
2. Controllers that can intelligently select the write format in which to return
resources, handling HTTP-related issues automatically. (E.g. status codes based
on the resource state.)

### ActiveResource

`ActiveResource << CActiveRecord` exposes methods that convert ActiveRecord objects
into string representations in different `MIME` types.

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

### MIME / Format

The MIME and Format classes represent information about the type of string data.

A MIME type is an HTTP Content-Type, e.g. "application/json". A Format is a shorthand
reference to a specific MIME type. A format corresponds to a single MIME type, however
a MIME type may correspond to multiple Formats.
