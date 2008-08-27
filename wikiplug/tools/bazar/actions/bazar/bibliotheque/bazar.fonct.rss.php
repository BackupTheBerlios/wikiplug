<?php
/*vim: set expandtab tabstop=4 shiftwidth=4: */
// +------------------------------------------------------------------------------------------------------+
// | PHP version 4.1                                                                                      |
// +------------------------------------------------------------------------------------------------------+
// | Copyright (C) 2004 Tela Botanica (accueil@tela-botanica.org)                                         |
// +------------------------------------------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or                                        |
// | modify it under the terms of the GNU Lesser General Public                                           |
// | License as published by the Free Software Foundation; either                                         |
// | version 2.1 of the License, or (at your option) any later version.                                   |
// |                                                                                                      |
// | This library is distributed in the hope that it will be useful,                                      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of                                       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU                                    |
// | Lesser General Public License for more details.                                                      |
// |                                                                                                      |
// | You should have received a copy of the GNU Lesser General Public                                     |
// | License along with this library; if not, write to the Free Software                                  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                            |
// +------------------------------------------------------------------------------------------------------+
// CVS : $Id: bazar.fonct.rss.php,v 1.3 2008/08/27 13:18:57 mrflos Exp $
/**
*
*@package bazar
//Auteur original :
*@author        Alexandre GRANIER <alexandre@tela-botanica.org>
*@author        Florian Schmitt <florian@ecole-et-nature.org>
//Autres auteurs :
*@copyright     Tela-Botanica 2000-2006
*@version       $Revision: 1.3 $
// +------------------------------------------------------------------------------------------------------+
*/

// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+

/** baz_valeur_template() - Renvoi des valeurs inscrite dans le fichier de template
*
* @param   string valeur du template de bazar_nature
*
* @return   mixed  tableau contenant les champs du fichier template
*/
function baz_valeurs_template($valeur_template) {
	//Parcours du fichier de templates, pour mettre les champs specifiques
	$tableau= array();
	$nblignes=0;
	$chaine = explode ("\n", $valeur_template);
	array_pop($chaine);
	foreach ($chaine as $ligne)  {
		$souschaine = explode ("***", $ligne) ;
		$tableau[$nblignes]['type'] = trim($souschaine[0]) ;
		if (isset($souschaine[1])) {$tableau[$nblignes]['nom_bdd'] = trim($souschaine[1]);}
		else {$tableau[$nblignes]['nom_bdd'] ='';}
		if (isset($souschaine[2])) $tableau[$nblignes]['label'] = trim($souschaine[2]);
		else {$tableau[$nblignes]['label'] ='';}
		if (isset($souschaine[3])) $tableau[$nblignes]['limite1'] = trim($souschaine[3]);
		else {$tableau[$nblignes]['limite1'] ='';}
		if (isset($souschaine[4])) $tableau[$nblignes]['limite2'] = trim($souschaine[4]);
		else {$tableau[$nblignes]['limite2'] ='';}
		if (isset($souschaine[5])) $tableau[$nblignes]['defaut'] = trim($souschaine[5]);
		else {$tableau[$nblignes]['defaut'] ='';}
		if (isset($souschaine[6])) $tableau[$nblignes]['table_source'] = trim($souschaine[6]);
		else {$tableau[$nblignes]['table_source'] ='';}
		if (isset($souschaine[7])) $tableau[$nblignes]['id_source'] = trim($souschaine[7]);
		else {$tableau[$nblignes]['id_source'] ='';}
		if (isset($souschaine[8])) $tableau[$nblignes]['obligatoire'] = trim($souschaine[8]);
		else {$tableau[$nblignes]['obligatoire'] ='';}
		if (isset($souschaine[9])) $tableau[$nblignes]['recherche'] = trim($souschaine[9]);
		else {$tableau[$nblignes]['recherche'] ='';}


		// traitement des cases � cocher, dans ce cas la, on a une table de jointure entre la table
		// de liste et la table bazar_fiche (elle porte un nom du genre bazar_ont_***)
		// dans le template, � la place d'un nom de champs dans 'nom_bdd', on a un nom de table
		// et 2 noms de champs s�par�s par un virgule ex : bazar_ont_theme,bot_id_theme,bot_id_fiche

		if (isset($tableau[$nblignes]['nom_bdd']) && preg_match('/,/', $tableau[$nblignes]['nom_bdd'])) {
			$tableau_info_jointe = explode (',', $tableau[$nblignes]['nom_bdd']) ;
			$tableau[$nblignes]['table_jointe'] = $tableau_info_jointe[0] ;
			$tableau[$nblignes]['champs_id_fiche'] = $tableau_info_jointe[1] ;
			$tableau[$nblignes]['champs_id_table_jointe'] = $tableau_info_jointe[2] ;
		}
		$nblignes++;
	}
	return $tableau;
}

/**  baz_voir_fiches() - Permet de visualiser en detail une liste de fiche  au format XHTML
*
* @global boolean Rajoute des informations internes a l'application (date de modification, lien vers la page de d�part de l'appli)
* @global integer Tableau d(Identifiant des fiches a afficher
*
* @return   string  HTML
*/
function baz_voir_fiches($danslappli, $idfiches=array()) {
	$res='';
	foreach($idfiches as $idfiche) {
			$res.=baz_voir_fiche($danslappli, $idfiche);
	}
	return $res;
}


