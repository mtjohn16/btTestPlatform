 <?php
    require_once 'braintree/lib/Braintree.php';
    
    $config = new Braintree\Configuration();
    $gateway = new Braintree\Gateway($config);


 ?>       
        <!DOCTYPE html>
        <html>
            <head>
                <title>Production Braintree - PayPal</title>
                <meta charset="UTF-8">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <!-- Load the client component. -->
                <script src="https://js.braintreegateway.com/web/3.73.1/js/client.min.js"></script>
                <script src="https://js.braintreegateway.com/web/3.73.1/js/data-collector.min.js"></script>

                <script src="https://js.braintreegateway.com/web/dropin/1.26.1/js/dropin.min.js"></script>
                <!-- Load the PayPal Checkout component. -->
                <script src="https://js.braintreegateway.com/web/3.73.1/js/paypal-checkout.min.js"></script>

                <!-- Latest compiled and minified CSS -->
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

                <!-- Optional theme -->
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap-theme.min.css" integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">

                <!-- Latest compiled and minified JavaScript -->
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
            </head>
 
            <body>


                <div class="container-fluid">
                    <!-- Columns are always 50% wide, on mobile and desktop -->
                    <div class="row">
                        <div class="col-xs-2">
                        </div>
                        <div class="col-xs-8">
                            <h1>Braintree - PayPal Production test using tokenization key</h1>
                            <h4>New User flow</h4>
                            <hr>
                        </div>
                        <div class="col-xs-2">
                        </div>
                    </div>
                </div>

                <div class="container-fluid">
                    <!-- Columns are always 50% wide, on mobile and desktop -->
                    <div class="row">
                        <div class="col-xs-4">
                        </div>
                        <div class="col-xs-4">
                            <p>Single Div both PP buttons:</p>
                            <div id="paypal-button-2"></div>
                            <br><hr>
                        </div>
                        <div class="col-xs-4">
                        </div>
                    </div>
                </div>
                

                <p>Single Div both PP buttons:</p>
                <div id="paypal-button" style="width: 200px;"></div>
                <br><hr>
                
                
                <hr>

                <div id="runSale" style="display: none;">
                    <br> 
                    Nonce generated from Braintree:
                    <br>
                    <code id="btNonce"></code><br>
                    <code id="btDeviceData"></code><br>
                    <br>
                    <br> Shipping address from Braintree:
                    <br>
                    <code id="shippingAddress"></code>
                    <form method="post" action="runSale.php">
                        <input id="nonce" type="hidden" name="nonce">
                        <input id="deviceData" type="hidden" name="deviceData">
                        <input id="type" type="hidden" name="type">
                        <input type="submit" name="Run Transaction">
                    </form>
                </div>
            

                <script type="text/javascript">
                    var tokenizationKey = 'production_7bfwqwhj_gg3p3zbpczmbr5tr';
                    // Create a client.
                    braintree.client.create({
                      authorization: tokenizationKey,
                    }, function (clientErr, clientInstance) {

                      // Stop if there was a problem creating the client.
                      // This could happen if there is a network error or if the authorization
                      // is invalid.
                      if (clientErr) {
                        console.error('Error creating client:', clientErr);
                        return;
                      }

                      // Create a PayPal Checkout component.
                      braintree.paypalCheckout.create({
                        client: clientInstance
                      }, function (paypalCheckoutErr, paypalCheckoutInstance) {
                        paypalCheckoutInstance.loadPayPalSDK({
                            components: 'buttons',
                            vault: true,
                            currency: 'USD',
                            'disable-funding': 'venmo',
                            //'enable-funding': 'paylater',
                            intent: 'authorize',
                        }, function () {

                                // Initialize the PayPal only button
                                var button = paypal.Buttons({
                                    fundingSource: paypal.FUNDING.PAYPAL,
                                    style: {
                                        //layout: 'horizontal',
                                        color:  'gold',
                                        shape:  'rect',
                                        label:  'pay',
                                        height: 38,
                                        size: 'responsive',
                                        tagline: false
                                    },

                                    createOrder: function () {
                                      return paypalCheckoutInstance.createPayment({
                                        flow: 'checkout', // Required
                                        amount: 10.00, // Required
                                        currency: 'USD', // Required, must match the currency passed in with loadPayPalSDK
                                        enableShippingAddress: true,
                                        intent: 'authorize', // Must match the intent passed in with loadPayPalSDK
                                        shippingAddressEditable: true,
                                        shippingAddressOverride: {
                                            recipientName: 'Scruff McGruff',
                                            line1: '1234 Main St.',
                                            line2: 'Unit 1',
                                            city: 'Beverly Hills',
                                            countryCode: 'US',
                                            postalCode: '90210',
                                            state: 'CA',
                                            phone: '123.456.7890'
                                        },
/*
                                    
                                        lineItems: [
                                            {
                                                description: 'This item is lovely.',
                                                kind: 'debit',
                                                name: 'Guilded Mirror',
                                                quantity: '1',
                                                unitAmount: '460.00'
                                            },
                                            {
                                                description: 'This one is better than the first one.',
                                                kind: 'debit',
                                                name: 'Antique end table',
                                                quantity: '1',
                                                unitAmount: '19.00'
                                            },
                                            {
                                                description: 'Saving the best for last.',
                                                kind: 'debit',
                                                name: 'Umbrella Stand',
                                                quantity: '1',
                                                unitAmount: '10.00'
                                            },
                                            {
                                                description: 'This is the insurance charge example',
                                                kind: 'debit',
                                                name: 'Premium',
                                                quantity: '1',
                                                unitAmount: '10.00'
                                            }
                                        ],
*/
                                         // requestBillingAgreement: true,
                                         // billingAgreementDetails: {
                                         //     description: 'Hello there!!'
                                         // } 
                                      });
                                    },

                                    onApprove: function (data, actions) {
                                      return paypalCheckoutInstance.tokenizePayment(data, function (err, payload) {
                                        // Submit `payload.nonce` to your server
                                        console.log("Output from the data.paymentSource in the onApprove() function: " + data.paymentSource);
                                        //console.log(payload);
                                        console.log(payload.nonce);
                                        $("#nonce").val(payload.nonce);
                                        $("#btNonce").html(payload.nonce);
                                        $("#type").val(payload.type);
                                        $("#shippingAddress").html(payload.details.shippingAddress.city);
                                        $( "#runSale" ).show();
                                      });
                                    },

                                     onClick: function(data)  {
                                        console.log("Output of the onClick function: " + data.fundingSource);
                                     },

                                    onCancel: function (data) {
                                      console.log('PayPal payment cancelled', JSON.stringify(data, 0, 2));
                                    },

                                    onError: function (err) {
                                      console.error('PayPal error', err);
                                    }
                                });
                                
                                console.log("paypal.FUNDING.PAYPAL:" + button.isEligible());
                                if (button.isEligible()) {

                                    // Render the standalone button for that funding source
                                    button.render('#paypal-button');
                                    button.render('#paypal-button-2');

                                }

                        });
                      });

                      braintree.dataCollector.create({
                        client: clientInstance,
                        paypal: true
                      }, function (err, dataCollectorInstance) {
                        if (err) {
                          // Handle error in creation of data collector
                          return;
                        }
                        // At this point, you should access the dataCollectorInstance.deviceData value and provide it
                        // to your server, e.g. by injecting it into your form as a hidden input.
                        var deviceData = dataCollectorInstance.deviceData;
                        $("#deviceData").val(deviceData);
                        $("#btDeviceData").html(deviceData);

                      });
                    });
                </script>
            </body>
        </html>