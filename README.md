Yii2 REST Proposal
=========

### Goals

1. Provide a simple interface for converting models to arbitrary MIME types. (E.g.
'application/json', 'application/hal+json', etc.) This interface defines a "Resource".
2. Bake Resource functionality into core classes like Model and ActiveRecord.
3. Provide a special ResourceResponse class that handles content negotiation and
formatting automatically.

### ResourceInterface

Resources can be represented in any number of arbitrary formats, and also maintain
the concept of "freshness" and identity.

The heart of Resource functionality is in the `ConvertibleInterface`:

```php
/**
 * @return string Object converted to a string of the given MIME type
 */
public function convertTo($mimeType, $options = array());

/**
 * @return bool Whether the object is convertible to the given type
 */
public function isConvertibleTo($mimeType, $options = array());
```

An additional resource, `SerializableResourceInterface`, further extends it with
the native `Serializable` and `JsonSerializable` interfaces, as well as `Arrayable`.

Some usage ideas:

```php
$resource = new ClassImplementingResourceInterface();

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

Resources also know what types they can be converted to. This allows us to do something
like this in our controllers:

```php
if (!$this->resource->isConvertibleTo(MIME::JSON)) {
	throw new NotAcceptableHttpException();
}
```

### ActiveResource << ActiveRecord

`ActiveResource << yii\db\ActiveRecord` represents ActiveRecord classes that
are also resources. You get all the sugar of AR, including relational mappings,
plus the promise that the record can be converted into different formats.

ActiveResource should, out of the box, allow embedding relations or linking to
them. (Default links determined by convention and ResourceInterface::getResourceId().)

### ResourceResponse

The `ResourceResponse` reduces boilerplate code and allows easily sending Resources
as a response. The default behavior should support:

1. Content-type negotiation via Accept header or "_format" request parameter
2. Rendering views on requests for "text/html".
3. Rendering partials on requests for "text/partial+html".
4. Rendering json/xml/etc. on other requests.
5. Setting the status code based on the state of the resource. (E.g. 422 if the
resource `hasErrors()`.)

(This is inspired by `Responders` in Ruby on Rails 4.)

Its use in a controller might look like this:

```php
public function actionCreate(array $Customer)
{
	// Immediately throws 406 Not Acceptable if the resource can't be converted
	// to an acceptable type.
	$response = new ResourceResponse(($customer = new Customer()));

	// Do your thing
	$customer->setAttributes($Customer);
	$customer->save();

	// Response inspects resource state and the request to determine how to respond
	return $response;
}

// similar to (but more powerful than)...

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

### MIME / Format

The MIME and Format classes represent information about the type of string data.

A MIME type is an HTTP Content-Type, e.g. "application/json".

A Format is a shorthand reference to a specific MIME type. A format corresponds
to a single MIME type, however a MIME type may correspond to multiple Formats.

These classes are proposed mainly as helpers, and to negotiate conversion between
formats and MIME types.
