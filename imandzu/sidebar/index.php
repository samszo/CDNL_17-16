<!DOCTYPE html>
<html>
<head>
    <title>W2UI Demo: grid-18</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
	<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="http://rawgit.com/vitmalina/w2ui/master/dist/w2ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="http://rawgit.com/vitmalina/w2ui/master/dist/w2ui.min.css" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<form action="request.php?q=all">
  <input type="submit" value="Connexion">
</form>
<div style="position: relative; height: 300px;">
    <div id="grid1" style="position: absolute; left: 0px; width: 49.9%; height: 300px;"></div>
    <div id="grid2" style="position: absolute; right: 0px; width: 49.9%; height: 300px;"></div>
</div>
<div class="sk-fading-circle" id="spinner">
  <div class="sk-circle1 sk-circle"></div>
  <div class="sk-circle2 sk-circle"></div>
  <div class="sk-circle3 sk-circle"></div>
  <div class="sk-circle4 sk-circle"></div>
  <div class="sk-circle5 sk-circle"></div>
  <div class="sk-circle6 sk-circle"></div>
  <div class="sk-circle7 sk-circle"></div>
  <div class="sk-circle8 sk-circle"></div>
  <div class="sk-circle9 sk-circle"></div>
  <div class="sk-circle10 sk-circle"></div>
  <div class="sk-circle11 sk-circle"></div>
  <div class="sk-circle12 sk-circle"></div>
</div>
<script type="text/javascript">

$(document).ready(function() {
//Preloader
$(window).load(function() {
preloaderFadeOutTime = 8000;
function hidePreloader() {
var preloader = $('.sk-fading-circle');
preloader.fadeOut(preloaderFadeOutTime);
}
hidePreloader();
});
});



	var str_array = Array();
	var id_calender;
	var element;
	
	
//-------------------------------------------------------------------------

 var frmEvent  = $().w2form({ 
    name   : 'frmEvent',
    header : 'Details',
	record: {
									Start_date : '',
									End_date : '',
									Description : ''
								},
								fields : [
									{ name: 'Start_date', type:'datetime', required: true },
									{ name: 'End_date', type:'datetime', required: true },
									{ name: 'Description', type: 'text', required: true },

								],
    actions: {
        effacer: function () {
            this.clear();
        },
        sauvegarder: function () {
            create_event(); 
        },
		quitter: function () {
			close_event();
             
        }
    }
});
	
//-------------------------------------------------------------------------------------------------
	
	var sidbarEvent  = $().w2sidebar({
	name: 'sidebar',
	img: null,
	nodes: [ 
		{ id: 'level', text: 'Type d\'événement', img: 'icon-folder', expanded: true, group: true,
		  nodes: [ { id: 'model_2', text: 'Université', icon: 'fa fa-university' },
				   { id: 'model_3', text: 'Entreprise', icon: 'fa fa-building' },
				   { id: 'model_1', text: 'Club', icon: 'fa fa-futbol-o' }
				 ]
		}
	],
	onClick: function (event) {
		
		element = event.target;
					w2ui['layout'].load('main', "palette/"+element+".svg", 'pop-in' , function () {
						
			
			d3.json("palette/" + element+".json", function(data) {
			    		console.log(data.titre);
						w2ui['frmEvent'].record['Description'] = data.titre; 
									
									
								    w2ui['frmEvent'].refresh();
						/*data.zones.forEach(function(d){
			    			var g = d3.select('svg').select("#"+d.id)
			    				.attr('class','zones')
			    				.on("click",function(e){

									
									
									w2ui['frmEvent'].record['Description'] = d.text; 
									
									
								    w2ui['frmEvent'].refresh();
									

			    				});
			    		});	*/								
			    });
			
			
			
			});
										
			
	
	
			    
				
	    			    		
	    	
}
	});

//---------------------------------------------------------------------------------------------------------

  var lyEvent =  $().w2layout({
     name: 'layout',
    panels: [
        { type: 'left', size: 200, resizable: true, style: 'background-color: #F5F6F7;', content: '' },
        { type: 'main',size:"50%", style: 'background-color: #F5F6F7; padding: 5px;', content: '' },
		{ type: 'bottom', size:"50%", content: '' }
    ]
 });
	//------------------------------------------------------------------------
	function close_event(){
		
	w2popup.close();
	
	}
	//--------------------------------------------------------------------------------------------------
	
function popup(calender_id) {	
	 w2popup.open({
     title   : 'Ajouter un événement',
	 width: 650,
	 height: 550,
     buttons: '', //<button class="w2ui-btn" onclick="">Create New Event</button>
     body    : '<div id="main" style="position: absolute; left: 5px; top: 5px; right: 5px; bottom: 5px;"></div>',
     showMax : true,
     onOpen  : function (event) {
	 
         event.onComplete = function () {
		 								
										
     	 	if(w2ui['sidebar'])w2ui['sidebar'].destroy();
			
     	 	if(w2ui['layout'])w2ui['layout'].destroy();
        	 
	        
												
			$('#w2ui-popup #main').w2layout(lyEvent); 
			
			w2ui['layout'].content('bottom', frmEvent);			
					
	        w2ui['layout'].content('left', sidbarEvent);
						
	        w2ui['layout'].load('main');
			//w2ui['layout'].refresh('main');
			

										
			
			

					
         		
         };
     },
 });
	
}


