<?php
    
  include 'lib/class.inc.php';
  session_start();

  // < [INITIALIZING] >
  $client_secret_link = "client_secret.json";
  $client = new GoogleAgenda($client_secret_link);
  //var_dump($client->client);
  

  if (isset($_SESSION['access_token']) && $_SESSION['access_token']) 
  {   
      
      try {
      //GET Service
      $cal_service = $client->getService($_SESSION['access_token']);
           
      switch ($_GET['q']) {
        
        case 'all':
          $all = $client->getAllCalendar($cal_service); 
          echo json_encode($all); 
          break;

        case 'info':
          $calendar = $cal_service->calendarList->get($_GET['id']);
          echo json_encode(getCalendarInfo($calendar,  $cal_service));
          //echo '[{title:"abdellah"},{title:"derfoufi"}]';
          break;

        case 'info_event':
          echo getInfoEvent( $cal_service , $_GET['id_cal'] , $_GET['id_event']);
          break;
        case 'add_new_event':
          echo json_encode(addEvent( $cal_service , $_GET['id_cal'] , $_GET['summary']));
          break;
      }
      

      }catch(Exception $e) {
      echo 'ERREUR : ',  $e->getMessage(), "\n";
      }
  }


function getCalendarInfo($cal, $service)
{
  
  if(isset($_GET['startdate']) && isset($_GET['enddate']) ){
      $optParams = array(
      "timeMin" => $_GET['startdate'],
      "timeMax" => $_GET['enddate']
      );

  }
  else{
        $optParams = array(
          "timeMin" => "2017-10-01T05:00:00-06:00",
          "timeMax" => "2017-11-5T20:00:01-06:00"
          );
  }

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


function getInfoEvent($service, $id_cal , $id_event){

  $event = $service->events->get($id_cal,$id_event);
  $html  = "<h1> Titre : " . $event->getSummary() . "</h1></br>";
  $html = $html . "<h3> Description :  </h3><p>" . $event->getDescription() . "</p></br>";
  return $html;

}


function addEvent($service, $calendarId, $summary){

    try {
            
        $event = new Google_Service_Calendar_Event(array(
            'summary' => $summary,
            'location' => 'Paris 8',
            'description' => '',
            'start' => array(
                'dateTime' => $_GET['startdate'],
                'timeZone' => 'Europe/Paris',
            ),
            'end' => array(
                'dateTime' => $_GET['enddate'],
                'timeZone' => 'Europe/Paris',
            )
        ));
        
        $event = $service->events->insert($calendarId, $event);
    
    } catch (Exception $e) {
        echo $e->getMessage();    
    }    
    //print_r($event);
    
    //return array('message'=>'Event created', 'event'=>$event);
    $info = array();
    $info["recid"] = $event['id'];
    $info["title"] = $event['summary'];
    
    return $info;
    
}

?>