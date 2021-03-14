# Laravel Paypal V2 Rest Client

This package creates a simple RestClient to make Payments via the PayPal payment system.

## Creating an Order

```PHP
use Drewdan\Paypal\PaypalClient;

$paypalClient = new PaypalClient;

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

$order = $paypalClient->createOrder($purchaseUnits, 'CAPTURE', $applicationContext);

return redirect($order->getLinkByRel('approve')->href);
```

The above snippet will create a Paypal Order with a value of £12.50 and return an instance of PaypalOrder. This object
contains a helper method to access the Paypal Links returned from the request. You can use this to redirect the user
to paypal to make payment.

The parameter for the application context can be found https://developer.paypal.com/docs/api/orders/v2/#definition-order_application_context

This will authorize the payment, but not capture it. To capture a payment, you will need the Order ID which will be passed in a get request
when the user is redirected back. You can use whatever mechanism fits your application to track this Order ID.

```PHP
use Drewdan\Paypal\PaypalClient;

$paypalClient = new PaypalClient;

//first retrieve the order from Paypal
$order = $paypalClient->showOrder(request()->PayerID);

//here you could do any validation you wish to verify the order is correct, checking the order amount etc before capturing the payment

$order = $paypalClient->captureOrder($order);
```

Once this is complete, you will have captured the payment.