/**  baz_voir_fiche() - Permet de visualiser en detail une fiche  au format XHTML
*
* @global boolean Rajoute des informations internes a l'application (date de modification, lien vers la page de depart de l'appli) si a 1
* @global integer Identifiant de la fiche a afficher
*
* @return   string  HTML
*/
function baz_voir_fiche($danslappli, $idfiche='') {
	$res='';
	if ($idfiche!='') $GLOBALS['_BAZAR_']['id_fiche']=$idfiche;
	if (isset($GLOBALS['_BAZAR_']['id_fiche']) && $GLOBALS['_BAZAR_']['id_fiche']>0) {		
		$url= $GLOBALS['_BAZAR_']['url'];
		$url->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
		$url->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
		$url = preg_replace ('/&amp;/', '&', $url->getURL()) ;
	
		//cas ou la fiche a ete validee
		if (isset($_GET['publiee'])) {
			publier_fiche($_GET['publiee']);
		}
	
		//cas on une structure s'approprie une ressource
		if ((BAZ_SANS_AUTH!=true) && isset($_GET['appropriation'])) {
			if ($_GET['appropriation']==1) {
				$requete = 'INSERT INTO bazar_appropriation VALUES ('.$GLOBALS['_BAZAR_']['id_fiche'].', '.$GLOBALS['AUTH']->getAuthData(BAZ_CHAMPS_ID).')';
				$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			}
			elseif ($_GET['appropriation']==0) {
				$requete = 'DELETE FROM bazar_appropriation WHERE  ba_ce_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].' AND ba_ce_id_structure='.$GLOBALS['AUTH']->getAuthData(BAZ_CHAMPS_ID);
				$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			}
		}
	
		//cas ou un commentaire a ete entre
		if (isset($_POST['Nom'])) {
			$requete = 'INSERT INTO bazar_commentaires VALUES ('.
						baz_nextid('bazar_commentaires', 'bc_id_commentaire', $GLOBALS['_BAZAR_']['db']).
						', '.$GLOBALS['_BAZAR_']['id_fiche'].', "'.$_POST['Nom'].'", "'.$_POST['Commentaire'].
						'", NOW() )';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		}
		//cas ou un commentaire va etre supprime
		elseif (isset($_GET['id_commentaire'])) {
			$requete = 'DELETE FROM bazar_commentaires WHERE bc_id_commentaire='.$_GET['id_commentaire'].' LIMIT 1';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		}
		else {
			if (isset($_GET[BAZ_VARIABLE_ACTION])) {
				if ($_GET[BAZ_VARIABLE_ACTION]==BAZ_VOIR_FICHE) {
					//sinon on met a jour le nb de visites pour la fiche, puisque c'est une simple consultation
					$requete = 'UPDATE bazar_fiche SET bf_nb_consultations=bf_nb_consultations+1 WHERE bf_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'];
					$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
				}
			}
		}
		if (isset($GLOBALS['_BAZAR_']['id_fiche'])) $requete = 'SELECT * FROM bazar_fiche,bazar_nature WHERE bf_ce_nature=bn_id_nature and bf_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'];
	
		if (isset($GLOBALS['_BAZAR_']['langue'])) $requete .= ' and bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		(DB::isError($resultat)) ? die ("Erreur : ".$resultat->getMessage()." pour la requete :<br>".$requete) : '';
	
		$ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC) ;
		if (!isset($GLOBALS['_BAZAR_']['typeannonce'])) $GLOBALS['_BAZAR_']['typeannonce'] = $ligne['bf_ce_nature'];
		if (!isset($GLOBALS['_BAZAR_']['fiche_valide'])) $GLOBALS['_BAZAR_']['fiche_valide'] = $ligne['bf_statut_fiche'];
		//on verifie si l'utilisateur est administrateur
		$est_admin=0;
	
		// Si on vient de l applette calendrier, $GLOBALS['_BAZAR_']['id_typeannonce'] est vide ...
		// mais on dispose de la constante BAZ_NUM_ANNONCE_CALENDRIER
		if (!isset($GLOBALS['_BAZAR_']['id_typeannonce']) && defined('BAZ_NUM_ANNONCE_CALENDRIER'))  $GLOBALS['_BAZAR_']['id_typeannonce'] = BAZ_NUM_ANNONCE_CALENDRIER;
	
		if (!isset($GLOBALS['_BAZAR_']['template'])) $GLOBALS['_BAZAR_']['template'] = $ligne['bn_template'];
		if (!isset($GLOBALS['_BAZAR_']['commentaire'])) $GLOBALS['_BAZAR_']['commentaire'] = $ligne['bn_commentaire'];
		if (!isset($GLOBALS['_BAZAR_']['class'])) $GLOBALS['_BAZAR_']['class'] = $ligne['bn_label_class'];
	
		if (BAZ_SANS_AUTH!=true) {
			$utilisateur = new Administrateur_bazar ($GLOBALS['AUTH']);
			if ($utilisateur->isAdmin($ligne['bn_id_nature']) || $utilisateur->isSuperAdmin()) {
				$est_admin=true;
			}
		} else $est_admin=true;
	
		//debut de la fiche
		$res .= '<div class="BAZ_cadre_fiche BAZ_cadre_fiche_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
		//affiche le titre sous forme d'image
		if (isset ($GLOBALS['_BAZAR_']['image_titre']) && $GLOBALS['_BAZAR_']['image_titre']!='') {
			$res .= '<img class="BAZ_img_titre" src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$GLOBALS['_BAZAR_']['image_titre'].'" alt="'.$ligne['bn_label_nature'].'" />'.'<br />'."\n";
		}
		//affiche le texte sinon
		else {
			$res .= '<h2 class="BAZ_titre BAZ_titre_'.$GLOBALS['_BAZAR_']['class'].'">'.$ligne['bn_label_nature'].'</h2>'."\n";
		}
		$GLOBALS['_BAZAR_']['annonceur'] = $ligne['bf_ce_utilisateur'] ;
		//si le template existe, on genere le template
		if ((file_exists(BAZ_CHEMIN.'actions'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'templates/'.$GLOBALS['_BAZAR_']['typeannonce'].'-fiche.php'))) {
			require_once(BAZ_CHEMIN.'actions'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'templates/'.$GLOBALS['_BAZAR_']['typeannonce'].'-fiche.php');
			$res .=genere_fiche($ligne);
		}
		//on affiche ligne par ligne sinon
		else {
			// Le titre
			$res .= '<h1 class="BAZ_fiche_titre BAZ_fiche_titre_'.$GLOBALS['_BAZAR_']['class'].'">'.$ligne['bf_titre'].'</h1>'."\n";
			// cas d'une image personalisee
			if (isset($ligne['bf_url_image'])) {
				$res .= '<div class="BAZ_fiche_image BAZ_fiche_image_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
				$res .= '<img class="BAZ_image" src="tools'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'actions'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.$ligne['bf_url_image'].'" border=0 alt="'.BAZ_TEXTE_IMG_ALTERNATIF.'" />'."\n";
				$res .= '</div>'."\n";
			}
			//cas d'une image par defaut
			elseif (isset ($GLOBALS['_BAZAR_']['image_logo']) && $GLOBALS['_BAZAR_']['image_logo']!='') {
				$res .= '<div class="BAZ_fiche_image BAZ_fiche_image_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
				$res .= '<img class="BAZ_image" src="client/bazar/images/'.$GLOBALS['_BAZAR_']['image_logo'].'" border=0 alt="'.BAZ_TEXTE_IMG_ALTERNATIF.'" width="130" height="130" />'."\n";
				$res .= '</div>'."\n";
			}
	
			$res .= '<div class="BAZ_description BAZ_description_'.$GLOBALS['_BAZAR_']['class'].'">'.nl2br($ligne['bf_description']).'</div>'."\n";
			$tableau=baz_valeurs_template($GLOBALS['_BAZAR_']['template']);
			for ($i=0; $i<count($tableau); $i++) {
				if (isset($ligne[$tableau[$i]['nom_bdd']]) && ( $tableau[$i]['type']=='texte' || $tableau[$i]['type']=='textelong' ) ) {
					$val=$tableau[$i]['nom_bdd'];
					if (!in_array($val, array ('bf_titre', 'bf_description'))) {
						if ($ligne[$val] != '' and $ligne[$val] != BAZ_CHOISIR and $ligne[$val] != BAZ_NON_PRECISE) {
							$res .= '<div class="BAZ_rubrique  BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".'<span class="BAZ_label" id="'.$tableau[$i]['nom_bdd'].'_rubrique">'.$tableau[$i]['label'].':</span>'."\n";
							$res .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].'" id="'.$tableau[$i]['nom_bdd'].'_description"> '.nl2br($ligne[$val]).'</span>'."\n".'</div>'."\n";
						}
					}
				}
				elseif ( $tableau[$i]['type']=='liste' || $tableau[$i]['type']=='checkbox' ) {
					//pour les champs renseignes par une liste, on va chercher le label de la liste, plutot que l'id
					$requete = 'SELECT blv_label FROM bazar_fiche_valeur_liste, bazar_liste_valeurs WHERE bfvl_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].
					' AND  bfvl_ce_liste='.$tableau[$i]['nom_bdd'].' AND bfvl_valeur=blv_valeur AND blv_ce_liste='.$tableau[$i]['nom_bdd'].' AND blv_ce_i18n="'.$GLOBALS['_BAZAR_']['langue'].'"';
					$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
					if (DB::isError ($resultat)) {
						die ($resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
					}
					$val='';$nb=0;
					while ($tab = $resultat->fetchRow()) {
						if ($nb>0) $val .= ', ';
						$val .= $tab[0];
						$nb++;
					}
					if ($val != '' and $val != BAZ_CHOISIR and $val != BAZ_NON_PRECISE) {
						$res .= '<div class="BAZ_rubrique BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".'<span class="BAZ_label" id="rubrique_'.$tableau[$i]['nom_bdd'].'">'.$tableau[$i]['label'].':</span>'."\n";
						$res .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].'" id="description_'.$tableau[$i]['nom_bdd'].'"> '.$val.'</span>'."\n".'</div>'."\n";
					}
				}
				elseif ( $tableau[$i]['type']=='listedatedeb' || $tableau[$i]['type']=='listedatefin' ) {
					$val=$tableau[$i]['nom_bdd'];
					if (!in_array($val, array ('bf_date_debut_validite_fiche', 'bf_date_fin_validite_fiche'))) {
						if ($ligne[$val] != '' && $ligne[$val] != '0000-00-00') {
							// Petit test pour afficher la date de debut et de fin d evenement
							if ($val == 'bf_date_debut_evenement' || $val == 'bf_date_fin_evenement') {
								if ($ligne['bf_date_debut_evenement'] == $ligne['bf_date_fin_evenement']) {
									if ($val == 'bf_date_debut_evenement') continue;
									$res .= '<div class="BAZ_rubrique BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".'<span class="BAZ_label" id="'.$tableau[$i]['nom_bdd'].'_rubrique">'.BAZ_LE.':</span>'."\n";
									$res .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].'" id="'.$tableau[$i]['nom_bdd'].'_description"> '.strftime('%d.%m.%Y',strtotime($ligne['bf_date_debut_evenement'])).'</span>'."\n".'</div>'."\n";
									continue;
								} else {
	
									if ($val == 'bf_date_debut_evenement') {
										$res .= '<div class="BAZ_rubrique BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".'<span class="BAZ_label" id="'.$tableau[$i]['nom_bdd'].'_rubrique">';
										$res .= BAZ_DU;
										$res .= '</span>'."\n".'<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].'" id="'.$tableau[$i]['nom_bdd'].'_description"> '.strftime('%d.%m.%Y',strtotime($ligne[$val])).'</span>'."\n";
									} else {
										$res .= '<span class="BAZ_label" id="'.$tableau[$i]['nom_bdd'].'_rubrique">'.BAZ_AU;
										$res .= '</span>'."\n".'<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].'" id="'.$tableau[$i]['nom_bdd'].'_description"> '.strftime('%d.%m.%Y',strtotime($ligne[$val])).'</span>'."\n".'</div>'."\n";
									}
	
									continue;
								}
							}
	
							$res .= '<div class="BAZ_rubrique BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".'<span class="BAZ_label" id="'.$tableau[$i]['nom_bdd'].'_rubrique">'.$tableau[$i]['label'].':</span>'."\n";
							$res .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].'" id="'.$tableau[$i]['nom_bdd'].'_description"> '.strftime('%d.%m.%Y',strtotime($ligne[$val])).'</span>'."\n".'</div>'."\n";
						}
					}
				}
				elseif ( $tableau[$i]['type']=='wikini' ) {
					$res .= '<div class="BAZ_lien_wikini BAZ_lien_wikini_'.$GLOBALS['_BAZAR_']['class'].'"><a href="wikini/'.genere_nom_wiki2($ligne["bf_titre"], TRUE).'">'.BAZ_ENTRER_PROJET.'</a></div>'."\n";
				} elseif ($tableau[$i]['type']=='labelhtml') {
					// On ecrit le label uniquement si le champs obligatoire est a 1
					if ($tableau[$i]['obligatoire'] == 1) $res .= '<div class="BAZ_label BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'.$tableau[$i]['label'].'</div>'."\n";
				}
			}
			//afficher les liens pour l'annonce
			$requete = 'SELECT  bu_url, bu_descriptif_url FROM bazar_url WHERE bu_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'];
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			if ($resultat->numRows()>0) {
				$res .= '<span class="BAZ_label BAZ_label_'.$GLOBALS['_BAZAR_']['class'].'">'.BAZ_LIEN_INTERNET.':</span>'."\n";
				$res .= '<span class="BAZ_description BAZ_description_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
				$res .= '<ul class="BAZ_liste BAZ_liste_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
				while ($ligne1 = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
					$res .= '<li class="BAZ_liste_lien BAZ_liste_lien_'.$GLOBALS['_BAZAR_']['class'].'"><a href="'.$ligne1['bu_url'].'" class="BAZ_lien" target="_blank">'.$ligne1['bu_descriptif_url'].'</a></li>'."\n";
				}
				$res .= '</ul></span>'."\n";
			}
	
			//afficher les fichiers pour l'annonce
			$requete = 'SELECT  bfj_description, bfj_fichier FROM bazar_fichier_joint WHERE bfj_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'];
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			if ($resultat->numRows()>0) {
				$res .= '<span class="BAZ_label BAZ_label_'.$GLOBALS['_BAZAR_']['class'].'">'.BAZ_LISTE_FICHIERS_JOINTS.':</span>'."\n";
				$res .= '<span class="BAZ_description BAZ_description_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
				$res .= '<ul class="BAZ_liste BAZ_liste_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
				while ($ligne2 = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
					$res .= '<li class="BAZ_liste_fichier BAZ_liste_fichier_'.$GLOBALS['_BAZAR_']['class'].'"><a href="tools'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'actions'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.$ligne2['bfj_fichier'].'">'.$ligne2['bfj_description'].'</a></li>'."\n";
				}
				$res .= '</ul></span>'."\n";
			}
			$res .= '<div class="BAZ_bulle_corps BAZ_bulle_corps_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
			$res .= '<div class="BAZ_info BAZ_infos_fiche_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
			$res .= '<span class="BAZ_nb_vues BAZ_nb_vues_'.$GLOBALS['_BAZAR_']['class'].'">'.BAZ_FICHE_NUMERO.$GLOBALS['_BAZAR_']['id_fiche'].BAZ_NB_VUS.$ligne['bf_nb_consultations'].BAZ_FOIS.'</span><br />'."\n";
			
			//affichage du redacteur de la fiche
			//dans le cas d'un annuaire externe
			if (!BAZ_SANS_AUTH) {
				$res .= '<span class="BAZ_fiche_ecrite">'.BAZ_FICHE_NUMERO.$GLOBALS['_BAZAR_']['id_fiche'].BAZ_ECRITE;
				$requete = 'SELECT '.BAZ_CHAMPS_NOM.', '.BAZ_CHAMPS_PRENOM.', '.BAZ_CHAMPS_EMAIL.
								' FROM '.BAZ_ANNUAIRE.' WHERE '.BAZ_CHAMPS_ID.'='.$ligne['bf_ce_utilisateur'];
				$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
				if (DB::isError($resultat)) {
					die ($resultat->getMessage().$resultat->getDebugInfo()) ;
				}
				while ($redacteur = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
					if (!defined('BAZ_FICHE_REDACTEUR_MAIL') || BAZ_FICHE_REDACTEUR_MAIL) {
						$res .= '<a href="mailto:'.$redacteur[BAZ_CHAMPS_EMAIL].'">'.$redacteur[BAZ_CHAMPS_PRENOM].' '.$redacteur[BAZ_CHAMPS_NOM].'</a>';
					} else {
						$res .= $redacteur[BAZ_CHAMPS_PRENOM].' '.$redacteur[BAZ_CHAMPS_NOM];
					}
					$res .= '<br /></span>'."\n";
				}
			//dans le cas d'un redacteur saisi avec la fiche
			} else {
				$res .= '<a href="mailto:'.$redacteur[BAZ_CHAMPS_EMAIL].'">'.$redacteur[BAZ_CHAMPS_PRENOM].' '.$redacteur[BAZ_CHAMPS_NOM].'</a>';
				$res .= '<br /></span>'."\n";
			}
	
			//informations complementaires (id fiche, etat publication,... )
			if ($danslappli==1) {
				if ($GLOBALS['_BAZAR_']['fiche_valide']==1 && $GLOBALS['_BAZAR_']['appropriation']!=1) {
					if ($ligne['bf_date_debut_validite_fiche'] != '0000-00-00' && $ligne['bf_date_fin_validite_fiche'] != '0000-00-00') {
					$res .= '<span class="BAZ_rubrique BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'.BAZ_PUBLIEE.':</span> '.BAZ_DU.
							' '.strftime('%d.%m.%Y',strtotime($ligne['bf_date_debut_validite_fiche'])).' '.
							BAZ_AU.' '.strftime('%d.%m.%Y',strtotime($ligne['bf_date_fin_validite_fiche'])).'<br />'."\n";
					}
				}
				elseif ($GLOBALS['_BAZAR_']['appropriation']!=1 || $GLOBALS['_BAZAR_']['fiche_valide']!=1) {
					$res .= '<span class="BAZ_rubrique  BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'.BAZ_PUBLIEE.':</span> '.BAZ_NON.'<br />'."\n";
				}
				//affichage des infos et du lien pour la mise a jour de la fiche
				if ( $est_admin || ((BAZ_SANS_AUTH!=true) && $GLOBALS['_BAZAR_']['annonceur']==$GLOBALS['AUTH']->getAuthData(BAZ_CHAMPS_ID)) ) {
					$res .= '<span class="BAZ_rubrique BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'" id="date_creation">'.BAZ_DATE_CREATION.'</span> '.strftime('%d.%m.%Y %H:%M',strtotime($ligne['bf_date_creation_fiche']))."\n";
					$res .= '<span class="BAZ_rubrique BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'" id="date_mise_a_jour">'.BAZ_DATE_MAJ.'</span> '.strftime('%d.%m.%Y %H:%M',strtotime($ligne['bf_date_maj_fiche']))."\n";
				}
				$res .= '</div>'."\n";
	
				if ( $est_admin || ((BAZ_SANS_AUTH!=true) && $GLOBALS['_BAZAR_']['annonceur']==$GLOBALS['AUTH']->getAuthData(BAZ_CHAMPS_ID)) ) {
					$res .= '<div class="BAZ_actions_fiche BAZ_actions_fiche_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
					$res .= '<ul>'."\n";
					if ( $est_admin ) {
						$lien_publie = &$GLOBALS['_BAZAR_']['url'];
						$lien_publie->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
						$lien_publie->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
						$lien_publie->addQueryString('typeannonce', $ligne['bf_ce_nature']);
						if ($GLOBALS['_BAZAR_']['fiche_valide']==0||$GLOBALS['_BAZAR_']['fiche_valide']==2) {
							$lien_publie->addQueryString('publiee', 1);
							$label_publie=BAZ_VALIDER_LA_FICHE;
							$class_publie='_valider';
						} elseif ($GLOBALS['_BAZAR_']['fiche_valide']==1) {
							$lien_publie->addQueryString('publiee', 0);
							$label_publie=BAZ_INVALIDER_LA_FICHE;
							$class_publie='_invalider';
						}
						$res .= '<li class="BAZ_liste'.$class_publie.'"><a href="'.$lien_publie->getURL().'">'.$label_publie.'</a></li>'."\n";
						$lien_publie->removeQueryString('publiee');
					}
					$lien_modifier=$GLOBALS['_BAZAR_']['url'];
					$lien_modifier->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER);
					$lien_modifier->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
					$lien_modifier->addQueryString('typeannonce', $ligne['bf_ce_nature']);
					$res .= '<li class="BAZ_liste_modifier"><a href="'.$lien_modifier->getURL().'" id="modifier_fiche">'.BAZ_MODIFIER_LA_FICHE.'</a></li>'."\n";
					$lien_supprimer=$GLOBALS['_BAZAR_']['url'];
					$lien_supprimer->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_SUPPRESSION);
					$lien_supprimer->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
					$lien_supprimer->addQueryString('typeannonce', $ligne['bf_ce_nature']);
					$res .= '<li class="BAZ_liste_supprimer"><a href="'.$lien_supprimer->getURL().'" id="supprimer_fiche">'.BAZ_SUPPRIMER_LA_FICHE.'</a></li>'."\n";
					$res .= '</ul>'."\n";
					$res .= '</div>'."\n";
				}
			}
			$res .= '</div>'."\n";
			$res .= '</div>'."\n";
		}
	
		// Nous v�rifions comment est appel� la fonction
		if ($danslappli == 0) {
		 $res .= '</div>'."\n";
		} else if ($danslappli == 1 ) {
	
			// Ajout des appropriations, s'il le faut
			if ($GLOBALS['_BAZAR_']['appropriation'] == 1) {
				$res .= '<br />'."\n".'<div class="BAZ_cadre_fiche BAZ_cadre_fiche_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
				$res .= '<h2 class="BAZ_titre BAZ_titre_'.$GLOBALS['_BAZAR_']['class'].'">'.BAZ_LES_STRUCTURES_POSSEDANT_UNE_RESSOURCE.'</h2>'."\n";
				$requete = 'SELECT '.BAZ_CHAMPS_ID.', '.BAZ_CHAMPS_NOM.' FROM bazar_appropriation,'.BAZ_ANNUAIRE.' WHERE ba_ce_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].' AND ba_ce_id_structure='.BAZ_CHAMPS_ID.' ORDER BY '.BAZ_CHAMPS_NOM.' ASC';
				$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
				if (DB::isError ($resultat)) {
					return $resultat->getMessage().'<br />'.$resultat->getDebugInfo();
				}
				$possede_ressource=0;
				if ($resultat->numRows()>0) {
					$res .= BAZ_IL_Y_A.$resultat->numRows().' ';
					if ($resultat->numRows()==1) $res .= BAZ_STRUCTURE_POSSEDANT.'<br />'."\n";
					else $res .= BAZ_STRUCTURES_POSSEDANT.'<br />'."\n";
					$res .= '<ul>'."\n";
					while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
						$res .= '<li><a href="'.BAZ_URL_ANNUAIRE.'&amp;voir_fiche='.$ligne[BAZ_CHAMPS_ID].'" onclick="javascript:window.open(this.href);return false;">'.$ligne[BAZ_CHAMPS_NOM].'</a></li>'."\n";
						if ((BAZ_SANS_AUTH!=true) && $GLOBALS['AUTH']->getAuth() && $GLOBALS['AUTH']->getAuthData(BAZ_CHAMPS_ID)==$ligne[BAZ_CHAMPS_ID]) $possede_ressource=1;
					}
					$res .= '</ul><br />'."\n";
				}
				else $res .= BAZ_PAS_D_APPROPRIATION.'<br /><br />'."\n";
				$res .='<p class="BAZ_bulle_corps BAZ_bulle_corps_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
				$lien_appropriation = $GLOBALS['_BAZAR_']['url'];
				$lien_appropriation->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
				$lien_appropriation->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
				if ($possede_ressource) {
					$lien_appropriation->addQueryString('appropriation', 0);
					$res .= BAZ_POSSEDE_DEJA_RESSOURCE.'<br />'."\n".'<a href="'.$lien_appropriation->getURL().'">'.BAZ_CLIQUER_POUR_VOUS_ENLEVER.'</a>'."\n";
					$lien_appropriation->removeQueryString('appropriation');
				}
				elseif ((BAZ_SANS_AUTH!=true) && $GLOBALS['AUTH']->getAuth() && $GLOBALS['AUTH']->getAuthData(BAZ_CHAMPS_EST_STRUCTURE)) {
					$lien_appropriation->addQueryString('appropriation', 1);
					$res .= BAZ_SI_POSSEDE_RESSOURCE.'<br />'."\n".'<a href="'.$lien_appropriation->getURL().'">'.BAZ_CLIQUER_POUR_APPARAITRE.'</a>'."\n";
					$lien_appropriation->removeQueryString('appropriation');
				}
				elseif ((BAZ_SANS_AUTH!=true) && $GLOBALS['AUTH']->getAuth() && !$GLOBALS['AUTH']->getAuthData(BAZ_CHAMPS_EST_STRUCTURE)) {
					$res .= BAZ_IL_FAUT_ETRE_STRUCTURE."\n";
				}
				elseif ((BAZ_SANS_AUTH!=true) && !$GLOBALS['AUTH']->getAuth()) {
					$res .= BAZ_IL_FAUT_ETRE_IDENTIFIE_STRUCTURE."\n";
				}
				$res .='</p>'."\n";
				$res .= '</div>'."\n";
			}
	
			// Ajout des commentaires, s'il le faut
			if ($GLOBALS['_BAZAR_']['commentaire'] == 1) {
				$res .= '<br />'."\n".'<div class="BAZ_cadre_fiche BAZ_cadre_fiche_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
				$res .= '<h2 class="BAZ_titre BAZ_titre_'.$GLOBALS['_BAZAR_']['class'].'">'.BAZ_LES_COMMENTAIRES.'</h2>'."\n";
				$requete = 'SELECT * FROM bazar_commentaires WHERE bc_ce_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].' ORDER BY bc_date ASC';
				$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
				if (DB::isError ($resultat)) {
					return $resultat->getMessage().'<br />'.$resultat->getDebugInfo();
				}
				if ($resultat->numRows()>0) {
					$res .= BAZ_IL_Y_A.$resultat->numRows().' ';
					if ($resultat->numRows()==1) $res .= BAZ_COMMENTAIRE.'<br />'."\n";
					else $res .= BAZ_COMMENTAIRES.'<br />'."\n";
					while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
						$res .= '<div class="BAZ_bulle_corps BAZ_bulle_corps_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
						//affichage du commentaire
						$res .= $ligne['bc_commentaire'].'<br />'."\n";
						$res .= '<div class="BAZ_commentaire_admin">'.BAZ_PAR.' : <strong>'.$ligne['bc_nom'].'</strong>'.BAZ_ECRIT_LE.strftime('%d.%m.%Y %H:%M',strtotime($ligne['bc_date']));
						//pour les identifies seulement, administrateurs de la rubrique ou superadmins
						if ($est_admin==1) {
							$url_comment= $GLOBALS['_BAZAR_']['url'];
							$url_comment->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
							$url_comment->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
							$url_comment->addQueryString('id_commentaire', $ligne['bc_id_commentaire']);
							$res .= '&nbsp;&nbsp;<a href="'.$url_comment->getURL().'">'.BAZ_SUPPRIMER.'</a>'."\n";
						}
						$res .= '</div>'."\n";
						$res .= '</div>'."\n";
					}
				}
				else $res .= BAZ_PAS_DE_COMMENTAIRES.'<br /><br />'."\n";
	
				//formulaire des commentaires
				$form_commentaire = new HTML_QuickForm('bazar_commentaire', 'post', $url);
				$squelette =& $form_commentaire->defaultRenderer();
				$squelette->setFormTemplate("\n".'<form {attributes}>'."\n".'{content}'."\n".'</form>'."\n");
				$squelette->setElementTemplate( '<label style="width:200px;">{label}'.
												'<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".
											    '</label><br />'."\n".'{element}<br />'."\n");
				$squelette->setRequiredNoteTemplate("\n".'<span class="symbole_obligatoire"> *{requiredNote}</span>'."\n");
				$option=array('style'=>'width:300px;border:1px solid #000;', 'maxlength'=>100);
				$form_commentaire->addElement('text', 'Nom', BAZ_ENTREZ_VOTRE_NOM, $option);
				$option=array('style'=>'width:95%;height:100px;white-space: pre;padding:3px;border:1px solid #000;');
				require_once 'HTML/QuickForm/textarea.php';
				$formtexte= new HTML_QuickForm_textarea('Commentaire', BAZ_ENTREZ_VOTRE_COMMENTAIRE, $option);
				$form_commentaire->addElement($formtexte) ;
				$option=array('style'=>'border:1px solid #000;');
				$form_commentaire->addElement('submit', 'Envoyer', BAZ_ENVOYER, $option);
				$form_commentaire->addRule('Nom', BAZ_NOM_REQUIS, 'required', '', 'client') ;
				$form_commentaire->addRule('Commentaire', BAZ_COMMENTAIRE_REQUIS, 'required', '', 'client') ;
				$form_commentaire->setRequiredNote(BAZ_CHAMPS_REQUIS) ;
				$res .= $form_commentaire->toHTML();
				$res .= '</div>'."\n";
			}
		}
	
		// Nettoyage de l'url
		$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
		$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
		$GLOBALS['_BAZAR_']['url']->removeQueryString('id_commentaire');
		$GLOBALS['_BAZAR_']['url']->removeQueryString('typeannonce');
		return $res ;
	}
	else exit('Vous devez rentrer un numero identifiant de fiche, superieur a 0, en parametre numfiche.');
}
 // merci PHP 5 ...