//------------------------------------------------------------------------------------------------------
function create_event(){
		


$.get( "request.php?q=present&id_cal="+ id_calender +"&titre="+w2ui['frmEvent'].record['Description']+"&start="+w2ui['frmEvent'].record['Start_date']+"&end="+w2ui['frmEvent'].record['End_date'], 
																		 function( data ) {$("#info_event").html( data );});
																		 alert("event created");
																		 w2popup.close();

  

	
			}
	//---------------------------------------------------------------------------------------------------
	
$(function () {
	
	$("#spinner").show();
	$.getJSON("request.php?q=all",
		function(data){
		data.forEach(function(d){
			d.recid = d.id;
		});
		$("#spinner").hide();
	    $('#grid1').w2grid({ 
	        header: 'Liste des agendas',	    	
	        name: 'grid1', 
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

        onClick: function (event) {
			id_calender = event.recid;
			var startdate = "2017-09-01T05:00:00-08:00";
			var enddate = "2018-11-5T21:00:01-08:00";
			
			$.getJSON("request.php?q=info&id="+event.recid+"&startdate="+startdate+"&enddate="+enddate,
					function(data){
			w2ui['grid2'].clear();
            w2ui['grid2'].add(data);
				
            });
        }

	    }); 

	    $('#grid2').w2grid({ 
        header: 'Details',
        show: { header: true, columnHeaders: true },
        name: 'grid2',
		show: { 
            toolbar: true,
            footer: true,
            toolbarAdd: true
        },		
        columns: [                
            { field: 'title', caption: 'Titre'}
        ],

		onAdd: function () {
			
			
			if(id_calender == null){alert('choisissez agenda');}
			else {
				
				popup(id_calender);
			/*
							w2popup.open({
								width: 550,
								height: 670,
								title: 'Événement',
								body    :   '<div id="svgOutputDiv" class="svgDiv">'+
											'<br><br><center>Choisissez le type d\'événement</center>'+
											'<svg width="400" height="450" fill="none" stroke-width="1px">'+
											'<g id="polygon"></g>'+
											'<g id="innerArc"></g>'+
											'<g id="arc">'+
											'<path id="Cours Techniques informatiques web" class="palette" fill-opacity="0.5" fill="rgb(177,175,183)" stroke="rgb(50,104,88)" d="M 250 250 L400 249.99999999706205 A 150 150 0 0 1 250.00000000367245 400z"></path>'+
											'<path id="Cours E-service Open source" class="palette" fill-opacity="0.5" fill="rgb(14,103,144)" stroke="rgb(252,181,227)" d="M 250 250 L249.99999999779652 100 A 150 150 0 0 1 400 249.99999999706205z"></path>'+
											'<path id="Cours Developpement Mobile" class="palette" fill-opacity="0.5" fill="rgb(106,238,156)" stroke="rgb(198,73,124)" d="M 250 250 L100 250.00000000146898 A 150 150 0 0 1 249.99999999779652 100z"></path>'+
											'<path id="Cours Gestion conduite de projet" class="palette" fill-opacity="0.5" fill="rgb(150,118,89)" stroke="rgb(41,138,89)" d="M 250 250 L250.0000000007345 400 A 150 150 0 0 1 100 250.00000000146898z"></path>'+
											'</g>'+
											'</svg>'+
											'</div>'+
										  '</palettediv><center><FONT COLOR="#B1AFB7"> Cours Techniques informatiques web</FONT> <br>'+
										  '<FONT COLOR="#0E6790"> Cours E-service Open source</FONT><br>'+
										  '<FONT COLOR="#6AEE9C"> Cours Developpement Mobile</FONT> <br>'+ 
										  '<FONT COLOR="#967659"> Cours Gestion conduite de projet</FONT> </center>',
								buttons : 
										  '<button class="w2ui-btn" onclick="w2popup.close()">Cancel</button>',
								showMax: true
							});
				*/			
			}

							//-------------------------------------------------------------------------------------------
							/* 
							
							$('.palette').click(function() 
											{
												
												var id_titre = $(this).attr('id');

													w2popup.message({ 
														width   : 400, 
														height  : 200,
														html    : '<div style="padding: 20px; text-align: center">'+id_titre+'</div>'+
																  '<center><div class="w2ui-field dt">'+
																	'<label>Date de debut :</label>'+
																	'<div> <input id="start" type="us-datetime"> </div>'+
																	'</div>'+
																	'<div class="w2ui-field dt">'+
																	'<label>Date de fin : </label>'+
																	'<div> <input id="end" type="us-datetime"> </div>'+
																	'</div><br><br>'+
																	'<div style="text-align: center"><button class="w2ui-btn" id="addfinal">Confirmer</button>'+
																	'</center>',
					
																});
														$('input[type=us-datetime]').w2field('datetime');
								
														$('#addfinal').click(function() 
																{
																	
																	
									
									
																		titre = id_titre;
																		id_cal = id_calender;
																		start = $("#start").val();
																		end = $("#end").val();
																		
																		
																		 $.get( "request.php?q=present&id_cal="+ id_cal +"&titre="+titre+"&start="+start+"&end="+end, 
																		 function( data ) {$("#info_event").html( data );});
																		 
																		 w2popup.close();
																		
																});
																	
											});*/
											
											
											
											
											
								
								
								
        }
    });		
		
	});		
})




</script>

<p id="info_event"></p>
</body>
</html>