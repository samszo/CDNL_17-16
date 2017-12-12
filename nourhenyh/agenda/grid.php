<?php
require_once '../../../google-api-php-client-2.2.0/vendor/autoload.php';

session_start();


$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
//$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
$client->addScope(array("https://www.googleapis.com/auth/calendar"));


if(isset($_GET['out'])){
    unset($_SESSION['access_token']);
    $client->revokeToken();
}



if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
	$client->setAccessToken($_SESSION['access_token']);
	$cal_service = new Google_Service_Calendar($client);
	//
//$_GET['q'] = 'present';
	//$_GET['id'] = 'amenibenmrad@gmail.com';
	/*try {

	    switch ($_GET['q']) {
	        case 'all':
	            //Pour la liste complète des calendrier de la personne
	            $r = getAllCalendar($cal_service);
        	        break;
	        case 'info':
	            //Pour les infos d'un calendrier
	            $calendar = $cal_service->calendarList->get($_GET['id']);
	            $r = getCalendarInfo($calendar, $cal_service);
		    $r=getAllEvent($cal_service);
	            break;
          case 'event':
              //Pour les événements
           getAllEvent($cal_service);
                 break;
	        case 'present':
	            //Pour ajouter un présent
	            $r = insertPresent($cal_service, $_GET['id'], $_GET['desc'], $_GET['email']);
	            break;
	        default:
	            $r = "rien";
	           break;
	    }
	    	echo json_encode($r);
	} catch (Exception $e) {
	    echo 'ERREUR : ',  $e->getMessage(), "\n";
	}*/
	//
} else {
	$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/THYP_17-18/nourhenyh/agenda/callback.php';
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

function getAllEvent($service)
{
$events = $service->events->listEvents($_GET['id']);
//var_dump($events);
$ev=json_encode($events);
return $events->{'items'};
//echo($events->{'accessRole'});
// /$event = $service->events->get('primary', "eventId");

/*while(true) {
  foreach ($events->getItems() as $event) {
  }
  $h= $events->{'items'};
  $utiles=json_encode($events->{'items'});
  //echo($utiles);
  $k=json_decode($utiles[1]);
  var_dump($h[0]);
  // celle laa!! print_r($events->{'items'});
  //var_dump(json_encode($events));

  $pageToken = $events->getNextPageToken();
  if ($pageToken) {
    $optParams = array('pageToken' => $pageToken);
    $events = $service->events->listEvents('primary', $optParams);
  } else {
    break;
  }
}*/
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
        $acls=getAclInfo($rule);
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

 function createEvent($service, $calendarId, $desc){
    //merci à https://developers.google.com/google-apps/calendar/v3/reference/events/insert
    $date = new DateTime($_GET['date']);
    $dateDeb = $date->format('Y-m-d').'T'.$date->format('H:i:s');//'2017-10-17T14:30:00'
    //$date->add(new DateInterval('PT60S'));
    $date2 = new DateTime($_GET['dateF']);
    $dateFin = $date2->format('Y-m-d').'T'.$date2->format('H:i:s');
    echo $dateDeb." - ".$dateFin;
    
$mails = explode(",", $mails);  
$attendees = array();
 foreach ($mails  as $m) {
      $attendees[]=array('email'=>$m);
  } 

  //pour la géolocalisation merci à https://stackoverflow.com/questions/409999/getting-the-location-from-an-ip-address
    
    $event = new Google_Service_Calendar_Event(array(
        'summary' => $desc,
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

<!DOCTYPE html>
<html>
<head>
    <title>W2UI Demo: grid-1</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
    <script type="text/javascript" src="http://rawgit.com/vitmalina/w2ui/master/dist/w2ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="http://rawgit.com/vitmalina/w2ui/master/dist/w2ui.min.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

</head>
<body ><br><br>
  <div class="container">
<div id="grid" style="width: 100%; height: 350px;"></div>
<div id="grid2" style="width: 100%; height: 350px;"></div>

<script type="text/javascript">
dtEtu = [];

$(function () { w2utils.lock($("#grid"),"loading...",true);
	$.getJSON("/THYP_17-18/nourhenyh/agenda/index.php?q=all",

		function(data){
      console.log(data);
		data.forEach(function(d){var h=0;h=h+1;
			d.recid =d.id;

		});
    for (var i = 0; i < data.length; i++) {
      data[i].recid="Calendar"+(i+1)
    }

	    $('#grid').w2grid({
	        header: 'Liste des agendas',
	        name: 'grid',
	        show: {
	            header         : true,
	            toolbar     : true,
	            footer        : true,
	            lineNumbers    : true,
	            selectColumn: true,
	            expandColumn: true
	        },

	        //url: 'list.json',
	        //method: 'GET', // need this to avoid 412 error on Safari
	        records: data,
	        columns: [
	            { field: 'recid', caption: 'recid', size: '30%' },
	            { field: 'access', caption: 'Autorisation', size: '30%' },
	            { field: 'description', caption: 'Description', size: '30%' },
	            { field: 'id', caption: 'ID', size: '40%' },
	            { field: 'location', caption: 'Lieux', size: '40%' },
	            { field: 'summary', caption: 'Titre', size: '120px' }
	        ],
          onClick: function(event) {

          var tdId =$("#grid_grid_rec_"+event.recid).children('td')[4].getAttribute('id');
          var divEl =$("#"+tdId).children('div')[0].getAttribute('title');
          console.log(divEl);
          showEvents(divEl);

      //  console.log(colonnes[6].attr("title"))

        //  alert($("#grid_grid_rec_"+event.recid).attr("id"));

      }
	    });
    //  $("#bfCaptchaEntry").click(function(){ myFunction(); });

	});

  //$(".w2ui-footer-left").ready(function(){alert()})

})

var inc=2;
function showEvents(EventId) {console.log(EventId); w2utils.lock($("#grid2"),"loading...",true);
var url="/THYP_17-18/nourhenyh/agenda/index.php?q=info&id="+EventId;
console.log(url);
  $.getJSON(url,
//  $.getJSON("/THYP_17-18/ameni26/agenda/index.php?q=info&id="+EventId,
//$.getJSON("/THYP_17-18/ameni26/agenda/index.php?q=all",
  function(data2){
      console.log(data2);
      for (var i = 0; i < data2.length; i++) {
        data2[i].recid="event"+(i+1)
      }
inc++;
        $('#grid2').w2grid({
            header: 'Liste evenements',
            name: 'grid'+inc,
            show: {
                header         : true,
                toolbar     : true,
                footer        : true,
                lineNumbers    : true,
                selectColumn: true,
                expandColumn: true,
		toolbarAdd: true,
            },
	onAdd: function (event) {
    var lyEvent =  $().w2layout({
        name: 'lyEvent',
        panels: [

            { type: 'main', size:"50%", content: '' },
            { type: 'bottom', size:"50%", content: '' }
        ]
    });
    var frmEvent  = $().w2form({
       name   : 'frmEvent',
       header : '',
       fields : [
           { name: 'debut_evenemnt',field: 'Date de début', type: 'dateTime', required: true },
           { name: 'fin_evenemnt',field: 'Date de fin', type: 'dateTime', required: true },
           { name: 'description_evenemnt',field: 'Description',  type: 'text', required: true },

       ],
       actions: {
           reset: function () {

               this.clear();
           },
           save: function () {var debut_ev=w2ui['frmEvent'].record['debut_evenemnt'];
           var fin_ev=w2ui['frmEvent'].record['fin_evenemnt'];
           var description_ev=w2ui['frmEvent'].record['description_evenemnt'];alert(fin_ev);
           var lien="http://localhost:8888/THYP_17-18/nourhenyh/agenda/index.php?q=presentDate&id="+EventId+"&desc="+description_ev+"&date="+debut_ev+"&dateF="+fin_ev;
            $.ajax({
             url: lien,
             context: document.body
           }).done(function(data) {
             console.log(data);showEvents(EventId);
           //  $("#result").append("Validé");
           });
               this.save();
           },
  
       }
   });
   //   alert($('input[type=us-time]').value);

    w2popup.open({
        title   : 'Créer un nouveau événement',
        body    : '<div id="main" style="position: absolute; left: 5px; top: 5px; right: 5px; bottom: 5px;"></div>',
        showMax : true,
        onOpen  : function (event) {
            event.onComplete = function () {
           if(w2ui['frmEvent'])w2ui['frmEvent'].destroy();
           if(w2ui['lyEvent'])w2ui['lyEvent'].destroy();

                     $('#w2ui-popup #main').w2layout(lyEvent);
                     w2ui['lyEvent'].content('main', $().w2form(frmEvent));
                     w2ui['lyEvent'].load('bottom', "http://localhost/THYP_17-18/nourhenyh/palette/dessin.svg"
                         , 'pop-in', function () {
                       console.log('content loaded');
                       
                   });


                     $('input[type=us-datetime]').w2field('datetime');

                     $('#enum').w2field('enum', {
                       items: dtEtu,
                       openOnFocus: true,
                       selected: []
                   });


                   w2popup.max();

            };
        },
       
});

var month = (new Date()).getMonth() + 1;
var year  = (new Date()).getFullYear();

$.getJSON('../palette/palette.json',
//$.getJSON("/THYP_17-18/ameni26/agenda/index.php?q=all",
  function(data3){console.log("c bon");
//  $("g").click(function(){ alert("sss"); });


    //els[i].onclick=myClickHandlerFunction;

  });

$('input[type=us-time]').w2field('time',  { format: 'h12' });

// US Format
$('input[type=us-date]').w2field('date');
        },
            //url: 'list.json',
            //method: 'GET', // need this to avoid 412 error on Safari
            records: data2,
            columns: [
                { field: 'recid', caption: 'recid', size: '30%' },
                { field: 'summary', caption: 'resume', size: '30%' },
                { field: 'creator.displayName', caption: 'createur', size: '30%' }

            ]

        });
     

    });               // Function returns the product of a and b
}


</script>

</body>
</html>
