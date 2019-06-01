<?php
get_header();
nectar_page_header($post->ID);

// full page
$fp_options = nectar_get_full_page_options();
extract($fp_options);

?>

<div class="container-wrap">

	<div
		class="<?php if($page_full_screen_rows != 'on') echo 'container'; ?> main-content">

		<div class="row">
			
			<?php

// breadcrumbs
if (function_exists('yoast_breadcrumb') && ! is_home() && ! is_front_page()) {
    yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
}

// buddypress
global $bp;
if ($bp && ! bp_is_blog_page())
    echo '<h1>' . get_the_title() . '</h1>';

// fullscreen rows
if ($page_full_screen_rows == 'on')
    echo '<div id="nectar_fullscreen_rows" data-animation="' . $page_full_screen_rows_animation . '" data-row-bg-animation="' . $page_full_screen_rows_bg_img_animation . '" data-animation-speed="' . $page_full_screen_rows_animation_speed . '" data-content-overflow="' . $page_full_screen_rows_content_overflow . '" data-mobile-disable="' . $page_full_screen_rows_mobile_disable . '" data-dot-navigation="' . $page_full_screen_rows_dot_navigation . '" data-footer="' . $page_full_screen_rows_footer . '" data-anchors="' . $page_full_screen_rows_anchors . '">';

if (have_posts()) :
    while (have_posts()) :
        the_post();
        
        // le formulaire permettant de faire une redirection avec post
        $hiddenForm = '
				 	<form id="hidden-form" method=POST action="' . get_home_url() . '/">
				 		<input type="hidden" id="info" name="info" value="" />
				 	</form>
			     
				 	';
        echo ($hiddenForm);
        
        $message = "Hello :) ";
        $hideInfoBar = 'hide';
        
        if (array_key_exists("info", $_POST) && $_POST["info"] != "") {
            $hideInfoBar = '';
            $message = $_POST['info'];
            $message = stripslashes($message);
        }
        
        if (array_key_exists("message", $_SESSION) && $_SESSION["message"] != "") {
            $hideInfoBar = '';
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
            $message = stripslashes($message);
        }
        
        // affiche la barre d'info
        echo (do_shortcode('[vc_row class = "' . $hideInfoBar . '" type="full_width_content" full_screen_row_position="middle" bg_color="#2e89ea" scene_position="center" text_color="dark" text_align="left" id="top-message" overlay_strength="0.3" shape_divider_position="bottom" shape_type=""][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/1" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][vc_column_text ]<div class="info-bar" style="text-align: left;"><p id = "band-message" style="text-align: center;">' . $message . '</p></div>[/vc_column_text][/vc_column][/vc_row]'));
       
        echo (do_shortcode('[vc_row type="in_container" full_screen_row_position="middle" scene_position="center" text_color="dark" text_align="left" class="hide" id="loading_screen" overlay_strength="0.3" shape_divider_position="bottom" bg_image_animation="none" shape_type=""][vc_column centered_text="true" column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_link_target="_self" column_shadow="none" column_border_radius="none" width="1/1" tablet_width_inherit="default" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid" bg_image_animation="none"][vc_raw_html css=".vc_custom_1559325680441{margin-top: 50% !important;margin-bottom: 50% !important;}"]JTNDZGl2JTIwaWQlM0QlMjJsb2FkaW5nU3Bpbm5lciUyMiUyMGNsYXNzJTIwJTNEJTIwJTIyc3Bpbm5lci1zdHlsZSUyMiUzRSUzQ2RpdiUyMGlkJTNEJTIyZm91bnRhaW5UZXh0R18xJTIyJTIwY2xhc3MlM0QlMjJmb3VudGFpblRleHRHJTIyJTNFQyUzQyUyRmRpdiUzRSUzQ2RpdiUyMGlkJTNEJTIyZm91bnRhaW5UZXh0R18yJTIyJTIwY2xhc3MlM0QlMjJmb3VudGFpblRleHRHJTIyJTNFaCUzQyUyRmRpdiUzRSUzQ2RpdiUyMGlkJTNEJTIyZm91bnRhaW5UZXh0R18zJTIyJTIwY2xhc3MlM0QlMjJmb3VudGFpblRleHRHJTIyJTNFYSUzQyUyRmRpdiUzRSUzQ2RpdiUyMGlkJTNEJTIyZm91bnRhaW5UZXh0R180JTIyJTIwY2xhc3MlM0QlMjJmb3VudGFpblRleHRHJTIyJTNFciUzQyUyRmRpdiUzRSUzQ2RpdiUyMGlkJTNEJTIyZm91bnRhaW5UZXh0R181JTIyJTIwY2xhc3MlM0QlMjJmb3VudGFpblRleHRHJTIyJTNFZyUzQyUyRmRpdiUzRSUzQ2RpdiUyMGlkJTNEJTIyZm91bnRhaW5UZXh0R182JTIyJTIwY2xhc3MlM0QlMjJmb3VudGFpblRleHRHJTIyJTNFZSUzQyUyRmRpdiUzRSUzQ2RpdiUyMGlkJTNEJTIyZm91bnRhaW5UZXh0R183JTIyJTIwY2xhc3MlM0QlMjJmb3VudGFpblRleHRHJTIyJTNFbSUzQyUyRmRpdiUzRSUzQ2RpdiUyMGlkJTNEJTIyZm91bnRhaW5UZXh0R184JTIyJTIwY2xhc3MlM0QlMjJmb3VudGFpblRleHRHJTIyJTNFZSUzQyUyRmRpdiUzRSUzQ2RpdiUyMGlkJTNEJTIyZm91bnRhaW5UZXh0R185JTIyJTIwY2xhc3MlM0QlMjJmb3VudGFpblRleHRHJTIyJTNFbiUzQyUyRmRpdiUzRSUzQ2RpdiUyMGlkJTNEJTIyZm91bnRhaW5UZXh0R18xMCUyMiUyMGNsYXNzJTNEJTIyZm91bnRhaW5UZXh0RyUyMiUzRXQlM0MlMkZkaXYlM0UlM0NkaXYlMjBpZCUzRCUyMmZvdW50YWluVGV4dEdfMTElMjIlMjBjbGFzcyUzRCUyMmZvdW50YWluVGV4dEclMjIlM0UlMjAlM0MlMkZkaXYlM0UlM0NkaXYlMjBpZCUzRCUyMmZvdW50YWluVGV4dEdfMTIlMjIlMjBjbGFzcyUzRCUyMmZvdW50YWluVGV4dEclMjIlM0UuJTNDJTJGZGl2JTNFJTNDZGl2JTIwaWQlM0QlMjJmb3VudGFpblRleHRHXzEzJTIyJTIwY2xhc3MlM0QlMjJmb3VudGFpblRleHRHJTIyJTNFLiUzQyUyRmRpdiUzRSUzQ2RpdiUyMGlkJTNEJTIyZm91bnRhaW5UZXh0R18xNCUyMiUyMGNsYXNzJTNEJTIyZm91bnRhaW5UZXh0RyUyMiUzRS4lM0MlMkZkaXYlM0UlM0MlMkZkaXYlM0U=[/vc_raw_html][vc_column_text]</p><p>[/vc_column_text][/vc_column][/vc_row]'));
        
        the_content();
    endwhile
    ; endif;

if ($page_full_screen_rows == 'on')
    echo '</div>';
?>

		</div>
		<!--/row-->

	</div>
	<!--/container-->

</div>
<!--/container-wrap-->

<?php get_footer(); ?>