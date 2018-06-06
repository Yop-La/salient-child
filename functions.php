

<?php 


add_action( 'wp_enqueue_scripts', 'salient_child_enqueue_styles');
function salient_child_enqueue_styles() {
	
wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array('font-awesome'));

if ( is_rtl() ) 
	wp_enqueue_style(  'salient-rtl',  get_template_directory_uri(). '/rtl.css', array(), '1', 'screen' );
}

/* shortcode perso */

function user_email_funct( $atts ){
	$current_user = wp_get_current_user();
	return 'User email: ' . $current_user->user_email . '<br />';
}
add_shortcode( 'user_email', 'user_email_funct' );

// include_once(get_stylesheet_directory() . '/functions-library/paiement-apres-essai.php');

include_once(get_stylesheet_directory() . '/functions-library/paiement-apres-essai-complet.php');


/********** partie de création d'un utilisateur wordpress **********************/

// action déclenché par la soumission du formulaire (à ajouter au formulaire)
// add_action( 'my_ninja_forms_processing', 'save_new_wordpress_user' ); ------------------ à décommenter pour utiilsation future 

function save_new_wordpress_user( $form_data ){
    $form_id       = $form_data[ 'id' ];
    $form_fields   =  $form_data[ 'fields' ];
    foreach( $form_fields as $field ){
        $field_id    = $field[ 'id' ];
        $field_key   = $field[ 'key' ];
        $field_value = $field[ 'value' ];
        
        
        if( $field_id == 739 ){ //si adresse mail eleve
        	$userdata = array(
        	    'user_email'    =>  $field_value,
        	    'user_pass'   =>  wp_generate_password(),
        	    'user_login' => $field_value,
        	    'role' => 'subscriber'
        	);

        	$user_id = wp_insert_user( $userdata ) ;

				//On success
				if (is_wp_error( $user_id ) ) {

					 $postfields = array(

						'email' => $field_value,
						'error' => $user_id ->get_error_message()


					 );
					 to_log_slack($postfields);

        	}else{
        		 $postfields = array(

        			'email' => $field_value,
        			'valid' => "ok"


        		 );
        		 to_log_slack($postfields);
        	}
        }
        if( $field_id == 753 ){ //si adresse mail parent

        }
    }
    $form_settings = $form_data[ 'settings' ];
    $form_title    = $form_data[ 'settings' ][ 'title' ];
}



/* ---------- pour envoyer notif dans log sur slack -----------  */

function to_log_slack($postfields){
	 $lien = 'https://hooks.zapier.com/hooks/catch/2126142/zu757i/';



	 $curl = curl_init();


	 curl_setopt($curl, CURLOPT_URL, $lien);
	 curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($curl, CURLOPT_POST, true);

	 curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);


	 $return = curl_exec($curl);

	 curl_close($curl);
	 return;
}

function to_log_abonnement($postfields){
     $lien = 'https://hooks.zapier.com/hooks/catch/2126142/fb99bx/';



     $curl = curl_init();


     curl_setopt($curl, CURLOPT_URL, $lien);
     curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($curl, CURLOPT_POST, true);

     curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);


     $return = curl_exec($curl);

     curl_close($curl);
     return
;
}


/* ---------- partie de contrôle des emails sur les formulaires d'inscriptions à l'essai publiques --------- */ 

// chargement du script js de validation de l'inscription à la semaine d'essai
add_action('wp_enqueue_scripts', 'add_validation_inscription_essai_js');

function add_validation_inscription_essai_js() {

	if(is_page('test-form-inscription-essai')){
		echo('scrit chargé');
		wp_enqueue_script( 'validation_form_essai_script', get_stylesheet_directory_uri() . '/js/validation_form_essai_script.js', array('nf-front-end'));

		// pass Ajax Url to script.js
		wp_localize_script('validation_form_essai_script', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
	}
}


// on ajoute l'action à exécuter par l'appel ajax

function validateInscriptionEssai() {

    include_once(get_home_path(). '/wp-content/themes/salient-child/ajax/validerInscriptionEssaiAjax.php');
    die();
}


add_action( 'wp_ajax_validateInscriptionEssai', 'validateInscriptionEssai' );
add_action( 'wp_ajax_nopriv_validateInscriptionEssai', 'validateInscriptionEssai' );


/* ------   script choix tarifs ------- */

add_action('wp_enqueue_scripts', 'add_tarifs_js');

function add_tarifs_js() {

    if(is_page('tarifs')){

        wp_enqueue_script( 'script_choix_tarif', get_stylesheet_directory_uri() . '/js/tarifs.js', array( 'jquery' ), '1.0.0', true );
    }
}






/* ---------- fin partie de contrôle des emails sur les formulaires d'inscriptions à l'essai publiques --------- */



/* ---------- partie preremplissage d'un formulaire pour lui passer l'id de l'utilisateur wordpress */





/* ----------- partie pour prémremplir le formulaire avec l'idenfiant user wordpress -------------   */

add_filter( 'ninja_forms_render_default_value', 'my_change_nf_default_value', 10, 3 );
function my_change_nf_default_value( $default_value, $field_type, $field_settings ) {
  if( $default_value == 'foo' ){
    	$default_value = 'foorrr';
    	$field_settings_str = "deb : ".json_encode($field_settings);
     $postfields = array(

    	'par1' => $default_value,
    	'par2' => $field_type,
    	'par3' => $field_settings_str


     );
   }
  return $default_value;
}


/* ------- partie pour rediriger les abonnées vers leur dashboard  ----------- */
function acme_login_redirect( $redirect_to, $request, $user  ) {
	return ( is_array( $user->roles ) && in_array( 'administrator', $user->roles ) ) ? admin_url() : site_url();
}
add_filter( 'login_redirect', 'acme_login_redirect', 10, 3 );





// pour enlever la barre d'admin
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}

// pour ajouter les pages privés au menu
add_filter( 'nav_menu_meta_box_object', 'show_private_pages_menu_selection' );
/**
* Add query argument for selecting pages to add to a menu
*/
function show_private_pages_menu_selection( $args ){
    if( $args->name == 'page' ) {
        $args->_default_query['post_status'] = array('publish','private');
    }
    return $args;
}