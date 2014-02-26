lbrs-rest
=========

Simple string serialization for your Yii ActiveRecord classes.

```php
// Load your ActiveResource
$customer = Customer::model()->find();

// To JSON string
echo $customer->convertTo(MIME::JSON);

// To XML string
echo $customer->convertTo(MIME::XML);

// To view ('views/customers/show.php')
echo $customer->convertTo(MIME::HTML);

// To partial view ('views/customers/show.php')
echo $customer->convertTo(MIME::PARTIAL);
```
