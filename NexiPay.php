<?php

/*
 * Copyright (c) 2020 Tilinin Sergei (Skyeeenet)

 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

abstract class NexiPayBase {

  protected $order_info;

  protected $customer_info;

  protected $configuration;

  abstract public function setOrderInfo($order_price, $transaction_code, $currency, $language, $description);

  abstract public function setCustomerInfo($customer_email);

  abstract public function setConfiguration($url, $alias, $mac, $url_success, $url_cancel, $url_back);
}

interface Payment {

  public function getForm();
}

class NexiPayCreator {

  private function __construct() {}

  static public function createRealNexiPay($alias, $mac, $success_url, $cancel_url, $back_url, $url = 'https://ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet') {

    $nexi_pay = new NexiPay();

    $nexi_pay->setConfiguration($url, $alias, $mac, $success_url, $cancel_url, $back_url);

    return $nexi_pay;
  }

  static public function createTestNexiPay($alias, $mac, $success_url = 'localhost/success', $cancel_url = 'localhost/cancel', $back_url = 'localhost/back', $url = 'https://int-ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet') {

    $nexi_pay = new NexiPay();

    $nexi_pay->setConfiguration($url, $alias, $mac, $success_url, $cancel_url, $back_url);

    $nexi_pay->setCustomerInfo('TestEmail@gmail.com');

    return $nexi_pay;
  }
}

class NexiPay extends NexiPayBase implements Payment {

  public function getForm() {

    $params = $this->getParamsForm();

    //array of hidden inputs
    $formInputs = [];

    foreach ($params as $key => $value) {
      $value = addslashes($value);
      $formInputs[] = '<input type="hidden" name="' . $key . '" value="' . $value . '" />' . PHP_EOL;
    }

    $nexi_xpay_inputs = implode('', $formInputs);

    $submit_form = '<form action="' . $this->configuration['request_url'] . '" method="post" id="nexi_xpay_payment_form">
                                        ' . $nexi_xpay_inputs . '
                                        <div class="pull-right">
                                        <input type="submit" class="btn btn-primary" id="submit_nexi_payment_form" value="Pay" />
                                        </div>
                                </form>';

    return $submit_form;
  }

  public function setOrderInfo($order_price, $transaction_code, $currency, $language, $description) {

    $this->order_info = [
      'order_price' => intval(round($order_price, 2, PHP_ROUND_HALF_UP) * 100),
      //transaction id must be unique
      'transaction_code' => trim($transaction_code),
      //currency string, example 'EUR'
      'currency' => trim($currency),
      //language string, example 'ENG'
      'language' => trim($language),
      //order description string
      'description' => trim($description)
    ];
  }

  public function setCustomerInfo($customer_email) {

    $this->customer_info = [
      'email' => trim($customer_email)
    ];
  }

  public function setConfiguration($url, $alias, $mac, $url_success, $url_cancel, $url_back) {

    $this->configuration = [
      'request_url' => trim($url),
      'alias' => trim($alias),
      'mac' => trim($mac),
      'url_success' => trim($url_success),
      'url_cancel' => trim($url_cancel),
      'url_back' => trim($url_back),
    ];
  }

  protected function getParamsForm() {

    $params = [
      'alias' => $this->configuration['alias'],
      'importo' => $this->order_info['order_price'],
      'divisa' => $this->order_info['currency'],
      'codTrans' => $this->order_info['transaction_code'],
      'url' => $this->configuration['url_success'],
      'urlpost' => $this->configuration['url_cancel'],
      'url_back' => $this->configuration['url_back'],
      'mac' => sha1('codTrans=' . $this->order_info['transaction_code'] . 'divisa=' . $this->order_info['currency'] . 'importo=' . $this->order_info['order_price'] . trim($this->configuration['mac'])),
      'languageId' => $this->order_info['language'],
      'mail' => $this->customer_info['email'],
      'descrizione' => $this->order_info['description'],
    ];

    return $params;
  }

}

//Usage Example
$test = NexiPayCreator::createTestNexiPay('YOUR ALIAS', 'YOUR MAC');
$test->setOrderInfo(256.77, strval(date('U')), 'EUR', 'ENG', 'Test Order Number 322223');
echo $test->getForm();
