<?php
/*
$Id: exportcsv.php,v 1.1 2010/10/21 12:32:59 mrflos Exp $
Copyright (c) 2010, Florian Schmitt <florian@outils-reseaux.org>
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

// Vérification de sécurité
if (!defined("WIKINI_VERSION"))
{
	die ("acc&egrave;s direct interdit");
}

$output = '<h1>Export CSV</h1>'."\n";

if (!isset($categorienature)) $categorienature = 'toutes';
$id_type_fiche = (isset($_POST['id_type_fiche'])) ? $_POST['id_type_fiche'] : '';

//On choisit un type de fiches pour parser le csv en conséquence
//requete pour obtenir l'id et le label des types d'annonces
$requete = 'SELECT bn_id_nature, bn_label_nature, bn_template FROM '.BAZ_PREFIXE.'nature WHERE';
($categorienature!='toutes') ? $requete .= ' bn_type_fiche="'.$categorienature.'"' : $requete .= ' 1';
(isset($GLOBALS['_BAZAR_']['langue'])) ? $requete .= ' AND bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ' : $requete .= '';
$requete .= ' ORDER BY bn_label_nature ASC';
$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	
$output .= '<form method="post" action="'.$this->href('exportcsv').'">'."\n";

//s'il y a plus d'un choix possible, on propose 
if ($resultat->numRows()>=1) {
	$output .= '<div class="formulaire_ligne">'."\n".'<div class="formulaire_label">'."\n".
				BAZ_TYPE_FICHE.' :</div>'."\n".'<div class="formulaire_input">';
	$output .= '<select name="id_type_fiche" onchange="javascript:this.form.submit();">'."\n";
	while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
		$output .= '<option value="'.$ligne['bn_id_nature'].'"'.(($id_type_fiche == $ligne['bn_id_nature']) ? ' selected="selected"' : '').'>'.$ligne['bn_label_nature'].'</option>'."\n";
	}
	$output .= '</select>'."\n".'</div>'."\n".'</div>'."\n";
}		
//sinon c'est vide
else {
	$output .= BAZ_PAS_DE_FORMULAIRES_TROUVES."\n";
}
$output .= '</form>'."\n";

if ($id_type_fiche != '') {
	$val_formulaire = baz_valeurs_type_de_fiche($id_type_fiche);

	//on parcourt le template du type de fiche pour fabriquer un csv pour l'exemple
	$tableau = formulaire_valeurs_template_champs($val_formulaire['bn_template']);
	$csv = ''; $nb=0;
	foreach ($tableau as $ligne) {
		if ($ligne[0] != 'labelhtml') {
			$csv .= '"'.str_replace('"','""',$ligne[2]).((isset($ligne[9]) && $ligne[9]==1) ? ' *' : '').'", ';
			$nb++;
		}
	}
	$csv = substr(trim($csv),0,-1)."\r\n";
	
	//on récupère toutes les fiches du type choisit et on les met au format csv
	$tableau_fiches = baz_requete_recherche_fiches(NULL, 'alphabetique', $id_type_fiche, $val_formulaire['bn_type_fiche']); 
	$total = count($tableau_fiches);
	foreach ($tableau_fiches as $fiche) {
		var_dump(baz_valeurs_fiche($fiche[0]));
		$csv .= implode(',',baz_valeurs_fiche($fiche[0]))."\r\n";
	}
	
	$output .= '<em>'.BAZ_VISUALISATION_FICHIER_CSV_A_IMPORTER.$val_formulaire["bn_label_nature"].' - '.BAZ_TOTAL_FICHES.' : '.$total.'</em>'."\n";
	$output .= '<pre style="height:125px; white-space:pre; padding:5px; word-wrap:break-word; border:1px solid #999; overflow:auto; ">'."\n".$csv."\n".'</pre>'."\n";
}	



if (isset($_POST['submit_file'])) {
		$row = 1;
		if (($handle = fopen($_FILES['fileimport']['tmp_name'], "r")) !== FALSE) {		
		    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
		    	//var_dump($data);
		        $num = count($data);
		        $output .=  "<em> $num champs pour la ligne $row: <br /></em>\n";
		        $row++;
		        for ($c=0; $c < $num; $c++) {
		            $output .= utf8_decode(str_replace('â€™','\'',$data[$c])) . "<br />\n";
		        }
		        $output .= '<hr />';
		    }
		    fclose($handle);
		}
}



echo $this->Header();
echo "<div class=\"page\">\n$output\n<hr class=\"hr_clear\" />\n</div>\n";
echo $this->Footer();
?>
