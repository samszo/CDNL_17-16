<?php
require_once '../../../google-api-php-client-2.2.0/vendor/autoload.php';
session_start();
$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
$client->addScope(array("https://www.googleapis.com/auth/calendar"));
if (isset($_GET['out'])) {
    unset($_SESSION['access_token']);
    $client->revokeToken();
}
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    $cal_service = new Google_Service_Calendar($client);
    try {
        switch ($_GET['q']) {
            case 'all':
                //Pour la liste complète des calendrier de la personne
                $r = getAllCalendar($cal_service);
                header('http://' . $_SERVER['HTTP_HOST'] . '/THYP_17-18/TheSimpleone0/grid.html');
                break;
            case 'info':
                //Pour les infos d'un calendrier
                $calendar = $cal_service->calendarList->get($_GET['id']);
                $r = getCalendarInfo($calendar, $cal_service);
                break;
            case 'present':
                //Pour ajouter un présent
                $r = insertPresent($cal_service, $_GET['id'], $_GET['desc'], $_GET['email']);
                break;
            case 'event':
                //Pour la liste complète des evenement du calendrier
                $r = getAllEvent($cal_service, $_GET['calendrier_id']) ;
                break;
            case 'addevent':
                $r = addEvent($cal_service);
                break;
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
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/THYP_17-18/TheSimpleone0/agenda/callback.php';
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
function getAllEvent($service, $calendar_id)
{
    $events = $service->events->listEvents($calendar_id);
    while(true) {
        foreach ($events->getItems() as $event) {
            $calendars[] = $event;
        }
        $pageToken = $events->getNextPageToken();
        if ($pageToken) {
            $optParams = array('pageToken' => $pageToken);
            $events = $service->events->listEvents($calendar_id, $optParams);
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
function getListeAcl($idCal,$service)
{
    $acls =array();
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
function addEvent($service, $calendarId,$summary)
{$event = new Google_Service_Calendar_Event(array(
        'summary' => 'Google I/O 2017',
         'location' => ' Paris, 75003',
         'description' => 'A chance to hear more about Google\'s developer products.',
         'start' => array(
             'dateTime' => '2017-11-28T09:00:00-07:00',
             'timeZone' => 'Europe/Paris',
         ),
         'end' => array(
             'dateTime' => '2017-11-28T17:00:00-07:00',
             'timeZone' => 'Europe/Paris',
         ),
         'recurrence' => array(
             'RRULE:FREQ=DAILY;COUNT=1'
         ),
         'attendees' => array(
             array('email' => 'lpage@example.com'),
             array('email' => 'sbrin@example.com'),
         ),
         'reminders' => array(
             'useDefault' => FALSE,
             'overrides' => array(
                 array('method' => 'email', 'minutes' => 24 * 60),
                 array('method' => 'popup', 'minutes' => 10),
             ),
         ),
     ));

     $calendarId = 'primary';
     $event = $service->events->insert($calendarId, $event);
     printf('Event created: %s\n', $event->htmlLink);
}
function insertPresent($service, $calendarId, $desc, $mails){
    //merci à https://developers.google.com/google-apps/calendar/v3/reference/events/insert
    $date = new DateTime();
    $dateDeb = $date->format('Y-m-d').'T'.$date->format('H:i:s');//'2017-10-17T14:30:00'
    $date->add(new DateInterval('PT60S'));
    $dateFin = $date->format('Y-m-d').'T'.$date->format('H:i:s');
    echo $dateDeb." - ".$dateFin;
    $attendees = array();
    foreach ($mails as $m) {
        $attendees[]=array('email'=>$m);
    }
    /*
     * array(
            array('email' => 'lpage@example.com'),
            array('email' => 'sbrin@example.com'),
        )
     */
    //pour la géolocalisation merci à https://stackoverflow.com/questions/409999/getting-the-location-from-an-ip-address
       
       $event = new Google_Service_Calendar_Event(array(
            'summary' => 'Présent',
            'location' => 'Paris 8',
            'description' => $desc,
            'start' => array(
                'dateTime' => $dateDeb,
                'timeZone' => 'Europe/Paris',
            ),
            'end' => array(
                'dateTime' => $dateFin,
                'timeZone' => 'Europe/Paris',
            ),
            'attendees' => $attendees,
        ));
        //print_r($event);
        $event = $service->events->insert($calendarId, $event);
        return array('message'=>'Event created', 'event'=>$event);
}
?>