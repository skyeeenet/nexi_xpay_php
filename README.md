# Nexi Xpay PHP
This small NexiPay.php file will allow you to generate a payment button for the payment gateway Nexi Xpay based on your alias and mac, 
which can be obtained in your personal account https://ecommerce.nexi.it/
### An example of creating a payment button in test mode

```php
$test = NexiPayCreator::createTestNexiPay('YOUR ALIAS', 'YOUR MAC');
$test->setOrderInfo(256.77, strval(date('U')), 'EUR', 'ENG', 'Test Order Number 322223');
echo $test->getForm();
```
This code will create a button for making a test payment at https://int-ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet based on the data about your payment

Method createTestNexiPay
```php
static public function createTestNexiPay($alias, $mac, $success_url = 'localhost/success', $cancel_url = 'localhost/cancel', $back_url = 'localhost/back', $url = 'https://int-ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet')
```
* $alias and $mac can be taken from your Ecommerce Nexi Xpay account
* $success_url is the address to which the user will be redirected in case of successful payment
* $cancel_url is the address to which the user will be redirected in case of cancellation of the operation
* $back_url is the address to which the user will be redirected in case of a return from the payment form

Method setOrderInfo
```php
public function setOrderInfo($order_price, $transaction_code, $currency, $language, $description)
```
This method sets the payment data.
* $order_price - sets the amount to be debited in the format 247.54
* $transaction_code - sets the transaction id. Must be unique
* $currency - sets payment currency in the format 'EUR'...
* $language - sets language form in the format 'ENG', 'RUS'...
* $description - sets payment description. Just a string in format 'This is description'

Method getForm
```php
public function getForm()
```
This method returns an HTML form with a submit button, which will send the given post from the form by request and redirect the client to the payment form.
```php
static public function createRealNexiPay($alias, $mac, $success_url, $cancel_url, $back_url, $url = 'https://ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet')
```
To create a real form of payment, you must use the static method createRealNexiPay, it works similar to the createTestNexiPay method, the difference is that this method sends form data to https://ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet
