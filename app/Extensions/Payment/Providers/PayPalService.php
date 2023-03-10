<?php
#App\Extension\Payment\Providers\PayPalService.php
namespace App\Extensions\Payment\Providers;

use App\Http\Controllers\ShopCart;
use App\Models\ShopOrder;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Request;

class PayPalService
{
    // Context API
    private $apiContext;
    // List items of order
    private $itemList;
    private $paymentCurrency;
    // Total amount
    private $totalAmount;
    // Return link after payment success
    private $returnUrl;
    // return link when customer click cancel
    private $cancelUrl;
    public function __construct()
    {
        $paypal_env = config('paypal');
        $client_id = empty(sc_config('paypal_client_id')) ? $paypal_env['client_id'] : sc_config('paypal_client_id');
        $secret = empty(sc_config('paypal_secrect')) ? $paypal_env['secret'] : sc_config('paypal_secrect');
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                $client_id,
                $secret
            )
        );
        $this->apiContext->setConfig([
            'mode' => sc_config('paypal_mode'),
            'http.ConnectionTimeOut' => 30,
            'log.logEnabled' => sc_config('paypal_log'),
            'log.FileName' => storage_path() . '/' . sc_config('paypal_path_log'),
            'log.LogLevel' => sc_config('paypal_logLevel'),
        ]);
        $this->paymentCurrency = sc_config('paypal_currency');
        $this->totalAmount = 0;
    }

/**
 * Set payment currency
 *
 * @param string $currency String name of currency
 * @return self
 */
    public function setCurrency($currency)
    {
        $this->paymentCurrency = $currency;

        return $this;
    }

    /**
     * Get current payment currency
     *
     * @return string Current payment currency
     */
    public function getCurrency()
    {
        return $this->paymentCurrency;
    }

/**
 * Add item to list
 *
 * @param array $itemData Array item data
 * @return self
 */
    public function setItem($itemData)
    {

        if (count($itemData) === count($itemData, COUNT_RECURSIVE)) {
            $itemData = [$itemData];
        }

        // Duy???t danh s??ch c??c item
        foreach ($itemData as $data) {
            $item = new Item();

            $item->setName($data['name'])
                ->setCurrency($this->paymentCurrency)
                ->setSku($data['sku']) //
                ->setQuantity($data['quantity'])
                ->setPrice($data['price']);
            $this->itemList[] = $item;
            $this->totalAmount += $data['price'] * $data['quantity'];

        }
        return $this;
    }

    /**
     * Get list item
     *
     * @return array List item
     */
    public function getItemList()
    {
        return $this->itemList;
    }
