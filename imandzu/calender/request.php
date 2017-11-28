<?php
require_once '../../../google-api-php-client-2.2.0/vendor/autoload.php';

session_start();


$client = new Google_Client();
$client->setAuthConfig('../trombinoscope/client_secret.json');
//$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
$client->addScope(array("https://www.googleapis.com/auth/calendar"));


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
	$cal_service = new Google_Service_Calendar($client);
	/*
	$_GET['q'] = 'present';
	$_GET['id'] = 'thyp1213@gmail.com';
	*/
	try {
	    
	    switch ($_GET['q']) {
	        case 'all':
	            //Pour la liste complète des calendrier de la personne
	            $r = getAllCalendar($cal_service);
        	        break;	        
	        case 'info':
	            //Pour les infos d'un calendrier
	            $calendar = $cal_service->calendarList->get($_GET['id']);
	            $r = getCalendarInfo($calendar, $cal_service);
	            break;
			case 'present':
	            //Pour ajouter un présent
	            $r = insertPresent($cal_service, $_GET['id_cal'], $_GET['titre'], $_GET['start'], $_GET['end']);
				echo getInfoEvent( $cal_service , $_GET['id_cal'] , $_GET['titre']);
	            break;
	        default:
	           break;
	    }
	    	    echo json_encode($r);   
	} catch (Exception $e) {
	    echo 'ERREUR : ',  $e->getMessage(), "\n";
	}
	//
} else {
	$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/THYP_17-18/imandzu/calender/callback.php';
	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}


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
	  if(isset($_GET['startdate']) && isset($_GET['enddate']) )
	  
	  {
		  $optParams = array(
		  "timeMin" => $_GET['startdate'],
		  "timeMax" => $_GET['enddate']
		  );
	  
	       $events = $service->events->listEvents($cal->getId(), $optParams);

			 $r = array();

			 foreach ($events->getItems() as $event) {
				
				$info = array();
				$info["recid"] = $event->getId();
				$info["title"] = $event->summary;
				$info["summary"]=$cal->getSummary();
				$info["id"]=$cal->getId();
				$info["access"]=$cal->getAccessRole();
				$info["description"]=$cal->getDescription();
				$info["location"]=$cal->getLocation();

				array_push($r , $info);
               
      }  
    return $r;

  }
  else{
        $optParams = array(
          "timeMin" => "2017-10-01T05:00:00-06:00",
          "timeMax" => "2017-11-5T20:00:01-06:00"
          );
		  
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

function insertPresent($service, $calendarId, $titre, $start, $end){
    //merci à https://developers.google.com/google-apps/calendar/v3/reference/events/insert
    $summary = $titre;
	$dateS = new DateTime($start);
    $dateDeb = $dateS->format('Y-m-d').'T'.$dateS->format('H:i:s');//'2017-10-17T14:30:00'

	$dateE = new DateTime($end);
    $dateFin = $dateE->format('Y-m-d').'T'.$dateE->format('H:i:s');


     
    //pour la géolocalisation merci à https://stackoverflow.com/questions/409999/getting-the-location-from-an-ip-address
    
    $event = new Google_Service_Calendar_Event(array(
        'summary' => $summary,
        'location' => 'Paris 8',
        'description' => $summary,
        'start' => array(
            'dateTime' => $dateDeb,
            'timeZone' => 'Europe/Paris',
        ),
        'end' => array(
            'dateTime' => $dateFin,
            'timeZone' => 'Europe/Paris',
        )
    ));
	
    $event = $service->events->insert($calendarId, $event);
    
    
}


function getInfoEvent($service, $id_cal , $titre){
	
  $html  = "<h1> Titre : " . $titre . "</h1></br>";
  $html = $html . "<h3> Description :  </h3><p>" . $titre . "</p></br>";
  return $html;

}
?>