<?php
require_once '../../vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfigFile('client_secret.json');
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/Master_thyp/CDNL_17-18/morynho/agenda/callback.php');
$client->addScope(array("https://www.googleapis.com/auth/calendar"));

//print_r($_GET);

if (! isset($_GET['code'])) {
	$auth_url = $client->createAuthUrl();
	header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
    $guzzleClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));
    $client->setHttpClient($guzzleClient);
	$client->authenticate($_GET['code']);
	$_SESSION['access_token'] = $client->getAccessToken();
	$redirect_uri = 'http://localhost/Master_thyp/CDNL_17-18/morynho/agenda/index.php';
	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}