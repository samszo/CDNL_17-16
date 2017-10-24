<?php 

	include 'lib/class.inc.php';

	session_start();

// < [INITIALIZING] >
	$client_secret_link = "client_secret.json";
	$client = new GoogleAgenda($client_secret_link);
	//var_dump($client->client);
// </ [END]  >

if(isset($_GET['out'])){
	$client->revokeToken();
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) 
{
	$cal_service = $client->getService($_SESSION['access_token']);
	try {
    
    switch ($_GET['q']) {
        case 'all':
            //Pour la liste complète des calendrier de la personne
            $r = $client->getAllCalendar($cal_service);
      	        break;	 
      	default:
            $r = $client->getAllCalendar($cal_service);
        break;

    }

    echo "<a href='http://" . $_SERVER['HTTP_HOST'] . "/THYP_17-18/geekloper/agenda/index.php?out=1'><img src='img/sign_out.png'></a>";
    echo "<br><br>";
    echo json_encode($r);

 	}catch(Exception $e) {
    echo 'ERREUR : ',  $e->getMessage(), "\n";
 	}
}
else 
{
	$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/THYP_17-18/geekloper/agenda/callback.php';
  echo "<a href='".filter_var($redirect_uri, FILTER_SANITIZE_URL) ."'><img src='img/sign_in.png'></a>";
}

?>