function mb_str_split2($str, $length = 1) {

  if ($length < 1) return FALSE;

  $result = array();

  for ($i = 0; $i < strlen($str); $i += $length) {
    $result[] = substr($str, $i, $length);
  }

  return $result;
}

function remove_accents2( $string )
{
    $string = htmlentities($string);
    return preg_replace("/&([a-z])[a-z]+;/i","$1",$string);
}

function genere_nom_wiki2($nom, $spaces = FALSE)
{
	// traitement des accents
	$nom = remove_accents2($nom);

	$temp = mb_str_split2($nom);

	$count = 0;
	$final = NULL;
	foreach($temp as $letter)
	{
		if(preg_match('/([[:space:]]|[[:punct:]])/', $letter))
		{
			$final .= ($spaces ? '_' : '');
		} elseif(preg_match ('/[a-zA-Z0-9]/', $letter)) {
            $final .= (($count == 0 || $count == (strlen($nom) - 1)) ? strtoupper($letter) : strtolower($letter));
        }
        $count++;
	}

	// v�rifions que le retour n'est pas uniquement un underscore
	if(preg_match('/^[[:punct:]]+$/', $final)) return FALSE;

 	// sinon retour du nom formaté
	return($final);
}


/** RSSversHTML () transforme un flux RSS (en XML) en page HTML
*
*   On passe en parametre le contenu du flux RSS, on affiche ou non la description,
*   et on choisit de format de la date a l'affichage. On a en sortie du code HTML a afficher
*
*   @param  string   le contenu du flux RSS
*   @param  boolean  afficher ou non la description
*   @param  string  choisir le format de date: jmah (12/02/2004 12h34) jmh (12/02 12h34) jma (12/02/2004) jm (12/02) ou rien
*
*   @return  string    le code HTML
*/
function RSSversHTML($rss, $voirdesc, $formatdate, $affichenb) {
	if ($rss!='') {
		$rawitems='';$title='';$url='';$cat='';$date='';
		$res='';
		if( eregi('<item>(.*)</item>', $rss, $rawitems ) ) {
			$items = explode('<item>', $rawitems[0]);
			$res.='<ul id="BAZ_liste_fiche">'."\n";
			for( $i = 0; $i < count($items)-1; $i++ ) {
				eregi('<title>(.*)</title>',$items[$i+1], $title );
				eregi('<link>(.*)</link>',$items[$i+1], $url );
				eregi('<description>(.*)</description>',$items[$i+1], $cat);
				eregi('<pubDate>(.*)</pubDate>',$items[$i+1], $date);
				$res.='<li>';
				if ($formatdate=='jm') {$res.=strftime('%d.%m',strtotime($date[1])).': ';}
				if ($formatdate=='jma') {$res.=strftime('%d.%m.%Y',strtotime($date[1])).': ';}
				if ($formatdate=='jmh') {$res.=strftime('%d.%m %H:%M',strtotime($date[1])).': ';}
				if ($formatdate=='jmah') {$res.=strftime('%d.%m.%Y %H:%M',strtotime($date[1])).': ';}
				$res.='<a href="'.preg_replace ('/&amp;/', '&', $url[1]).'">'.$title[1].'</a>';
				if ($voirdesc) {$res.=$cat[1];}
				// Ajout du bouton supprimer pour les superadministrateur
				if (BAZ_SANS_AUTH!=true) $utilisateur = new Administrateur_bazar($GLOBALS['AUTH']);
				if ((BAZ_SANS_AUTH!=true) && ($GLOBALS['AUTH']->getAuth() && $utilisateur->isSuperAdmin())and($url[1]!='#')) {
					$mon_url = preg_replace ('/&amp;/', '&', $url[1]) ;
					$url_suppr = new Net_URL(preg_replace ('/&amp;/', '&', $mon_url)) ;
					$url_suppr->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_SUPPRESSION) ;
		        	$res .= ' ( <a href="'.$url_suppr->getURL().
							'" onclick="javascript:return confirm(\''.BAZ_SUPPRIMER.' ?\');">'.
							BAZ_SUPPRIMER.'</a> )'."\n" ;
					}
				$res.='</li>'."\n";
			}
			$res.='</ul>'."\n";
			if ($affichenb==1) {
				//une annonce trouvee, on accorde au singulier
				if (((count($items)-1)==1)and($title!=BAZ_PAS_D_ANNONCES)) {
					$res = '<br /><h4>'.BAZ_IL_Y_A.' 1 '.BAZ_FICHE_CORRESPONDANTE.'</h4><br />'."\n".$res;
				}
				//plusieures annonces trouvees, on accorde au pluriel
				else {
					$res = '<br /><h4>'.BAZ_IL_Y_A.(count($items)-1).' '.BAZ_FICHES_CORRESPONDANTES.'</h4><br />'."\n".$res;
				}
			}
			//cas des fiches pas trouv�es
			if (((count($items)-1)==1)and($title[1]==BAZ_PAS_D_ANNONCES)) {
				$res = '<br /><h4>'.BAZ_PAS_D_ANNONCES.'</h4><br />'."\n";
			}
		}
	}
	else $res = BAZ_PAS_D_ANNONCES;

	// Nettoyage de l'url
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);

	return $res;
}

