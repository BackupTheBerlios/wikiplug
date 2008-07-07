-- 
-- Structure de la table `bazar_abonnement`
-- 

CREATE TABLE `bazar_abonnement` (
  `ba_id_utilisateur` int(11) NOT NULL default '0',
  `ba_id_rubrique` int(11) NOT NULL default '0'
);

-- 
-- Contenu de la table `bazar_abonnement`
-- 


-- --------------------------------------------------------

-- 
-- Structure de la table `bazar_appropriation`
-- 

CREATE TABLE `bazar_appropriation` (
  `ba_ce_id_fiche` int(11) NOT NULL default '0',
  `ba_ce_id_structure` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ba_ce_id_fiche`,`ba_ce_id_structure`)
);

-- --------------------------------------------------------

-- 
-- Structure de la table `bazar_commentaires`
-- 

CREATE TABLE `bazar_commentaires` (
  `bc_id_commentaire` int(11) NOT NULL auto_increment,
  `bc_ce_id_fiche` int(11) NOT NULL default '0',
  `bc_nom` varchar(255) NOT NULL default '',
  `bc_commentaire` text NOT NULL,
  `bc_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`bc_id_commentaire`)
);

-- --------------------------------------------------------

-- 
-- Structure de la table `bazar_droits`
-- 

CREATE TABLE `bazar_droits` (
  `bd_id_utilisateur` int(11) unsigned NOT NULL default '0',
  `bd_id_nature_offre` int(10) unsigned NOT NULL default '0',
  `bd_niveau_droit` int(10) unsigned default NULL,
  PRIMARY KEY  (`bd_id_utilisateur`,`bd_id_nature_offre`)
);


-- 
-- Contenu de la table `bazar_droits`
-- 

INSERT INTO `bazar_droits` (`bd_id_utilisateur`, `bd_id_nature_offre`, `bd_niveau_droit`) VALUES 
(1, 0, 0);

-- --------------------------------------------------------

-- 
-- Structure de la table `bazar_fiche`
-- 

CREATE TABLE `bazar_fiche` (
  `bf_id_fiche` int(10) unsigned NOT NULL default '0',
  `bf_ce_utilisateur` int(11) unsigned NOT NULL default '0',
  `bf_ce_nature` int(10) unsigned NOT NULL default '0',
  `bf_ce_departement` int(10) unsigned default NULL,
  `bf_ce_region` int(10) unsigned default NULL,
  `bf_ce_pays` char(2) default NULL,
  `bf_ce_langue` int(11) NOT NULL default '0',
  `bf_titre` varchar(255) default NULL,
  `bf_theme` varchar(255) NOT NULL default '',
  `bf_code` varchar(20) default NULL,
  `bf_description` text,
  `bf_url_image` varchar(255) default NULL,
  `bf_date_debut_validite_fiche` date NOT NULL default '0000-00-00',
  `bf_date_fin_validite_fiche` date NOT NULL default '0000-00-00',
  `bf_date_creation_fiche` datetime NOT NULL default '0000-00-00 00:00:00',
  `bf_date_maj_fiche` datetime NOT NULL default '0000-00-00 00:00:00',
  `bf_statut_fiche` int(10) unsigned default NULL,
  `bf_date_debut_evenement` date default NULL,
  `bf_date_fin_evenement` date default NULL,
  `bf_lieu_evenement` varchar(255) default NULL,
  `bf_capacite_accueil` int(10) unsigned default NULL,
  `bf_nb_animateurs` int(10) unsigned default NULL,
  `bf_tarif_individuel` varchar(255) default NULL,
  `bf_numero_module` varchar(8) NOT NULL default '',
  `bf_total_module` varchar(8) NOT NULL default '',
  `bf_adresse_contact` varchar(255) NOT NULL,
  `bf_qualif_preparee` varchar(60) NOT NULL default '',
  `bf_contenu_formation` text NOT NULL,
  `bf_conditions_acces` text NOT NULL,
  `bf_date_fin_inscription` date NOT NULL default '0000-00-00',
  `bf_cp_lieu_evenement` varchar(7) NOT NULL default '',
  `bf_tarif_entreprise` varchar(255) NOT NULL default '',
  `bf_tarif_opca` varchar(255) NOT NULL default '',
  `bf_num_agrement` varchar(20) NOT NULL default '',
  `bf_prenom_contact` varchar(60) NOT NULL default '',
  `bf_nom_contact` varchar(60) NOT NULL default '',
  `bf_mail` varchar(50) NOT NULL default '',
  `bf_telephone` varchar(14) NOT NULL default '',
  `bf_intervenants` text NOT NULL,
  `bf_infos_complementaires` text NOT NULL,
  `bf_public` varchar(255) NOT NULL default '',
  `bf_nb_consultations` int(11) NOT NULL default '0',
  `bf_auteur` varchar(255) NOT NULL default '',
  `bf_auteur_coordonnee` varchar(255) NOT NULL default '',
  `bf_editeur` varchar(255) NOT NULL default '',
  `bf_editeur_coordonnee` varchar(255) NOT NULL default '',
  `bf_annee_parution` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`bf_id_fiche`)
);


-- --------------------------------------------------------

-- 
-- Structure de la table `bazar_fiche_valeur_liste`
-- 

