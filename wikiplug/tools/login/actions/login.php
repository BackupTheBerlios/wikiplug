<?php
/*
login.php
Copyright 2010  Florian SCHMITT
All rights reserved.
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
notice, this list of conditions and the following disclaimer in the
documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

// Lecture des parametres de l'action
$signupurl = $this->GetParameter('signupurl');
// si pas de pas d'url d'inscription renseign�e, on utilise ParametresUtilisateur
if (empty($signupurl)) {
	$signupurl = $this->href("", "ParametresUtilisateur", "");
}

$incomingurl = 'http'.((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || $_SERVER['SERVER_PORT'] == 443) ? 's' : '').'://'.
		(($_SERVER['SERVER_PORT']!='80') ? $_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].$_SERVER['SCRIPT_NAME'] : 
		$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']).
		(($_SERVER['QUERY_STRING']>' ') ? '?'.$_SERVER['QUERY_STRING'] : '');

$userpage = $this->GetParameter("userpage");

// si pas d'url de page de sortie renseign�e, on retourne sur la page courante
if (empty($userpage)) {
	$userpage = $incomingurl;
		
	//si l'url de sortie contient le passage de parametres de d�connexion, on l'efface
	if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "logout") {
		$userpage = str_replace('&action=logout', '', $userpage);
	}
}

$class = $this->GetParameter("class");

$template = $this->GetParameter("template");
if (empty($template) || !file_exists('tools/login/presentation/templates/'.$template) ) {
	$template="default.tpl.html";
}

$error = '';
$PageMenuUser = '';

// on initialise la valeur vide si elle n'existe pas
if (!isset($_REQUEST["action"])) $_REQUEST["action"] = '';

// cas de la d�connexion
if ($_REQUEST["action"] == "logout") {
	$this->LogoutUser();
	$this->SetMessage("Vous &ecirc;tes maintenant d&eacute;connect&eacute; !");
	$this->Redirect($incomingurl);
}

// cas de l'identification
if ($_REQUEST["action"] == "login") {	
	// si l'utilisateur existe, on v�rifie son mot de passe
	if (isset($_POST["name"]) && $existingUser = $this->LoadUser($_POST["name"])) {
		// Si le mot de passe est bon, on cr��e le cookie et on redirige sur la bonne page
		if ($existingUser["password"] == md5($_POST["password"])) {
			$this->SetUser($existingUser, $_POST["remember"]);
			// si l'on veut utiliser la page d'accueil correspondant au nom d'utilisateur
			if ( $userpage=='user' && $this->LoadPage($_POST["name"]) ) {
				$this->Redirect($this->href('', $_POST["name"], ''));
			}
			// on va sur la page d'ou on s'est identifie sinon
			else {
				$this->Redirect($_POST['incomingurl']);
			}			
		}
		// on affiche une erreur sinon
		else {
			$error = "Mauvais mot de passe&nbsp;!";
			$this->Redirect($_POST['incomingurl'].'&error='.urlencode($error));
		}
	}
}

// cas d'une personne connect�e d�j�
if ($user = $this->GetUser()) {
	$connected = true;
	if ( $userpage=='user' ) {
		$PageMenuUser .= '<a class="login-user-page-link" href="'.$this->href('', $user["name"], '').'" title="Voir mon espace personnel">Mon espace personnel</a><br />';
	}
	if ($this->LoadPage("PageMenuUser")) { 
		$PageMenuUser .= $this->Format("{{include page=\"PageMenuUser\"}}");
	}
}
// cas d'une personne non connect�e
else {
	$connected = false;
	// si l'authentification passe mais la session n'est pas cr��e, on a un probl�me de cookie	
	if ($_REQUEST['action'] == 'checklogged') {
		$error = 'Vous devez accepter les cookies pour pouvoir vous connecter.';
	}
}

//on affiche le template
if (!class_exists('SquelettePhp')) include_once('tools/login/libs/squelettephp.class.php');
$squel = new SquelettePhp('tools/login/presentation/templates/'.$template);
$squel->set(array(
	"connected" => $connected,
	"user" => ((isset($user["name"])) ? $user["name"] : ((isset($_POST["name"])) ? $_POST["name"] : '' )), 
	"incomingurl" => $incomingurl, 
	"signupurl" => $signupurl, 
	"userpage" => $userpage,
	"PageMenuUser" => $PageMenuUser,
	"error" => $error
));

$output = (!empty($class)) ? '<div class="'.$class.'">'."\n".$squel->analyser()."\n".'</div>'."\n" : $squel->analyser() ;

echo $output;
?>
