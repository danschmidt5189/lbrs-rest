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

### Resources

Resources represent classes that implement the `SerializableResourceInterface`.

The killer feature of this interface is the ability of objects implementing it
to represent themselves in any number of arbitrary formats, e.g.:

```php
// Attributes, errors, and metadata as JSON
echo $resource->convertTo(MIME::JSON);
echo json_encode($resource);

// Plain serialized
echo $resource->convertTo(MIME::SERIALIZED_PHP);
echo serialize($resource);

// Attributes, errors, and metadata as XML
echo $resource->convertTo(MIME::XML);

// Renders the resource into 'views/{resourceClass}/show.php'
echo $resource->convertTo(MIME::HTML);

// Partially renders the resource into 'views/{resourceClass}/show.php'
echo $resource->convertTo(MIME::PARTIAL);
```

Resources also know what types they can be converted to, so that in your controller
you could do something like this:

```php
if (!$resource->isConvertibleTo(MIME::JSON)) {
	throw new NotAcceptableHttpException();
}
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

	return new ResourceResponse($customer);
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

	return new ResourceResponse($customer);
}
```

The logic represented above would essentially be equivalent to this:

```php
public function actionCreate(array $Customer)
{
	$customer = new Customer();
	$customer->setAttributes($Customer);
	$saved = $customer->save();

	$response = new yii\web\Response();

	if (Yii::$app->request->wants('json')) {
		$response->format     = 'json';
		$response->statusCode = $customer->hasErrors() ? 422 : 201;
		$response->content    = $customer->convertTo(MIME::JSON);
		return $response;
	}

	if ($saved) {
		Yii::$app->user->setFlash('success', 'Customer created!');
		$this->redirect(['view', 'id' => $customer->id]);
	} else {
		Yii::$app->user->setFlash('error', 'Error creating customer...');
		$this->redirect(['view', 'id' => $customer->id], true, 307);
	}
}
```

... except that the actual logic is delegated to `Responder` classes, which can
be overridden or customized by developers.

### MIME / Format

The MIME and Format classes represent information about the type of string data.

A MIME type is an HTTP Content-Type, e.g. "application/json".

A Format is a shorthand reference to a specific MIME type. A format corresponds
to a single MIME type, however a MIME type may correspond to multiple Formats.

These classes are proposed mainly as helpers, and to negotiate conversion between
formats and MIME types.
