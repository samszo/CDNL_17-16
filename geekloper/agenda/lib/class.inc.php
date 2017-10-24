<?php

require_once '../../../google-api-php-client-2.2.0/vendor/autoload.php';

class GoogleAgenda{

	private $client = NULL;

// < [GET & SET] > 

	public function __get($property) {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

  public function __set($property, $value) {
    if (property_exists($this, $property)) {
      $this->$property = $value;
    }

    return $this;
  }
// </ [END]>

// < [CONSTRUCT] > 

	public function __construct($client_secret,$RedirectUri=NULL) 
	{
		$this->client = new Google_Client();
		$this->client->setAuthConfig($client_secret);
		$this->client->setRedirectUri($RedirectUri);
		$this->client->addScope(array("https://www.googleapis.com/auth/calendar"));
	}
// </ [END] >

// <[ Methods ]
	
	//------------------------- NEW ------------------------------------
	
	public function getService($AccessToken)
	{
		$this->client->setAccessToken($AccessToken);
		return new Google_Service_Calendar($this->client);
	}

	public function revokeToken()
	{  
    unset($_SESSION['access_token']);
    $this->client->revokeToken();
	}

	//----------------------Copy Paste----------------------------------
	public function getAllCalendar($service)
	{
	    //Pour la liste complète des calendrier de la personne
	    $calendarList = 	$service->calendarList->listCalendarList();    
	    while(true) {
	        foreach ($calendarList->getItems() as $calendarListEntry) {
	            $calendars[] = $this->getCalendarInfo($calendarListEntry, $service);
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
	    
	public function getCalendarInfo($cal, $service)
	{
	    
	    $r = array("summary"=>$cal->getSummary()
	        ,"id"=>$cal->getId()
	        ,"access"=>$cal->getAccessRole()
	        ,"description"=>$cal->getDescription()
	        ,"location"=>$cal->getLocation()
	    );
	        
	    //récupère les roles
	    if($r["access"]!="writer" && $r["access"]!="reader"){
	        $roles = $this->getListeAcl($r["id"], $service);
	        $r["roles"]=$roles;
	    }
	    
	    return $r;
	}

	public function getListeAcl($idCal, $service)
	{
	    $acls =array();
	    $acl = $service->acl->listAcl($idCal);
	    foreach ($acl->getItems() as $rule) {
	        $acls[]=$this->getAclInfo($rule);
	    }
	    return $acls;
	}


	public function getAclInfo($acl)
	{
	    $r = array("id"=>$acl->getId()
	        ,"role"=>$acl->getRole()
	    );
	    return $r;
	}

	public function insertPresent($service, $calendarId)
	{    
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
// </ [END] >



}

?>