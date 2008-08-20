<?php
/*
usersettings.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2008 David DELON
Copyright 2002, 2003 Charles NEPOTE
Copyright 2002  Patrick PAUL
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

//Lecture des parametres de l'action
$titre = $this->GetParameter("titre");
if (empty($titre)) {
	$titre='Identifiez-vous ici :';
}
$bienvenue = $this->GetParameter("bienvenue");
if (empty($bienvenue)) {
	$bienvenue='Bonjour, ';
}

$urllogin = $this->GetParameter("url");
if (empty($urllogin)) {
	$urllogin=$this->href("", "ParametresUtilisateur", "");
}

if (!isset($_REQUEST["action"])) $_REQUEST["action"] = '';
if ($_REQUEST["action"] == "logout")
{
	$this->LogoutUser();
	$this->SetMessage("Vous &ecirc;tes maintenant d&eacute;connect&eacute; !");
	$this->Redirect($_POST['urldepart']);
}
else if ($user = $this->GetUser())
{
	// user is logged in; display config form
	echo "<form action=\"".$urllogin."\" method=\"post\">\n";
	?>
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="urldepart" value="<?php echo $this->href(); ?>" />
	<?php echo $bienvenue.$this->Link($user["name"]) ?>&nbsp;!
	<input class="bouton_iden" type="button" value="D&eacute;connexion" onclick="document.location='<?php echo $urllogin."&action=logout"; ?>'" />
	<span class="inscription_iden"><a href="<?php echo $urllogin;  ?>" title="S'inscrire">Modifier mon inscription</a></span>
	<?php
	echo $this->FormClose();

}
else
{
	// user is not logged in
	
	// is user trying to log in or register?
	if ($_REQUEST["action"] == "login")
	{
		// if user name already exists, check password
		if ($existingUser = $this->LoadUser($_POST["name"]))
		{
			// check password
			if ($existingUser["password"] == md5($_POST["password"]))
			{
				$this->SetUser($existingUser, 0);
				SetCookie("name", $existingUser["name"],0, $this->CookiePath);
				SetCookie("password", $existingUser["password"],0, $this->CookiePath);
				$this->Redirect($_POST['urldepart']);
			}
			else
			{
				$error = "Mauvais mot de passe&nbsp;!";
			}
		}
	}
	elseif ($_REQUEST['action'] == 'checklogged')
	{
		$error = 'Vous devez accepter les cookies pour pouvoir vous connecter.';
	}

	echo "<form action=\"".$urllogin."\" method=\"post\">\n";
	?>
	<input type="hidden" name="action" value="login" />
	<input type="hidden" name="urldepart" value="<?php echo $this->href(); ?>" />
	<?php
		if (isset($error))
		{
			echo "<div class=\"error\">", $this->Format($error), "</div>\n";
		}
		
		echo '
		<span class="texte_iden">'.IDEN_DEJA_MEMBRE.'</span>
		<span class="label_iden">'.IDEN_NOM_WIKI.'</span><input name="name" class="input_iden" size="7" value="';
		if (isset($name)) echo htmlspecialchars($name);
		echo '" /><br />
		<span class="label_iden">'.IDEN_MDP.'</span><input type="password" name="password" class="input_iden" size="7" /><br />
		<input type="hidden" name="remember" value="0" /><input type="checkbox" id="remember" name="remember" value="1" />
		<label for="remember">'.IDEN_SOUVENIR.'</label>
		<input type="submit" class="bouton_iden" value="'.IDEN_S_IDENTIFIER.'" />
		<span class="texte_iden">'.IDEN_PAS_MEMBRE.'</span>
		<span class="inscription_iden"><a href="';
		echo $urllogin;
		echo '" title="'.IDEN_S_ENREGISTRER.'">'.IDEN_S_ENREGISTRER.'</a></span>';
		?>
	<?php
	echo $this->FormClose();
}
?>