CREATE TABLE `bazar_fiche_valeur_liste` (
  `bfvl_ce_fiche` int(10) unsigned NOT NULL default '0',
  `bfvl_ce_liste` int(10) unsigned NOT NULL default '0',
  `bfvl_valeur` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bfvl_ce_fiche`,`bfvl_ce_liste`,`bfvl_valeur`)
);


-- --------------------------------------------------------

-- 
-- Structure de la table `bazar_fichier_joint`
-- 

CREATE TABLE `bazar_fichier_joint` (
  `bfj_id_fichier` int(10) unsigned NOT NULL default '0',
  `bfj_ce_fiche` int(10) unsigned NOT NULL default '0',
  `bfj_description` text,
  `bfj_fichier` varchar(255) default NULL,
  PRIMARY KEY  (`bfj_id_fichier`)
);


-- --------------------------------------------------------

-- 
-- Structure de la table `bazar_liste`
-- 

CREATE TABLE `bazar_liste` (
  `bl_id_liste` int(11) NOT NULL default '0',
  `bl_label_liste` varchar(255) NOT NULL default '',
  `bl_ce_i18n` varchar(5) NOT NULL default '',
  PRIMARY KEY  (`bl_id_liste`,`bl_ce_i18n`)
);

-- 
-- Contenu de la table `bazar_liste`
-- 

INSERT INTO `bazar_liste` (`bl_id_liste`, `bl_label_liste`, `bl_ce_i18n`) VALUES 
(1, 'Type de formation', 'fr-FR'),
(2, 'Tranches d''&acirc;ges', 'fr-FR'),
(3, 'Dipl&ocirc;mes', 'fr-FR'),
(4, 'Niveau formation', 'fr-FR'),
(5, 'Agr&eacute;ments', 'fr-FR'),
(6, 'Aides', 'fr-FR'),
(7, 'Echelle g&eacute;ographique', 'fr-FR'),
(8, 'H&eacute;bergement', 'fr-FR'),
(9, 'Milieu dominant', 'fr-FR'),
(10, 'Nature des aides', 'fr-FR'),
(11, 'Niveau scolaire', 'fr-FR'),
(12, 'Oui / Non', 'fr-FR'),
(13, 'P&eacute;riode', 'fr-FR'),
(14, 'Prix', 'fr-FR'),
(15, 'Qualification', 'fr-FR'),
(16, 'Secteur', 'fr-FR'),
(17, 'Th&egrave;mes', 'fr-FR'),
(18, 'Type de s&eacute;jour', 'fr-FR'),
(19, 'D&eacute;partements fran&ccedil;ais', 'fr-FR'),
(20, 'Type de parution', 'fr-FR'),
(22, 'Publics cibles', 'fr-FR'),
(23, 'Age minimum', 'fr-FR'),
(24, 'Approches', 'fr-FR'),
(26, 'Type activit&eacute;', 'fr-FR'),
(27, 'Lieu activit&eacute;', 'fr-FR'),
(28, 'Type de structure', 'fr-FR'),
(29, 'Age maximum', 'fr-FR');

-- --------------------------------------------------------

-- 
-- Structure de la table `bazar_liste_valeurs`
-- 

CREATE TABLE `bazar_liste_valeurs` (
  `blv_ce_liste` int(11) unsigned NOT NULL default '0',
  `blv_valeur` int(11) unsigned NOT NULL default '0',
  `blv_label` varchar(255) NOT NULL default '',
  `blv_ce_i18n` varchar(5) NOT NULL default '',
  PRIMARY KEY  (`blv_ce_liste`,`blv_valeur`,`blv_ce_i18n`)
);

-- 
-- Contenu de la table `bazar_liste_valeurs`
-- 

INSERT INTO `bazar_liste_valeurs` (`blv_ce_liste`, `blv_valeur`, `blv_label`, `blv_ce_i18n`) VALUES 
(1, 1, 'Continue', 'fr-FR'),
(1, 2, 'Dipl&ocirc;mante', 'fr-FR'),
(1, 3, 'Qualifiante', 'fr-FR'),
(2, 1, '4 &agrave; 7 ans', 'fr-FR'),
(2, 2, '8 &agrave; 12 ans', 'fr-FR'),
(2, 3, '12 &agrave; 15 ans', 'fr-FR'),
(2, 4, '15 &agrave; 18 ans', 'fr-FR'),
(2, 5, 'Adultes', 'fr-FR'),
(2, 7, 'Autre', 'fr-FR'),
(2, 6, 'Tous publics', 'fr-FR'),
(2, 11, 'Choisir...', 'fr-FR'),
(2, 8, '9 &agrave; 14 ans', 'fr-FR'),
(3, 1, 'BTS', 'fr-FR'),
(3, 2, 'BEATEP', 'fr-FR'),
(3, 3, 'DEFA', 'fr-FR'),
(3, 4, 'BP JEPS', 'fr-FR'),
(3, 5, 'BAFA', 'fr-FR'),
(3, 6, 'BAFD', 'fr-FR'),
(3, 7, 'DEDPAD', 'fr-FR'),
(3, 8, 'DUT', 'fr-FR'),
(3, 0, 'Choisir...', 'fr-FR'),
(3, 9, 'Autre', 'fr-FR'),
(4, 1, 'VI', 'fr-FR'),
(4, 2, 'V (bis)', 'fr-FR'),
(4, 3, 'V', 'fr-FR'),
(4, 4, 'IV', 'fr-FR'),
(4, 5, 'III', 'fr-FR'),
(4, 6, 'II', 'fr-FR'),
(4, 7, 'I', 'fr-FR'),
(4, 0, 'Choisir...', 'fr-FR'),
(5, 1, 'Education Nationale', 'fr-FR'),
(5, 2, 'Jeunesse et Sports', 'fr-FR'),
(5, 3, 'Classe Patrimoine', 'fr-FR'),
(5, 4, 'Autres', 'fr-FR'),
(6, 1, 'CAF', 'fr-FR'),
(6, 2, 'ANCV', 'fr-FR'),
(6, 3, 'CE', 'fr-FR'),
(6, 4, 'Autres', 'fr-FR'),
(7, 1, 'R&eacute;gionale', 'fr-FR'),
(7, 2, 'Nationale', 'fr-FR'),
(7, 3, 'Europ&eacute;enne', 'fr-FR'),
(7, 4, 'Mondiale', 'fr-FR'),
(8, 1, 'Tente', 'fr-FR'),
(8, 2, 'G&icirc;te', 'fr-FR'),
(8, 3, 'Chambres', 'fr-FR'),
(8, 4, 'Autre', 'fr-FR'),
(8, 0, 'Choisir...', 'fr-FR'),
(8, 5, 'Plusieurs formules', 'fr-FR'),
(9, 1, 'Marin', 'fr-FR'),
(9, 2, 'Aquatique', 'fr-FR'),
(9, 3, 'Rural', 'fr-FR'),
(9, 4, 'Montagnard', 'fr-FR'),
(9, 5, 'Forestier', 'fr-FR'),
(9, 6, 'Garrigue', 'fr-FR'),
(9, 7, 'Autre', 'fr-FR'),
(9, 0, 'Choisir...', 'fr-FR'),
(10, 1, 'Financi&egrave;re', 'fr-FR'),
(10, 2, 'Technique', 'fr-FR'),
(11, 1, 'Tous niveaux', 'fr-FR'),
(11, 2, 'Maternelle', 'fr-FR'),
(11, 3, 'Primaire (tous)', 'fr-FR'),
(11, 4, '&nbsp;CP', 'fr-FR'),
(11, 5, '&nbsp;CE1', 'fr-FR'),
(11, 6, '&nbsp;CE2', 'fr-FR'),
(11, 7, '&nbsp;CM1', 'fr-FR'),
(11, 8, '&nbsp;CM2', 'fr-FR'),
(11, 9, 'Coll&egrave;ge (tous)', 'fr-FR'),
(11, 10, '&nbsp;6&egrave;me', 'fr-FR'),
(11, 11, '&nbsp;5&egrave;me', 'fr-FR'),
(11, 12, '&nbsp;4&egrave;me', 'fr-FR'),
(11, 13, '&nbsp;3&egrave;me', 'fr-FR'),
(11, 14, 'Lyc&egrave;e (tous)', 'fr-FR'),
(11, 15, '&nbsp;Seconde', 'fr-FR'),
(11, 16, '&nbsp;Premi&egrave;re', 'fr-FR'),
(11, 17, '&nbsp;Terminale', 'fr-FR'),
(11, 18, 'Autre', 'fr-FR'),
(12, 1, 'Oui', 'fr-FR'),
(12, 2, 'Non', 'fr-FR'),
(13, 1, 'Printemps', 'fr-FR'),
(13, 2, 'Et', 'fr-FR'),
(13, 3, 'Automne', 'fr-FR'),
(13, 4, 'Hiver', 'fr-FR'),
(13, 5, 'Juillet', 'fr-FR'),
(13, 6, 'Ao&ucirc;t', 'fr-FR'),
(13, 7, 'Annuel', 'fr-FR'),
(13, 0, 'Choisir...', 'fr-FR'),
(14, 1, 'Total du s&eacute;jour', 'fr-FR'),
(14, 2, 'Par semaine', 'fr-FR'),
(14, 3, 'Par jour', 'fr-FR'),
(14, 0, 'Choisir...', 'fr-FR'),
(15, 1, 'BAFA', 'fr-FR'),
(15, 2, 'BAFD', 'fr-FR'),
(15, 3, 'BESAPT', 'fr-FR'),
(15, 4, 'BEATEP', 'fr-FR'),
(15, 5, 'BEES', 'fr-FR'),
(15, 6, 'DEFA', 'fr-FR'),
(15, 7, 'BTS GPN', 'fr-FR'),
(15, 9, 'Autre', 'fr-FR'),
(16, 1, 'Public', 'fr-FR'),
(16, 2, 'Priv', 'fr-FR'),
(17, 34, 'Tourisme', 'fr-FR'),
(17, 33, 'Coop&eacute;ration internationale', 'fr-FR'),
(17, 32, 'Sol / Sous sols', 'fr-FR'),
(17, 31, 'Soci&eacute;t&eacute; - g&eacute;n&eacute;ralit&eacute;s', 'fr-FR'),
(17, 30, 'Sant&eacute;', 'fr-FR'),
(17, 29, 'Risques majeurs (naturels et industriels)', 'fr-FR'),
(17, 28, 'Pollution / Nuisances - g&eacute;n&eacute;ralit&eacute;s', 'fr-FR'),
(17, 27, 'P&eacute;dagogie / Education / Formation', 'fr-FR'),
(17, 26, 'Patrimoine culturel', 'fr-FR'),
(17, 25, 'Partenariat', 'fr-FR'),
(17, 24, 'Nature / Patrimoine naturel', 'fr-FR'),
(17, 23, 'Montagne', 'fr-FR'),
(17, 22, 'Mobilit&eacute; / Transport', 'fr-FR'),
(17, 21, 'Mer / Milieu marin', 'fr-FR'),
(17, 20, 'For&ecirc;t', 'fr-FR'),
(17, 19, 'Flore', 'fr-FR'),
(17, 18, 'Faune', 'fr-FR'),
(17, 17, 'Environnement -g&eacute;n&eacute;ralit&eacute;s', 'fr-FR'),
(17, 16, 'Energie', 'fr-FR'),
(17, 15, 'Emploi-m&eacute;tiers', 'fr-FR'),
(17, 14, 'Economie', 'fr-FR'),
(17, 13, 'Eau', 'fr-FR'),
(17, 12, 'Droit de l''environnement', 'fr-FR'),
(17, 11, 'D&eacute;veloppement durable', 'fr-FR'),
(17, 10, 'D&eacute;chets', 'fr-FR'),
(17, 9, 'Consommation', 'fr-FR'),
(17, 8, 'Climat', 'fr-FR'),
(17, 7, 'Citoyennet&eacute;', 'fr-FR'),
(17, 6, 'Bruit', 'fr-FR'),
(17, 5, 'Am&eacute;nagement / Cadre de vie', 'fr-FR'),
(17, 4, 'Alimentation', 'fr-FR'),
(17, 3, 'Air', 'fr-FR'),
(17, 2, 'Agriculture', 'fr-FR'),
(18, 1, 'S&eacute;jours', 'fr-FR'),
(18, 2, 'Chantiers', 'fr-FR'),
(18, 3, 'Classes environnement', 'fr-FR'),
(18, 0, 'Choisir...', 'fr-FR'),
(19, 1, 'Ain', 'fr-FR'),
(19, 2, 'Aisne', 'fr-FR'),
(19, 3, 'Allier', 'fr-FR'),
(19, 4, 'Alpes-de-Haute-Provence', 'fr-FR'),
(19, 5, 'Hautes-Alpes', 'fr-FR'),
(19, 6, 'Alpes-Maritimes', 'fr-FR'),
(19, 7, 'Ard&egrave;che', 'fr-FR'),
(19, 8, 'Ardennes', 'fr-FR'),
(19, 9, 'Ari&egrave;ge', 'fr-FR'),
(19, 10, 'Aube', 'fr-FR'),
(19, 11, 'Aude', 'fr-FR'),
(19, 12, 'Aveyron', 'fr-FR'),
(19, 13, 'Bouches-du-Rh&ocirc;ne', 'fr-FR'),
(19, 14, 'Calvados', 'fr-FR'),
(19, 15, 'Cantal', 'fr-FR'),
(19, 16, 'Charente', 'fr-FR'),
(19, 17, 'Charente-Maritime', 'fr-FR'),
(19, 18, 'Cher', 'fr-FR'),
(19, 19, 'Corr&egrave;ze', 'fr-FR'),
(19, 20, 'Corse', 'fr-FR'),
(19, 21, 'C&ocirc;te-d''Or', 'fr-FR'),
(19, 22, 'C&ocirc;tes-d''Armor', 'fr-FR'),
(19, 23, 'Creuse', 'fr-FR'),
(19, 24, 'Dordogne', 'fr-FR'),
(19, 25, 'Doubs', 'fr-FR'),
(19, 26, 'Dr&ocirc;me', 'fr-FR'),
(19, 27, 'Eure', 'fr-FR'),
(19, 28, 'Eure-et-Loir', 'fr-FR'),
(19, 29, 'Finist&egrave;re', 'fr-FR'),
(19, 30, 'Gard', 'fr-FR'),
(19, 31, 'Haute-Garonne', 'fr-FR'),
(19, 32, 'Gers', 'fr-FR'),
(19, 33, 'Gironde', 'fr-FR'),
(19, 34, 'H&eacute;rault', 'fr-FR'),
(19, 35, 'Ille-et-Vilaine', 'fr-FR'),
(19, 36, 'Indre', 'fr-FR'),
(19, 37, 'Indre-et-Loire', 'fr-FR'),
(19, 38, 'Is&egrave;re', 'fr-FR'),
(19, 39, 'Jura', 'fr-FR'),
(19, 40, 'Landes', 'fr-FR'),
(19, 41, 'Loir-et-Cher', 'fr-FR'),
(19, 42, 'Loire', 'fr-FR'),
(19, 43, 'Haute-Loire', 'fr-FR'),
(19, 44, 'Loire-Atlantique', 'fr-FR'),
(19, 45, 'Loiret', 'fr-FR'),
(19, 46, 'Lot', 'fr-FR'),
(19, 47, 'Lot-et-Garonne', 'fr-FR'),
(19, 48, 'Loz&egrave;re', 'fr-FR'),
(19, 49, 'Maine-et-Loire', 'fr-FR'),
(19, 50, 'Manche', 'fr-FR'),
(19, 51, 'Marne', 'fr-FR'),
(19, 52, 'Haute-Marne', 'fr-FR'),
(19, 53, 'Mayenne', 'fr-FR'),
(19, 54, 'Meurthe-et-Moselle', 'fr-FR'),
(19, 55, 'Meuse', 'fr-FR'),
(19, 56, 'Morbihan', 'fr-FR'),
(19, 57, 'Moselle', 'fr-FR'),
(19, 58, 'Ni&egrave;vre', 'fr-FR'),
(19, 59, 'Nord', 'fr-FR'),
(19, 60, 'Oise', 'fr-FR'),
(19, 61, 'Orne', 'fr-FR'),
(19, 62, 'Pas-de-Calais', 'fr-FR'),
(19, 63, 'Puy-de-D?me', 'fr-FR'),
(19, 64, 'Pyr&eacute;nn&eacute;es-Atlantiques', 'fr-FR'),
(19, 65, 'Hautes-Pyr&eacute;nn&eacute;es', 'fr-FR'),
(19, 66, 'Pyr&eacute;nn&eacute;es-Orientales', 'fr-FR'),
(19, 67, 'Bas-Rhin', 'fr-FR'),
(19, 68, 'Haut-Rhin', 'fr-FR'),
(19, 69, 'Rh&ocirc;ne', 'fr-FR'),
(19, 70, 'Haute-Sa&ocirc;ne', 'fr-FR'),
(19, 71, 'Sa&ocirc;ne-et-Loire', 'fr-FR'),
(19, 72, 'Sarthe', 'fr-FR'),
(19, 73, 'Savoie', 'fr-FR'),
(19, 74, 'Haute-Savoie', 'fr-FR'),
(19, 75, 'Paris', 'fr-FR'),
(19, 76, 'Seine-Maritime', 'fr-FR'),
(19, 77, 'Seine-et-Marne', 'fr-FR'),
(19, 78, 'Yvelines', 'fr-FR'),
(19, 79, 'Deux-S&egrave;vres', 'fr-FR'),
(19, 80, 'Somme', 'fr-FR'),
(19, 81, 'Tarn', 'fr-FR'),
(19, 82, 'Tarn-et-Garonne', 'fr-FR'),
(19, 83, 'Var', 'fr-FR'),
(19, 84, 'Vaucluse', 'fr-FR'),
(19, 85, 'Vend', 'fr-FR'),
(19, 86, 'Vienne', 'fr-FR'),
(19, 87, 'Haute-Vienne', 'fr-FR'),
(19, 88, 'Vosges', 'fr-FR'),
(19, 89, 'Yonne', 'fr-FR'),
(19, 90, 'Territoire-de-Belfort', 'fr-FR'),
(19, 91, 'Essonne', 'fr-FR'),
(19, 92, 'Hauts-de-Seine', 'fr-FR'),
(19, 93, 'Seine-Saint-Denis', 'fr-FR'),
(19, 94, 'Val-de-Marne', 'fr-FR'),
(19, 95, 'Val-d''Oise', 'fr-FR'),
(19, 99, 'Etranger', 'fr-FR'),
(19, 971, 'Guadeloupe', 'fr-FR'),
(19, 972, 'Martinique', 'fr-FR'),
(19, 973, 'Guyane', 'fr-FR'),
(19, 974, 'R&eacute;union', 'fr-FR'),
(19, 975, 'St-Pierre-et-Miquelon', 'fr-FR'),
(19, 976, 'Mayotte', 'fr-FR'),
(19, 980, 'Monaco', 'fr-FR'),
(19, 986, 'Wallis-et-Futuna', 'fr-FR'),
(19, 987, 'Polyn&eacute;sie-Francaise', 'fr-FR'),
(19, 988, 'Nouvelle-Cal&eacute;donie', 'fr-FR'),
(20, 1, 'Publication', 'fr-FR'),
(20, 2, 'Outil p&eacute;dagogique', 'fr-FR'),
(20, 3, 'Revue', 'fr-FR'),
(20, 4, 'Fiche p&eacute;dagogique', 'fr-FR'),
(22, 1, 'tous publics', 'fr-FR'),
(22, 2, 'enfants', 'fr-FR'),
(22, 3, 'scolaires', 'fr-FR'),
(22, 4, 'adultes', 'fr-FR'),
(22, 5, 'jeunes', 'fr-FR'),
(22, 6, '&eacute;tudiants', 'fr-FR'),
(22, 7, 'familles', 'fr-FR'),
(22, 8, 'enseignants / formateurs', 'fr-FR'),
(22, 9, 'animateurs', 'fr-FR'),
(22, 10, 'handicap&eacute;s', 'fr-FR'),
(22, 11, 'seniors', 'fr-FR'),
(22, 12, 'touristes', 'fr-FR'),
(24, 1, 'ludique', 'fr-FR'),
(24, 2, 'p&eacute;dagogique', 'fr-FR'),
(24, 3, 'analyse-r&eacute;flexion', 'fr-FR'),
(24, 4, 'information', 'fr-FR'),
(24, 5, 'technique', 'fr-FR'),
(24, 6, 'sensorielle', 'fr-FR'),
(24, 7, 'autre', 'fr-FR'),
(23, 1, 'de 1 an', 'fr-FR'),
(23, 2, 'de 2 ans', 'fr-FR'),
(23, 3, 'de 3 ans', 'fr-FR'),
(23, 4, 'de 4 ans', 'fr-FR'),
(23, 5, 'de 5 ans', 'fr-FR'),
(23, 6, 'de 6 ans', 'fr-FR'),
(23, 7, 'de 7 ans', 'fr-FR'),
(23, 8, 'de 8 ans', 'fr-FR'),
(23, 9, 'de 9 ans', 'fr-FR'),
(23, 10, 'de 10 ans', 'fr-FR'),
(23, 11, 'de 11 ans', 'fr-FR'),
(23, 12, 'de 12 ans', 'fr-FR'),
(23, 13, 'de 13 ans', 'fr-FR'),
(23, 14, 'de 14 ans', 'fr-FR'),
(23, 15, 'de 15 ans', 'fr-FR'),
(23, 16, 'de 16 ans', 'fr-FR'),
(23, 17, 'de 17 ans', 'fr-FR'),
(23, 18, 'de 18 ans', 'fr-FR'),
(23, 19, 'de 19 ans', 'fr-FR'),
(23, 20, 'de 20 ans', 'fr-FR'),
(23, 21, 'de 21 ans', 'fr-FR'),
(23, 22, 'de 22 ans', 'fr-FR'),
(23, 23, 'de 23 ans', 'fr-FR'),
(23, 24, 'de 24 ans', 'fr-FR'),
(23, 25, 'de 25 ans', 'fr-FR'),
(23, 26, 'de 26 ans', 'fr-FR'),
(23, 27, 'de 27 ans', 'fr-FR'),
(23, 28, 'de 28 ans', 'fr-FR'),
(23, 29, 'de 29 ans', 'fr-FR'),
(23, 30, 'de 30 ans', 'fr-FR'),
(23, 31, 'de 31 ans', 'fr-FR'),
(23, 32, 'de 32 ans', 'fr-FR'),
(23, 33, 'de 33 ans', 'fr-FR'),
(23, 34, 'de 34 ans', 'fr-FR'),
(23, 35, 'de 35 ans', 'fr-FR'),
(23, 36, 'de 36 ans', 'fr-FR'),
(23, 37, 'de 37 ans', 'fr-FR'),
(23, 38, 'de 38 ans', 'fr-FR'),
(23, 39, 'de 39 ans', 'fr-FR'),
(23, 40, 'de 40 ans', 'fr-FR'),
(23, 41, 'de 41 ans', 'fr-FR'),
(23, 42, 'de 42 ans', 'fr-FR'),
(23, 43, 'de 43 ans', 'fr-FR'),
(23, 44, 'de 44 ans', 'fr-FR'),
(23, 45, 'de 45 ans', 'fr-FR'),
(23, 46, 'de 46 ans', 'fr-FR'),
(23, 47, 'de 47 ans', 'fr-FR'),
(23, 48, 'de 48 ans', 'fr-FR'),
(23, 49, 'de 49 ans', 'fr-FR'),
(23, 50, 'de 50 ans', 'fr-FR'),
(23, 51, 'de 51 ans', 'fr-FR'),
(23, 52, 'de 52 ans', 'fr-FR'),
(23, 53, 'de 53 ans', 'fr-FR'),
(23, 54, 'de 54 ans', 'fr-FR'),
(23, 55, 'de 55 ans', 'fr-FR'),
(23, 56, 'de 56 ans', 'fr-FR'),
(23, 57, 'de 57 ans', 'fr-FR'),
(23, 58, 'de 58 ans', 'fr-FR'),
(23, 59, 'de 59 ans', 'fr-FR'),
(23, 60, 'de 60 ans', 'fr-FR'),
(23, 61, 'de 61 ans', 'fr-FR'),
(23, 62, 'de 62 ans', 'fr-FR'),
(23, 63, 'de 63 ans', 'fr-FR'),
(23, 64, 'de 64 ans', 'fr-FR'),
(23, 65, 'de 65 ans', 'fr-FR'),
(23, 66, 'de 66 ans', 'fr-FR'),
(23, 67, 'de 67 ans', 'fr-FR'),
(23, 68, 'de 68 ans', 'fr-FR'),
(23, 69, 'de 69 ans', 'fr-FR'),
(23, 70, 'de 70 ans', 'fr-FR'),
(23, 71, 'de 71 ans', 'fr-FR'),
(23, 72, 'de 72 ans', 'fr-FR'),
(23, 73, 'de 73 ans', 'fr-FR'),
(23, 74, 'de 74 ans', 'fr-FR'),
(23, 75, 'de 75 ans', 'fr-FR'),
(23, 76, 'de 76 ans', 'fr-FR'),
(23, 77, 'de 77 ans', 'fr-FR'),
(23, 78, 'de 78 ans', 'fr-FR'),
(23, 79, 'de 79 ans', 'fr-FR'),
(23, 80, 'de 80 ans', 'fr-FR'),
(23, 81, 'de 81 ans', 'fr-FR'),
(23, 82, 'de 82 ans', 'fr-FR'),
(23, 83, 'de 83 ans', 'fr-FR'),
(23, 84, 'de 84 ans', 'fr-FR'),
(23, 85, 'de 85 ans', 'fr-FR'),
(23, 86, 'de 86 ans', 'fr-FR'),
(23, 87, 'de 87 ans', 'fr-FR'),
(23, 88, 'de 88 ans', 'fr-FR'),
(23, 89, 'de 89 ans', 'fr-FR'),
(23, 90, 'de 90 ans', 'fr-FR'),
(23, 91, 'de 91 ans', 'fr-FR'),
(23, 92, 'de 92 ans', 'fr-FR'),
(23, 93, 'de 93 ans', 'fr-FR'),
(23, 94, 'de 94 ans', 'fr-FR'),
(23, 95, 'de 95 ans', 'fr-FR'),
(23, 96, 'de 96 ans', 'fr-FR'),
(23, 97, 'de 97 ans', 'fr-FR'),
(23, 98, 'de 98 ans', 'fr-FR'),
(23, 99, 'de 99 ans', 'fr-FR'),
(23, 100, 'de 100 ans', 'fr-FR'),
(23, 101, 'de 101 ans', 'fr-FR'),
(23, 102, 'de 102 ans', 'fr-FR'),
(23, 103, 'de 103 ans', 'fr-FR'),
(23, 104, 'de 104 ans', 'fr-FR'),
(23, 105, 'de 105 ans', 'fr-FR'),
(23, 106, 'de 106 ans', 'fr-FR'),
(23, 107, 'de 107 ans', 'fr-FR'),
(23, 108, 'de 108 ans', 'fr-FR'),
(23, 109, 'de 109 ans', 'fr-FR'),
(23, 110, 'de 110 ans', 'fr-FR'),
(23, 111, 'de 111 ans', 'fr-FR'),
(23, 112, 'de 112 ans', 'fr-FR'),
(23, 113, 'de 113 ans', 'fr-FR'),
(23, 114, 'de 114 ans', 'fr-FR'),
(23, 115, 'de 115 ans', 'fr-FR'),
(23, 116, 'de 116 ans', 'fr-FR'),
(23, 117, 'de 117 ans', 'fr-FR'),
(23, 118, 'de 118 ans', 'fr-FR'),
(23, 119, 'de 119 ans', 'fr-FR'),
(23, 120, 'de 120 ans', 'fr-FR'),
(29, 1, '&agrave; 1 an', 'fr-FR'),
(29, 2, '&agrave; 2 ans', 'fr-FR'),
(29, 3, '&agrave; 3 ans', 'fr-FR'),
(29, 4, '&agrave; 4 ans', 'fr-FR'),
(29, 5, '&agrave; 5 ans', 'fr-FR'),
(29, 6, '&agrave; 6 ans', 'fr-FR'),
(29, 7, '&agrave; 7 ans', 'fr-FR'),
(29, 8, '&agrave; 8 ans', 'fr-FR'),
(29, 9, '&agrave; 9 ans', 'fr-FR'),
(29, 10, '&agrave; 10 ans', 'fr-FR'),
(29, 11, '&agrave; 11 ans', 'fr-FR'),
(29, 12, '&agrave; 12 ans', 'fr-FR'),
(29, 13, '&agrave; 13 ans', 'fr-FR'),
(29, 14, '&agrave; 14 ans', 'fr-FR'),
(29, 15, '&agrave; 15 ans', 'fr-FR'),
(29, 16, '&agrave; 16 ans', 'fr-FR'),
(29, 17, '&agrave; 17 ans', 'fr-FR'),
(29, 18, '&agrave; 18 ans', 'fr-FR'),
(29, 19, '&agrave; 19 ans', 'fr-FR'),
(29, 20, '&agrave; 20 ans', 'fr-FR'),
(29, 21, '&agrave; 21 ans', 'fr-FR'),
(29, 22, '&agrave; 22 ans', 'fr-FR'),
(29, 23, '&agrave; 23 ans', 'fr-FR'),
(29, 24, '&agrave; 24 ans', 'fr-FR'),
(29, 25, '&agrave; 25 ans', 'fr-FR'),
(29, 26, '&agrave; 26 ans', 'fr-FR'),
(29, 27, '&agrave; 27 ans', 'fr-FR'),
(29, 28, '&agrave; 28 ans', 'fr-FR'),
(29, 29, '&agrave; 29 ans', 'fr-FR'),
(29, 30, '&agrave; 30 ans', 'fr-FR'),
(29, 31, '&agrave; 31 ans', 'fr-FR'),
(29, 32, '&agrave; 32 ans', 'fr-FR'),
(29, 33, '&agrave; 33 ans', 'fr-FR'),
(29, 34, '&agrave; 34 ans', 'fr-FR'),
(29, 35, '&agrave; 35 ans', 'fr-FR'),
(29, 36, '&agrave; 36 ans', 'fr-FR'),
(29, 37, '&agrave; 37 ans', 'fr-FR'),
(29, 38, '&agrave; 38 ans', 'fr-FR'),
(29, 39, '&agrave; 39 ans', 'fr-FR'),
(29, 40, '&agrave; 40 ans', 'fr-FR'),
(29, 41, '&agrave; 41 ans', 'fr-FR'),
(29, 42, '&agrave; 42 ans', 'fr-FR'),
(29, 43, '&agrave; 43 ans', 'fr-FR'),
(29, 44, '&agrave; 44 ans', 'fr-FR'),
(29, 45, '&agrave; 45 ans', 'fr-FR'),
(29, 46, '&agrave; 46 ans', 'fr-FR'),
(29, 47, '&agrave; 47 ans', 'fr-FR'),
(29, 48, '&agrave; 48 ans', 'fr-FR'),
(29, 49, '&agrave; 49 ans', 'fr-FR'),
(29, 50, '&agrave; 50 ans', 'fr-FR'),
(29, 51, '&agrave; 51 ans', 'fr-FR'),
(29, 52, '&agrave; 52 ans', 'fr-FR'),
(29, 53, '&agrave; 53 ans', 'fr-FR'),
(29, 54, '&agrave; 54 ans', 'fr-FR'),
(29, 55, '&agrave; 55 ans', 'fr-FR'),
(29, 56, '&agrave; 56 ans', 'fr-FR'),
(29, 57, '&agrave; 57 ans', 'fr-FR'),
(29, 58, '&agrave; 58 ans', 'fr-FR'),
(29, 59, '&agrave; 59 ans', 'fr-FR'),
(29, 60, '&agrave; 60 ans', 'fr-FR'),
(29, 61, '&agrave; 61 ans', 'fr-FR'),
(29, 62, '&agrave; 62 ans', 'fr-FR'),
(29, 63, '&agrave; 63 ans', 'fr-FR'),
(29, 64, '&agrave; 64 ans', 'fr-FR'),
(29, 65, '&agrave; 65 ans', 'fr-FR'),
(29, 66, '&agrave; 66 ans', 'fr-FR'),
(29, 67, '&agrave; 67 ans', 'fr-FR'),
(29, 68, '&agrave; 68 ans', 'fr-FR'),
(29, 69, '&agrave; 69 ans', 'fr-FR'),
(29, 70, '&agrave; 70 ans', 'fr-FR'),
(29, 71, '&agrave; 71 ans', 'fr-FR'),
(29, 72, '&agrave; 72 ans', 'fr-FR'),
(29, 73, '&agrave; 73 ans', 'fr-FR'),
(29, 74, '&agrave; 74 ans', 'fr-FR'),
(29, 75, '&agrave; 75 ans', 'fr-FR'),
(29, 76, '&agrave; 76 ans', 'fr-FR'),
(29, 77, '&agrave; 77 ans', 'fr-FR'),
(29, 78, '&agrave; 78 ans', 'fr-FR'),
(29, 79, '&agrave; 79 ans', 'fr-FR'),
(29, 80, '&agrave; 80 ans', 'fr-FR'),
(29, 81, '&agrave; 81 ans', 'fr-FR'),
(29, 82, '&agrave; 82 ans', 'fr-FR'),
(29, 83, '&agrave; 83 ans', 'fr-FR'),
(29, 84, '&agrave; 84 ans', 'fr-FR'),
(29, 85, '&agrave; 85 ans', 'fr-FR'),
(29, 86, '&agrave; 86 ans', 'fr-FR'),
(29, 87, '&agrave; 87 ans', 'fr-FR'),
(29, 88, '&agrave; 88 ans', 'fr-FR'),
(29, 89, '&agrave; 89 ans', 'fr-FR'),
(29, 90, '&agrave; 90 ans', 'fr-FR'),
(29, 91, '&agrave; 91 ans', 'fr-FR'),
(29, 92, '&agrave; 92 ans', 'fr-FR'),
(29, 93, '&agrave; 93 ans', 'fr-FR'),
(29, 94, '&agrave; 94 ans', 'fr-FR'),
(29, 95, '&agrave; 95 ans', 'fr-FR'),
(29, 96, '&agrave; 96 ans', 'fr-FR'),
(29, 97, '&agrave; 97 ans', 'fr-FR'),
(29, 98, '&agrave; 98 ans', 'fr-FR'),
(29, 99, '&agrave; 99 ans', 'fr-FR'),
(29, 100, '&agrave; 100 ans', 'fr-FR'),
(29, 101, '&agrave; 101 ans', 'fr-FR'),
(29, 102, '&agrave; 102 ans', 'fr-FR'),
(29, 103, '&agrave; 103 ans', 'fr-FR'),
(29, 104, '&agrave; 104 ans', 'fr-FR'),
(29, 105, '&agrave; 105 ans', 'fr-FR'),
(29, 106, '&agrave; 106 ans', 'fr-FR'),
(29, 107, '&agrave; 107 ans', 'fr-FR'),
(29, 108, '&agrave; 108 ans', 'fr-FR'),
(29, 109, '&agrave; 109 ans', 'fr-FR'),
(29, 110, '&agrave; 110 ans', 'fr-FR'),
(29, 111, '&agrave; 111 ans', 'fr-FR'),
(29, 112, '&agrave; 112 ans', 'fr-FR'),
(29, 113, '&agrave; 113 ans', 'fr-FR'),
(29, 114, '&agrave; 114 ans', 'fr-FR'),
(29, 115, '&agrave; 115 ans', 'fr-FR'),
(29, 116, '&agrave; 116 ans', 'fr-FR'),
(29, 117, '&agrave; 117 ans', 'fr-FR'),
(29, 118, '&agrave; 118 ans', 'fr-FR'),
(29, 119, '&agrave; 119 ans', 'fr-FR'),
(29, 120, '&agrave; 120 ans', 'fr-FR'),
(26, 1, 'exp&eacute;rience', 'fr-FR'),
(26, 2, 'enqu&ecirc;te', 'fr-FR'),
(26, 3, 'expression', 'fr-FR'),
(26, 4, 'autres', 'fr-FR'),
(27, 1, 'en salle', 'fr-FR'),
(27, 2, 'dehors', 'fr-FR'),
(27, 3, 'sur un site particulier', 'fr-FR'),
(27, 4, 'autres', 'fr-FR'),
(28, 1, 'Les services de l''Etat', 'fr-FR'),
(28, 2, 'Les &eacute;tablissements publics de l''Etat', 'fr-FR'),
(28, 3, 'Les collectivit&eacute;s territoriales et locales', 'fr-FR'),
(28, 4, 'Les chambres consulaires', 'fr-FR'),
(28, 5, 'Les associations', 'fr-FR'),
(28, 6, 'Les entreprises priv&eacute;es', 'fr-FR'),
(28, 7, 'Autre', 'fr-FR'),
(17, 35, 'Art et litt&eacute;rature', 'fr-FR'),
(17, 36, 'Jardin', 'fr-FR'),
(17, 37, 'Ville et environnement urbain', 'fr-FR'),
(17, 38, 'Biodiversit&eacute;', 'fr-FR'),
(17, 39, 'Astronomie, espace', 'fr-FR'),
(17, 1, 'Autre', 'fr-FR');

-- --------------------------------------------------------

-- 
-- Structure de la table `bazar_nature`
-- 

CREATE TABLE `bazar_nature` (
  `bn_id_nature` int(10) unsigned NOT NULL default '0',
  `bn_ce_i18n` varchar(5) NOT NULL default '',
  `bn_label_nature` varchar(255) default NULL,
  `bn_template` text NOT NULL,
  `bn_description` text,
  `bn_condition` text,
  `bn_commentaire` tinyint(4) NOT NULL default '0',
  `bn_appropriation` tinyint(4) NOT NULL default '0',
  `bn_image_titre` varchar(255) NOT NULL default '',
  `bn_image_logo` varchar(255) NOT NULL default '',
  `bn_couleur_calendrier` varchar(255) NOT NULL default '',
  `bn_picto_calendrier` varchar(255) NOT NULL default '',
  `bn_label_class` varchar(255) NOT NULL,
  `bn_ce_id_menu` int(11) NOT NULL default '0',
  PRIMARY KEY  (`bn_id_nature`,`bn_ce_i18n`)
);
-- 
-- Contenu de la table `bazar_nature`
-- 

INSERT INTO `bazar_nature` (`bn_id_nature`, `bn_ce_i18n`, `bn_label_nature`, `bn_template`, `bn_description`, `bn_condition`, `bn_commentaire`, `bn_appropriation`, `bn_image_titre`, `bn_image_logo`, `bn_couleur_calendrier`, `bn_picto_calendrier`, `bn_label_class`, `bn_ce_id_menu`) VALUES 
(1, 'fr-FR', 'Br&egrave;ves', 'checkbox***17***Th&eacute;matiques***160***0***0***0*** ***1***1\r\ntexte***bf_titre***Titre***45***200*** *** *** ***1***0\r\ntextelong***bf_description***Ton texte***40***20*** *** *** ***1***0\r\nlabelhtml*** ***<h4>Indique ici le temps oÃ¹ la br&egrave;ve apparaitra sur le site</h4>*** *** *** *** *** ***0***0\r\nlistedatedeb***bf_date_debut_validite_fiche***Date de d&eacute;but***10***20*** *** *** ***1***0\r\nlistedatefin***bf_date_fin_validite_fiche***Date de fin***10***20*** *** ***  ***1***0\r\nfichier***1***Joindre un fichier (facultatif)***4000000***2000000*** *** *** ***0***0\r\nimage***2***Joindre une image (facultatif)***4000000***2000000*** *** *** ***0***0\r\nurl***1***Saisir le nom et l''adresse d''un site internet (facultatif)***45***200*** *** *** ***0***0\r\n', 'Entre ici des informations courtes sur tes projets ou sur l''environnement.', NULL, 1, 0, '', '', '#FF0000', '', 'breves', 0),
(2, 'fr-FR', 'Petites annonces', 'checkbox***17***Th&eacute;matiques***160***0***0***0*** ***1***1\r\ntexte***bf_titre***Titre***45***200*** *** *** ***1***0\r\ntextelong***bf_description***Texte de l''annonce***40***20*** *** *** ***1***0\r\nlabelhtml*** ***<h4>Indique ici le temps oÃ¹ la petite annonce apparaitra sur le site</h4>*** *** *** *** *** ***0***0\r\nlistedatedeb***bf_date_debut_validite_fiche***Date de d&eacute;but***10***20*** *** *** ***1***0\r\nlistedatefin***bf_date_fin_validite_fiche***Date de fin***10***20*** *** ***  ***1***0\r\nimage***2***Illustre ton annonce par une image (facultatif)***4000000***2000000*** *** *** ***0***0\r\n', 'Passe ici tes petites annonces (recherche, offre, question, demande,...) class&eacute;es par th&eacute;matiques.', NULL, 1, 0, '', '', '#00FF00', '', 'petites_annonces', 0),
(3, 'fr-FR', 'Ev&egrave;nements', 'checkbox***17***Th&eacute;matiques***160***0***0***0*** ***1***1\r\ntexte***bf_titre***Titre***45***200*** *** *** ***1***0\r\ntexte***bf_lieu_evenement***Lieu de l''&eacute;v&egrave;nement***40***60***  *** *** ***0***0\r\ntexte***bf_cp_lieu_evenement***Code postal du lieu de l''&eacute;v&egrave;nement***7***7***  *** *** ***0***0\r\nlistedatedeb***bf_date_debut_evenement***Date de d&eacute;but de l''&eacute;v&egrave;nement***10***20*** *** *** ***0***0\r\nlistedatefin***bf_date_fin_evenement***Date de fin de l''&eacute;v&egrave;nement***10***20***  *** ***  ***0***0\r\ntextelong***bf_description***D&eacute;crit ici l''&eacute;v&egrave;nement***40***20*** *** *** ***1***0\r\nlabelhtml*** ***<h4>Indique ici le temps oÃ¹ l''&eacute;v&egrave;nement apparaitra sur le site</h4>*** *** *** *** *** ***0***0\r\nlistedatedeb***bf_date_debut_validite_fiche***Date de d&eacute;but***10***20*** *** *** ***1***0\r\nlistedatefin***bf_date_fin_validite_fiche***Date de fin***10***20*** *** ***  ***1***0\r\nfichier***1***Joindre un fichier (facultatif)***4000000***2000000*** *** *** ***0***0\r\nimage***2***Joindre une image (facultatif)***4000000***2000000*** *** *** ***0***0\r\nurl***1***Saisir le nom et l''adresse d''un site internet (facultatif)***45***200*** *** *** ***0***0\r\n', 'Ev&egrave;nements, sorties, expo... : propose tes dates !', NULL, 1, 0, '', '', '#0000FF', '', 'evenements', 0),
(4, 'fr-FR', 'Comptes rendus', 'checkbox***17***Th&eacute;matiques***160***1***1*** *** ***1***1\r\ntexte***bf_titre***Titre du compte rendu***45***200*** *** *** ***1***0\r\ntextelong***bf_description***Description***40***20*** *** *** ***1***0\r\nfichier***1***le fichier du compte rendu***400000***2000000*** *** *** ***1***0\r\nimage***2***Joindre une image (facultatif)***4000000***2000000*** *** *** ***0***0\r\nurl***1***Saisir le nom et l''adresse internet du lien (facultatif)***45***200*** *** *** ***0***0\r\n', 'Vos travaux, enquÃªtes, reportages...', NULL, 1, 0, '', '', '', '', 'comptes_rendus', 1),
(12, 'fr-FR', 'Projets', 'checkbox***17***Th&eacute;matiques***160*** *** *** *** ***1***1\r\ntexte***bf_titre***Titre du projet***45***200*** *** *** ***1***1\r\ntextelong***bf_description***Description***40***20*** *** *** ***1***1\r\nliste***12***Est-ce un projet scolaire? ***160***1***0*** *** ***1***1\r\nliste***23***Age minimum***160***1***0*** *** ***1***1\r\nliste***29***Age maximum***160***1***0*** *** ***1***1\r\ntexte***bf_theme***Nom du responsable***40***60***  *** *** ***1***0\r\ntexte***bf_lieu_evenement***Email du responsable***45***200****** *** ***1***0\r\nlistedatedeb***bf_date_debut_validite_fiche***Date de d&eacute;but du projet***10***20*** *** *** ***1***0\r\nlistedatefin***bf_date_fin_validite_fiche***Date de fin du projet***10***20*** *** ***  ***1***0\r\nwikini*** *** *** *** *** *** *** ***0***0\r\nimage***2***Joindre une image***4000000***2000000*** *** *** ***0***0\r\n', 'Inscrit ton projet ici, tu auras acc&egrave;s Ã  un espace internet pour r&eacute;diger tes d&eacute;couvertes tout au long du projet.', NULL, 1, 0, '', '', '', '', 'projets', 2),
(5, 'fr-FR', 'Jeux', 'checkbox***17***Th&eacute;matiques***160***1***1*** *** ***1***1\r\ntexte***bf_titre***Titre***45***200*** *** *** ***1***0\r\ntextelong***bf_description***Description***40***10*** *** *** ***1***0\r\nurl***1***Saisir le nom et l''adresse internet du jeu (facultatif)***45***200*** *** *** ***0***0\r\nfichier***1***S''il existe, joindre le fichier du jeu***200000***200000*** *** *** ***0***0\r\nimage***2***Joindre une image (facultatif)***4000000***2000000*** *** *** ***0***0\r\n', 'Quizz, QCM, devinettes...', NULL, 1, 0, '', '', '', '', 'jeux', 1),
(6, 'fr-FR', 'R&eacute;alisations', 'checkbox***17***Th&eacute;matiques***160***1***1*** *** ***1***1\r\ntexte***bf_titre***Titre***45***200*** *** *** ***1***0\r\ntextelong***bf_description***Description***40***20*** *** *** ***1***0\r\nfichier***1***Joindre le fichier de votre r&eacute;alisation***4000000***2000000*** *** *** ***0***0\r\nimage***2***Joindre une image (facultatif)***4000000***2000000*** *** *** ***0***0\r\n', 'Dessins, photos, vid&eacute;os, animations, textes...', '', 1, 0, '', '', '', '', 'realisations', 1),
(7, 'fr-FR', 'Personnes ressources', 'checkbox***17***Th&eacute;matiques***160***1***1*** *** ***1***1\r\ntexte***bf_titre***Nom*** *** *** *** *** ***1\r\ntexte***bf_prenom_contact***Pr&eacute;nom*** *** *** *** *** ***1\r\ntexte***bf_adresse_contact***Adresse postale***40***5*** *** *** ***0\r\ntexte***bf_mail***Adresse mail***40***50*** *** *** ***1\r\ntexte***bf_telephone***T&eacute;l&eacute;phone*** *** *** *** *** ***0\r\ntextelong***bf_description***Description***40***20*** *** *** ***0\r\nimage***2***Joindre une image (facultatif)***4000000***2000000*** *** *** ***0***0\r\n', 'Les sp&eacute;cialistes Ã  votre &eacute;coute sur une th&eacute;matique environnement.', NULL, 0, 0, '', '', '', '', 'personnes_ressources', 1),
(8, 'fr-FR', 'Site Internet', 'checkbox***17***Th&eacute;matiques***160***1***1*** *** ***1***1\r\ntexte***bf_titre***Titre***45***200*** *** *** ***1***0\r\ntextelong***bf_description***Description***40***20*** *** *** ***1***0\r\nurl***1***Entrez le texte du lien et l''adresse URL***45***200*** *** *** ***1***0\r\nimage***2***Joindre une image (facultatif)***4000000***2000000*** *** *** ***0***0\r\n', 'Proposez des sites ressources utilisables pour vos projets.', NULL, 1, 0, '', '', '', '', 'sites_internet', 1);

-- --------------------------------------------------------

-- 
-- Structure de la table `bazar_template`
-- 

CREATE TABLE `bazar_template` (
  `bt_id_template` int(10) unsigned NOT NULL default '0',
  `bt_id_i18n` varchar(5) NOT NULL default '',
  `bt_nom_template` varchar(255) NOT NULL default '',
  `bt_template` text NOT NULL,
  `bt_documentation` text NOT NULL,
  `bt_categorie_nature` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bt_id_template`,`bt_id_i18n`,`bt_categorie_nature`)
);


