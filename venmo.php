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
		<script src="https://js.braintreegateway.com/web/3.97.2/js/client.min.js"></script>
		<script src="https://js.braintreegateway.com/web/3.97.2/js/venmo.min.js"></script>
		<script src="https://js.braintreegateway.com/web/3.97.2/js/data-collector.min.js"></script>
				<script scr="https://code.jquery.com/jquery-3.7.0.js"></script>
	</head>
	<body>
		

		<h1>Venmo test button</h1>
		<hr>

		<div>
			<button type="button" id="venmo-button"><img src="/image/venmo.png"></button>
		</div>


		<div id="afterVenmo" style="display: none;">
			<br><br>
			<p>Nonce:
				<div id="nonce"></div>
			</p>
			<p>Username:
				<div id="username"></div>
			</p>
		</div>
		<script>

			var venmoButton = document.getElementById('venmo-button');

			// Create a client.
			braintree.client.create({
			authorization: '<?php echo $clientToken; ?>'
			}, function (clientErr, clientInstance) {
			// Stop if there was a problem creating the client.
			// This could happen if there is a network error or if the authorization
			// is invalid.
			if (clientErr) {
			console.error('Error creating client:', clientErr);
			return;
			}

			braintree.dataCollector.create({
			client: clientInstance,
			paypal: true
			}, function (dataCollectorErr, dataCollectorInstance) {
			if (dataCollectorErr) {
			// Handle error in creation of data collector.
			return;
			}

			    // At this point, you should access the deviceData value and provide it
			    // to your server, e.g. by injecting it into your form as a hidden input.
			    console.log('Got device data:', dataCollectorInstance.deviceData);

			});

			braintree.venmo.create({
				client: clientInstance,
				mobileWebFallBack: true,
				allowDesktop: true,
				allowNewBrowserTab: false,
				paymentMethodUsage: 'single_use' // available in v3.77.0+
				// Add allowNewBrowserTab: false if your checkout page does not support
				// relaunching in a new tab when returning from the Venmo app. This can
				// be omitted otherwise.
				// allowNewBrowserTab: false
			}, function (venmoErr, venmoInstance) {
				if (venmoErr) {
				console.error('Error creating Venmo:', venmoErr);
				return;
			}

			    // Verify browser support before proceeding.
			    if (!venmoInstance.isBrowserSupported()) {
			      console.log('Browser does not support Venmo');
			      return;
			    }

			    displayVenmoButton(venmoInstance);

			    // Check if tokenization results already exist. This occurs when your
			    // checkout page is relaunched in a new tab. This step can be omitted
			    // if allowNewBrowserTab is false.
			    if (venmoInstance.hasTokenizationResult()) {
			      venmoInstance.tokenize(function (tokenizeErr, payload) {
			        if (err) {
			          handleVenmoError(tokenizeErr);
			        } else {
			          handleVenmoSuccess(payload);
			        }
			      });
			      return;
			    }

			});
			});

			function displayVenmoButton(venmoInstance) {
			// Assumes that venmoButton is initially display: none.
			venmoButton.style.display = 'block';

			venmoButton.addEventListener('click', function () {
			venmoButton.disabled = true;

			    venmoInstance.tokenize(function (tokenizeErr, payload) {
			      venmoButton.removeAttribute('disabled');

			      if (tokenizeErr) {
			        handleVenmoError(tokenizeErr);
			      } else {
			        handleVenmoSuccess(payload);
			      }
			    });

			});
			}

			function handleVenmoError(err) {
			if (err.code === 'VENMO_CANCELED') {
			console.log('App is not available or user aborted payment flow');
			} else if (err.code === 'VENMO_APP_CANCELED') {
			console.log('User canceled payment flow');
			} else {
			console.error('An error occurred:', err.message);
			}
			}

			function handleVenmoSuccess(payload) {
			// Send the payment method nonce to your server, e.g. by injecting
			// it into your form as a hidden input.
			console.log('Got a payment method nonce:', payload.nonce);
			document.getElementById("nonce").innerHTML = payload.nonce;
			// Display the Venmo username in your checkout UI.
			console.log('Venmo user:', payload.details.username);
			document.getElementById("username").innerHTML = payload.details.username;

			document.getElementById('afterVenmo').style.display = 'block';
			}
		</script>
					
	</body>
</html>	