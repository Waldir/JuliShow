var pageurl = "session.php";

/* Wai for document to be ready*/
$(document).ready(function() { 

	/* Submit form */
	$(document).on('submit','form', function()
	{
	  $.post(pageurl, $(this).serialize()+"&ajaxrequest=1", function(data)
	  {
	  //alert(data);
	  var json = $.parseJSON(data);
	  
	  $('#message_box').hide();
	  $('#message_box_msg').empty();
	  $('#error_success').empty();
		  
	  /* Check If we want to remove an element & if the element exists */
	  if(json.remove !== '' && $('#'+json.remove).length !== 0 )
	  {
		$('#'+json.remove).fadeOut('Slow', function() {$('#'+json.remove).remove();});
	  }
	  /* Check if the submition was successful */ 
	  if(json.error_success == 1)
	  {
	   $('#error_success').removeClass().addClass('success').append('* Success: ');
	  } else {
	   $('#error_success').removeClass().addClass('error').append('* Error: ');
	  }
	      $('#message_box').slideDown();
          $('#message_box_msg').append(json.msg);
		  setTimeout(function(){ $('#message_box').fadeOut('Slow');}, 15000); // <-- time in milliseconds  
		})
	  return false;	
	});

	  /*****************/
	 /* Toggle coding */
	/*****************/
	$('.ShowBox').hide(); // Hide all Showboxes
	
	/* Check if a Box has a cookie */
	$('div[id^="ShowBox_"]').each(function()
	{
	  var id = $(this).attr('id'); // The ID
	  if( $.cookie(id) == null )
	  {
	    $('#' + id).hide(); // No cookie, Hide the box
	  } else {
	    $('#' + id).show(); // Cookie found, Show the box
	  }
    })
	
	/* toggle Link Clicked */
	$('a.toggle').click(function() {
	  var id = $(this).attr('id'); // The ID
	  var nextDiv = $(this).children('span:first'); 
	  
	  /* Check to see if a cookie exists for this click */
	  if( $.cookie( 'ShowBox_' + id ) == null ) 
	  { 
		$.cookie( 'ShowBox_' + id, '1',  { expires: 7, path: '/' } ); // No cookie, Create one.
	  } else {
	    $.cookie( 'ShowBox_' + id, null, { expires: 0, path: '/' } ); // Cookie Found, destroy it.
	  }

		/* Toggle arrow up & down */
		if( $(nextDiv).hasClass('toggleDown') )
		{ 
		  $(nextDiv).removeClass('toggleDown').addClass('toggleUp');
		} else {
		  $(nextDiv).removeClass('toggleUp').addClass('toggleDown'); 
		}
		
	  $('#ShowBox_' + id).toggle(500);
	  return false;
     });

	  /***************/
	 /* Message Box */
	/***************/
	$('#message_box').hide();
	$(window).scroll(function()
	{
  		$('#message_box').animate({top:$(window).scrollTop()+"px" },{queue: false, duration: 350});  
	});
	$('#close_message').click(function()
	{
		$('#message_box').slideUp();
	});

	  /*************************/
	 /* Video Functions start */
	/*************************/
	$("#recordform").hide(); // Hide the record form.
	
	/* Add a Record */
	$("#addrecordwrap a").click(function(){ // Link clicked, slide form down.
		$("#recordform").slideDown();
		return false;
	});
	
	$("#cancelctcform").click(function(){ // Cancel clicked, slide form up.
		$("#recordform").slideUp();		
	});
	
	/* Edit Link */
	$(".editvideolink").click(function()
	{
	  $("#recordform").slideDown(); //Edit clicked, show the form.	
	  var recordid = $(this).parent().attr("id").split("_")[1]; //Get the video id
	  //Get the JSON values for this video
	  $.post('session.php', { todo: "loadvideo", recordid: recordid, ajaxrequest: 1 }, function(data)
	  {
	    var data = $.parseJSON(data); // Convert the returned data.
	    // And populate the associated fields
	    $("#recordform form input[name=video_id]").val(data.video_id);
	    $("#recordform form input[name=video_url]").val(data.video_url);
	    $("#recordform form input[name=video_title]").val(data.video_title);
	  });
		return false;
	}); /* Video Functions end */
	
	$('.addTextToInput').click(function()
	{
	var id = $(this).attr('id'); // The ID
	var InsertGalwithDir = "{gallery: 'images/' dir: 1}";
	var link = $(this).attr('href');
	if( link == '#') 
	{
	  var InsertGal = "{gallery: 'images/'}";
	} else {
	  var InsertGal = "{gallery: 'images/' dir: 1}";
	}
	var curContent = nicEditors.findEditor(id + '_ta').getContent();
	nicEditors.findEditor(id + '_ta').setContent(curContent+InsertGal);
	$(id + '_ta').val($(id + '_ta').val()+"{gallery: 'folder'}");
	  return false;
	})
	
}); // document ready ends

	/* Are you Sure? Function */
	function deletechecked(message)
	{
      var answer = confirm(message)
      if (answer)
	  {
        document.messages.submit();
      }
    return false;  
	} 