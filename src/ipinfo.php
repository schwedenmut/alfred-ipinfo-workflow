<?php
function ipinfo($request){
	
	if (!empty($request) && !isValidIP($request)) {
		$request = isValidDomain($request) ;
	}
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
	$result = '{"items":[';
		
	$result .= createJSON($data);
	
	$result .= ']}';
	echo $result;
}

function createJSON($contentArray ) {
	$json = '';
	foreach ($contentArray as $key => $value) {
		if (!empty($value) && (preg_match('(postal|country|loc|phone)', $key) === 0)) {
			$json .= '{"title": "'.$value.'",';
			switch ($key) {
				case 'city':
				$json .= '"subtitle": "Postcode: '.$contentArray["postal"].'",';
				$json .= '"icon": {"path": "city-solid.png"},';
				$json .= '"mods": {"alt": {"valid": true, "arg": "https://www.google.com/search?q='.$value.'", "subtitle": "Search '.$value.' on Google.com"},"cmd": {"arg": "https://duckduckgo.com/'.$value.'", "subtitle": "Search '.$value.' on DuckDuckGo.com"}},';
				break;
				case 'region':
				$json .= '"subtitle": "Countrycode: '.$contentArray["country"].'",';
				$json .= '"icon": {"path": "map-regular.png"},';
				$json .= '"mods": {"alt": {"valid": true, "arg": "https://www.google.com/search?q='.$value.'", "subtitle": "Search '.$value.' on Google.com"},"cmd": {"arg": "https://duckduckgo.com/'.$value.'", "subtitle": "Search '.$value.' on DuckDuckGo.com"}},';
				break;
				case 'org':
				$json .= '"icon": {"path": "building-regular.png"},';
				$value = trim(strstr($value, ' '));
				$json .= '"mods": {"alt": {"valid": true, "arg": "https://www.google.com/search?q='.$value.'", "subtitle": "Search '.$value.' on Google.com"},"cmd": {"arg": "https://duckduckgo.com/'.$value.'", "subtitle": "Search '.$value.' on DuckDuckGo.com"}},';
				break;
				case 'hostname':
				$json .= '"icon": {"path": "server-solid.png"},';
				$json .= '"mods": {"alt": {"valid": true, "arg": "http://'.$value.'", "subtitle": "Open http://'.$value.'"},"cmd": {"arg": "https://'.$value.'", "subtitle": "Open https://'.$value.'"}},';
				break;
				case 'ip':
				$json .= '"icon": {"path": "globe-solid.png"},';
				$json .= '"mods": {"alt": {"valid": true, "arg": "https://www.google.com/maps/?q='.$contentArray["loc"].'", "subtitle": "Show '.$contentArray["loc"].' on Google Maps."},"cmd": {"arg": "https://www.openstreetmap.org/search?query='.$contentArray["loc"].'", "subtitle": "Show '.$contentArray["loc"].' on Open Street Map."}},';
				break;
			}
			$json .= '"arg": "'.$value.'"},';
		} 
	}
	return $json;
}

function isValidIP($request) {
	//Check if provided input is valid IP in not prived range
	return filter_var($request, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE) ? true : false;
}

function isValidDomain($request) {
	$valid = preg_match('/(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]/m',$request,$matches);
	if ($valid == 1) {
		$matches = explode(".", $matches[0]);
		/*while (count($matches) > 2) {
			array_shift($matches);
		}*/
		return gethostbyname(implode(".",$matches));
	} else {
		return false;
	}
}
?>