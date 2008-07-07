<?php
function insertion ($valeur, &$db) {	
	// Calcul dernier identifiant
    $id_wikini_bdd = baz_nextId('bazar_wikini', 'bw_id_wikini', $db) ;
    $requete = "insert into bazar_wikini set bw_id_wikini ="
                .$id_wikini_bdd.","
                .requete_wikini($valeur) ;
    $resultat = $db->query ($requete) ;
    if (DB::isError ($resultat)) {
        trigger_error("Echec de la requete : $requete<br />".$resultat->getMessage(),E_USER_WARNING) ;
    }

	if (GEN_FTP_UTILISE) {
	    /** Inclusion bibliothèque de PEAR gérant le FTP.*/
	    require_once PAP_CHEMIN_API_PEAR.'Net/FTP.php';

	    // création de l'objet pear ftp
	    $objet_pear_ftp = new Net_FTP(PAP_FTP_SERVEUR, PAP_FTP_PORT);
	    // création de la connexion
	    $ftp_conn = $objet_pear_ftp->connect(PAP_FTP_SERVEUR, PAP_FTP_PORT);
	    // identification
	    $ftp_login_result = $objet_pear_ftp->login(PAP_FTP_UTILISATEUR, PAP_FTP_MOT_DE_PASSE);
	    
	    // Gestion des erreurs ftp
	    if ((PEAR::isError($ftp_conn) || PEAR::isError($ftp_login_result))) {
	        $message =  '<p class="pap_erreur"> ERREUR Papyrus admin : impossible de se connecter par ftp.<br />'.
	                    'Serveur : '. PAP_FTP_SERVEUR .'<br />'.
	                    'Utilisateur : '. PAP_FTP_UTILISATEUR .'<br />'.
	                    'Erreur connexion : '.$ftp_conn->getMessage().'<br />'.
	                    'Erreur login : '.$ftp_login_result->getMessage().'<br />'.
	                    'Ligne n° : '. __LINE__ .'<br />'.
	                    'Fichier n° : '. __FILE__ .'<br /><p>';
	        print  $message;
	    }
	    $objet_pear_ftp->mkdir(PAP_FTP_RACINE.ADWI_CHEMIN_WIKINI.$valeur['code_alpha_wikini']) ;
	
	    $chemin_wikini_bibliotheque = ADWI_CHEMIN_BIBLIOTHEQUE_WIKINI;
	    $chemin_wikini = PAP_FTP_RACINE.ADWI_CHEMIN_WIKINI.$valeur['code_alpha_wikini'].GEN_SEP;
	    
	    // Overwrite = fale (3eme parametre)
	    $resultat = $objet_pear_ftp->putRecursive($chemin_wikini_bibliotheque, $chemin_wikini, false, FTP_BINARY);
	    
	    if (PEAR::isError($resultat)) {
	        $message =  '<p class="pap_erreur"> ERREUR Papyrus admin : impossible de copier le wikini de reference par ftp.<br />'.
	                    'Fichier origine : '. $chemin_wikini_bibliotheque .'<br />'.
	                    'Fichier copié : '. $chemin_wikini .'<br />'.
	                    'Erreur origine : '. $resultat->getMessage() .'<br />'.
	                    'Ligne n° : '. __LINE__ .'<br />'.
	                    'Fichier n° : '. __FILE__ .'<br /></p>';
	            print $message;
	    }
	}
	
	// else {
	
	// Gestion sans FTP à faire 
	
		//}
		 
    // Creation tables wikini
    
    include_once 'gestion_wikini.class.php' ;
     
    $g_wikini = new gestion_wikini($db);
    
    
	if ((!isset($valeur['table_prefix'])) || (empty($config_wikini['$valeur'])))  {
		$valeur['table_prefix'] = $valeur['code_alpha_wikini'];
	}
    $g_wikini->creation_tables($valeur['table_prefix']);
 	
 	// Creation Wakka.config.php
 	
 	$config_wikini = adwi_config_wikini($valeur['code_alpha_wikini'] ,$db );
	
	
	$base_url=parse_url(PAP_URL);
	$dirname_base_url=dirname($base_url['path']);
	$config_base_url=$dirname_base_url.GEN_SEP.ADWI_CHEMIN_WIKINI.GEN_SEP.$config_wikini['code_alpha_wikini'].GEN_SEP."wakka.php?wiki=";
	$config_base_url = str_replace("//", "/", $config_base_url);
	

	$config = array(
    "wakka_version" => "0.1.1",
	"wikini_version" => "0.4.3",
    'mysql_host'            => $config_wikini['bdd_hote'],
    'mysql_database'        => $config_wikini['bdd_nom'],
    'mysql_user'            => $config_wikini['bdd_utilisateur'],
    'mysql_password'        => $config_wikini['bdd_mdp'],
    'table_prefix'          => $config_wikini['table_prefix'],
    'root_page'             => $config_wikini['page'],
    'wakka_name'            => $config_wikini['code_alpha_wikini'],
    'base_url'              => $config_base_url,
    'rewrite_mode'          => '0',
    'meta_keywords'         => '',
    'meta_description'      => '',
    'action_path'           => 'actions',
    'handler_path'          => 'handlers',
    'header_action'         => 'header',
    'footer_action'         => 'footer',
    'navigation_links'      => 'DerniersChangements :: DerniersCommentaires :: ParametresUtilisateur',
    'referrers_purge_time'  => 24,
    'pages_purge_time'      => 90,
    'default_write_acl'     => '*',
    'default_read_acl'      => '*',
    'default_comment_acl'   => '*',
    'preview_before_save'   => '0');

	
	
	// convert config array into PHP code
	$configCode = "<?php\n// wakka.config.php cr&eacute;&eacute;e ".strftime("%c")."\n// ne changez pas la wikini_version manuellement!\n\n\$wakkaConfig = array(\n";
	foreach ($config as $k => $v)
	{
		$entries[] = "\t\"".$k."\" => \"".$v."\"";
	}
	$configCode .= implode(",\n", $entries).");\n?>";


	$tempfn = tempnam("","");
	$temp = fopen($tempfn, "w");
	
	fwrite($temp, $configCode);
	fclose($temp);


	$fichier_config_source = $tempfn;
    $fichier_config_cible = PAP_FTP_RACINE.ADWI_CHEMIN_WIKINI.$valeur['code_alpha_wikini'].GEN_SEP."wakka.config.php";
    
    $resultat = $objet_pear_ftp->put($fichier_config_source, $fichier_config_cible, false, FTP_BINARY);
    
    if (PEAR::isError($resultat)) {
        $message =  '<p class="pap_erreur"> ERREUR Papyrus admin : impossible de copier le wikini de reference par ftp.<br />'.
                    'Fichier origine : '. $chemin_wikini_bibliotheque .'<br />'.
                    'Fichier copié : '. $chemin_wikini .'<br />'.
                    'Erreur origine : '. $resultat->getMessage() .'<br />'.
                    'Ligne n° : '. __LINE__ .'<br />'.
                    'Fichier n° : '. __FILE__ .'<br /></p>';
            print $message;
    }
    
	$objet_pear_ftp->disconnect();
	 
	unlink($tempfn); 	   
}


