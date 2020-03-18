<?php
/*
	Plugin name: Logo Roulette
	Description: Show sponsor logos on sidebar widget
	Version: 0.3
	Author: heidiwik
	License: GPLv2 or later
*/



/* Admin page */

function logoroulette_setup_menu(){
    add_menu_page( 'Logo Roulette', 'Logo Roulette', 'manage_options', 'logoroulette', 'logoroulette_init' );
}
add_action('admin_menu', 'logoroulette_setup_menu');


function logoroulette_init() {
	
     logoroulette_handle_image();
     $options = get_option('logoroulette_options', false);
    ?>
	
    <div class="wrap">
	<h1>Logo Roulette</h1>
	
	<h2>Add sponsor logo</h2>
	<p>Logos are shown in widget on random order</p>
	<input class="upload_image_button button" type="button" value="Add new logo" />
	<div id="image_to_add"></div>

	<div id="logoroulette">
	    <h2>Logos</h2>
	    <div id="logot">
		<?php if ($options) {
		    foreach ($options as $logo) { 
			$logo_url = wp_get_attachment_image_src($logo[0], 'medium');
			
			echo '<div class="logo">';
			    echo '<img width="150" src="' . $logo_url[0] . '"><br>';
			    echo '<p>' . $logo[1] . '</p>';
			    echo '<p><a href="?page=logoroulette&poista=' . $logo[0] . '">Delete</a></p>';
			echo '</div>';
		    }
		}
		?>
	    </div>
	</div>
    </div>
    <?php
}



/* Upload image */

function logoroulette_handle_image() {
    
    if(isset($_POST['image_to_add_id'])){ 
	$image_to_add = filter_input(INPUT_POST, 'image_to_add_id', FILTER_SANITIZE_SPECIAL_CHARS);
	$link_to_add = filter_input(INPUT_POST, 'link_to_add', FILTER_SANITIZE_URL);
	
	if (!$link_to_add) {
	    echo '<div id="notice" class="error notice is-dismissible"><p>Link error, image not loaded </p></div>';
	} else {
	    $uusi_logo = array($image_to_add, $link_to_add);
	    
	    if (get_option('logoroulette_options', false)) {
			$options = get_option('logoroulette_options', false);
			$options[] = $uusi_logo;
	    } else {
			$options = array($uusi_logo);
	    }

	    if (update_option('logoroulette_options', $options)) {
			echo '<div id="notice" class="updated notice is-dismissible"><p>Logo added</p></div>';
	    } else {
			echo '<div id="notice" class="error notice is-dismissible"><p>Error</p></div>';
	    }
	}
    } elseif (isset($_GET['poista'])) {
		$poistettava_logo_id = filter_input(INPUT_GET, 'poista', FILTER_SANITIZE_SPECIAL_CHARS);
		
		$current_options = get_option('logoroulette_options');
		
		foreach ($current_options as $logo_key => $logo) {
			if ($logo[0] == $poistettava_logo_id) {
			unset($current_options[$logo_key]);
			continue;
			}
		}
		
		if (update_option('logoroulette_options', $current_options)) { 
			echo '<div id="notice" class="updated notice is-dismissible"><p>Logo deleted</p></div>';
		} else {
			echo '<div id="notice" class="error notice is-dismissible"><p>Error</p></div>';
		}
    }
}




/* Logo Roulette widget */

function logoroulette_display($args) {
   echo $args['before_widget'];

   $logot = get_option('logoroulette_options');
   $random_logo_key = array_rand($logot, 1);
   $logo = $logot[$random_logo_key];
   
  $logo_data = wp_get_attachment_image_src($logo[0], 'medium');
   echo "<br><a target='_blank' href='http://" .  $logo[1] . " '>";
   echo '<img width="180" src="' . $logo_data[0] . '"></a>';
   echo $args['after_widget'];
}


wp_register_sidebar_widget(
    'logoroulette_widget_1', 
    'Logo Roulette',         
    'logoroulette_display',  
    array(                  
        'description' => 'Logo Roulette widget'
    )
);


function logoroulette_admin_scripts() {
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_script('jquery');
    wp_enqueue_script('logoroulette-media-library', plugins_url('js/logoroulette.js', __FILE__), array(), '1.0', true); 
}


function logoroulette_admin_styles() {
    wp_enqueue_style('thickbox');
    wp_enqueue_style('logoroulette', plugins_url('css/logoroulette.css', __FILE__),  '1.0'); 
}

add_action('admin_print_scripts', 'logoroulette_admin_scripts');
add_action('admin_print_styles', 'logoroulette_admin_styles');


function logoroulette_load_wp_media_files() {
  wp_enqueue_media();
}

add_action( 'admin_enqueue_scripts', 'logoroulette_load_wp_media_files' );
