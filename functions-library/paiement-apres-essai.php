<?php

/*

date création : 27-03-2018
Ce srcipt est destiné à fonctionner sur cette page :  abonnement-apres-essai
Il sert à charger le script js et faire les appels ajax nécessaire au fonctionnement du paiement

*/


// chargement du script js de récupération du ou des comptes d'essai à partir de l'adresse mail saisie
add_action('wp_enqueue_scripts', 'paiementEssai');

function paiementEssai() {

	if(is_page('abonnement-apres-essai') ){

		wp_enqueue_script( 'get_trial_account_js', get_stylesheet_directory_uri() . '/js/get_trial_account.js', array('nf-front-end'),  time() );
		wp_enqueue_script( 'stripe_js', 'https://checkout.stripe.com/checkout.js' );

		// pass Ajax Url to script.js
		wp_localize_script('get_trial_account_js', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
	}
}



/********* debut ajax récupérer comptes stp formulaire d'inscription ***************/
// cette fonction sera exécute par dans un appel ajax réalisé dans get_trial_account.js

function ajaxGetTrialAccount() {
	header('Content-type: application/json');

	//On enregistre notre autoload.
	
	function chargerClasseStp($classname)
	{	
	  include_once(get_stylesheet_directory().'/stp_api/'.$classname.'.php');
	}

	spl_autoload_register('chargerClasseStp');

	try
	{
	    $bdd = new PDO('pgsql:host='.DB_HOST_PG.';port=5432;dbname='.DB_NAME_PG.';user='.DB_USER_PG.';password='.DB_PASSWORD_PG);

	}
	catch(Exception $e)
	{
	        die('Erreur : '.$e->getMessage());
	}


	$accountManager = new AccountManager($bdd);
	$accounts = $accountManager->getList($_POST["email"]);
	$accounts = $accountManager->filterByAttentePaiement($accounts,true);

	echo(json_encode($accounts));
	die();
}


add_action( 'wp_ajax_ajaxGetTrialAccount', 'ajaxGetTrialAccount' );
add_action( 'wp_ajax_nopriv_ajaxGetTrialAccount', 'ajaxGetTrialAccount' );

/********* fin ajax récupérer comptes stp formulaire d'inscription ***************/

/********* debut ajax faire l'abonnnement stripe dans formulaire d'inscription ***************/
// cette fonction sera exécute par dans un appel ajax réalisé dans get_trial_account.js

function ajaxStripeSubscription() {
	require_once(get_stylesheet_directory().'/stripe-php-6.4.2/init.php');
	require_once('/home/clients/yopyopla/prod/spamtonprof/getresponse/GetResponseAPI3.class.php' );
	header('Content-type: application/json');

	//On enregistre notre autoload.
	
	function chargerClasseStp($classname)
	{	
	  include_once(get_stylesheet_directory().'/stp_api/'.$classname.'.php');
	}

	spl_autoload_register('chargerClasseStp');

	try
	{
	    $bdd = new PDO('pgsql:host='.DB_HOST_PG.';port=5432;dbname='.DB_NAME_PG.';user='.DB_USER_PG.';password='.DB_PASSWORD_PG);

	}
	catch(Exception $e)
	{
	        die('Erreur : '.$e->getMessage());
	}

	$token = $_POST['token'];
	$tokenId = $token["id"];
	$refCompte = $_POST['ref_compte'];
	$emailParent = $_POST['email_parent'];
	$planStripe = $_POST['plan_stripe'];

	\Stripe\Stripe::setApiKey(PROD_SECRET_KEY_STRP);

	try
	{
		$customer = \Stripe\Customer::create(array(
		'email' => $emailParent,
		'source'  => $tokenId,
		"metadata" => array(
			"compte" => $refCompte
		)
		));


		$subscription = \Stripe\Subscription::create(array(
		"customer" => $customer->id,
		"items" => array(
		  array(
		    "plan" => $planStripe
		  )
		)
		));
		to_log_abonnement(array("str1" => "ref compte : ".$refCompte,"str2" => "emailParent : ".$emailParent,"str3" => "ref abonnement stripe : ".$subscription->id));
		echo(json_encode('done'));
	}
	catch(Exception $e)
	{
		to_log_slack(array("str1" => "error paiement apres essai".$e->getMessage()));
		echo(json_encode("paiement_failure"));
		return;			  
	}



	// metre à jour le compte stp
	// retrouver le compte dans l'api stp
	$accountManager = new AccountManager($bdd);
	$account = $accountManager->get($refCompte);
	$account->setAttente_paiement(false);
	$account->setStatut("inscrit");
	$accountManager->updateAfterSubsCreated($account);

	$email_eleve = $account->eleve()->adresse_mail();
	$email_parent = $account->proche()->adresse_mail();
	$prenom_eleve = $account->eleve()->prenom();
	$prenom_parent = $account->proche()->prenom();

	// faire les changements de liste
	$getresponse = new GetResponse(GR_API);

	//déterminer les ref de campagne
	$campaignIdProche; 
	$campaignIdEleve;
	$campaignIdEleveOld;
	$campaignIdProcheOld;
	if($account ->francais()){
		$campaignIdProche = "4b4hs";
		$campaignIdEleve = "4b4vi";
		$campaignIdProcheOld = "4t7kQ";
		$campaignIdEleveOld = "4t7ut";
	}else if($account ->maths() or $account ->physique()){
		$campaignIdProche = "45XJl";
		$campaignIdEleve = "45X2f";
		$campaignIdProcheOld = "4TPZW";
		$campaignIdEleveOld = "4TP5I";
	}

	// supprimer les doublons d'emails élèves
	$contact_eleve;
	$contacts = $getresponse->getContacts(array("query[email]"=>$email_eleve,"query[campaignId]"=>$campaignIdEleveOld));
	$number_contact = count((array)$contacts);
	if($number_contact > 1){ //pour supprimer les contacts doublons
		$i = 0;
		foreach ($contacts as $contact) {
			if(++$i === $number_contact) {
				$contact_eleve = $contact;
			}else{
				$ret =$getresponse->deleteContact($contact->contactId);
			}
		}
	}else if($number_contact == 1){
		$contacts = (array)$contacts;
		$contact_eleve = $contacts[0];	

		if($email_eleve != $email_parent){
			// changement de campagne élève
			$params = '{
			    "campaign": {
			    	"campaignId": "'.$campaignIdEleve.'"
			    },
			    "dayOfCycle": "0"
			}';
			$params = json_decode($params);
			$res = $getresponse->updateContact($contact_eleve->contactId, $params);
			// update du contact proche
			$params = '{
			    "name": "'.$prenom_eleve.'",
			    "customFieldValues": [
			        {
			            "customFieldId": "3ytt8",
			            "value": [
			                "'.$prenom_parent.'"
			            ]
			        }
			    ]
			}';
			$params = json_decode($params);
			$res = $getresponse->updateContact($contact_eleve->contactId, $params);
		}else{
			$ret =$getresponse->deleteContact($contact_eleve->contactId);
		}

	}else if($number_contact == 0){

		if($email_eleve != $email_parent){
			$params = '{
			    "name": "'.$prenom_eleve.'",
			    "email": "'.$email_eleve.'",
			    "campaign": {
			    	"campaignId": "'.$campaignIdEleve.'"
			    },
			    "dayOfCycle": "0",
			    "customFieldValues": [
			        {
			            "customFieldId": "3ytt8",
			            "value": [
			                "'.$prenom_parent.'"
			            ]
			        }
			    ]
			}';
			$params = json_decode($params);
			$res = $getresponse->addContact($params);
		}
	}

		
	// supprimer les doublons d'emails parents
	$contact_parent;
	$contacts = $getresponse->getContacts(array("query[email]"=>$email_parent,"query[campaignId]"=>$campaignIdProcheOld));
	$number_contact = count((array)$contacts);
	if($number_contact > 1){ //pour supprimer les contacts doublons
		$i = 0;
		foreach ($contacts as $contact) {
			if(++$i === $number_contact) {
				$contact_parent = $contact;
			}else{
				$ret =$getresponse->deleteContact($contact->contactId);
			}
		}
	}else if($number_contact == 1){
		$contacts = (array)$contacts;
		$contact_parent = $contacts[0];	
		// changement de campagne parent
		$params = '{
		    "campaign": {
		    	"campaignId": "'.$campaignIdProche.'"
		    },
		    "dayOfCycle": "0"
		}';
		$params = json_decode($params);
		$res = $getresponse->updateContact($contact_parent->contactId, $params);

		// update du contact proche
		$params = '{
		    "name": "'.$prenom_parent.'",
		    "customFieldValues": [
		        {
		            "customFieldId": "3ytt8",
		            "value": [
		                "'.$prenom_eleve.'"
		            ]
		        }
		    ]
		}';
		$params = json_decode($params);
		$res = $getresponse->updateContact($contact_parent->contactId, $params);

	}else if($number_contact == 0){
		// update du contact proche
		$params = '{
		    "name": "'.$prenom_parent.'",
		    "email": "'.$email_parent.'",
		    "campaign": {
		    	"campaignId": "'.$campaignIdProche.'"
		    },
		    "dayOfCycle": "0",
		    "customFieldValues": [
		        {
		            "customFieldId": "3ytt8",
		            "value": [
		                "'.$prenom_eleve.'"
		            ]
		        }
		    ]
		}';
		$params = json_decode($params);
		$res = $getresponse->addContact( $params);

	}

	
	die();
}


add_action( 'wp_ajax_ajaxStripeSubscription', 'ajaxStripeSubscription' );
add_action( 'wp_ajax_nopriv_ajaxStripeSubscription', 'ajaxStripeSubscription' );

/********* fin ajax faire l'abonnnement stripe dans formulaire d'inscription ***************/


?>