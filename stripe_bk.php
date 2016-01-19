<?php 
if (!$_REQUEST) {
?>
	<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<?php
}else{
	header('Content-Type: application/json');
}
?>

<?php
require_once('vendor/autoload.php');
// Set your secret key: remember to change this to your live secret key in production
// See your keys here https://dashboard.stripe.com/account/apikeys

//pk_test_9PaLT64x8k3WR6h4tqzzd8K3

\Stripe\Stripe::setApiKey("sk_test_uJyZN9IKJqPCtG8KSZbdLAWl");

$error ='';
$success = '';

if ($_REQUEST) {

	function create_plan(){
	   try {
	     
		$create_plan = \Stripe\Plan::create(array(
		  "amount" => 1000,
		  "interval" => "year",
		  "name" => "Year Suscription Plan",
		  "currency" => "usd",
		  "id" => "year_suscription")
		);
		return $create_plan;
	   } catch (Exception $e) {
		  
			  return($e->getJsonBody());
			
	   }
	}

	function create_customer($planID,$email,$stripeToken){
	  try {
	      if (!isset($stripeToken)) throw new Exception("The Stripe Token was not generated correctly");
		$customer = \Stripe\Customer::create(array(
		  "plan" => $planID,
		  "card" => $stripeToken,
		  "email" => $email 
		));
		return $customer;
	   } catch (Exception $e) {
		    $error = $e->getMessage();
		    return $error;
	   }
	   
	}
    
	function subscribe_to_plan($stripeToken,$planID,$email){
		try {
	      if (!isset($stripeToken)) throw new Exception("The Stripe Token was not generated correctly");
			// Get the credit card details submitted by the form
			$token = $stripeToken;

			$customer = \Stripe\Customer::create(array(
			  "source" => $token,
			  "plan" => $planID,
			  "email" => $email)
			);
			return $customer;
		 } catch (Exception $e) {
		    $error = $e->getMessage();
		    return $error;
	     }
	}

	function cancel_suscription($customer_id,$suscription_id){

		$customer = \Stripe\Customer::retrieve($customer_id);
		$subscription = $customer->subscriptions->retrieve($suscription_id);
		$subscription->cancel();
	}
	function charge_subscription($stripeToken){
		try {
	      if (!isset($stripeToken)) throw new Exception("The Stripe Token was not generated correctly");
		    \Stripe\Charge::create(array("amount" => 1000,
		                                "currency" => "usd",
		                                "card" => $stripeToken));
		    $success = 'Your payment was successful.';

		  }
		  catch (Exception $e) {
		    $error = $e->getMessage();
	     }
	}
	
	//create_plan();
	//subscribe_to_plan($_REQUEST['stripeToken'],$_REQUEST['planID'],$_REQUEST['email']);
	$customer_id = create_customer("year_suscription","fernando4@actbold.com",$_REQUEST["stripeToken"]);
	echo json_encode($customer_id, JSON_PRETTY_PRINT);
	//$customer_id
	//charge_subscription($_REQUEST['stripeToken'],$customer_id);
}else{
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>Stripe Getting Started Form</title>
        <script type="text/javascript" src="https://js.stripe.com/v1/"></script>
        <!-- jQuery is used only for this example; it isn't required to use Stripe -->
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
        <script type="text/javascript">
            // this identifies your website in the createToken call below
            Stripe.setPublishableKey('pk_test_9PaLT64x8k3WR6h4tqzzd8K3');
            function stripeResponseHandler(status, response) {
                if (response.error) {
                    // re-enable the submit button
                    $('.submit-button').removeAttr("disabled");
                    // show the errors on the form
                    $(".payment-errors").html(response.error.message);
                } else {
                    var form$ = $("#payment-form");
                    // token contains id, last4, and card type
                    var token = response['id'];
                    // insert the token into the form so it gets submitted to the server
                    form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
                    // and submit
                    form$.get(0).submit();
                }
            }
            $(document).ready(function() {
                $("#payment-form").submit(function(event) {
                    // disable the submit button to prevent repeated clicks
                    $('.submit-button').attr("disabled", "disabled");
                    // createToken returns immediately - the supplied callback submits the form if there are no errors
                    Stripe.createToken({
                        number: $('.card-number').val(),
                        cvc: $('.card-cvc').val(),
                        exp_month: $('.card-expiry-month').val(),
                        exp_year: $('.card-expiry-year').val()
                    }, stripeResponseHandler);
                    return false; // submit from callback
                });
            });
        </script>
    </head>
    <body>
        <h1>Charge $10 with Stripe</h1>
        <!-- to display errors returned by createToken -->
        <span class="payment-errors"><?= $error ?></span>
        <span class="payment-success"><?= $success ?></span>
        <form action="" method="GET" id="payment-form">
            <div class="form-row">
                <label>Card Number</label>
                <input type="text" size="20" autocomplete="off" class="card-number" />
            </div>
            <div class="form-row">
                <label>CVC</label>
                <input type="text" size="4" autocomplete="off" class="card-cvc" />
            </div>
            <div class="form-row">
                <label>Expiration (MM/YYYY)</label>
                <input type="text" size="2" class="card-expiry-month"/>
                <span> / </span>
                <input type="text" size="4" class="card-expiry-year"/>
            </div>
            <button type="submit" class="submit-button">Submit Payment</button>
        </form>
    </body>
</html>
<?php 
}
?>