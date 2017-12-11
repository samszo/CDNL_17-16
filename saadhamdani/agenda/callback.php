<?php
require_once 'google-api-php-client-2.2.0/vendor/autoload.php';
session_start();
$client = new Google_Client();
$client->setAuthConfigFile('client_secret.json');
$client->setRedirectUri('http://127.0.0.1/saadhamdani/agenda/callback.php');
$client->addScope(array("https://www.googleapis.com/auth/calendar"));
//print_r($_GET);
if (! isset($_GET['code'])) {
	$auth_url = $client->createAuthUrl();
	header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
	$client->authenticate($_GET['code']);
	$_SESSION['access_token'] = $client->getAccessToken();
	header('Location: ' . filter_var('http://127.0.0.1/saadhamdani/agenda/index.php', FILTER_SANITIZE_URL));
}
