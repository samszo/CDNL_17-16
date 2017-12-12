<?php
require_once '../../../google-api-php-client-2.2.0/vendor/autoload.php';

session_start();


$client = new Google_Client();
$client->setAuthConfig('../agenda/client_secret.json');
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
	//
	$_GET['q'] = 'present';
	$_GET['q'] = '';
	$_GET['id'] = 'amenibenmrad@gmail.com';
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
	            $r = insertPresent($cal_service, $_GET['id'], $_GET['desc'], $_GET['email']);
	            break;
	        default:
	            $r = "";
	           break;
	    }

	} catch (Exception $e) {
	    echo 'ERREUR : ',  $e->getMessage(), "\n";
	}
	//
} else {
	$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/THYP_17-18/ameni26/trombinoscope/callback.php';
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
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script type="text/javascript" src="http://rawgit.com/vitmalina/w2ui/master/dist/w2ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="http://rawgit.com/vitmalina/w2ui/master/dist/w2ui.min.css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<style>
body{
    width:1060px;
    margin:50px auto;
}
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
td,th{
  padding:5px;
}
td{
  height:200px
}
img{
   -webkit-transform: rotate(90deg);
   -moz-transform: rotate(90deg);
   -o-transform: rotate(90deg);
   -ms-transform: rotate(90deg);
   transform: rotate(90deg);
}
</style>
</head>
<body>
  <div class="container">
      <ul class="nav nav-pills">
        <li role="presentation" ><a href="http://localhost/THYP_17-18/ameni26/">Acceuil</a></li>
        <li role="presentation" ><a href="http://localhost/THYP_17-18/ameni26/dash.html">Compétences</a></li>
        <li role="presentation" class="active"><a href="http://localhost/THYP_17-18/ameni26/trombinoscope/">Présence</a></li>
        <li role="presentation" ><a href="http://localhost/THYP_17-18/ameni26/grid/grid.php">Evenements</a></li>
      </ul>
      <br>

  </div>
<h1 id="head">Liste de présence</h1>
Id du calendrier <input type="text" id="idCal"><label style="color:red" id="succ"></label>
<div id="etu" >

</div>
<div id='dashboard'>
</div><p></p>
<form action="#" method="get">
<table id="tableAppel" style="float:left;width:400">
  <tr>
    <th >Nom et prénom</th>
    <th >Photo</th>
    <th >Présence</th>
  </tr>
</table>
<div style="width:200px;margin-left:600px;margin-top:100px" id="result">
<input type="button" value="valider" style="margin-top:100px;width:70px;height:30px" onclick="validerPresence()"><br>
</div>
</form>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script type="text/javascript" src="../js/jquery.min.js" ></script>
<script>
function validerPresence(){
  w2utils.lock($("#dashboard"),"loading...",true);
  var h="";var c=0;
  $('.presence:checked').each(function() {
    if(c==0){
  h=this.value;c++}
  else {
    h=h+"&"+this.value
  }
});if(h== ""){alert("sélectionnez les présents");w2utils.unlock($("#dashboard"));}
 else{var lien="http://localhost/THYP_17-18/ameni26/agenda/index.php?"+"q=present&id="+$("#idCal").val()+"&desc=Presence&email[]="+h;
 $.ajax({
  url: lien,
  context: document.body,
  success: function (response) {w2utils.unlock($("#dashboard"));
      $('#succ').text("validé");

    },
    error: function (jqXHR, exception) {
        var msg = '';
        if (jqXHR.status === 0) {
            msg = 'Not connect.\n Verify Network.';
        } else if (jqXHR.status == 404) {
            msg = 'Requested page not found. [404]';
        } else if (jqXHR.status == 500) {
            msg = 'Internal Server Error [500].';
        } else if (exception === 'parsererror') {
            msg = 'Requested JSON parse failed.';
        } else if (exception === 'timeout') {
            msg = 'Time out error.';
        } else if (exception === 'abort') {
            msg = 'Ajax request aborted.';
        } else {
            msg = 'Uncaught Error.\n' + jqXHR.responseText;
        }
        alert(msg);
    },
})
}
}

var body=d3.select("body");
d3.csv("https://docs.google.com/spreadsheets/d/e/2PACX-1vQxmWDytc5hSTaF-V-96gefaJxHJWnLGS7xudeNJChpgpvqWdskujnlt03TkiWRHtW5uoTV8sYAH3HZ/pub?gid=642939185&single=true&output=csv",function(data){
  data.forEach(function(d){
    console.log(d);
    var h='http://www.samszo.univ-paris8.fr/THYP/17-18/photo/'+d["lien vers la photo"];
    if(d["lien vers la photo"] == ""){h="https://cdn0.iconfinder.com/data/icons/large-glossy-icons/512/No.png"}

  //  $("<img/>").attr({src: h, height: "20px"}).appendTo("#tableAppel");
  $("#tableAppel").append('<tr><td><b>'+d["Prénom"]+' '+d["Nom"]+'<b/></td><td><img src="'+h+'" height="100px;width:50px"></td><td> présent(e)<input type="checkbox" class="presence" name="présent(e)" value="'+d["E-mail"]+'"><br></td></tr>');

  });
  })




</script>
</body>
</html>
