<?php
require_once dirname(__FILE__).'/google-api-php-client-2.2.0/vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfigFile('client_secret.json');
$client->setRedirectUri('http://localhost/samszo/agenda/callback.php');
$client->addScope(array("https://www.googleapis.com/auth/calendar"));

if (! isset($_GET['code'])) {
	$auth_url = $client->createAuthUrl();
	header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
	echo 'Hello, world.';	
	$client->authenticate($_GET['code']);
	$_SESSION['access_token'] = $client->getAccessToken();
	$redirect_uri = 'http://localhost/samszo/agenda/index.php';
	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}