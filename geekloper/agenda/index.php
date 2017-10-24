<?php 

	include 'lib/class.inc.php';
  include 'header.php';
	session_start();

// < [INITIALIZING] >
	$client_secret_link = "client_secret.json";
	$client = new GoogleAgenda($client_secret_link);
	//var_dump($client->client);
// </ [END]  >

if(isset($_GET['out'])){
	$client->revokeToken();
}

if(isset($_POST['send'])){

	$id='derfoufiabdel@gmail.com';
	$desc ='Liste des étudiants présents';

	$cal_service = $client->getService($_SESSION['access_token']);

	$mails = json_decode(stripslashes($_POST['mails']));

	$client->insertPresent($cal_service, $id, $desc, $mails);;

} else{

	if (isset($_SESSION['access_token']) && $_SESSION['access_token']) 
	{
		$cal_service = $client->getService($_SESSION['access_token']);
		try {

	      header('Location: ' . filter_var('setabsences.php', FILTER_SANITIZE_URL));

	 	}catch(Exception $e) {
	    echo 'ERREUR : ',  $e->getMessage(), "\n";
	 	}
	}
	else 
	{
		
	  include 'jumbotron.php';

	}

	include 'footer.php';
}

?>