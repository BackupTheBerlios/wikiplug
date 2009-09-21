<?php
/*
Credits: Bit Repository
URL: http://www.bitrepository.com/
*/
$post = (!empty($_POST)) ? true : false;

if($post)
{
	include 'functions.php';
	$mailenvoi = trim($_POST['mail']);
	$email = trim($_POST['email']);

	if ($_POST['type']=='contact')
	{
		$entete = trim($_POST['entete']);
		$name = stripslashes($_POST['name']);
		$subject = stripslashes($_POST['subject']);
		$message = stripslashes($_POST['message']);


		$error = '';

		// Check name

		if(!$name)
		{
		$error .= 'Vous devez entrer un nom.<br />';
		}

		// Check email

		if(!$email)
		{
		$error .= 'Vous devez entrer une adresse mail.<br />';
		}

		if($email && !ValidateEmail($email))
		{
		$error .= 'Votre adresse mail n\'est pas valide.<br />';
		}

		// Check message (length)

		if(!$message || strlen($message) < 10)
		{
		$error .= "Veuillez entrer un message. Il doit faire au minimum 10 caract&egrave;res.<br />";
		}


		if(!$error)
		{
			include_once('Mail.php');

			$entetes['From']    = $email;
			$entetes['To']      = $email;
			$entetes['Subject'] = '['.$entete.'] '.$subject.' de '.$name;

			$corps = $message;			

			// Creer un objet mail en utilisant la methode Mail::factory.
			$objet_mail =& Mail::factory('smtp');

			if($objet_mail->send($mailenvoi, $entetes, $corps))
			{
				echo 'OK';
			}
		}
		else
		{
			echo '<div class="notification_error">'.$error.'</div>';
		}
	}
	elseif ($_POST['type']=='abonne' || $_POST['type']=='desabonne')
	{
		// Check email

		if(!$email)
		{
		$error .= 'Vous devez entrer une adresse mail.<br />';
		}

		if($email && !ValidateEmail($email))
		{
		$error .= 'Votre adresse mail n\'est pas valide.<br />';
		}
	
		if(!$error)
		{			
			include_once('Mail.php');

			$entetes['From']    = $email;
			$entetes['To']      = $email;
			$entetes['Subject'] = 'newsletter';

			$corps = 'newsletter';			

			// Creer un objet mail en utilisant la methode Mail::factory.
			$objet_mail =& Mail::factory('smtp');

			if($objet_mail->send($mailenvoi, $entetes, $corps))
			{
				if ($_POST['type']=='abonne') echo 'abonne';
				elseif  ($_POST['type']=='desabonne') echo 'desabonne';
			}
		}
		else
		{
			echo '<div class="notification_error">'.$error.'</div>';
		}
	}

}
?>