/**
 *
 * Formate code sql pour insertion à partir des valeurs entrees dans le formulaire 
 * 
 * @return  string  un morceau de code SQL
 */
function requete_wikini (&$valeur) {
    return   'bw_code_alpha_wikini ="'.$valeur['code_alpha_wikini'].'", '
            .'bw_bdd_hote ="'.$valeur['bdd_hote'].'", '
            .'bw_bdd_nom="'.$valeur['bdd_nom'].'", '
            .'bw_bdd_utilisateur ="'.$valeur['bdd_utilisateur'].'", '
            .'bw_bdd_mdp="'.$valeur['bdd_mdp'].'", '
            .'bw_table_prefix="'.$valeur['table_prefix'].'", '
            .'bw_chemin="'.$valeur['chemin'].'", '
            .'bw_page="'.$valeur['page'].'"';
}

function adwi_config_wikini($code_alpha_wikini,&$db) {
	
	$config_wikini = adwi_valeurs_par_code_alpha($code_alpha_wikini,$db );

	// Parametres de base de donnée de Papyrus par défaut  
	
	if ((!isset($config_wikini['bdd_hote'])) || (empty($config_wikini['bdd_hote']))) {
	   $config_wikini['bdd_hote'] = PAP_BDD_SERVEUR;
	}
	
	if ((!isset($config_wikini['bdd_nom'])) || (empty($config_wikini['bdd_nom'])))  {
	   $config_wikini['bdd_nom'] = PAP_BDD_NOM;
	}
	
	if ((!isset($config_wikini['bdd_utilisateur'])) || (empty($config_wikini['bdd_utilisateur'])))  {
	   $config_wikini['bdd_utilisateur'] = PAP_BDD_UTILISATEUR;
	}
	
	if ((!isset($config_wikini['bdd_mdp'])) || (empty($config_wikini['bdd_mdp'])))  {
	   $config_wikini['bdd_mdp'] = PAP_BDD_MOT_DE_PASSE;
	}
	
	if ((!isset($config_wikini['table_prefix'])) || (empty($config_wikini['table_prefix'])))  {
		$config_wikini['table_prefix'] = $code_alpha_wikini.'_';
	}
		
	
	// Ordre de selection de la page de demarrage :
	
	// Page par defaut du Wiki enregistré
	// PagePrincipale
	
	if ((!isset($config_wikini['page']))  || (empty($config_wikini['page']))) {
	   	$config_wikini['page'] = 'PagePrincipale';
	}
	
	
	/** Constante stockant le chemin du dossier contenant le site Wikini en cours */
	
	if ((!isset($config_wikini['chemin'])) || (empty($config_wikini['chemin'])))  {
		$config_wikini['chemin'] = GEN_CHEMIN_WIKINI.$config_wikini['code_alpha_wikini'].GEN_SEP;
	}
	
	
	return $config_wikini;
}

