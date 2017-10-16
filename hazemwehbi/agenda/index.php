<?php
require_once '../../../google-api-php-client-2.2.0/vendor/autoload.php';

session_start();


$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
//$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
//$client->addScope(array("https://www.googleapis.com/auth/drive"));
$client->addScope(Google_Service_Drive::DRIVE);

//$client->setScopes ( array ('https://www.googleapis.com/auth/drive' ) );


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
//$_SESSION['access_token'] = array("access_token"=>"ya29.GlvYBAAizcoG4SH14m1nTmBnZXqgabVmkNJyd0d1wFBMfDOTDmJvHWaD86CRJjFXRSY0SEiTfZjpvpWGzFAAkTfuhCICZ_hznkuCkDtSI5OIlCAz2M4aPOwZp3jS","token_type"=>"Bearer", "expires_in"=>"3599", "created"=>1506954203);


if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
	$client->setAccessToken($_SESSION['access_token']);
//	$cal_service = new Google_Service_Calendar($client);
	
$service = new Google_Service_Drive( $client );

	try {
	    
	    switch ($_GET['q']) {
	        case 'all':
	            // Lists the user's files.
	            {	getallfiles($service);	break;}
	        case 'app':
	            // Lists a user's installed apps.
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
	$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/samszo/agenda/callback.php';
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



/*
function getAllCalendar($service)
{
    //Pour la liste complète des calendrier de la personne
    $calendarList = 	$service->calendarList->listCalendarList();    
    while(true) {
        foreach ($calendarList->getItems() as $calendarListEntry) {
            $calendars[] = getCalendarInfo($calendarListEntry, $service);
        }
        $pageToken = $calendarList->getNextPageToken();
        if ($pageToken) {
            $optParams = array('pageToken' => $pageToken);
            $calendarList = $service->calendarList->listCalendarList($optParams);
        } else {
            break;
        }
    }
    return $calendars;
    
}
    
function getCalendarInfo($cal, $service)
{
    
    $r = array("summary"=>$cal->getSummary()
        ,"id"=>$cal->getId()
        ,"access"=>$cal->getAccessRole()
        ,"description"=>$cal->getDescription()
        ,"location"=>$cal->getLocation()
    );
        
    //récupère les roles
    if($r["access"]!="writer" && $r["access"]!="reader"){
        $roles = getListeAcl($r["id"], $service);
        $r["roles"]=$roles;
    }
    
    return $r;
}

function getListeAcl($idCal, $service)
{
    $acls ="";
    $acl = $service->acl->listAcl($idCal);
    foreach ($acl->getItems() as $rule) {
        $acls[]=getAclInfo($rule);
    }
    return $acls;
}


function getAclInfo($acl)
{
    $r = array("id"=>$acl->getId()
        ,"role"=>$acl->getRole()
    );
    return $r;
}

function insertPresent($service, $calendarId){
    
    $event = new Google_Service_Calendar_Event(array(
        'summary' => 'Présent',
        'location' => 'Paris 8',
        'description' => 'Cours E-service',
        'start' => array(
            'dateTime' => '2017-10-02T09:00:00',
            'timeZone' => 'Europe/Paris',
        ),
        'end' => array(
            'dateTime' => '2017-10-02T10:00:00',
            'timeZone' => 'Europe/Paris',
        ),
        'attendees' => array(
            array('email' => 'lpage@example.com'),
            array('email' => 'sbrin@example.com'),
        ),
    ));
    
    $event = $service->events->insert($calendarId, $event);
    return array('message'=>'Event created', 'event'=>$event);
    
}
*/
