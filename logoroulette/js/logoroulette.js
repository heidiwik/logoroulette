jQuery(document).ready(function($){

    var logoroulette_file_frame;

      jQuery('.upload_image_button').live('click', function( event ){

		event.preventDefault();

		$('#image_to_add').empty();
			
		if ( logoroulette_file_frame ) {
		  logoroulette_file_frame.open();
		  return;
		}

		logoroulette_file_frame = wp.media.frames.logoroulette_file_frame = wp.media({
		  title: jQuery( this ).data( 'uploader_title' ),
		  button: {
			text: jQuery( this ).data( 'uploader_button_text' ),
		  },
		  multiple: false  // Set to true to allow multiple files to be selected
		});


		logoroulette_file_frame.on( 'select', function() {

		  attachment = logoroulette_file_frame.state().get('selection').first().toJSON();
			  
		  $('.upload_image_input').val(attachment.url);
		  $('#image_to_add').append("<p><img width='200' src='" + attachment.url + "' /></p>");
		  $('#image_to_add').append("<p><form action='' method='POST'><input type='hidden' name='image_to_add_id' value='" + attachment.id + "'><label>Link to sponsor webiste: </label><input type='text' class='link_to_add' name='link_to_add'><br><input class='button tallenna-logo' type='submit' value='Add logo' /></form></p>");
		});

		logoroulette_file_frame.open();
      });
	});