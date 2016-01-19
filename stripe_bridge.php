<?php
require_once('vendor/autoload.php');
// Set your secret key: remember to change this to your live secret key in production
// See your keys here https://dashboard.stripe.com/account/apikeys

//pk_test_9PaLT64x8k3WR6h4tqzzd8K3

\Stripe\Stripe::setApiKey("sk_test_uJyZN9IKJqPCtG8KSZbdLAWl");

$error ='';
$success = '';

if ($_REQUEST) {
	
	function create_plan($amount,$interval,$name,$currency,$id){
	   try {
	     
		$create_plan = \Stripe\Plan::create(array(
		  "amount" => $amount,
		  "interval" => $interval,
		  "name" => $name,
		  "currency" => $currency,
		  "id" => $id)
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
		    return($e->getJsonBody()); 
	   }
	   
	}
    
	
	function cancel_suscription($customer_id,$suscription_id){
     try {
		$customer = \Stripe\Customer::retrieve($customer_id);
		$subscription = $customer->subscriptions->retrieve($suscription_id);
		$subscription->cancel();
	 } catch (Exception $e) {
		    return($e->getJsonBody()); 
	   }
	}

	if($_REQUEST['requestedOption']){
		$requestedOption = $_REQUEST['requestedOption'];
		header('Content-Type: application/json');
		switch ($requestedOption) {
		    case "create_customer":
		        $customer_id = create_customer($_REQUEST['planID'],$_REQUEST['email'],$_REQUEST["stripeToken"]);
				echo json_encode($customer_id, JSON_PRETTY_PRINT);
		        break;
		    case "create_plan":
		        $plan_id = create_plan($_REQUEST['amount'],$_REQUEST['interval'],$_REQUEST["name"],$_REQUEST["currency"],$_REQUEST["id"]);
				echo json_encode($plan_id, JSON_PRETTY_PRINT);
		        break;
		    case "cancel_suscription":
		        $cancel_suscription_response = cancel_suscription($_REQUEST['customer_id'],$_REQUEST['suscription_id']);
		        echo json_encode($cancel_suscription_response, JSON_PRETTY_PRINT);
		        break;
		    default:
		        $error = array('Error' => 'Invalid Request');
   				echo json_encode($error);
			}
	}
	else{
		$error = array('Error' => 'Invalid Request');
   		echo json_encode($error);
	}


}else{
   $error = array('Error' => 'Invalid Request');
   echo json_encode($error);
}
?>