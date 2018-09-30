<?php
/*
Template Name: autoblogging
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
					try
					{
						$bdd = new PDO('pgsql:host='.DB_HOST_EXT.';port=5432;dbname='.DB_NAME_EXT.';user='.DB_USER_EXT.';password='.DB_PASSWORD_EXT);

					}
					catch(Exception $e)
					{
					        die('Erreur : '.$e->getMessage());
					}
			
						/*

					 --------------- rassembler les messages d'une même journée n'appartenant pas à un article sous un même article --------

					*/

					// on récupère les messages qui ne sont pas dans un article
					$req_message = $bdd->prepare("select ref_message, message, date(date_message) as date_article from message_eleve where ref_message not in (select ref_message from composition_article_eleve) and EXTRACT(DAY FROM date_message) != ? and EXTRACT(DAY FROM date_message) != ? and message_formate is not null");
					$req_message->execute(array($jour_mois,$jour_mois-1));
					$ret="";
					while($message = $req_message->fetch())
					{
						$date_article = $message['date_article'];
						$ref_message = $message['ref_message'];

						$req = $bdd->prepare("select ref_article, nb_message from article_eleve where date_message = ?");
						$req->execute(array($date_article));
						if($article = $req->fetch()){ // si l'article existe
							$ref_article = $article['ref_article'];
							$nb_message = $article['nb_message'];
							$req = $bdd->prepare("insert into composition_article_eleve(ref_article,ref_message) values(?,?)");
							$req->execute(array($ref_article,$ref_message));
							$req = $bdd->prepare("update article_eleve set nb_message = ? where ref_article = ?");
							$req->execute(array($nb_message+1,$ref_article));
						}else{
							$req = $bdd->prepare("insert into article_eleve(date_message) values(?)");
							$req->execute(array($date_article));
							$ref_article = $bdd->lastInsertId();
							$req = $bdd->prepare("insert into composition_article_eleve(ref_article,ref_message,publie,nb_message) values(?,?)");
							$req->execute(array($ref_article,$ref_message));
							$req = $bdd->prepare("update article_eleve set nb_message = 0, publie = false where ref_article = ?");
							$req->execute(array($ref_article));
						}




					}


					/*

					 --------------- publication des articles --------

					*/

					/* on sélectionne les articles pas publiés qui comportent de plus 40 articles */
					$req_article = $bdd->prepare("select ref_article, date_message, nb_message from article_eleve where publie = false and nb_message > 40");
					$req_article->execute();

					$article = $req_article->fetch();

						$date_question = strtotime($article['date_message']);
						$ref_article = $article['ref_article'];
						$nb_message = $article['nb_message'];



						setlocale(LC_TIME, "fr_FR"); //pour avoir les dates en français
						$post_title = 'Toutes les question de maths, physique, chimie posées par nos élèves '. strftime("le %A %e %B %Y" ,$date_question);

						// paragraphe d'intro (toujours le même)
						$post_content = '[vc_row type="in_container" full_screen_row_position="middle" scene_position="center" text_color="dark" text_align="left" overlay_strength="0.3" shape_divider_position="bottom"][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/1" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][vc_column_text]
