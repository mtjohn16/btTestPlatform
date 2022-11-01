<?php

	require_once 'lib/Braintree.php';

	$config = new Braintree\Configuration();
	$gateway = new Braintree\Gateway($config);
	// pass $clientToken to your front-end
	$clientToken = $gateway->clientToken()->generate([
		"merchantAccountId" => "USSeller"
	]);

?>
<!DOCTYPE html>
<html>
	<head>
		<script src="https://js.braintreegateway.com/web/3.87.0/js/client.min.js"></script>
		<script src="https://js.braintreegateway.com/web/3.87.0/js/apple-pay.min.js"></script>
		<script src="https://applepay.cdn-apple.com/jsapi/v1/apple-pay-sdk.js"></script>

		<style>
			apple-pay-button {
				--apple-pay-button-width: 140px;
				--apple-pay-button-height: 30px;
				--apple-pay-button-border-radius: 5px;
				--apple-pay-button-padding: 5px 0px;
			}
		</style>

	</head>
	<body>

		<h1>Braintree - Apple Pay Button</h1>

		<p>Here's the button:</p>
		<apple-pay-button id="myAPButton" buttonstyle="black" type="buy" locale="en-US"></apple-pay-button>




		<script type="text/javascript">

			var applePayButton = document.getElementById('myAPButton');
			console.log("testing session:");
			if (!window.ApplePaySession) {
				console.error('This device does not support Apple Pay');
			}

			if (!ApplePaySession.canMakePayments()) {
				console.error('This device is not capable of making Apple Pay payments');
			}

			// testing having the BT client create and AP create outside of event listener


					// Set up your Apple Pay button here


			applePayButton.addEventListener('click', function() {



				braintree.client.create({
					authorization: '<?php echo $clientToken;?>'
				}, function (clientErr, clientInstance) {
					console.log("full Client Instance: " +  JSON.stringify(clientInstance));
					if (clientErr) {
						console.error('Error creating client:', clientErr);
						return;
					}

					braintree.applePay.create({
						client: clientInstance
					}, function (applePayErr, applePayInstance) {
						console.log("Putting AP instance here: " + applePayInstance._instantiatedWithClient); // JSON.stringify(applePayInstance));
						if (applePayErr) {
							console.error('Error creating applePayInstance:', applePayErr);
							return;
						}




						// Set up your Apple Pay button here

						var paymentRequest = applePayInstance.createPaymentRequest({
							total: {
								label: 'My Store',
								amount: '19.99'
							},

							// We recommend collecting billing address information, at minimum
							// billing postal code, and passing that billing postal code with
							// all Apple Pay transactions as a best practice.
							requiredBillingContactFields: ["postalAddress", "emailAddress"]
						});
						console.log(paymentRequest.countryCode);
						console.log(paymentRequest.currencyCode);
						console.log(paymentRequest.merchantCapabilities);
						console.log(paymentRequest.supportedNetworks);

						var session = new ApplePaySession(3, paymentRequest);

						session.onvalidatemerchant = function (event) {
							applePayInstance.performValidation({
								validationURL: event.validationURL,
								displayName: 'My Store'
							}, function (err, merchantSession) {
								if (err) {
								// You should show an error to the user, e.g. 'Apple Pay failed to load.'
								return;
								}
								session.completeMerchantValidation(merchantSession);
							});
						};
						session.begin();
						
						session.onpaymentauthorized = function (event) {
							console.log('Your shipping address is:', event.payment);

							applePayInstance.tokenize({
								token: event.payment.token
							}, function (tokenizeErr, payload) {
								if (tokenizeErr) {
									console.error('Error tokenizing Apple Pay:', tokenizeErr);
									session.completePayment(ApplePaySession.STATUS_FAILURE);
									return;
								}

								// Send payload.nonce to your server.
								console.log('nonce:', payload.nonce);

								// If requested, address information is accessible in event.payment
								// and may also be sent to your server.
								console.log('billingPostalCode:', event.payment.billingContact.postalCode);

								// After you have transacted with the payload.nonce,
								// call `completePayment` to dismiss the Apple Pay sheet.
								session.completePayment(ApplePaySession.STATUS_SUCCESS);
							});
						};



		
					});
				});
			});
		</script>
	</body>
</html>