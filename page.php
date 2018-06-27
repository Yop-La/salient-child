<?php 

get_header(); 
nectar_page_header($post->ID); 

//full page
$fp_options = nectar_get_full_page_options();
extract($fp_options);

?>

<div class="container-wrap">
	
	<div class="<?php if($page_full_screen_rows != 'on') echo 'container'; ?> main-content">
		
		<div class="row">
			
			<?php 

			//breadcrumbs
			if ( function_exists( 'yoast_breadcrumb' ) && !is_home() && !is_front_page() ){ yoast_breadcrumb('<p id="breadcrumbs">','</p>'); } 

			 //buddypress
			 global $bp; 
			 if($bp && !bp_is_blog_page()) echo '<h1>' . get_the_title() . '</h1>';
			
			 //fullscreen rows
			 if($page_full_screen_rows == 'on') echo '<div id="nectar_fullscreen_rows" data-animation="'.$page_full_screen_rows_animation.'" data-row-bg-animation="'.$page_full_screen_rows_bg_img_animation.'" data-animation-speed="'.$page_full_screen_rows_animation_speed.'" data-content-overflow="'.$page_full_screen_rows_content_overflow.'" data-mobile-disable="'.$page_full_screen_rows_mobile_disable.'" data-dot-navigation="'.$page_full_screen_rows_dot_navigation.'" data-footer="'.$page_full_screen_rows_footer.'" data-anchors="'.$page_full_screen_rows_anchors.'">';

				 if(have_posts()) : while(have_posts()) : the_post(); 
				 
				 // le formulaire permettant de faire une redirection avec post
				 $hiddenForm = '
				 	<form id="hidden-form" method=POST action="'.get_home_url().'/">
				 		<input type="hidden" id="info" name="info" value="" />
				 	</form>
			     
				 	';
				 echo($hiddenForm);
				 
				 	$message = "Hello :) ";
				 	$hideInfoBar = 'hide'; 

				 	
				 	
				 	if(array_key_exists("info",$_POST) && $_POST["info"] != ""){
				 		$hideInfoBar = '';
				 		$message = $_POST['info'];
				 	}

					 // affiche la barre d'info
					 	echo(do_shortcode('[vc_row class = "'.$hideInfoBar.'" type="full_width_content" full_screen_row_position="middle" bg_color="#2e89ea" scene_position="center" text_color="dark" text_align="left" id="top-message" overlay_strength="0.3" shape_divider_position="bottom" shape_type=""][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/1" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][vc_column_text ]<div class="info-bar" style="text-align: left;"><p id = "band-message" style="text-align: center;">'.$message.'</p></div>[/vc_column_text][/vc_column][/vc_row]'));
					the_content(); 

		
				 endwhile; endif; 
				
			if($page_full_screen_rows == 'on') echo '</div>'; ?>

		</div><!--/row-->
		
	</div><!--/container-->
	
</div><!--/container-wrap-->

<?php get_footer(); ?>