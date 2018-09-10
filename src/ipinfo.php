<?php
function ipinfo($request){
	$url="https://ipinfo.io/" .urlencode($request);
	
	$defaults = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_URL => $url,
		CURLOPT_FRESH_CONNECT => true
	);
	
	$curl = curl_init();
	curl_setopt_array($curl, $defaults);
	$response = curl_exec($curl);
	$error = curl_error($curl);
	curl_close($curl);
	
	$data = json_decode($response, true);
	$location = $data["city"] . " " . $data["region"]. " " . $data["country"];
	$result = '{"items":[';
	
	$result .= '{"title": "'.trim($location).'",';
	$result .= '"subtitle": "'.$data["org"].'",';
	$result .= '"arg": "'.$data["loc"].'"}';
	
	$result .= ']}';
	echo $result;
}
?>