/** gen_RSS() - generer un fichier de flux RSS par type d'annonce
*
* @param   string Le type de l'annonce (laisser vide pour tout type d'annonce)
* @param   integer Le nombre d'annonces a regrouper dans le fichier XML (laisser vide pour toutes)
* @param   integer L'identifiant de l'emetteur (laisser vide pour tous)
* @param   integer L'etat de validation de l'annonce (laisser 1 pour les annonces validees, 0 pour les non-validees)
* @param   string La requete SQL personnalisee
* @param   integer La categorie des fiches bazar
*
* @return  string Le code du flux RSS
*/
function gen_RSS($typeannonce='', $nbitem='', $emetteur='', $valide=1, $requeteSQL='', $requeteSQLFrom = '', $requeteWhereListe = '', $categorie_nature='') {
	// generation de la requete MySQL personnalisee
	$req_where=0;
	$requete = 'SELECT DISTINCT bf_id_fiche, bf_titre, bf_date_debut_validite_fiche, bf_description,  bn_label_nature, bf_date_creation_fiche '.
				'FROM bazar_fiche, bazar_nature '.$requeteSQLFrom.' WHERE '.$requeteWhereListe;
	if ($valide!=2) {
		$requete .= 'bf_statut_fiche='.$valide;
		$req_where=1;
	}
	$nomflux=html_entity_decode(BAZ_DERNIERE_ACTU);
	if (!is_array ($typeannonce) && $typeannonce!='' and $typeannonce!='toutes') {
		if ($req_where==1) {$requete .= ' AND ';}
		$requete .= 'bf_ce_nature='.$typeannonce.' and bf_ce_nature=bn_id_nature ';;
		$req_where=1;
		//le nom du flux devient le type d'annonce
		$requete_nom_flux = 'select bn_label_nature from bazar_nature where bn_id_nature = '.$typeannonce;
		$nomflux = $GLOBALS['_BAZAR_']['db']->getOne($requete_nom_flux) ;
	}
	// Cas ou il y plusieurs type d annonce demande
	if (is_array ($typeannonce)) {
		if ($req_where==1) {$requete .= ' AND ';}
		$requete .= 'bf_ce_nature IN (' ;
		$chaine = '';
		foreach ($typeannonce as $valeur) $chaine .= '"'.$valeur.'",' ;
		$requete .= substr ($chaine, 0, strlen ($chaine)-1) ;
		$requete .= ') and bf_ce_nature=bn_id_nature ';
	}
	if (BAZ_SANS_AUTH!=true) $utilisateur = new Administrateur_bazar ($GLOBALS['AUTH']) ;
	if ($valide!=0) {
		if ((BAZ_SANS_AUTH!=true) && $utilisateur->isSuperAdmin()) {
			$req_where=1;
		} else {
			if ($req_where==1) {
				$requete .= ' AND ';
			}
			$requete .= '(bf_date_debut_validite_fiche<=NOW() or bf_date_debut_validite_fiche="0000-00-00")'.
						' AND (bf_date_fin_validite_fiche>=NOW() or bf_date_fin_validite_fiche="0000-00-00") AND bn_id_nature=bf_ce_nature';
		}
	}
	else $nomflux .= BAZ_A_MODERER;
	if ($emetteur!='' && $emetteur!='tous') {
		if ($req_where==1) {$requete .= ' AND ';}
		$requete .= 'bf_ce_utilisateur='.$emetteur;
		$req_where=1;
		//requete pour afficher le nom de la structure
		$requetenom = 'SELECT '.BAZ_CHAMPS_NOM.', '.BAZ_CHAMPS_PRENOM.' FROM '.
						BAZ_ANNUAIRE.' WHERE '.BAZ_CHAMPS_ID.'='.$emetteur;
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requetenom) ;
		if (DB::isError($resultat)) {
			die ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		$ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC);
		$nomflux .= ' ('.$ligne[BAZ_CHAMPS_NOM].' '.$ligne[BAZ_CHAMPS_PRENOM].')';
	}
	if ($requeteSQL!='') {
		if ($req_where==1) {$requete .= ' AND ';}
		$requete .= '('.$requeteSQL.')';
		$req_where=1;
	}
	if ($categorie_nature!='') {
		if ($req_where==1) {$requete .= ' AND ';}
		$requete .= 'bn_type_fiche IN ('.$categorie_nature.') and bf_ce_nature=bn_id_nature ';
		$req_where=1;
	}

	$requete .= ' ORDER BY   bf_date_creation_fiche DESC, bf_date_fin_validite_fiche DESC, bf_date_maj_fiche DESC';
	if ($nbitem!='') {$requete .= ' LIMIT 0,'.$nbitem;}
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		die ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}

	require_once 'XML/Util.php' ;

	// passage en utf-8 --julien
	// --

	// setlocale() pour avoir les formats de date valides (w3c) --julien
	setlocale(LC_TIME, "C");

	$xml = XML_Util::getXMLDeclaration('1.0', 'UTF-8', 'yes') ;
	$xml .= "\r\n  ";
	$xml .= XML_Util::createStartElement ('rss', array('version' => '2.0', 'xmlns:atom' => "http://www.w3.org/2005/Atom")) ;
	$xml .= "\r\n    ";
	$xml .= XML_Util::createStartElement ('channel');
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('title', null, utf8_encode(html_entity_decode($nomflux)));
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('link', null, utf8_encode(html_entity_decode(BAZ_RSS_ADRESSESITE)));
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('description', null, utf8_encode(html_entity_decode(BAZ_RSS_DESCRIPTIONSITE)));
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('language', null, 'fr-FR');
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('copyright', null, 'Copyright (c) '. date('Y') .' '. BAZ_RSS_NOMSITE);
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('lastBuildDate', null, strftime('%a, %d %b %Y %H:%M:%S GMT'));
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('docs', null, 'http://www.stervinou.com/projets/rss/');
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('category', null, BAZ_RSS_CATEGORIE);
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('managingEditor', null, BAZ_RSS_MANAGINGEDITOR);
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('webMaster', null, BAZ_RSS_WEBMASTER);
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('ttl', null, '60');
	$xml .= "\r\n      ";
	$xml .= XML_Util::createStartElement ('image');
	$xml .= "\r\n        ";
		$xml .= XML_Util::createTag ('title', null, utf8_encode(html_entity_decode($nomflux)));
		$xml .= "\r\n        ";
		$xml .= XML_Util::createTag ('url', null, BAZ_RSS_LOGOSITE);
		$xml .= "\r\n        ";
		$xml .= XML_Util::createTag ('link', null, BAZ_RSS_ADRESSESITE);
		$xml .= "\r\n      ";
	$xml .= XML_Util::createEndElement ('image');
	if ($resultat->numRows() > 0) {
		// Creation des items : titre + lien + description + date de publication
		while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
			$xml .= "\r\n      ";
			$xml .= XML_Util::createStartElement ('item');
			$xml .= "\r\n        ";
			$xml .= XML_Util::createTag('title', null, encoder_en_utf8(html_entity_decode($ligne['bf_titre'])));
			$xml .= "\r\n        ";
			$lien=$GLOBALS['_BAZAR_']['url'];
			$lien->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
			$lien->addQueryString('id_fiche', $ligne['bf_id_fiche']);
			$xml .= XML_Util::createTag ('link', null, '<![CDATA['.$lien->getURL().']]>' );
			$xml .= "\r\n        ";
			$xml .= XML_Util::createTag ('guid', null, '<![CDATA['.$lien->getURL().']]>' );
			$xml .= "\r\n        ";
			$tab = explode("wakka.php?wiki=",$lien->getURL());
			$xml .= XML_Util::createTag ('description', null, '<![CDATA['.encoder_en_utf8(html_entity_decode(str_replace('"tools/bazar/', '"'.$tab[0].'tools/bazar/',baz_voir_fiche(0,$ligne['bf_id_fiche'])))).']]>' );
			$xml .= "\r\n        ";
			if ($ligne['bf_date_debut_validite_fiche'] != '0000-00-00' &&
			$ligne['bf_date_debut_validite_fiche']>$ligne['bf_date_creation_fiche']) {
				$date_pub =  $ligne['bf_date_debut_validite_fiche'];
			} else $date_pub = $ligne['bf_date_creation_fiche'] ;
			$xml .= XML_Util::createTag ('pubDate', null, strftime('%a, %d %b %Y %H:%M:%S GMT',strtotime($date_pub)));
			$xml .= "\r\n      ";
			$xml .= XML_Util::createEndElement ('item');
		}
	}
	else {//pas d'annonces
		$xml .= "\r\n      ";
		$xml .= XML_Util::createStartElement ('item');
		$xml .= "\r\n          ";
		$xml .= XML_Util::createTag ('title', null, utf8_encode(html_entity_decode(BAZ_PAS_D_ANNONCES)));
		$xml .= "\r\n          ";
		$xml .= XML_Util::createTag ('link', null, '<![CDATA['.$GLOBALS['_BAZAR_']['url']->getUrl().']]>' );
		$xml .= "\r\n          ";
		$xml .= XML_Util::createTag ('guid', null, '<![CDATA['.$GLOBALS['_BAZAR_']['url']->getUrl().']]>' );
		$xml .= "\r\n          ";
		$xml .= XML_Util::createTag ('description', null, utf8_encode(html_entity_decode(BAZ_PAS_D_ANNONCES)));
		$xml .= "\r\n          ";
		$xml .= XML_Util::createTag ('pubDate', null, strftime('%a, %d %b %Y %H:%M:%S GMT',strtotime("01/01/%Y")));
		$xml .= "\r\n      ";
		$xml .= XML_Util::createEndElement ('item');
	}
	$xml .= "\r\n    ";
	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FLUX_RSS);
