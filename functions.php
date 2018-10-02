<?php 



add_action( 'wp_enqueue_scripts', 'salient_child_enqueue_styles');
function salient_child_enqueue_styles() {
	
wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array('font-awesome'));

if ( is_rtl() ) 
	wp_enqueue_style(  'salient-rtl',  get_template_directory_uri(). '/rtl.css', array(), '1', 'screen' );
}

/* shortcode perso */

function user_name_funct( $atts ){
	$current_user = wp_get_current_user();
	return $current_user->user_firstname  . '<br />';
}
add_shortcode( 'user_name', 'user_name_funct' );

function ajax_spinner_funct( $atts ){

    return '<div id="fountainTextG" ><div id="fountainTextG_1" class="fountainTextG">C</div><div id="fountainTextG_2" class="fountainTextG">h</div><div id="fountainTextG_3" class="fountainTextG">a</div><div id="fountainTextG_4" class="fountainTextG">r</div><div id="fountainTextG_5" class="fountainTextG">g</div><div id="fountainTextG_6" class="fountainTextG">e</div><div id="fountainTextG_7" class="fountainTextG">m</div><div id="fountainTextG_8" class="fountainTextG">e</div><div id="fountainTextG_9" class="fountainTextG">n</div><div id="fountainTextG_10" class="fountainTextG">t</div><div id="fountainTextG_11" class="fountainTextG"> </div><div id="fountainTextG_12" class="fountainTextG">.</div><div id="fountainTextG_13" class="fountainTextG">.</div><div id="fountainTextG_14" class="fountainTextG">.</div></div>';
}
add_shortcode('ajax_spinner', 'ajax_spinner_funct' );









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

/* ----------- partie pour pré-remplir le formulaire avec l'idenfiant user wordpress -------------   */

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

/* pour activer shortcode dans menu */

add_filter('wp_nav_menu_items', 'do_shortcode');

/* enlever barre d'admin sauf pour admin*/

add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}

// /* pour faire un reset des rôles et capacités  */

// if ( !function_exists( 'populate_roles' ) ) {
//     require_once( ABSPATH . 'wp-admin/includes/schema.php' );
// }

// populate_roles();

/* pour pouvoir exécuter du php dans les widgets */

function php_execute($html){
    if(strpos($html,"<"."?php")!==false){ ob_start(); eval("?".">".$html);
    $html=ob_get_contents();
    ob_end_clean();
    }
    return $html;
}
add_filter('widget_text','php_execute',100);



?>