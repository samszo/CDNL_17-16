<?php

include 'lib/class.inc.php';

session_start();

$client_secret_link = "client_secret.json";
$RedirectUri = "http://" . $_SERVER['HTTP_HOST'] . "/THYP_17-18/geekloper/agenda/callback.php";

$google_agenda = new GoogleAgenda($client_secret_link,$RedirectUri);

if (! isset($_GET['code'])) {
	$auth_url = $google_agenda->client->createAuthUrl();
	header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
	$google_agenda->client->authenticate($_GET['code']);
	$_SESSION['access_token'] = $google_agenda->client->getAccessToken();
	$redirect_uri = "http://" . $_SERVER['HTTP_HOST'] . "/THYP_17-18/geekloper/agenda/index.php?q=all";
	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}