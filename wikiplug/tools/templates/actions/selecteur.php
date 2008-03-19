<?php
/*
selecteur.php
Code original de ce fichier : Florian SCHMITT
Copyright (c) 2007, Florian SCHMITT <florian.schmitt@laposte.net>
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

/* Paramètres :
 -- type : type de selecteur: theme, style, ou squelette

*******************************************************************************/



if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}


$type = $this->GetParameter("type");

// Affichage de la page ou d'un message d'erreur
if (empty($type)) {
	echo $this->Format("//Le paramètre \"type\" est manquant.//");
} elseif ($type=='squelette' || $type=='style' || $type=='theme' ) {
	$wikini_selecteur= '<div class="selecteur_'.$type.'">
      <form action="" method="post">
        <select name="'.$type.'" onchange="javascript:this.form.submit();">';
        foreach($this->config[$type.'s'] as $key => $value) {
                if($key !== $this->config['favorite_'.$type]) {
                        $wikini_selecteur .= '<option value="'.$key.'">'.$value.'</option>'."\n";
                }
                else {
                		$squelettes=$this->config[$type.'s'];
                        $wikini_selecteur .= '<option value="'.$this->config['favorite_'.$type].'" selected="selected">'.	$squelettes[$this->config['favorite_'.$type]].'</option>'."\n";
                }
        }
        $wikini_selecteur .= '</select>
        <input type="submit" value="Changer le '.$type.'" />
      </form>
    </div>  <!--fin div selecteur_'.$type.'-->';

    echo $wikini_selecteur;
}
else {
	echo $this->Format("//Le paramètre \"type\" ne contient pas les valeurs 'squelette', 'style' ou 'theme'.//");
}
?>
