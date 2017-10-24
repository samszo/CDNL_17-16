$( document ).ready(function() {
    var emails = new Array();

    $( ".card" ).click(function() {

  		if(!$(this).find(".card-img.img-fluid").hasClass('selected')){
  		
  			$(this).find(".card-img.img-fluid").addClass('selected');

  			emails.push($(this).find(".text-muted").html());

  			$('#confirm').prop('disabled', false);
  			
  		}
  		else{

  			console.log(emails.length);

  			$(this).find(".card-img.img-fluid").removeClass('selected');

  			// Find and remove email from a emails array
				var i = emails.indexOf($(this).find(".text-muted").html());
				if(i != -1) {
					emails.splice(i, 1);
				}

				if(emails.length <= 0)
					$('#confirm').prop('disabled', true);

  		}

  		return false;

  		//alert("work");
		});

    $("#confirm").click(function(){

      $('.modal-body').html('Emails of students : <br> ');
      
      for(var i in emails){
          $('.modal-body').append(" - " + emails[i] + "<br>" );
      }

    });

    $("#send").click(function(){

  		$('#confirm').prop('disabled', true);
      
      var jsonString = JSON.stringify(emails);
      $.ajax({
            type: "POST",
            url: "index.php",
            data: {mails : jsonString, send:"hi"}, 
            cache: false,

            success: function(data){
                alert('DONE');
            }
      });

      $('#send').prop('disabled', true);

    });

});