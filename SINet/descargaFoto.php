<?

$url = "https://ajax.googleapis.com/ajax/services/search/images?" .
       "v=1.0&q=la%20muerte&userip=217.217.164.83";

// sendRequest
// note how referer is set manually
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_REFERER, "http://localhost/SSII/");
$body = curl_exec($ch);
curl_close($ch);

// now, process the JSON string
$json = json_decode($body);
var_dump($json);
echo '<img src="'.($json->{"responseData"}->{'results'}[0]->{'unescapedUrl'}).'" />'; 

?>