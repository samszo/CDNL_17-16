<?php
require_once '../../../google-api-php-client-2.2.0/vendor/autoload.php';
session_start();
$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
//$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
//$client->addScope(array("https://www.googleapis.com/auth/calendar"));
$client->addScope(Google_Service_Drive::DRIVE)
if(isset($_GET['out'])){
    unset($_SESSION['access_token']);
    $client->revokeToken();
}
//vérifie que le token n'ets pas expéré
//if ($client->isAccessTokenExpired()) {
//    unset($_SESSION['access_token']);
//}
//pour supprimer les droits https://myaccount.google.com/permissions?pli=1
//print_r($_SESSION['access_token']);
//$_SESSION['access_token'] = array("access_token"=>"ya29.GlznBOwSIyspxzMQnUG7IVmqqUUnQ5c7GXY16rPPqPo6nrJ80rUK0WUQwootMzwuNPQLrTKUfITfN71XM-g0zii6yu_V6ugGE4Jsp56mV2bWH0UbsmUdV6-kyTnNMw","token_type"=>"Bearer", "expires_in"=>"3599", "created"=>1508238940);
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
	$client->setAccessToken($_SESSION['access_token']);

	
$service = new Google_Service_Drive( $client );
	try {
	    
	    switch ($_GET['q']) {
	        case 'all':
	            // Lists the user's files.
	            {	getallfiles($service);	break;}
	        case 'app':
	    
	            {retrieveAllApps($service);break; }
				
	        default:
	            $r = "rien";
	           break;
	    }
	    	echo json_encode($r);    
	} catch (Exception $e) {
	    echo 'ERREUR : ',  $e->getMessage(), "\n";
	}
	//
} else {
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/THYP_17-18/salmisim/agenda/callback.php';
	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
// Lists the user's files.
		function getallfiles($service)
		{
		$files_list = $service->files->listFiles(array());
		if (count($files_list->getFiles()) == 0) {
			print "No files found.\n";
		} else {
			foreach ($files_list->getFiles() as $file) {
				$res['name'] = $file->getName();
				$res['id'] = $file->getId();
				$files[] = $res;
			}
			print_r($files);
		}
		}
// Lists a user's installed apps.
		function retrieveAllApps($service) {
		  try {
			$apps = $service->apps->listApps();
			return $apps->getFiles();
		  } catch (Exception $e) {
			print "An error occurred: " . $e->getMessage();
		  }
		  return NULL;
		}