//	$xml .= utf8_encode(html_entity_decode('<atom:link href="'.$GLOBALS['_BAZAR_']['url']->getUrl().'" rel="self" type="application/rss+xml" />'."\r\n  "));
	$xml .= XML_Util::createEndElement ('channel');
	$xml .= "\r\n  ";
	$xml .= XML_Util::createEndElement('rss') ;

	// Nettoyage de l'url
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');

	return $xml;
}


/** baz_liste() Formate la liste de toutes les annonces actuelles
*
*   @return  string    le code HTML a afficher
*/
function baz_liste($typeannonce='toutes') {
	//creation du lien pour le formulaire de recherche
	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_TOUTES_ANNONCES);
	if (isset($_REQUEST['recherche_avancee'])) $GLOBALS['_BAZAR_']['url']->addQueryString ('recherche_avancee', $_REQUEST['recherche_avancee']);
	$lien_formulaire = preg_replace ('/&amp;/', '&', $GLOBALS['_BAZAR_']['url']->getURL()) ;
	$formtemplate = new HTML_QuickForm('formulaire', 'post', $lien_formulaire) ;
	$squelette =& $formtemplate->defaultRenderer();
   	$squelette->setFormTemplate("\n".'<form {attributes}>'."\n".'<table>'."\n".'{content}'."\n".'</table>'."\n".'</form>'."\n");
    $squelette->setElementTemplate( '<tr>'."\n".'<td>'."\n".'{label}'.
    		                        '<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".
    								' :</td>'."\n".'<td style="text-align:left;padding:5px;"> '."\n".'{element}'."\n".
                                    '<!-- BEGIN error --><span class="erreur">{error}</span><!-- END error -->'."\n".
                                    '</td>'."\n".'</tr>'."\n");
 	$squelette->setElementTemplate( '<tr>'."\n".'<td colspan="2" class="liste_a_cocher"><strong>{label}&nbsp;{element}</strong>'."\n".
                                    '<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".'</td>'."\n".'</tr>'."\n", 'accept_condition');
  	$squelette->setElementTemplate( '<tr><td colspan="2">{label}{element}</td></tr>'."\n", 'rechercher');

 	$squelette->setRequiredNoteTemplate("\n".'<tr>'."\n".'<td colspan="2" class="symbole_obligatoire">* {requiredNote}</td></tr>'."\n");
	//Traduction de champs requis
	$formtemplate->setRequiredNote(BAZ_CHAMPS_REQUIS) ;
	$formtemplate->setJsWarnings(BAZ_ERREUR_SAISIE,BAZ_VEUILLEZ_CORRIGER);


	//cas du formulaire de recherche proposant de chercher parmis tous les types d'annonces
	//requete pour obtenir l'id et le label des types d'annonces
	$requete = 'SELECT bn_id_nature, bn_label_nature FROM bazar_nature WHERE ';
	if ($typeannonce!='toutes') $requete .= 'bn_type_fiche="'.$typeannonce.'" ';
	else $requete .= '1 ';
	if (isset($GLOBALS['_BAZAR_']['langue'])) $requete .= ' AND bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ';
	$requete .=' ORDER BY bn_label_nature ASC';
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		return ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}
	//on recupere le nb de types de fiches, pour plus tard
	$nb_type_de_fiches=$resultat->numRows();
	$type_annonce_select['toutes']=BAZ_TOUS_TYPES_FICHES;
	while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
		$type_annonce_select[$ligne['bn_id_nature']] = $ligne['bn_label_nature'];
		$tableau_typeannonces[] = $ligne['bn_id_nature'] ;
	}
	if ($nb_type_de_fiches>1 && $typeannonce=='toutes' && BAZ_AFFICHER_FILTRE_MOTEUR) {
		$res = '<h2>'.BAZ_TOUTES_LES_ANNONCES.'</h2>'."\n";
		$option=array('onchange' => 'javascript:this.form.submit();');
		$formtemplate->addElement ('select', 'nature', BAZ_TYPEANNONCE, $type_annonce_select, $option) ;
		if (isset($_REQUEST['nature'])) {
			$defauts=array('nature'=>$_REQUEST['nature']);
			$formtemplate->setDefaults($defauts);
		}
	}
	//cas du type d'annonces predefini
	else {
		if ($nb_type_de_fiches==1) {
			$GLOBALS['_BAZAR_']['typeannonce']=end($type_annonce_select);
			$GLOBALS['_BAZAR_']['id_typeannonce']=key($type_annonce_select);
		}
		$res = '<h2>'.BAZ_TOUTES_LES_ANNONCES_DE_TYPE.' '.$GLOBALS['_BAZAR_']['typeannonce'].'</h2>'."\n";
	}

	//requete pour obtenir l'id, le nom et prenom de toutes les personnes ayant depose une fiche
	// dans le but de construire l'element de formulaire select avec les noms des emetteurs de fiche
	if (BAZ_RECHERCHE_PAR_EMETTEUR) {
		$requete = 'SELECT DISTINCT '.BAZ_CHAMPS_ID.', '.BAZ_CHAMPS_NOM.', '.BAZ_CHAMPS_PRENOM.' '.
		           'FROM bazar_fiche,'.BAZ_ANNUAIRE.' WHERE ' ;

		$requete .= ' bf_date_debut_validite_fiche<=NOW() AND bf_date_fin_validite_fiche>=NOW() and';

		$requete .= ' bf_ce_utilisateur='.BAZ_CHAMPS_ID.' ';
	    if (!isset($_REQUEST['nature'])) {
	    		if (isset($GLOBALS['_BAZAR_']['id_typeannonce'])) {
	    			$requete .= 'AND bf_ce_nature="'.$GLOBALS['_BAZAR_']['id_typeannonce'].'" ';
	    		}
		}
		else {
	    		if ($_REQUEST['nature']!='toutes') {
	    			$requete .= 'AND bf_ce_nature='.$_REQUEST['nature'].' ';
	    		}
	    }

	    $requete .= 'ORDER BY '.BAZ_CHAMPS_NOM.' ASC';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			die ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		$personnes_select['tous']=BAZ_TOUS_LES_EMETTEURS;
		while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
			$personnes_select[$ligne[BAZ_CHAMPS_ID]] = $ligne[BAZ_CHAMPS_NOM]." ".$ligne[BAZ_CHAMPS_PRENOM] ;
		}
		$option=array('style'=>'border:1px solid #000;width: 200px;font:12px Myriad, Arial, sans-serif;');
		$formtemplate->addElement ('select', 'personnes', BAZ_EMETTEUR, $personnes_select, $option) ;
	} else {
		$formtemplate->addElement ('hidden', 'personnes', 'tous') ;
	}

	//pour les super-administrateurs, on peut voir les annonces non validees
	//on verifie si l'utilisateur est administrateur
	if (!BAZ_SANS_AUTH) {
		$utilisateur = new Administrateur_bazar($GLOBALS['AUTH']) ;
		if ($utilisateur->isSuperAdmin()) {
			$option=array('style'=>'border:1px solid #000;width: 200px;font:12px Myriad, Arial, sans-serif;');
			$valide_select[0] = BAZ_FICHES_PAS_VALIDEES;
			$valide_select[1] = BAZ_FICHES_VALIDEES;
			$valide_select[2] = BAZ_LES_DEUX;
			$formtemplate->addElement ('select', 'valides', BAZ_VALIDE, $valide_select, $option) ;
			$defauts=array('valides'=>1);
			$formtemplate->setDefaults($defauts);
		}
	}

	//champs texte pour entrer les mots cles
	$option=array('maxlength'=>60,'style'=>'border:1px solid #000;width:200px;font:12px Myriad, Arial, sans-serif;');
	$formtemplate->addElement('text', 'recherche_mots_cles', BAZ_MOT_CLE, $option) ;

	//option cachee pour savoir si le formulaire a ete appele deja
	$formtemplate->addElement('hidden', 'recherche_effectuee', 1) ;

	// Ajout des options si un type de fiche a ete choisie
	if ( (isset($_REQUEST['nature']) && $_REQUEST['nature'] != 'toutes') || (isset($GLOBALS['_BAZAR_']['categorie_nature']) && $nb_type_de_fiches==1)) {
		if ( BAZ_MOTEUR_RECHERCHE_AVANCEE || ( isset($_REQUEST['recherche_avancee'])&&$_REQUEST['recherche_avancee']==1) ) {
			if ($GLOBALS['_BAZAR_']['categorie_nature'] != '') {
				$champs_requete = '' ;
				if (!isset($_REQUEST['nature']) || $_REQUEST['nature'] == '') {
					$_REQUEST['nature'] = $tableau_typeannonces[0];
				}
			}
			// Recuperation du template
			$requete = 'SELECT bn_template FROM bazar_nature WHERE bn_id_nature = '.$_REQUEST['nature'];
			$resultat = $GLOBALS['_BAZAR_']['db']->getOne($requete) ;
			if (DB::isError($resultat)) {
				return ($resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
			}

			if (isset($_REQUEST['recherche_avancee']) && $_REQUEST['recherche_avancee']==1) {
				foreach(array_merge($_POST, $_GET) as $cle => $valeur) $GLOBALS['_BAZAR_']['url']->addQueryString($cle, $valeur);
				$GLOBALS['_BAZAR_']['url']->addQueryString('recherche_avancee', '0');
				$lien_recherche_de_base = '<a href="'.$GLOBALS['_BAZAR_']['url']->getURL().'">'.BAZ_RECHERCHE_DE_BASE.'</a><br />';
				//lien recherche de base
				labelhtml($formtemplate,'',$lien_recherche_de_base,'','','','','');
			}

			$tableau = baz_valeurs_template($resultat) ;
			for ($i=0; $i<count($tableau); $i++) {
				if (($tableau[$i]['type'] == 'liste' || $tableau[$i]['type'] == 'checkbox' || $tableau[$i]['type'] == 'labelhtml') && $tableau[$i]['recherche'] == 1) {
					$tableau[$i]['type']($formtemplate, $tableau[$i]['nom_bdd'], $tableau[$i]['label'], $tableau[$i]['limite1'],
			                         $tableau[$i]['limite2'], $tableau[$i]['defaut'], $tableau[$i]['table_source'], $tableau[$i]['obligatoire'], 1, 'bazar') ;
				}
			}

		}
		else {
			$url_rech_avance = $GLOBALS['_BAZAR_']['url'];
			foreach(array_merge($_POST, $_GET) as $cle => $valeur) $url_rech_avance->addQueryString($cle, $valeur);
			$url_rech_avance->addQueryString('recherche_avancee', '1');
			$lien_recherche_avancee = '<a href="'.$url_rech_avance->getURL().'">'.BAZ_RECHERCHE_AVANCEE.'</a><br />';
			unset ($url_rech_avance);
		}
	}

	//lien recherche avancee
	if (isset($lien_recherche_avancee)) {
		labelhtml($formtemplate,'',$lien_recherche_avancee,'','','','','');
	}

	//Bouton de validation du formulaire
	$option=array('style'=>'border:1px solid #000;width:100px;font:15px Myriad, Arial, sans-serif;');
	$formtemplate->addElement('submit', 'rechercher', BAZ_RECHERCHER, $option);

	//affichage du formulaire
	//$res.=$formtemplate->toHTML()."\n";


	// Ajout de la table bazar_fiche_liste_valeur dans le from de la requete
	$case_coche = false ;
	$nb_jointures=0;
	$requeteFrom = '' ;
	$requeteWhere = ' bn_type_fiche IN ('.$GLOBALS['_BAZAR_']['categorie_nature'].') ';
	if ($GLOBALS['_BAZAR_']['id_typeannonce'] != 'toutes') $requeteWhere .= 'AND bn_id_nature='.$GLOBALS['_BAZAR_']['id_typeannonce'] ;
	$requeteWhere .= ' AND bn_id_nature=bf_ce_nature AND ' ;
	if (isset($GLOBALS['_BAZAR_']['langue'])) {
		$requeteWhere .= ' bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" and ';
	}
	$requeteWhereListe = '' ;

 	if ( isset($tableau) ) {
	 	for ($i = 0; $i < count ($tableau); $i++) {
			if ($tableau[$i]['type'] == 'checkbox' || $tableau[$i]['type'] == 'liste') {
				$nb_jointures++;
				$nom_liste = $tableau[$i]['type'].$tableau[$i]['nom_bdd'] ;
				if (isset($_REQUEST[$nom_liste]) && is_array($_REQUEST[$nom_liste])) {
					$case_coche = true;
					$requeteFrom .= ', bazar_fiche_valeur_liste  as bfvl'.($nb_jointures) ;
					$requeteWhereListe .= ' bfvl'.$nb_jointures.'.bfvl_ce_liste='.$tableau[$i]['nom_bdd'].' AND ' ; // Num�ro de la liste
					$requeteWhere .= ' bfvl'.($nb_jointures).'.bfvl_ce_fiche=bf_id_fiche AND ';
					$requeteWhereListe .= ' bfvl'.$nb_jointures.'.bfvl_valeur IN (' ;
					$chaine = '';
					//var_dump($_REQUEST[$nom_liste]);
					foreach ($_REQUEST[$nom_liste] as $cle =>$valeur) {
						if ($valeur == 1) {
							$chaine .= '"'.$cle.'",' ;
						}
					}
					$requeteWhereListe .= substr ($chaine, 0, strlen ($chaine)-1) ;
					$requeteWhereListe .= ') AND ';
				} else {
					if (isset ($_REQUEST[$nom_liste]) && $_REQUEST[$nom_liste]!=0) {
						$requeteFrom .= ', bazar_fiche_valeur_liste  as bfvl'.($nb_jointures) ;
						$requeteWhereListe .= ' bfvl'.$nb_jointures.'.bfvl_ce_liste='.$tableau[$i]['nom_bdd'].' AND ' ; // Num�ro de la liste
						$requeteWhereListe .= ' bfvl'.$nb_jointures.'.bfvl_valeur='.$_REQUEST[$nom_liste].' AND ';
						$requeteWhere .= ' bfvl'.($nb_jointures).'.bfvl_ce_fiche=bf_id_fiche AND ';
						$case_coche = true;
					}
				}
			}
		}
 	}
	if ($case_coche) {
		 $requeteWhere .= $requeteWhereListe;
	}
	if (isset($_REQUEST['nature']) && $_REQUEST['nature']!='' && $_REQUEST['nature']!='toutes') {
		$requeteWhere = 'bf_ce_nature="'.$_REQUEST['nature'].'" AND '.$requeteWhere;
	}

	if (BAZ_UTILISE_TEMPLATE) {
		// Appel du template n 1
		require_once BAZ_CHEMIN.'actions'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'bibliotheque/bazarTemplate.class.php' ;

		$template = new bazarTemplate ($GLOBALS['_BAZAR_']['db']) ;
		$chaine = $template->getTemplate(1, $GLOBALS['_BAZAR_']['langue'], $GLOBALS['_BAZAR_']['categorie_nature']);
		if (bazarTemplate::isError ($chaine)) return $chaine->getMessage() ;
		ob_start();
		eval ($chaine) ;
		$res .= ob_get_contents();
		ob_end_clean() ;
	} else {
    	$res .= $formtemplate->toHTML();
    	if (!isset($_REQUEST['recherche_effectuee'])) {
        	$res .= '<p class="BAZ_info">'.BAZ_ENTRER_VOS_CRITERES_DE_RECHERCHE.'</p>'."\n";
        	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION,BAZ_VOIR_FLUX_RSS);
        	//$GLOBALS['_BAZAR_']['url']->addQueryString('annonce',$GLOBALS['_BAZAR_']['id_typeannonce']);
	        //if ($typeannonce!='toutes') $GLOBALS['_BAZAR_']['url']->addQueryString('categorie_nature',$GLOBALS['_BAZAR_']['categorie_nature']);
	        
	        //on affiche les 10 dernieres fiches
		    $requete = 'SELECT DISTINCT bf_id_fiche, bf_titre, bf_date_debut_validite_fiche, bf_description, '.
		               'bn_label_nature, bf_date_creation_fiche FROM bazar_fiche, bazar_nature '.
		               'WHERE bn_id_nature=bf_ce_nature ';
            if ($typeannonce!='toutes') $requete .= 'AND bn_type_fiche="'.$typeannonce.'" ';
            $requete .= 'AND (bf_date_debut_validite_fiche<=NOW() or bf_date_debut_validite_fiche="0000-00-00") AND (bf_date_fin_validite_fiche>=NOW() or bf_date_fin_validite_fiche="0000-00-00") '.
						'ORDER BY bf_date_creation_fiche DESC, bf_date_fin_validite_fiche DESC, bf_date_maj_fiche DESC';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete);
			if (DB::isError($resultat)) {
				return ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
	        if($resultat->numRows() != 0) {
			$res .= '<h2>'.BAZ_DERNIERES_FICHES.'</h2>';
			$res .= '<ul class="BAZ_liste">';
			while($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {		    		
		    		$GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $ligne['bf_id_fiche']);
		    		$res .= '<li class="BAZ_titre_fiche">'."\n";		    		
		    		$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);		
					$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_SUPPRESSION);
					$res .= '<a href="'.$GLOBALS['_BAZAR_']['url']->getURL().'"  onclick="javascript:return confirm(\''.BAZ_CONFIRM_SUPPRIMER_FICHE.' ?\');">';
                    $res .= '<img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'delete.gif" alt="'.BAZ_EFFACER.'"></a>'."\n";
					$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
					$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER);
					$res .= '<a href="'.$GLOBALS['_BAZAR_']['url']->getURL().'"><img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'modify.gif" alt="'.BAZ_MODIFIER.'">'."\n";
					$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
					$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);	    	
		    		$res .= '<a href="'. $GLOBALS['_BAZAR_']['url']->getURL() .'" alt="lire la fiche">'. $ligne['bf_titre'].'</a></li>'."\n";
				}
				$res .= '</ul>';
			}
    	}
	}



	//affichage des resultats de la recherche si le formulaire a ete envoye
	$requeteSQL='';
	if (isset($_REQUEST['recherche_effectuee'])) {
		//preparation de la requete pour trouver les mots cles
		if (($_REQUEST['recherche_mots_cles']!='')and($_REQUEST['recherche_mots_cles']!=BAZ_MOT_CLE)) {
			//decoupage des mots cles
			$recherche = split(' ', $_REQUEST['recherche_mots_cles']) ;
			$nbmots=count($recherche);
			$requeteSQL='';
			for ($i=0; $i<$nbmots; $i++) {
				if ($i>0) $requeteSQL.=' OR ';
				$requeteSQL.='bf_titre LIKE "%'.$recherche[$i].'%" OR bf_description LIKE "%'.$recherche[$i].'%" ';
			}
		}
		if (!isset($_REQUEST['nature'])) {
			if (!isset ($GLOBALS['_BAZAR_']['id_nature'])) $typedefiches = $tableau_typeannonces;
			else $typedefiches = $GLOBALS['_BAZAR_']['id_nature'] ;
		} else {
			$typedefiches = $_REQUEST['nature'] ;
			if ($typedefiches == 'toutes') $typedefiches = $tableau_typeannonces ;
		}
		if ($typeannonce!='toutes') $typedefiches=$typeannonce;
		if (isset($_REQUEST['valides'])) {$valides=$_REQUEST['valides'];}
		else {$valides=1;}
		//generation de la liste de flux a afficher
		if (!isset($_REQUEST['personnes'])) $_REQUEST['personnes']='tous';
		$res .= baz_liste_pagine_HTML($typedefiches, '', $_REQUEST['personnes'], $valides, $requeteSQL, $requeteFrom, $requeteWhere);
	}

	// Nettoyage de l'url
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	$GLOBALS['_BAZAR_']['url']->removeQueryString('annonce');
	$GLOBALS['_BAZAR_']['url']->removeQueryString('categorie_nature');
	$GLOBALS['_BAZAR_']['url']->removeQueryString('recherche_avancee');

	return $res;
}

