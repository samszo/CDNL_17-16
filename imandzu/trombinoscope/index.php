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

//vérifie que le token n'ets pas expéré
//if ($client->isAccessTokenExpired()) {
//    unset($_SESSION['access_token']);
//}


//pour supprimer les droits https://myaccount.google.com/permissions?pli=1

//print_r($_SESSION['access_token']);
//$_SESSION['access_token'] = array("access_token"=>"ya29.GlvYBAAizcoG4SH14m1nTmBnZXqgabVmkNJyd0d1wFBMfDOTDmJvHWaD86CRJjFXRSY0SEiTfZjpvpWGzFAAkTfuhCICZ_hznkuCkDtSI5OIlCAz2M4aPOwZp3jS","token_type"=>"Bearer", "expires_in"=>"3599", "created"=>1506954203);


if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
	$client->setAccessToken($_SESSION['access_token']);
	$cal_service = new Google_Service_Calendar($client);
	//
	
	$_GET['desc'] = 'la liste des present';
	$_GET['id'] = 'mansour.ismail.pro@gmail.com';
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
				$json = $_POST['myData'];
				$myDataArray = json_decode($json,true);
				print_r($myDataArray);
	            $r = insertPresent($cal_service, $_GET['id'], $_GET['desc'], $myDataArray);
	            break;
	        default:
	            $r = '';
	           break;
	    }
		
		
	    	echo json_encode($r); 
			echo "<br><br>";
			echo "<br><br>";
			echo "<center>";
			echo "<a href='http://" . $_SERVER['HTTP_HOST'] . "/THYP_17-18/imandzu/trombinoscope/index.php?out=1'>se deconnecter</a>";
			echo "</center>";
        
			
	} catch (Exception $e) {
	    echo 'ERREUR : ',  $e->getMessage(), "\n";
	}
	//
} else {
	$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/THYP_17-18/imandzu/trombinoscope/callback.php';
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
	$date = new DateTime();
    $dateDeb = $date->format('Y-m-d').'T'.$date->format('H:i:s');//'2017-10-17T14:30:00'
    $date->add(new DateInterval('PT3H'));
    $dateFin = $date->format('Y-m-d').'T'.$date->format('H:i:s');
    echo $dateDeb." - ".$dateFin;
	
	
    $attendees = array();
    foreach ($mails as $m) {
        $attendees[]=array('email'=>$m);
    }
	
	
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
        'attendees' => $attendees
    ));
    
    $event = $service->events->insert($calendarId, $event);
    
    
}

if(!isset ($_POST['myData']))
{

?>
<!DOCTYPE html>
<html>
 <head>
    <title>Simple Template Example</title>
	<meta charset="utf-8">    
    <script type="text/javascript" src="../js/d3.v3.js"></script>
 	<script type="text/javascript" src="../js/jquery.min.js" ></script>
 
 <style>
 input {
	border:5px solid grey;
	float:left;
	-webkit-transform: rotate(90deg);
    -moz-transform: rotate(90deg);
    -o-transform: rotate(90deg);
    -ms-transform: rotate(90deg);
	width:150px;
	height:100px;
	margin-bottom: 1cm;
	margin-top: 2cm;
}

button {
    background-color: #56A0F4; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
}
 
 </style>

 </head>
 <body>
 <br>
 
 	<div id="etuTrombi"/>
 	<div id="etuData"/>
	

	
	
	<script>
	var x = 0;
	var str_array = Array();
	function mark(el) {
    el.style.border = "5px solid #56A0F4";
	str_array[x] = document.getElementById(el.id).value;
	//alert("Element: " + array[x] + " Added at index " + x);
	x++;
	
	}


	 //$("<img onclick='selected("");' />").attr({src: photo, alt: item.title}).appendTo("#etuTrombi").wrap("<li><a href=' "+ url +"' title=' "+ item.title +" ' ></a></li>");
	   d3.csv("https://docs.google.com/spreadsheets/d/e/2PACX-1vQxmWDytc5hSTaF-V-96gefaJxHJWnLGS7xudeNJChpgpvqWdskujnlt03TkiWRHtW5uoTV8sYAH3HZ/pub?gid=642939185&single=true&output=csv", function(data){
    
	for (i = 0; i < data.length; i++) { 
	
	
       		var email = data[i]['E-mail'];
			var photo = data[i]['lien vers la photo'];
			var urlTof = 'http://www.samszo.univ-paris8.fr/THYP/17-18/photo/'+photo;
			
			$("<input type='image' />").attr('src', urlTof).attr('id', 'img'+i).attr('value', email).attr('onclick', 'mark(this)').appendTo("#etuTrombi");
       	}
  
});
	
	$(document).ready(function(){
	$("#envoyer").click(function(){
     
    $.ajax({
       url : '?q=present', 
       type : 'POST',
	   data: { myData : JSON.stringify(str_array) },
       success: function(msg){
         alert("Event created")
       }
    });
	return false;
});

});

   	    
</script>

<center>


<button type="submit" name="send" id="envoyer" value="send">Send</button>



 </center>
 </body>
</html>

<?php
}
?>