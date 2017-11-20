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

  return $event->getSummary();

}

?>