/**
 * Cette fonction renvoie du HTML
 */
function baz_liste_pagine_HTML($typeannonce, $nbitem, $emetteur, $valide, $requeteSQL = '', $requeteFrom = '', $requeteWhere = '') {
	// generation de la requete MySQL personnalisee
	$req_where=0;
	$requete = 'SELECT * '.
				'FROM bazar_fiche, bazar_nature '.$requeteFrom.' WHERE '.$requeteWhere;
	if ($valide!=2) {
		if ($req_where==1) {$requete .= ' AND ';}
		$req_where=1;
		$requete .= 'bf_statut_fiche='.$valide;
	} else {
		$requete .= '1 ' ;
	}

	if ($valide!=0) {
		if ($req_where==1) {$requete .= ' AND ';}
		$requete .= '(bf_date_debut_validite_fiche<=NOW() or bf_date_debut_validite_fiche="0000-00-00")'.
					' AND (bf_date_fin_validite_fiche>=NOW() or bf_date_fin_validite_fiche="0000-00-00") AND bn_id_nature=bf_ce_nature';
		$req_where=1;
	}
	if ($emetteur!='' && $emetteur!='tous') {
		if ($req_where==1) {$requete .= ' AND ';}
		$requete .= 'bf_ce_utilisateur='.$emetteur;
		$req_where=1;
	}
	if ($requeteSQL!='') {
		if ($req_where==1) {$requete .= ' AND ';}
		$requete .= '('.$requeteSQL.')';
		$req_where=1;
	}
	$requete .= ' ORDER BY  bf_date_debut_validite_fiche DESC, bf_date_fin_validite_fiche DESC, bf_date_maj_fiche DESC';
	if ($nbitem!='') {$requete .= ' LIMIT 0,'.$nbitem;}
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		return  $resultat->getMessage().'<br /><br />'.$resultat->getDebugInfo() ;
	}
	$res = '<p class="BAZ_info">'.BAZ_IL_Y_A;
	$nb_result=$resultat->numRows();
	if ($nb_result<=1) $res .= $resultat->numRows().' '.BAZ_FICHE_CORRESPONDANTE.'</p>'."\n";
	else $res .= $resultat->numRows().' '.BAZ_FICHES_CORRESPONDANTES.'</p>'."\n";
	

	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);

	$donnees = array();
	while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
		$GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $ligne['bf_id_fiche']) ;
		array_push ($donnees, $ligne);
	}
	// Mise en place du Pager
	require_once 'Pager/Pager.php';
	$params = array(
    'mode'       => BAZ_MODE_DIVISION,
    'perPage'    => BAZ_NOMBRE_RES_PAR_PAGE,
    'delta'      => BAZ_DELTA,
    'httpMethod' => 'GET',
    'extraVars' => array_merge($_POST, $_GET),
    'altNext' => BAZ_SUIVANT,
    'altPrev' => BAZ_PRECEDENT,
    'nextImg' => BAZ_SUIVANT,
    'prevImg' => BAZ_PRECEDENT,
    'itemData'   => $donnees
	);
	$pager = & Pager::factory($params);
	$data  = $pager->getPageData();
	$links = $pager->getLinks();

    if (BAZ_UTILISE_TEMPLATE) {
		//Appel du template n 2
		require_once BAZ_CHEMIN.'actions'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'bibliotheque/bazarTemplate.class.php' ;
		$template = new bazarTemplate ($GLOBALS['_BAZAR_']['db']) ;
		$chaine = $template->getTemplate(2, $GLOBALS['_BAZAR_']['langue'], $GLOBALS['_BAZAR_']['categorie_nature']);
		if (bazarTemplate::isError ($chaine)) return $chaine->getMessage() ;
		ob_start();
		eval ($chaine) ;
		$res .= ob_get_contents();
		ob_end_clean() ;
    } else {
    	$res .= '<div class="bazar_numero">'.$pager->links.'</div>'."\n";
    	$res .= '<ul>'."\n" ;
    	foreach ($data as $valeur) {
	        $res .='<li class="BAZ_'.$valeur['bn_label_class'].'">'."\n";
	        $GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $valeur['bf_id_fiche']) ;
	        if ((!BAZ_SANS_AUTH && ($utilisateur->isSuperAdmin() || $GLOBALS['id_user']==$valeur['bf_ce_utilisateur'])) || BAZ_SANS_AUTH) {		    		   		
		    		$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);		
					$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_SUPPRESSION);
					$res .= '<a href="'.$GLOBALS['_BAZAR_']['url']->getURL().'"  onclick="javascript:return confirm(\''.BAZ_CONFIRM_SUPPRIMER_FICHE.' ?\');">';
                    $res .= '<img src="'.BAZ_CHEMIN.'actions'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'images/delete.gif" alt="'.BAZ_EFFACER.'"></a>'."\n";
					$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
					$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER);
					$res .= '<a href="'.$GLOBALS['_BAZAR_']['url']->getURL().'"><img src="'.BAZ_CHEMIN.'actions'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'images/modify.gif" alt="'.BAZ_MODIFIER.'">'."\n";
			}
	        $GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);	    	
		    $res .= '<a href="'. $GLOBALS['_BAZAR_']['url']->getURL() .'" alt="lire la fiche">'. $valeur['bf_titre'].'</a>'."\n";
	        $res .='</li>'."\n";
	    }
	    $res .= '</ul>'."\n".'<div class="bazar_numero">'.$pager->links.'</div>'."\n";
    }


	// Nettoyage de l'url
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
	$GLOBALS['_BAZAR_']['url']->removeQueryString('typeannonce');
	$GLOBALS['_BAZAR_']['url']->removeQueryString('recherche_avancee');

	return $res ;
}

