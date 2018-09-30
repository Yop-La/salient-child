<?php 

wp_enqueue_style('landing', get_stylesheet_directory_uri().'/css/pages/landing.css','','0.10'); 

?>


<?php
/*
Template Name: landing
*/
?>

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
					
					 the_content(); 

					 global $wp;
					 $actuak_url = home_url( $wp->request );
					 $key = explode ("/", $actuak_url)[4];
					 $json = file_get_contents(get_stylesheet_directory_uri().'/json/page_atterrissage.json');
					 $php_obj = json_decode(json_encode(json_decode($json)),true);

					 $h1 = $php_obj["h1"];
					 $h2 = $php_obj["h2"];
					 $h2_rel = $php_obj["$key"]["0"];
					 $av1 = $php_obj["avantage1"];
					 $av1_rel = $php_obj["$key"]["1"];
					 $av2 = $php_obj["avantage2"];
					 $av2_rel = $php_obj["$key"]["2"];
					 $av3 = $php_obj["avantage3"];
					 

					 /*  première version - page d'atterrissage 


					 [vc_row type="in_container" full_screen_row_position="middle" scene_position="center" text_color="dark" text_align="left" class="vc_hidden-lg" overlay_strength="0.3" shape_divider_position="bottom" shape_type=""][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/1" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][image_with_animation image_url="12328" alignment="center" animation="Fade In" border_radius="none" box_shadow="none" max_width="100%"][/vc_column][/vc_row][vc_row type="in_container" full_screen_row_position="middle" scene_position="center" text_color="dark" text_align="left" top_padding="1%" class="vc_hidden-md vc_hidden-md vc_hidden-xs" overlay_strength="0.3" shape_divider_position="bottom" shape_type=""][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/4" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][image_with_animation image_url="12328" alignment="" animation="Fade In" border_radius="none" box_shadow="none" max_width="100%"][/vc_column][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/4" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][/vc_column][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/4" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][/vc_column][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/4" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][/vc_column][/vc_row][vc_row type="full_width_background" full_screen_row_position="middle" bg_color="#5ccaff" scene_position="center" text_color="dark" text_align="left" overlay_strength="0.3" shape_divider_position="bottom" shape_type=""][vc_column column_padding="padding-5-percent" column_padding_position="left-right" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/1" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][vc_row_inner column_margin="default" top_padding="2%" text_align="left"][vc_column_inner column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/1" column_border_width="none" column_border_style="solid"][vc_column_text]
					 <h1 style="text-align: center;"><strong>Échange maintenant avec un prof toujours connecté
					 </strong></h1>
					 <h3 style="text-align: center;" class="sous-titre">Cette vidéo t\'explique comment il peut t\'aider sur les bissectrices d\'un angle</h3>
					 [/vc_column_text][/vc_column_inner][/vc_row_inner][vc_row_inner column_margin="default" text_align="left"][vc_column_inner column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/2" column_border_width="none" column_border_style="solid"][text-with-icon icon_type="font_icon" icon="icon-arrow-right" color="Accent-Color"]Un prof de maths <strong class="orange">toujours disponible</strong>. Tu peux lui demander de l’aide <strong class="orange">maintenant par messages.</strong>[/text-with-icon][text-with-icon icon_type="font_icon" icon="icon-arrow-right" color="Accent-Color"]Tout est possible ! Il peut <strong class="orange">corriger tes exercices </strong>, répondre à tes questions, te donner un cours sur les bissectrices d\'un angle[/text-with-icon][text-with-icon icon_type="font_icon" icon="icon-arrow-right" color="Accent-Color"]Tous les échanges se font par <strong class="orange">messages</strong>. C\'est gratuit et tu peux envoyer <strong class="orange">des photos de ton travail </strong> pour correction.[/text-with-icon][nectar_btn size="jumbo" button_style="regular-tilt" button_color="Extra-Color-3" icon_family="none" el_class="cta" text="Échanger gratuitement par message avec un prof de maths" margin_right="65%" margin_top="40%" margin_left="65%" css_animation="bounce" url="https://spamtonprof.com/envoyer-message-eleve/"][/vc_column_inner][vc_column_inner column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/2" column_border_width="none" column_border_style="solid"][nectar_video_lightbox link_style="play_button_2" nectar_play_button_color="Default-Accent-Color" image_url="12339" hover_effect="defaut" box_shadow="none" border_radius="none" play_button_size="default" video_url="https://vimeo.com/250474543"][/vc_column_inner][/vc_row_inner][/vc_column][/vc_row][vc_row type="in_container" full_screen_row_position="middle" scene_position="center" text_color="dark" text_align="left" overlay_strength="0.3" shape_divider_position="bottom"][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/1" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][vc_column_text css=".vc_custom_1515670741872{margin-top: 2% !important;margin-bottom: 2% !important;}"]
					 <h2 style="text-align: center;"><strong class="orange">Plus de</strong> <strong>5000 messages envoyés </strong><strong class="orange">par les élèves de SpamTonProf chaque mois</strong></h2>
					 <p style="text-align: right;">* Et les chiffres ne font qu\'augmenter</p>
					 [/vc_column_text][/vc_column][/vc_row]*/
					 


					 $shortcode_content = '[vc_row type="in_container" full_screen_row_position="middle" scene_position="center" text_color="dark" text_align="left" class="vc_hidden-lg" overlay_strength="0.3" shape_divider_position="bottom" shape_type=""][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/1" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][image_with_animation image_url="12328" alignment="center" animation="Fade In" border_radius="none" box_shadow="none" max_width="100%"][/vc_column][/vc_row][vc_row type="in_container" full_screen_row_position="middle" scene_position="center" text_color="dark" text_align="left" top_padding="1%" class="vc_hidden-md vc_hidden-md vc_hidden-xs" overlay_strength="0.3" shape_divider_position="bottom" shape_type=""][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/4" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][image_with_animation image_url="12328" alignment="" animation="Fade In" border_radius="none" box_shadow="none" max_width="100%"][/vc_column][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/4" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][/vc_column][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/4" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][/vc_column][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/4" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][/vc_column][/vc_row][vc_row type="full_width_background" full_screen_row_position="middle" bg_color="#5ccaff" scene_position="center" text_color="dark" text_align="left" overlay_strength="0.3" shape_divider_position="bottom" shape_type=""][vc_column column_padding="padding-5-percent" column_padding_position="left-right" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/1" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][vc_row_inner column_margin="default" top_padding="2%" text_align="left"][vc_column_inner column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/1" column_border_width="none" column_border_style="solid"][vc_column_text]
					 <h1 style="text-align: center;"><strong>'.$h1.'
					 </strong></h1>
					 <h3 style="text-align: center;" class="sous-titre"><strong>'.$h2.' '.$h2_rel.'</strong></h3>
					 [/vc_column_text][/vc_column_inner][/vc_row_inner][vc_row_inner column_margin="default" text_align="left"][vc_column_inner column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/2" column_border_width="none" column_border_style="solid"][text-with-icon icon_type="font_icon" icon="icon-arrow-right" color="Accent-Color"]'.$av1.' '.$av1_rel.'[/text-with-icon][text-with-icon icon_type="font_icon" icon="icon-arrow-right" color="Accent-Color"]'.$av2.' '.$av2_rel.'[/text-with-icon][text-with-icon icon_type="font_icon" icon="icon-arrow-right" color="Accent-Color"]'.$av3.'[/text-with-icon][nectar_btn size="jumbo" button_style="regular-tilt" button_color="Extra-Color-3" icon_family="none" el_class="cta" text="Discuter gratuitement par message avec un prof de maths" margin_right="65%" margin_top="40%" margin_left="65%" css_animation="bounce" url="https://spamtonprof.com/envoyer-message-eleve/"][/vc_column_inner][vc_column_inner column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/2" column_border_width="none" column_border_style="solid"][nectar_video_lightbox link_style="play_button_2" nectar_play_button_color="Default-Accent-Color" image_url="12339" hover_effect="defaut" box_shadow="none" border_radius="none" play_button_size="default" video_url="https://vimeo.com/250474543"][/vc_column_inner][/vc_row_inner][/vc_column][/vc_row][vc_row type="in_container" full_screen_row_position="middle" scene_position="center" text_color="dark" text_align="left" overlay_strength="0.3" shape_divider_position="bottom"][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/1" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][vc_column_text css=".vc_custom_1515670741872{margin-top: 100% !important;margin-bottom: 2% !important;}"]
					 <h2 style="text-align: center;" class="preuve-sociale"><strong class="orange">Plus de</strong> <strong>5000 messages envoyés </strong><strong class="orange">par les élèves de SpamTonProf chaque mois</strong></h2>
					 <p style="text-align: right;">* Et les chiffres ne font qu\'augmenter</p>
					 [/vc_column_text][/vc_column][/vc_row]';

				 echo do_shortcode($shortcode_content);


		
				 endwhile; endif; 
				
			if($page_full_screen_rows == 'on') echo '</div>'; ?>

		</div><!--/row-->
		
	</div><!--/container-->
	
</div><!--/container-wrap-->

<?php get_footer(); ?>