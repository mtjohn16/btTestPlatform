<!DOCTYPE html>
<html>
	<head>
		<script src="https://js.braintreegateway.com/web/3.87.0/js/client.min.js"></script>
		<script src="https://js.braintreegateway.com/web/3.87.0/js/apple-pay.min.js"></script>
	</head>
	<body>
		<script type="text/javascript">
			console.log("testing session:");
			if (window.ApplePaySession && ApplePaySession.supportsVersion(3) && ApplePaySession.canMakePayments()) {
				// This device supports version 3 of Apple Pay.
				console.log("success 1");
			}

			if (!window.ApplePaySession) {
				console.error('This device does not support Apple Pay');
			}

		</script>
	</body>
</html>