function encoder_en_utf8($txt) {
	// Nous remplacons l'apostrophe de type RIGHT SINGLE QUOTATION MARK et les & isol�s qui n'auraient pas �t�
	// remplac�s par une entit�e HTML.
	$cp1252_map = array(
	   "\xc2\x80" => "\xe2\x82\xac", /* EURO SIGN */
	   "\xc2\x82" => "\xe2\x80\x9a", /* SINGLE LOW-9 QUOTATION MARK */
	   "\xc2\x83" => "\xc6\x92",     /* LATIN SMALL LETTER F WITH HOOK */
	   "\xc2\x84" => "\xe2\x80\x9e", /* DOUBLE LOW-9 QUOTATION MARK */
	   "\xc2\x85" => "\xe2\x80\xa6", /* HORIZONTAL ELLIPSIS */
	   "\xc2\x86" => "\xe2\x80\xa0", /* DAGGER */
	   "\xc2\x87" => "\xe2\x80\xa1", /* DOUBLE DAGGER */
	   "\xc2\x88" => "\xcb\x86",     /* MODIFIER LETTER CIRCUMFLEX ACCENT */
	   "\xc2\x89" => "\xe2\x80\xb0", /* PER MILLE SIGN */
	   "\xc2\x8a" => "\xc5\xa0",     /* LATIN CAPITAL LETTER S WITH CARON */
	   "\xc2\x8b" => "\xe2\x80\xb9", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
	   "\xc2\x8c" => "\xc5\x92",     /* LATIN CAPITAL LIGATURE OE */
	   "\xc2\x8e" => "\xc5\xbd",     /* LATIN CAPITAL LETTER Z WITH CARON */
	   "\xc2\x91" => "\xe2\x80\x98", /* LEFT SINGLE QUOTATION MARK */
	   "\xc2\x92" => "\xe2\x80\x99", /* RIGHT SINGLE QUOTATION MARK */
	   "\xc2\x93" => "\xe2\x80\x9c", /* LEFT DOUBLE QUOTATION MARK */
	   "\xc2\x94" => "\xe2\x80\x9d", /* RIGHT DOUBLE QUOTATION MARK */
	   "\xc2\x95" => "\xe2\x80\xa2", /* BULLET */
	   "\xc2\x96" => "\xe2\x80\x93", /* EN DASH */
	   "\xc2\x97" => "\xe2\x80\x94", /* EM DASH */
	   "\xc2\x98" => "\xcb\x9c",     /* SMALL TILDE */
	   "\xc2\x99" => "\xe2\x84\xa2", /* TRADE MARK SIGN */
	   "\xc2\x9a" => "\xc5\xa1",     /* LATIN SMALL LETTER S WITH CARON */
	   "\xc2\x9b" => "\xe2\x80\xba", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
	   "\xc2\x9c" => "\xc5\x93",     /* LATIN SMALL LIGATURE OE */
	   "\xc2\x9e" => "\xc5\xbe",     /* LATIN SMALL LETTER Z WITH CARON */
	   "\xc2\x9f" => "\xc5\xb8"      /* LATIN CAPITAL LETTER Y WITH DIAERESIS*/
	);
	return  strtr(preg_replace('/ \x{0026} /u', ' &#38; ', mb_convert_encoding($txt, 'UTF-8','HTML-ENTITIES')), $cp1252_map);
}

