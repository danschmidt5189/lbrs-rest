lbrs-rest
=========

In-progress library of REST-related classes for the Yii framework.

Currently, this is primarily a set of interfaces that define the concept of a
"Resource" and its representations in different formats.

The goal is:

1. Model classes that can easily convert themselves to different representations
(json, xml, html, etc.).
2. Controllers that can intelligently select the correct format in which to return
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

### Resource Controller

The goal of the ResourceController is to reduce boilerplate code, like sending
different error formats to JSON requests, setting flash messages, or rendering
partials on Ajax requests.

Ideally, most actions would just have to load a resource, optionally act on that
resource (e.g. by invoking a method on it), and then responding with the resource:

```php
/**
 * Creates a new customer in the database
 *
 * @param array $Customer Customer attributes (@post)
 */
public function actionCreate(array $Customer)
{
	$customer = new Customer();
	$customer->setAttributes($Customer);
	$customer->save();

	$this->sendResource($customer);
}

/**
 * Renders the edit page for a customer
 *
 * If the request submits customer attributes, set those attributes on the customer
 * and perform validation. sendResource() handles formatting the customer for all
 * possible request types.
 *
 * @param int $customerId Customer ID (@get)
 * @param array $Customer Customer attributes to validate (@post)
 */
public function actionEdit($customerId, array $Customer = null)
{
	$customer = $this->loadCustomerById($customerId);

	if ($Customer !== null) {
		$customer->setAttributes($Customer);
		$customer->validate();
	}

	$this->sendResource($customer);
}
```

### MIME / Format

The MIME and Format classes represent information about the type of string data.

A MIME type is an HTTP Content-Type, e.g. "application/json". A Format is a shorthand
reference to a specific MIME type. A format corresponds to a single MIME type, however
a MIME type may correspond to multiple Formats.