-- 
-- Contenu de la table `bazar_template`
-- 

INSERT INTO `bazar_template` (`bt_id_template`, `bt_id_i18n`, `bt_nom_template`,`bt_template`, `bt_documentation`, `bt_categorie_nature`) VALUES 
(1, 'fr-FR', '','echo $formtemplate->toHTML();\r\nif (!isset($_REQUEST[''recherche_effectuee''])) { ?>\r\n<p class="zone_info">Pr&eacute;cisez vos crit&egrave;res de recherche et appuyez sur le bouton "Rechercher" pour consulter les fiches.\r\n</p>\r\n{{Syndication titre="Derni&egrave;res actualit&eacute;s" url="http://ecoformations-lr.com/papyrus.php?menu=11&action=18&categorie_nature=1" nb=10 nouvellefenetre=0 formatdate="jma"}}\r\n<?php }', 'Ce template sert Ã  cr&eacute;er la liste des fiches, les variables disponibles sont :\r\n - $pager (un objet pear Pager) avec $pager->links qui contient la liste des chiffres\r\n - $data qui contient les donnees, tableau multidimentionnel :\r\n  - $data[0][''bf_id_fiche''] contient l identifiant\r\n  - $data[0][''bf_titre''] contient le titre\r\n  - $data[0][''bf_ce_utilisateur''] contient l identifiant de l''utilisateur\r\n\r\n--- \r\npenser Ã  femer le tag php ?> pour mettre du HTML\r\nne pas rÃ©-ouvrir le tag <?php Ã  la fin', 0);
INSERT INTO `bazar_template` VALUES (2, 'fr-FR', '', '?><div class="bazar_numero"><?php echo $pager->links ?></div>\r\n<?php	foreach ($data as $valeur) {\r\n		$GLOBALS[\'_BAZAR_\'][\'url\']->addQueryString(\'id_fiche\', $valeur[\'bf_id_fiche\']) ;\r\n		$GLOBALS[\'_BAZAR_\'][\'url\']->addQueryString(\'action\', BAZ_VOIR_FICHE) ;\r\n		?><li><a href="<?php echo $GLOBALS[\'_BAZAR_\'][\'url\']->getURL() ?> "><?php echo $valeur[\'bf_titre\'] ?></a>\r\n<?php if ($utilisateur->isSuperAdmin() || $GLOBALS[\'id_user\']==$valeur[\'bf_ce_utilisateur\']) {\r\n			$GLOBALS[\'_BAZAR_\'][\'url\']->addQueryString(\'action\', BAZ_ACTION_MODIFIER);\r\n			$GLOBALS[\'_BAZAR_\'][\'url\']->addQueryString(\'typeannonce\', $GLOBALS[\'_BAZAR_\'][\'id_typeannonce\']);\r\n			$GLOBALS[\'_BAZAR_\'][\'url\']->removeQueryString(\'personnes\');\r\n			$GLOBALS[\'_BAZAR_\'][\'url\']->removeQueryString(\'recherche_effectuee\');\r\n			?><a href="<?php echo $GLOBALS[\'_BAZAR_\'][\'url\']->getURL() ?>">( <?php echo BAZ_MODIFIER ; ?>)</a>&nbsp; <?php $GLOBALS[\'_BAZAR_\'][\'url\']->removeQueryString(\'action\');\r\n			$GLOBALS[\'_BAZAR_\'][\'url\']->addQueryString(\'action\', BAZ_ACTION_SUPPRESSION);\r\n?><a href="<?php echo $GLOBALS[\'_BAZAR_\'][\'url\']->getURL() ?>" onclick="javascript:return confirm(\'\'<?php echo BAZ_SUPPRIMER ?>\');">(<?php echo BAZ_SUPPRIMER?>\')</a>\r\n<?php			$GLOBALS[\'_BAZAR_\'][\'url\']->removeQueryString(\'action\');\r\n		}\r\n		?></li><?php\r\n	}\r\n	?></ul><div class="bazar_numero"><?php echo $pager->links ?></div>\r\n', 'Ce template sert Ã  crÃ©er la liste des fiches, les variables disponibles sont :\r\n - $pager (un objet pear Pager) avec $pager->links qui contient la liste des chiffres\r\n - $data qui contient les donnees, tableau multidimentionnel :\r\n  - $data[0][\'bf_id_fiche\'] contient l identifiant\r\n  - $data[0][\'bf_titre\'] contient le titre\r\n  - $data[0][\'bf_ce_utilisateur\'] contient l identifiant de l\'utilisateur\r\n\r\n--- \r\npenser Ã  femer le tag php ?> pour mettre du HTML\r\nne pas rÃ©-ouvrir le tag <?php Ã  la fin', 0);
INSERT INTO `bazar_template` VALUES (3, 'fr-FR', 'Modèle du sujet du mail aux modérateurs pour nouvelle fiche', '[connaiSciences] Une nouvelle fiche à valider', 'Une phrase simple.', 1);
INSERT INTO `bazar_template` VALUES (4, 'fr-FR', 'Modèle du mail pour prevenir les admins d\'une nouvelle fiche (corps)', 'Une nouvelle fiche a été déposé sur connaisciences.fr', 'Le texte du mail.', 1);
INSERT INTO `bazar_template` VALUES (5, 'fr-FR', 'Modèle du message lorsque l\'utilisateur n\'est pas loggué', 'Cette page est r&eacute;serv&eacute;e aux membres institutionels du r&eacute;seau.', 'Un texte pourvant contenir du HTML.', 1);
INSERT INTO `bazar_template` VALUES (6, 'fr-FR', 'Modèle de la page d\'accueil de la carte google', '<h1>Cartographie des &eacute;v&egrave;nements</h1>\r\n<br />\r\n{CARTE}', 'Du HTML, avec la balise {CARTE} qui sera remplacée par la carte.', 0);
INSERT INTO `bazar_template` VALUES (7, 'fr-FR', 'Modèle, de la page saisie formulaire où l\'on choisit le type de fiche à saisir', 'Vous pouvez ici communiquer sur une actualité. Celle-ci sera ensuite diffusée :\r\n<br /><br />\r\n<h3>Sur l\'ensemble du site visible par le grand public :</h3>\r\n<ul>\r\n<li>sur la cartographie montrant les lieux des événements en temps réel</li>\r\n<li>dans les agendas des événements : celui du bandeau visible sur toutes les pages, ainsi que dans la rubrique dédiée -du menu actualités régionales</li>\r\n</ul>\r\n<h3>Dans la lettre d\'actualités périodiquement envoyée par mail :</h3><br />', 'du HTML.', 1);
INSERT INTO `bazar_template` VALUES (8, 'fr-FR', 'Modele du formulaire', '<h2>Proposer des ressources </h2>\r\nSi vous souhaitez valoriser et/ou mutualiser des ressources, nous vous invitons à les intégrer à la base de données régionale. Pour les mettre en ligne, remplissez ce formulaire. <br />\r\n{FORMULAIRE}', 'Sert à ajouter du HTML avant ou après le formulaire.\r\nUtiliser la balise {FORMULAIRE} pour débuter le formulaire', 1);

-- --------------------------------------------------------

-- 
-- Structure de la table `bazar_url`
-- 

CREATE TABLE `bazar_url` (
  `bu_id_url` int(10) unsigned NOT NULL auto_increment,
  `bu_ce_fiche` int(10) unsigned NOT NULL default '0',
  `bu_url` varchar(255) default NULL,
  `bu_descriptif_url` varchar(255) default NULL,
  PRIMARY KEY  (`bu_id_url`)
);

-- --------------------------------------------------------

CREATE TABLE `bazar_wikini` (
  `bw_id_wikini` int(11) NOT NULL default '0',
  `bw_code_alpha_wikini` varchar(255) NOT NULL,
  `bw_bdd_hote` varchar(20) NOT NULL default '',
  `bw_bdd_nom` varchar(20) NOT NULL default '',
  `bw_bdd_utilisateur` varchar(20) NOT NULL default '',
  `bw_bdd_mdp` varchar(20) NOT NULL default '',
  `bw_table_prefix` varchar(255) NOT NULL,
  `bw_page` varchar(20) NOT NULL default '',
  `bw_chemin` varchar (100) NOT NULL,
  PRIMARY KEY  (`bw_id_wikini`)
) ;