/** baz_affiche_flux_RSS() - affiche le flux rss à partir de parametres
*
*
* @return  string Le flux RSS, avec les headers et tout et tout
*/
function baz_affiche_flux_RSS() {
if (isset($_GET['annonce'])) {
	$annonce=$_GET['annonce'];
}
else {
	$annonce='';
}

if (isset($_GET['categorie_nature'])) {
	$categorie_nature=$_GET['categorie_nature'];
}
else {
	$categorie_nature='';
}

if (isset($_GET['nbitem'])) {
	$nbitem=$_GET['nbitem'];
}
else {
	$nbitem='';
}

if (isset($_GET['emetteur'])) {
	$emetteur=$_GET['emetteur'];
}
else {
	$emetteur='';
}

if (isset($_GET['valide'])) {
	$valide=$_GET['valide'];
}
else {
	$valide=1;
}

if (isset($_GET['sql'])) {
	$requeteSQL=$_GET['sql'];
}
else {
	$requeteSQL='';
}
// Gestion de la langue
if (isset($_GET['langue'])) {
	$requeteWhere = ' bn_ce_i18n like "'.$_GET['langue'].'%" and ';
} else {
	$requeteWhere = '';
}
return html_entity_decode(gen_RSS($annonce, $nbitem, $emetteur, $valide, $requeteSQL, '', $requeteWhere, $categorie_nature));

}

function afficher_flux_rss() {
	if (isset($_GET['annonce'])) {
		$annonce=$_GET['annonce'];
	}
	else {
		$annonce='';
	}

	if (isset($_GET['categorie_nature'])) {
		$categorie_nature=$_GET['categorie_nature'];
	}
	else {
		$categorie_nature='';
	}

	if (isset($_GET['nbitem'])) {
		$nbitem=$_GET['nbitem'];
	}
	else {
		$nbitem='';
	}

	if (isset($_GET['emetteur'])) {
		$emetteur=$_GET['emetteur'];
	}
	else {
		$emetteur='';
	}

	if (isset($_GET['valide'])) {
		$valide=$_GET['valide'];
	}
	else {
		$valide=1;
	}

	if (isset($_GET['sql'])) {
		$requeteSQL=$_GET['sql'];
	}
	else {
		$requeteSQL='';
	}
	// Gestion de la langue
	if (isset($_GET['langue'])) {
		$requeteWhere = ' bn_ce_i18n like "'.$_GET['langue'].'%" and ';
	} else {
		$requeteWhere = '';
	}
	echo html_entity_decode(gen_RSS($annonce, $nbitem, $emetteur, $valide, $requeteSQL, '', $requeteWhere, $categorie_nature));
}


/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: bazar.fonct.rss.php,v $
* Revision 1.3  2008/08/27 13:18:57  mrflos
* maj générale
*
* Revision 1.2  2008/07/29 17:32:25  mrflos
* maj générale
*
* Revision 1.1  2008/07/07 18:00:39  mrflos
* maj carto plus calendrier
*
* Revision 1.2  2008/03/06 00:15:40  mrflos
* correction des bugs bazar, ajout de fichiers d'images
*
* Revision 1.1  2008/02/18 09:12:47  mrflos
* Premiere release de 3 extensions en version alpha (bugs nombreux!) des plugins bazar, e2gallery, et templates
*
*
*
* +-- Fin du code ----------------------------------------------------------------------------------------+
*/
?>
