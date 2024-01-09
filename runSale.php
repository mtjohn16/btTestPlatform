<?php
    require_once 'braintree/lib/Braintree.php';
    $config = new Braintree\Configuration();
    $gateway = new Braintree\Gateway($config);

    $milliseconds = round(microtime(true) * 1000);
    $nonce = $_POST['nonce'];
    $deviceData = $_POST['deviceData'];
    $type 'ApplePay';//$_POST['type'];
    $token = '0vhmh60r'; //'testingEmailAddTOken123';
    $amount = $_POST['amount'];
    $custID = '';
    // possible values currently are CreditCard or PayPalAccount



    if ($type == 'ApplePay') {
        echo $milliseconds;
        echo "Apple Pay processed!\n";

        $result = $gateway->transaction()->sale([
            'amount' => $amount,
            //'paymentMethodToken' => $token,
            'paymentMethodNonce' => $nonce,

            'orderId' => $milliseconds,
            'merchantAccountId' => 'USSeller',
            'transactionSource' => 'recurring_first',
            'options' => [
                'submitForSettlement' => false,
            ],
            'customFields' => [
                'whatever' => '12344321'
            ],   
            'deviceData' => $deviceData,
            'customer' => [
                'email' => 'test@testing.com'
            ]
        ]);

        if ($result->success) {
            print_r("Success ID: " . $result->transaction->id);
            $token = $result->transaction->creditCardDetails->token;
            $custID = $result->transaction->customerDetails->id;
        } else {
          print_r("Error Message: " . $result);

          print_r("\n\nfull result:" . $result->message);
        }


    }  


 ?>       
   
        <!DOCTYPE html>
        <html>
            <head>
                

            </head>
            <body>


                <form method="get" action="applePay.php">
                    <input type="submit" name="Submit" value="Back to Start">
                </form>
                <br><br>
                    
                </form>
                <pre>  <br>Transaction response: <?php var_dump($result->transaction); ?></pre>
            </body>
        </html>