/**
 * Get total amount
 *
 * @return mixed Total amount
 */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * Set return URL
     *
     * @param string $url Return URL for payment process complete
     * @return self
     */
    public function setReturnUrl($url)
    {
        $this->returnUrl = $url;

        return $this;
    }

    /**
     * Get return URL
     *
     * @return string Return URL
     */
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * Set cancel URL
     *
     * @param $url Cancel URL for payment
     * @return self
     */
    public function setCancelUrl($url)
    {
        $this->cancelUrl = $url;

        return $this;
    }

    /**
     * Get cancel URL of payment
     *
     * @return string Cancel URL
     */
    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    /**
     * Create payment
     *
     * @param string $transactionDescription Description for transaction
     * @return mixed Paypal checkout URL or false
     */
    public function createPayment($transactionDescription)
    {
        $checkoutUrl = false;

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $itemList = new ItemList();
        $itemList->setItems($this->itemList);

        $amount = new Amount();
        $amount->setCurrency($this->paymentCurrency)
            ->setTotal($this->totalAmount);

        // Transaction
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription($transactionDescription);

        // ???????ng d???n ????? x??? l?? m???t thanh to??n th??nh c??ng.
        $redirectUrls = new RedirectUrls();

        // Ki???m tra xem c?? t???n t???i ???????ng d???n khi ng?????i d??ng h???y thanh to??n
        // hay kh??ng. N???u kh??ng, m???c ?????nh ch??ng ta s??? d??ng lu??n $redirectUrl
        if (is_null($this->cancelUrl)) {
            $this->cancelUrl = $this->returnUrl;
        }

        $redirectUrls->setReturnUrl($this->returnUrl)
            ->setCancelUrl($this->cancelUrl);

        // Kh???i t???o m???t payment
        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions([$transaction]);

        // Th???c hi???n vi???c t???o payment
        try {
            $payment->create($this->apiContext);
        } catch (\PayPal\Exception\PPConnectionException $paypalException) {
            throw new \Exception($paypalException->getMessage());
        }

        // N???u vi???c thanh t???o m???t payment th??nh c??ng. Ch??ng ta s??? nh???n
        // ???????c m???t danh s??ch c??c ???????ng d???n li??n quan ?????n vi???c
        // thanh to??n tr??n PayPal
        foreach ($payment->getLinks() as $link) {
            // Duy???t t???ng link v?? l???y link n??o c?? rel
            // l?? approval_url r???i g??n n?? v??o $checkoutUrl
            // ????? chuy???n h?????ng ng?????i d??ng ?????n ????.
            if ($link->getRel() == 'approval_url') {
                $checkoutUrl = $link->getHref();
                // L??u payment ID v??o session ????? ki???m tra
                // thanh to??n ??? function kh??c
                session(['paypal_payment_id' => $payment->getId()]);

                break;
            }
        }

        // Tr??? v??? url thanh to??n ????? th???c hi???n chuy???n h?????ng
        return $checkoutUrl;
    }

    /**
     * Get payment status
     *
     * @return mixed Object payment details or false
     */
    public function getPaymentStatus()
    {
        // Kh???i t???o request ????? l???y m???t s??? query tr??n
        // URL tr??? v??? t??? PayPal
        $request = Request::all();

        // L???y Payment ID t??? session
        $paymentId = session('paypal_payment_id');
        // X??a payment ID ???? l??u trong session
        session()->forget('paypal_payment_id');

        // Ki???m tra xem URL tr??? v??? t??? PayPal c?? ch???a
        // c??c query c???n thi???t c???a m???t thanh to??n th??nh c??ng
        // hay kh??ng.
        if (empty($request['PayerID']) || empty($request['token'])) {
            return false;
        }

        // Kh???i t???o payment t??? Payment ID ???? c??
        $payment = Payment::get($paymentId, $this->apiContext);

        // Th???c thi payment v?? l???y payment detail
        $paymentExecution = new PaymentExecution();
        $paymentExecution->setPayerId($request['PayerID']);

        $paymentStatus = $payment->execute($paymentExecution, $this->apiContext);

        return $paymentStatus;
    }

    /**
     * Get payment list
     *
     * @param int $limit Limit number payment
     * @param int $offset Start index payment
     * @return mixed Object payment list
     */
    public function getPaymentList($limit = 10, $offset = 0)
    {
        $params = [
            'count' => $limit,
            'start_index' => $offset,
        ];

        try {
            $payments = Payment::all($params, $this->apiContext);
        } catch (\PayPal\Exception\PPConnectionException $paypalException) {
            throw new \Exception($paypalException->getMessage());
        }

        return $payments;
    }

    /**
     * Get payment details
     *
     * @param string $paymentId PayPal payment Id
     * @return mixed Object payment details
     */
    public function getPaymentDetails($paymentId)
    {
        try {
            $paymentDetails = Payment::get($paymentId, $this->apiContext);
        } catch (\PayPal\Exception\PPConnectionException $paypalException) {
            throw new \Exception($paypalException->getMessage());
        }

        return $paymentDetails;
    }

    public function index(Request $request)
    {
        $data = session('dataPayment');
        $order_id = $data['order_id'];
        $currency = $data['currency'];
        unset($data['order_id']);
        unset($data['currency']);
        session()->forget('dataPayment');
        $transactionDescription = "From website";
        try {
            $paypalCheckoutUrl = $this
                ->setCurrency($currency)
                ->setReturnUrl(route('returnPaypal', ['order_id' => $order_id]))
                ->setCancelUrl(route('cart'))
                ->setItem($data)
                ->createPayment($transactionDescription);
            if ($paypalCheckoutUrl) {
                return redirect($paypalCheckoutUrl);
            } else {
                $msg = 'Error while process Paypal case';
                (new ShopOrder)->updateStatus($order_id, $status = sc_config('paypal_order_status_faild'), $msg);
                return redirect()->route('cart')->with(["error" => $msg]);
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            (new ShopOrder)->updateStatus($order_id, $status = sc_config('paypal_order_status_faild'), $msg);
            return redirect()->route('cart')->with(["error" => $msg]);
        }

    }

    public function getReturn($order_id)
    {
        if (!empty(session('paypal_payment_id'))) {
            $paymentStatus = $this->getPaymentStatus();
            // dd($paymentStatus);
            if ($paymentStatus) {
                ShopOrder::find($order_id)->update(['transaction' => $paymentStatus->id, 'status' => sc_config('paypal_order_status_success')]);
                //Add history
                $dataHistory = [
                    'order_id' => $order_id,
                    'content' => 'Transaction ' . $paymentStatus->id,
                    'user_id' => auth()->user()->id ?? 0,
                    'order_status_id' => sc_config('paypal_order_status_success'),
                ];
                (new ShopOrder)->addOrderHistory($dataHistory);
                return (new ShopCart)->completeOrder();
            } else {
                return redirect()->route('cart')->with(['error' => 'Have an error paypal']);
            }
        } else {
            return redirect()->route('cart')->with(['error' => 'Can\'t get payment id']);
        }

    }

}
