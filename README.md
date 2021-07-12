# Laravel Paypal V2 Rest Client

This laravel package creates an interface with the PayPal API by generating API requests using the assorted classes
available.

I built this package, because at the time, I could not find a suitable package to do what I wanted (though this might be
due to poor Google skills.) - so I built this.

The documentation for which this package is based on can be found here: https://developer.paypal.com/docs/api/payments/v2/

## Orders

The orders class is the class you would use to create and capture payments from customers.

### Creating an Order

```injectablephp
use Drewdan\Paypal\Services\Orders\Order;

$order = new Order;

$purchaseUnits = [
	[
		'amount' => [
		    'currency_code' => 'GBP',
		    'value' => 12.50,
	    ],
    ],
];

$applicationContext = [
    'brand_name' => 'My Online Shop',
    'shipping_preference' => 'NO_SHIPPING',
    'user_action' => 'PAY_NOW',
    'return_url' => 'https://localhost/return',
    'cancel_url' => 'https://localhost/cancel',
];

$paypalOrder = $order->create($purchaseUnits, 'CAPTURE', $applicationContext);

return redirect($paypalOrder->getLinkByRel('approve')->href);
```

The above snippet will create a PayPal Order with a value of £12.50 and return an instance of PaypalOrder. This object
contains a helper method to access the PayPal Links returned from the request. You can use this to redirect the user to
PayPal to make payment.

The parameter for the application context can be
found https://developer.paypal.com/docs/api/orders/v2/#definition-order_application_context

This will authorize the payment, but not capture it. To capture a payment, you will need the Order ID which will be
passed in a get request when the user is redirected back. You can use whatever mechanism fits your application to track
this Order ID.

### Capturing a Payment

```injectablephp
use Drewdan\Paypal\Services\Orders\Order;

$order = new Order;
//first retrieve the order from Paypal
$paypalOrder = $order->show(request()->PayerID);

//here you could do any validation you wish to verify the order is correct, checking the order amount etc before capturing the payment

$order->capture($paypalOrder);
```

Once this is complete, you will have captured the payment.

## Captures

### Refunding a captured payment

After payment has been completed, you might sometimes need to refund the payment, if for example, you are unable to
fulfil the order. The captures class has the ability to show an existing captured payment, or refund it.

The refund method accepts the following parameters:

| Parameter | Type | Required | Description |
| --------  | ---- | -------- | ----------- |
| captureId | string | True     | This is the capture ID, used to identify the capture to provide the refund against |
| amount | float | True | This is amount you wish to refund. |
| currency | string | false | This is the currency in which the refund os given. Defaults to GBP |
| reason | string | false | You can optionally provide a reason for the refund which will be displayed to the customer |
| invoiceId | string | false | You can optionally  provide an invoice ID if applicable |


```injectablephp
use Drewdan\Paypal\Services\Payments\Captures;

$client = new Captures;

$capture = $client->refund('captureId', 5.99, 'GBP', 'Some reason', 'Invoice 123');
```

### Showing a captured payment

This call will show the details of a captured payment. They are retrieved via their capture ID. Ideally, when you 
take and capture a payment you would store the capture ID in a database against a given order, so you can reference
it in future to make refunds or view details about the capture.

```injectablephp
use Drewdan\Paypal\Services\Payments\Captures;

$client = new Captures;

$capture = $client->show('captureId');

```

## Contributing

Contributions are welcome, if you have anything you'd like to add, please open a Pull Request. If you find a bug or 
other issue with this package, please open an issue on the repo.
