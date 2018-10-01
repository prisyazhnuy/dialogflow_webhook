<?php 

$method = $_SERVER['REQUEST_METHOD'];

// Process only when method is POST
if($method == 'POST'){
	$requestBody = file_get_contents('php://input');
	$json = json_decode($requestBody);

	$text = $json->queryResult->intent->displayName;

	switch ($text) {
		case 'hi':
			$speech = "Hi, Nice to meet you";
			break;
			
		case 'Vacancies':
			$curl = curl_init();

			curl_setopt_array($curl, array(
  				CURLOPT_URL => "https://api.rabota.ua/company/2707069/vacancies",
 				CURLOPT_RETURNTRANSFER => true,
 				CURLOPT_TIMEOUT => 30,
  				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  				CURLOPT_CUSTOMREQUEST => "GET",
  				CURLOPT_HTTPHEADER => array(
    				"cache-control: no-cache"
  				),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);
			
			$response = json_decode($response, true); //because of true, it's in an array
			$speech = "";
			foreach($response['documents'] as $item) {
				$speech .= $item['name']. '<br />';
			}
			break;

		case 'bye':
			$speech = "Bye, good night";
			break;

		case 'anything':
			$speech = "Yes, you can type anything here.";
			break;
		
		default:
			$speech = "Sorry, I didnt get that. Please ask me something else.";
			break;
	}

	$response = new \stdClass();
	$response->fulfillmentText = $speech;
	$response->source = "webhook";
	echo json_encode($response);
}
else
{
	echo "Method not allowed";
}

?>