<div>Toi aussi tu bloque sur ces questions ? Toutes les questions ici nous sont posées par nos élèves. Et nos profs y répondent tous les jours ! Mais pas que ... En fait, nous répondons à toute leurs demandes en maths et en physique. Par exemple, <strong>on corrige leurs exos à la main,</strong> on leur envoie des cours.  Ce soutien est<strong> illimité</strong> et valable tous les jours de 8h à 22h (sauf le dimanche). Nos profs font bien plus que donner des cours particuliers, ce sont de véritables coachs qui savent comment donner la motivation.</div>
<div></div>
<div>Si toi aussi, tu veux une réponse à ta question alors rejoins nous. Il y a une semaine <strong>d\'essai offerte</strong>. Je suis sûr qu\'e l\'on peut t\'aider ! Pour te faire une idée, je t\'invite à regarder ci dessous tous les messages et questions envoyés par nos élèves. Cela te permettra de voir tout ce que nos profs apportent en une journée seulement à nos élèves</div>
[/vc_column_text][/vc_column][/vc_row]';


						// compter le nombre de questions
						$req_question = $bdd->prepare("select count(*) as nb_question from composition_article_eleve, message_eleve 
							where composition_article_eleve.ref_message = message_eleve.ref_message
								and ref_article = ?
						        and question is not null");
						$req_question->execute(array($ref_article));
						$nb_question = $req_question->fetch();
						$nb_question = $nb_question['nb_question'];

						$nb_message_by_question = intval($nb_message/$nb_question);
						
						$nb_message_at_the_end = $nb_message%$nb_question;

						$req_message = $bdd->prepare("select message_formate, date_message from composition_article_eleve, message_eleve 
							where composition_article_eleve.ref_message = message_eleve.ref_message
								and ref_article = ?
						        and message_formate is not null");
						$req_message->execute(array($ref_article));

						$req_question = $bdd->prepare("select question from composition_article_eleve, message_eleve 
							where composition_article_eleve.ref_message = message_eleve.ref_message
								and ref_article = ?
						        and question is not null");
						$req_question->execute(array($ref_article));

						$nb_message_in_row = 0; //le nombre de message dans une row courante. Une row = une question + des messages
						$row = "";
						$nb_row = 0;
						$still_question = true;
						$no_more_row = false;
						$col1 = "";
						$col2 = "";
						$col3 = "";

						echo("nb message by quetion : ".$nb_message_by_question."<br>");
						echo("nb message : ".$nb_message."<br>");
						echo("nb message at the end : ".$nb_message_at_the_end."<br>");
						echo("nb message at the end : ".$nb_message_at_the_end."<br>");
						echo("nb question : ".$nb_question."<br>");

						while($message = $req_message->fetch()){


							// création de la row avec  la question en h2, du début de l'inner row et le début des 3 colonnes
							if($nb_message_in_row == 0 && ! $no_more_row)  {

								$question = $req_question->fetch();
								$question = $question['question'];


								$row = 	'[vc_row type="in_container" full_screen_row_position="middle" scene_position="center" text_color="dark" text_align="left" overlay_strength="0.3" shape_divider_position="bottom"][vc_column column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/1" tablet_text_alignment="default" phone_text_alignment="default" column_border_width="none" column_border_style="solid"][vc_column_text]<h2>'.$question.'</h2>[/vc_column_text][vc_row_inner column_margin="default" text_align="left" css=".vc_custom_1512641830475{margin-top: 2% !important;}"]';
								$col1 = '[vc_column_inner column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/3" column_border_width="none" column_border_style="solid"]';
								$col2 = '[vc_column_inner column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/3" column_border_width="none" column_border_style="solid"]';
								$col3 = '[vc_column_inner column_padding="no-extra-padding" column_padding_position="all" background_color_opacity="1" background_hover_color_opacity="1" column_shadow="none" column_border_radius="none" width="1/3" column_border_width="none" column_border_style="solid"]';

								$nb_row = $nb_row + 1;
								echo("nb row : ".$nb_row."<br>");
								if($nb_row == $nb_question){
									$no_more_row = true;
								}
							}

							$col_to_fill = ($nb_message_in_row % 3 ) + 1;
							$message = $message['message_formate'];
							$date_message = date("d/m/Y",strtotime($message['date_message']));
							$hour = date("H:i:s",strtotime($message['date_message']));



							if($col_to_fill == 1 ){
								$col1 = $col1.'[nectar_flip_box bg_color="#ff7400" text_color="dark" icon_color="Accent-Color" text_color_2="dark" h_text_align="center" v_text_align="center" flip_direction="horizontal-to-left" front_content="'.$message.'" min_height="20"]<p style="text-align: center;">Envoyé le</p><p style="text-align: center;">'.$date_message.'</p><p style="text-align: center;">à</p><p style="text-align: center;">'.$hour.'</p>[/nectar_flip_box]';
							}
							if($col_to_fill == 2 ){
								$col2 = $col2.'[nectar_flip_box bg_color="#ff7400" text_color="dark" icon_color="Accent-Color" text_color_2="dark" h_text_align="center" v_text_align="center" flip_direction="horizontal-to-left" front_content="'.$message.'" min_height="20"]<p style="text-align: center;">Envoyé le</p><p style="text-align: center;">'.$date_message.'</p><p style="text-align: center;">à</p><p style="text-align: center;">'.$hour.'</p>[/nectar_flip_box]';
							}
							if($col_to_fill == 3 ){
								$col3 = $col3.'[nectar_flip_box bg_color="#ff7400" text_color="dark" icon_color="Accent-Color" text_color_2="dark" h_text_align="center" v_text_align="center" flip_direction="horizontal-to-left" front_content="'.$message.'" min_height="20"]<p style="text-align: center;">Envoyé le</p><p style="text-align: center;">'.$date_message.'</p><p style="text-align: center;">à</p><p style="text-align: center;">'.$hour.'</p>[/nectar_flip_box]';
							}
							$nb_message_in_row = $nb_message_in_row + 1;

							echo("nb message : ".$nb_message_in_row."<br>");




							if(($nb_message_in_row == $nb_message_by_question && ! $no_more_row ) || ($no_more_row && $nb_message_in_row == $nb_message_by_question + $nb_message_at_the_end))  {
								$nb_message_in_row = 0;
								$col1 = $col1.'[/vc_column_inner]';
								$col2 = $col2.'[/vc_column_inner]';
								$col3 = $col3.'[/vc_column_inner]';
								$row = $row.$col1.$col2.$col3.'[/vc_row_inner][/vc_column][/vc_row]';
								$post_content=$post_content.$row; 
							}
						}
						$new_page_id = wp_insert_post(array(
						    'post_title'     => $post_title,
						    'post_type'      => 'post',
						    'comment_status' => 'closed',
						    'ping_status'    => 'closed',
						    'post_content'   => $post_content,
						    'post_status'    => 'publish'
						));
						echo($new_page_id); 
		
				 endwhile; endif; 
				
			if($page_full_screen_rows == 'on') echo '</div>'; ?>

		</div><!--/row-->
		
	</div><!--/container-->
	
</div><!--/container-wrap-->

<?php get_footer(); ?>