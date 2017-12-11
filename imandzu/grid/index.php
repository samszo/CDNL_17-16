<!DOCTYPE html>
<html>
<head>
    <title>W2UI Demo: grid-18</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
    <script type="text/javascript" src="http://rawgit.com/vitmalina/w2ui/master/dist/w2ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="http://rawgit.com/vitmalina/w2ui/master/dist/w2ui.min.css" />
</head>
<body>
<div style="position: relative; height: 300px;">
    <div id="grid1" style="position: absolute; left: 0px; width: 49.9%; height: 300px;"></div>
    <div id="grid2" style="position: absolute; right: 0px; width: 49.9%; height: 300px;"></div>
</div>

<script type="text/javascript">

$(function () {
	$.getJSON("request.php?q=all",
		function(data){
		data.forEach(function(d){
			d.recid = d.id;
		});
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
		// enter the start Date for the search
			var startdate = prompt("la date de debut","2017-09-01T05:00:00-08:00");
			
		// enter the End Date for the search
			var enddate = prompt("la date de fin","2017-11-5T21:00:01-08:00");

			$.getJSON("request.php?q=info&id="+event.recid+"&startdate="+startdate+"&enddate="+enddate,
					function(data){
				
			w2ui['grid2'].clear();
            //var record = data;
            w2ui['grid2'].add(data);				
            });
        }

	    }); 

	    $('#grid2').w2grid({ 
        header: 'Details',
        show: { header: true, columnHeaders: true },
        name: 'grid2', 
        columns: [                
            { field: 'title', caption: 'Titre'}
        ] 
    });		
		
	});		
});
</script>

</body>
</html>