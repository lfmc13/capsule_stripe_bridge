# capsule stripe bridge
Bridge for stripe system

Send request to: stripe_bridge.php

Developed functions:

Create Plan:

	create_plan($amount,$interval,$name,$currency,$id)

	Example:

	stripe_bridge.php?requestedOption=create_plan&amount=1000&interval=year&name=Year Suscription Plan&currency=usd&id=year_suscription

Create Customer:  

	create_customer($planID,$email,$stripeToken)

	Example:

	stripe_bridge.php?requestedOption=create_customer&email=fernando@actbold.com&stripeToken=tok_17UlZ4F0g2uiJMDYS5k4OY9k&planID=year_suscription

Cancel Subscription:

   cancel_suscription($customer_id,$suscription_id)

   Example:

	stripe_bridge.php?requestedOption=cancel_suscription&customer_id=cus_7kG57y5ZRsGCed&suscription_id=sub_7kG5QHsDiI2ZiN