function adwi_valeurs_par_code_alpha($code_alpha_wikini, &$db) {
	
	$requete = "SELECT * FROM bazar_wikini WHERE bw_code_alpha_wikini='". $code_alpha_wikini."'" ;
    $resultat = $db->query ($requete) ;
 	if (DB::isError ($resultat)) {
        $GLOBALS['_GEN_commun']['debogage_erreur']->gererErreur(E_USER_WARNING, "Echec de la requete : $requete<br />".$resultat->getMessage(),
                                                                        __FILE__, __LINE__, 'admin_wikini')   ;
        return ;
    }

	$ligne = $resultat->fetchRow (DB_FETCHMODE_OBJECT) ;
	$resultat->free();
	unset ($requete, $resultat);
	return adwi_valeurs_par_defaut($ligne->gewi_id_wikini,$db);
	
}

function adwi_valeurs_par_defaut($id_wikini, &$db) {
	
    // requete sur bazar_wikini
    
    $requete = "SELECT * FROM bazar_wikini WHERE bw_id_wikini=$id_wikini" ;
    $resultat = $db->query ($requete) ;
    if (DB::isError ($resultat)) {
        trigger_error("Echec de la requete : $requete<br />".$resultat->getMessage(), E_USER_WARNING) ;
        return ;
    }
    $tableau_retour = array () ;
    $ligne = $resultat->fetchRow (DB_FETCHMODE_OBJECT) ;
    
    $tableau_retour['code_alpha_wikini'] = $ligne->bw_code_alpha_wikini;
    $tableau_retour['bdd_hote'] = $ligne->bw_bdd_hote ;
    $tableau_retour['bdd_nom'] = $ligne->bw_bdd_nom;
    $tableau_retour['bdd_utilisateur'] = $ligne->bw_bdd_utilisateur;
    $tableau_retour['bdd_mdp'] = $ligne->bw_bdd_mdp;
    $tableau_retour['table_prefix'] = $ligne->bw_table_prefix ;
    $tableau_retour['page'] = $ligne->bw_page;
    $tableau_retour['chemin'] = $ligne->bw_chemin ;
    
    unset ($requete, $resultat);
    return $tableau_retour ;
}
?>