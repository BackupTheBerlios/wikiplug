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
// CVS : $Id: baz_langue_fr.inc.php,v 1.5 2009/08/01 17:01:58 mrflos Exp $
/**
* Fichier de traduction en fran�ais de l'application Bazar
*
*@package bazar
//Auteur original :
*@author        Alexandre GRANIER <alexandre@tela-botanica.org>
*@author        Florian Schmitt <florian@ecole-et-nature.org>
//Autres auteurs :
*@author        Aucun
*@copyright     Tela-Botanica 2000-2004
*@version       $Revision: 1.5 $ $Date: 2009/08/01 17:01:58 $
// +------------------------------------------------------------------------------------------------------+
*/

if (file_exists(BAZ_CHEMIN_APPLI.'langues/baz_langue_fr.local.php')) {
	include_once BAZ_CHEMIN_APPLI.'langues/baz_langue_fr.local.php' ;
}

define ('BAZ_FORMULAIRE', 'Formulaires');
define ('BAZ_MODIFIER_FORMULAIRES', 'Gestion des formulaires');
define ('BAZ_INTRO_MODIFIER_FORMULAIRE', 'Pour &eacute;diter un formulaire, cliquez sur son nom ou sur l\'icone avec le crayon.<br />Pour supprimer un formulaire, cliquez sur l\'icone de la corbeille.<br /> Pour cr&eacute;er un nouveau formulaire, cliquez sur le lien en dessous de cette liste.');
define ('BAZ_CONFIRM_SUPPRIMER_FORMULAIRE', 'Attention! Toutes les donn&eacute;es enregistr&eacute;es seront perdues.. Etes-vous s&ucirc;rs de vouloir supprimer ce type de formulaire et toutes ses fiches associ&eacute;es');
define ('BAZ_NOUVEAU_FORMULAIRE', 'Saisir un nouveau type de questionnaire');
define ('BAZ_FORMULAIRE_ET_FICHES_SUPPRIMES', 'Le type de formulaire et ses fiches associ&eacute;es ont bien &eacute;t&eacute; supprim&eacute;s.');
define ('BAZ_NOM_FORMULAIRE','Nom du formulaire'); 	
define ('BAZ_EFFACER', 'Effacer');
define ('BAZ_TEMPLATE','Template');
define ('BAZ_CONDITION','Conditions de saisie');
define ('BAZ_AUTORISER_COMMENTAIRE','Autoriser les commentaires sur les fiches');
define ('BAZ_AUTORISER_APPROPRIATION','Autoriser l\'appropriation des fiches');
define ('BAZ_NOM_CLASSE_CSS','Nom de la classe CSS');
define ('BAZ_NOUVELLE_CATEGORIE_FORMULAIRE', 'Nouvelle cat&eacute;gorie de formulaire');
define ('BAZ_NOUVEAU_FORMULAIRE_ENREGISTRE', 'Le nouveau formulaire a bien &eacute;t&eacute; enregistr&eacute;.');
define ('BAZ_FORMULAIRE_MODIFIE', 'Le formulaire a bien &eacute;t&eacute; modifi&eacute;.');
define ('BAZ_CONFIRM_SUPPRIMER_FICHE', 'Etes vous sur de vouloir supprimer la fiche');
define ('BAZ_FICHE_SUPPRIMEE', 'La fiche a bien &eacute;t&eacute; supprim&eacute;e.');
define ('BAZ_FICHE_MODIFIEE', 'La fiche a bien &eacute;t&eacute; modifi&eacute;e.');
define ('BAZ_FICHE_ENREGISTREE', 'La fiche a bien &eacute;t&eacute; enregistr&eacute;e.');
define ('BAZ_TITREAPPLI','Gestionnaire de fiches (Bazar) ');
define ('BAZ_DESCRIPTIONAPPLI','D&eacute;couvrez Bazar, une application de gestion de fiches class&eacute;es, consultables en ligne ou par flux RSS, grandement personnalisable.');
define ('BAZ_MOTS_CLES','Bazar, fiches, flux, rss, nouveaut&eacute;, information, gestionnaire, xml, syndication, info, application, php, dynamique, mise, aacute; , jour ');
if (!defined ('BAZ_TOUTES_LES_ANNONCES')) define ('BAZ_TOUTES_LES_ANNONCES', 'Consulter les fiches');
define ('BAZ_CONSULTER_FICHES_VALIDEES', 'Consulter les fiches valid&eacute;es');
define ('BAZ_TOUTES_LES_ANNONCES_DE_TYPE', 'Consulter toutes les fiches de type:');
define ('BAZ_TOUS_TYPES_FICHES', 'Tous types de fiches');
if (!defined ('BAZ_TOUS_LES_EMETTEURS')) define ('BAZ_TOUS_LES_EMETTEURS', 'Tous les emetteurs');
define ('BAZ_ENTRER_VOS_CRITERES_DE_RECHERCHE','Pr&eacute;cisez vos crit&egrave;res de recherche et appuyez sur le bouton "Rechercher" pour consulter les fiches.');
define ('BAZ_MODIFIE_RSS','Modifi&eacute;: ' );
define ('BAZ_FLUX_RSS_GENERAL', 'Flux RSS de toutes les fiches');
define ('BAZ_NOM', 'Nom') ;
define ('BAZ_PRENOM', 'Pr&eacute;nom') ;
define ('BAZ_TOUS', 'Tous');
define ('BAZ_TOUTES', 'Toutes');
define ('BAZ_MOT_CLE', 'Mots cl&eacute;s');
if (!defined ('BAZ_EMETTEUR')) define ('BAZ_EMETTEUR', '&Eacute;metteur');
define ('BAZ_NATURE', 'Nature de la fiche' );
define ('BAZ_STATUT', 'Statut' );
define ('BAZ_DATE_CREATION', 'Date de cr&eacute;ation' );
define ('BAZ_DATE_MAJ', 'Date de mise &agrave; jour' );
define ('BAZ_URL_IMAGE', 'Image' );
define ('BAZ_LANGUES_PARLES', 'Langues parl&eacute;s :') ;
define ('BAZ_EMAIL', 'E-mail :') ;
define ('BAZ_MOT_DE_PASSE', 'Mot de passe') ;
define ('BAZ_TITREANNONCE', 'Titre de la fiche') ;
if (!defined ('BAZ_TYPEANNONCE')) define ('BAZ_TYPEANNONCE', 'Type de fiche') ;
define ('BAZ_ANNONCEUR', 'Annonceur') ;
define ('BAZ_REPETE_MOT_DE_PASSE', 'R&eacute;p&eacute;ter le mot de passe :') ;
define ('BAZ_OUI', 'Oui') ;
define ('BAZ_NON', 'Non') ;
define ('BAZ_ANNULER', 'Annuler') ;
define ('BAZ_RETOUR', 'Retour') ;
define ('BAZ_RETABLIR', 'R&eacute;tablir') ;
define ('BAZ_VALIDER', 'Valider') ;
define ('BAZ_PUBLIER', 'Valider la publication') ;
define ('BAZ_ETATPUBLICATION', 'Etat de publication') ;
define ('BAZ_ENCOURSDEVALIDATION', 'En attente de validation');
define ('BAZ_REJETEE', 'Rejet&eacute;e');
define ('BAZ_PUBLIEE', 'Publi&eacute;e') ;
define ('BAZ_PAS_DE_FICHE', '<br />Vous n\'avez pas encore d&eacute;pos&eacute; de fiches.') ;
define ('BAZ_PAS_DE_FICHE_A_VALIDER', 'Pas de fiche &agrave; valider pour l\'instant.');
define ('BAZ_VOS_ANNONCES', 'Vos fiches d&eacute;pos&eacute;es') ;
define ('BAZ_ANNONCES_A_ADMINISTRER', 'Les fiches &agrave; valider') ;
define ('BAZ_MOTS_DE_PASSE_DIFFERENTS', 'Les mots de passe sont diff&eacute;rents !') ;
define ('BAZ_EMAIL_REQUIS', 'Vous devez saisir un email.') ;
define ('BAZ_MOT_DE_PASSE_REQUIS', 'Vous devez saisir un mot de passe.') ;
define ('BAZ_MAIL_INCORRECT', 'L\'email doit avoir une forme correcte, utilisateur@domaine.ext') ;
define ('BAZ_MAIL_DOUBLE', 'Cet email est d&eacute;j&agrave utilis&eacute; par quelqu\'un d\'autre') ;
define ('BAZ_NOTE_REQUIS', 'Indique les champs requis') ;
define ('BAZ_ERREUR_SAISIE', 'Erreur de saisie ') ;
define ('BAZ_VEUILLEZ_CORRIGER', 'Veuillez corriger') ;
define ('BAZ_MODIFIER', 'Modifier') ;
define ('BAZ_MODIFIER_LA_FICHE', 'Modifier la fiche') ;
define ('BAZ_SUPPRIMER', 'Supprimer') ;
define ('BAZ_SELECTION', '--S&eacute;lectionnez ici--');
define ('BAZ_DROITS_PAR_TYPE', 'Droits par type de fiches:');
define ('BAZ_TITRE_SAISIE_ANNONCE', 'Entrer une fiche de type: ');
define ('BAZ_ACCUEIL','Accueil');
define ('BAZ_SORTIRDELAPPLI','Quittez l\'application Bazar');
define ('BAZ_DEPOSE_UNE_NOUVELLE_ANNONCE', 'Saisir une fiche') ;
define ('BAZ_CHOIX_TYPEANNONCE', 'Choisissez le type de fiche que vous souhaitez d&eacute;poser:') ;
if (!defined('BAZ_GESTION_DES_DROITS')) define ('BAZ_GESTION_DES_DROITS', 'Gestion des droits');
define ('BAZ_DESCRIPTION_GESTION_DES_DROITS', 'Veuillez choisir un utilisateur dans la liste d&eacute;roulante ci-dessous pour administrer ses droits.');
define ('BAZ_LABEL_CHOIX_PERSONNE', 'Choix de la personne dans l\'annuaire: ');
define ('BAZ_CONFIRMATION_SUPPRESSION', 'Etes-vous s&ucirc;r de vouloir supprimer cette fiche ?') ;
define ('BAZ_NON_VALIDE', 'Non valide') ;
define ('BAZ_VALIDE', 'Valide') ;
define ('BAZ_VALIDER_LA_FICHE', 'Valider la fiche') ;
define ('BAZ_PRECEDENT', 'Pr&eacute;c&eacute;dent') ;
define ('BAZ_SUIVANT', 'Suivant') ;
define ('BAZ_PAS_DE_FICHE_CRIT', 'Pas de fiche correspondant &agrave; vos crit&egrave;res.') ;
define ('BAZ_TEXTE_IMG_ALTERNATIF', 'Image de la fiche');
define ('BAZ_NUM_FICHE', 'Num&eacute;ro de fiche');
define ('BAZ_ADMIN_ANNONCES', 'Modifier les types de fiches');
define ('BAZ_RECHERCHER_DES_ANNONCES', 'Rechercher des fiches');
define ('BAZ_RECHERCHE_AVANCEE', 'Recherche avanc&eacute;e >>');
define ('BAZ_RECHERCHE_DE_BASE','<< Recherche simple');
define ('BAZ_DESCRIPTION_RECHERCHE', 'En pr&eacute;cisant, ci dessus, le type de fiche cherch&eacute;, vous pourrez obtenir des fonctions de recherche avanc&eacute;.');
define ('BAZ_PAS_D_ANNONCES', 'Pas de fiches trouv&eacute;es.');
if (!defined ('BAZ_S_INSCRIRE_AUX_ANNONCES')) define ('BAZ_S_INSCRIRE_AUX_ANNONCES', 'S\'abonner &agrave; un type de fiche');
define ('BAZ_ABONNE', 'Abonn&eacute;');
define ('BAZ_PAS_ABONNE', 'Pas abonn&eacute;');
define ('BAZ_S_ABONNER', 'S\'abonner');
if (!defined ('BAZ_LAIUS_S_ABONNER')) define ('BAZ_LAIUS_S_ABONNER', 'Il y a deux mani&egrave;res de s\'abonner:<br />- soit en s\'abonnant pour recevoir les fiches par mails<br />- soit par flux RSS');
define ('BAZ_SE_DESABONNER', 'Se d&eacute;sabonner');
define ('BAZ_RSS', 'Flux RSS');
define ('BAZ_DERNIERE_ACTU', 'Derni&egrave;res actualit&eacute;s');
if (!defined ('BAZ_DERNIERES_FICHES')) define ('BAZ_DERNIERES_FICHES', 'Les derni&egrave;res fiches enregistr&eacute;es');
define ('BAZ_A_MODERER',' &agrave; mod&eacute;rer');
define ('BAZ_CONSULTER','Consulter');
define ('BAZ_SAISIR','Saisir');
define ('BAZ_ADMINISTRER','Administrer');
define ('BAZ_FICHE_ECRITE','Fiche &eacute;crite par : ');
define ('BAZ_NB_VUS',', consult&eacute;e ');
define ('BAZ_FOIS', ' fois depuis sa cr&eacute;ation.');
define ('BAZ_LES_COMMENTAIRES', 'Les commentaires sur cette fiche');
define ('BAZ_PAS_DE_COMMENTAIRES', 'Pas de commentaires post&eacute;s pour l\'instant.');
define ('BAZ_IL_Y_A', 'Il y a ');
define ('BAZ_COMMENTAIRE', 'commentaire : ');
define ('BAZ_COMMENTAIRES', 'commentaires : ');
define ('BAZ_ENTREZ_VOTRE_NOM', 'Entrez votre nom : ');
define ('BAZ_ENTREZ_VOTRE_COMMENTAIRE', 'Entrez votre commentaire : ');
define ('BAZ_ENVOYER','Envoyer');
define ('BAZ_NOM_REQUIS', 'Le champs nom ne doit pas rester vide');
define ('BAZ_COMMENTAIRE_REQUIS', 'Le champs commentaire ne doit pas rester vide');
define ('BAZ_LES_STRUCTURES_POSSEDANT_UNE_RESSOURCE', 'Les structures poss�dant cette ressource');
define ('BAZ_FICHES_PAS_VALIDEES','Seulement les fiches pas valid&eacute;es');
define ('BAZ_FICHES_VALIDEES','Seulement les fiches valid&eacute;es');
define ('BAZ_LES_DEUX','Fiches valid&eacute;es et non valid&eacute;es');
define ('BAZ_FICHES_PERIMEES','Seulement les fiches p&eacute;rim&eacute;es');
define ('BAZ_FICHES_PAS_PERIMEES','Seulement les fiches non p&eacute;rim&eacute;es');
define ('BAZ_TOUTES_LES_DATES','Les fiches p&eacute;rim&eacute;es et non p&eacute;rim&eacute;es');
define ('BAZ_DATE','Dates des fiches');
define ('BAZ_VOIR_VOS_ANNONCES', 'Vos fiches');
define ('BAZ_RECHERCHER','Rechercher');
define ('BAZ_SAISIR_UNE_NOUVELLE_FICHE','Saisir une nouvelle fiche');
define ('BAZ_POUR_CHANGER_IMAGE','Pour changer d\'image, il suffit d\'en saisir une autre ci dessous.');
define ('BAZ_CONFIRMATION_SUPPRESSION_LIEN', 'Etes-vous s&ucirc;r de vouloir supprimer ce lien ?') ;
define ('BAZ_CONFIRMATION_SUPPRESSION_FICHIER', 'Etes-vous s&ucirc;r de vouloir supprimer ce fichier ?') ;
define ('BAZ_CONFIRMATION_SUPPRESSION_IMAGE', 'Etes-vous s&ucirc;r de vouloir supprimer cette image ?') ;
define ('BAZ_VALIDER_PUBLICATION', 'Valider la publication');
define ('BAZ_ENTRER_PROJET', 'ENTRER SUR L\'ESPACE DU PROJET');
define ('BAZ_GOOGLE_MSG', '<br />Si l\'&eacute;v&egrave;nement est bien situ&eacute; vous pouvez valider la fiche<br />');
define ('BAZ_SUPPRIMER_LA_FICHE', 'Supprimer la fiche');
define ('BAZ_INVALIDER_LA_FICHE', 'Invalider la fiche');
define ('BAZ_TOUTES_LES_FICHES', 'Toutes les fiches');

define ('BAZ_LATITUDE', 'Latitude');
define ('BAZ_LONGITUDE', 'Longitude');
define ('BAZ_VERIFIER_MON_ADRESSE', 'V&eacute;rifier mon adresse avec la carte');


//================Textes pour les libell�s======================================
define ('BAZ_ANNONCES','annonces');
define ('BAZ_PUBLICATIONS','publications');
define ('BAZ_EVENEMENTS','&eacute;v&egrave;nements');
define ('BAZ_FORMATIONS','formations');
define ('BAZ_SEJOURS','s&eacute;jours');
define ('BAZ_EMPLOIS','emplois');
define ('BAZ_RESS_HUMAINES','Ressources humaines');
define ('BAZ_RESS_DOCS','Ressources documentaires');
define ('BAZ_RESS_PHYSIQUES','Ressources physiques');
define ('BAZ_RESS_FINANCIERES','Ressources financi&egrave;res');
define ('BAZ_JEUX','jeux');
define ('BAZ_PETITES_ANNONCES','petites annonces');
define ('BAZ_BREVES','br&egrave;ves');
define('BAZ_COMPTES_RENDUS','comptes rendus');
define ('BAZ_REALISATION','r&eacute;alisation');
define ('BAZ_PERSONNES_EXPERTES','personnes expertes');
define ('BAZ_THEMATIQUE','th&eacute;matique');
define ('BAZ_THEMATIQUE_REQUIS','la thematique est requise');
define ('BAZ_SITE_INTERNET','site internet');
define ('BAZ_ADRESSE_CONTACT','Adresse du contact');
define ('BAZ_MAIL_CONTACT','Mail du contact');
define ('BAZ_MAIL_CONTACT_REQUIS','Le mail du contact est requis');

//================Textes pour les formations====================================
define ('BAZ_TITRE_FORMATION','Intitul&eacute; de la formation');
define ('BAZ_TITRE_FORMATION_REQUIS','L\'intitul&eacute; de la formation est obligatoire, veuillez le saisir');
define ('BAZ_SI_MODULE','Si plusieurs modules de formation');
define ('BAZ_NUMERO_MODULE','Num&eacute;ro de module');
define ('BAZ_NB_TOTAL_MODULE','Nombre total de modules');
define ('BAZ_TYPE_FORMATION','Type de la formation');
define ('BAZ_TYPE_FORMATION_REQUIS','Le type de la formation est obligatoire, veuillez le saisir');
define ('BAZ_SI_DIPLOMANTE','Si la formation est diplomante');
define ('BAZ_DIPLOME_PREPARE', 'Dipl&ograve;me pr&eacute;par&eacute;');
define ('BAZ_NIVEAU', 'Niveau');
define ('BAZ_SI_QUALIFIANTE', 'Si la formation est qualifiante');
define ('BAZ_QUALIF_PREPAREE', 'Qualification pr&eacute;par&eacute;e');
define ('BAZ_OBJECTIFS', 'Objectifs');
define ('BAZ_OBJECTIFS_REQUIS', 'Entrer les objectifs est obligatoire, veuillez les saisir');
define ('BAZ_CONTENU', 'Contenu');
define ('BAZ_PUBLIC', 'Publics');
define ('BAZ_PUBLIC_REQUIS', 'Publics');
define ('BAZ_CONDITIONS_ACCES', 'Conditions d\'acc&egrave;s');
define ('BAZ_DATE_DEBUT_FORMATION', 'Date de d&eacute;but de la formation');
define ('BAZ_DATE_FIN_FORMATION', 'Date de fin de la formation');
define ('BAZ_DATE_FIN_INSCRIPTION', 'Date limite d\'inscription');
define ('BAZ_CP_LIEU_EVENEMENT', 'Code postal du lieu de l\'&eacute;v&eacute;nement');
define ('BAZ_TARIF_INDIVIDUEL', 'Tarif individuel (en euros)');
define ('BAZ_TARIF_ENTREPRISE', 'Tarif entreprise (en euros)');
define ('BAZ_TARIF_OPCA', 'Tarif OPCA (en euros)');
define ('BAZ_NUM_AGREMENT', 'Num&eacute;ro d\'agr&eacute;ement de la structure');
define ('BAZ_NUM_AGREMENT_REQUIS', 'Le num&eacute;ro d\'agr&eacute;ement de la structure est obligatoire, veuillez le saisir');
define ('BAZ_ANNONCE_REQUIS','Le corps de l\'annonce est requis');
define ('BAZ_PRENOM_CONTACT', 'Pr&eacute;nom');
define ('BAZ_PRENOM_CONTACT_REQUIS', 'Le pr&eacute;nom de la personne contact est obligatoire, veuillez le saisir');
define ('BAZ_NOM_CONTACT', 'Nom');
define ('BAZ_NOM_CONTACT_REQUIS', 'Le nom de la personne contact est obligatoire, veuillez le saisir');
define ('BAZ_MAIL', 'Adresse &eacute;lectronique');
define ('BAZ_TELEPHONE', 'T&eacute;l&eacute;phone');
define ('BAZ_TELEPHONE_REQUIS', 'Le t&eacute;l&eacute;phone du contact est obligatoire, veuillez le saisir');
define ('BAZ_INTERVENANTS', 'Les intervenants');
define ('BAZ_INFOS_COMPLEMENTAIRE', 'Informations compl&eacute;mentaires');
define ('BAZ_COORDONNEES_CONTACT', 'Coordonn&eacute;es de la personne contact<br />');
define ('BAZ_DUREE_DE_PARUTION', '<strong>Dur&eacute;e de parution:</strong> la date de d&eacute;but de parution indique le moment ou la fiche devient visible sur le site, et la date de fin de parution, le moment o&ugrave; elle disparait.<br />');
define ('BAZ_LIGNE_HORIZONTALE', '<hr />');
define ('BAZ_CHAMPS_REQUIS', 'champs requis');
define ('BAZ_PAR', 'par');
define ('BAZ_CHAMPS_INDISPENSABLES_CLASSES', 'Champs indispensables pour les classes<br />');
define ('BAZ_ECRIT_LE',', &eacute;crit le ');
define ('BAZ_FICHES_CORRESPONDANTES', 'fiches correspondantes &agrave; votre recherche') ;
define ('BAZ_FICHE_CORRESPONDANTE', 'fiche correspondante &agrave; votre recherche') ;

//================Le formulaire ================================================
define ('BAZ_AJOUTER_CHAMPS_DE_BASE', 'Ajouter les informations de base pour la fiche');
define ('BAZ_PAYS', 'Pays');
define ('BAZ_PAYS_REQUIS', 'Le champs pays est requis!');
define ('BAZ_ORGANISME', 'Organisme');
define ('BAZ_CONTACT', 'Contact');
define ('BAZ_REGION', 'R&eacute;gion');
define ('BAZ_REGION_REQUIS', 'Le champs r&eacute;gion est requis!');
define ('BAZ_DEPARTEMENT', 'D&eacute;partement') ;
define ('BAZ_DEPARTEMENT_REQUIS', 'Le champs d&eacute;partement est requis!');
define ('BAZ_LICENCE', 'Type de licence') ;
define ('BAZ_LICENCE_REQUIS', 'Le type de licence est requis!') ;
define ('BAZ_TITRE', 'Titre') ;
define ('BAZ_TITRE_REQUIS', 'Un titre de fiche est requis!') ;
define ('BAZ_DESCRIPTION', 'Description') ;
define ('BAZ_DESCRIPTION_REQUIS', 'Une description de la fiche est requise!!') ;
define ('BAZ_DATEDEBVALID', 'D&eacute;but de parution' );
define ('BAZ_DATEDEBVALID_REQUIS', 'La date de d&eacute;but de parution est requise!!') ;
define ('BAZ_DATEFINVALID', 'Fin de parution' );
define ('BAZ_DATEFINVALID_REQUIS', 'La date de fin de parution est requise!!') ;
define ('BAZ_DU', 'du') ;
define ('BAZ_AU', 'au') ;
define ('BAZ_LE', 'Le') ;
define ('BAZ_DATE_DEBUT_EVENEMENT', 'D&eacute;but de l\'&eacute;venement' );
define ('BAZ_DATE_DEBUT_EVENEMENT_REQUIS', 'La date de d&eacute;but de l\'&eacute;venement est requise!!') ;
define ('BAZ_DATE_FIN_EVENEMENT', 'Fin de l\'&eacute;venement' );
define ('BAZ_DATE_FIN_EVENEMENT_REQUIS', 'La date de fin de l\'&eacute;venement est requise!!') ;
define ('BAZ_LIEU_EVENEMENT', 'Lieu de l\'&eacute;venement' );
define ('BAZ_LIEU_EVENEMENT_REQUIS', 'Le lieu de l\'&eacute;venement est requis!!') ;
define ('BAZ_PAS_UNE_IMAGE', 'Ce fichier n\'est pas une image.' );
define ('BAZ_AJOUTER_IMAGE', 'Pour l\'instant, pas d\'image associ&eacute;e &agrave; la fiche, vous pouvez en ajouter une ci-dessous.' );
define ('BAZ_IMAGE', 'Image pour la fiche (facultatif)' );
define ('BAZ_IMAGE_VALIDE_REQUIS', 'Le fichier image n\'est pas valide.') ;

//================Textes pour les parutions=====================================
define ('BAZ_CODE', 'Code ISBN ou ISSN (s\'il existe)' );
define ('BAZ_NOM_AUTEUR', 'Auteur' );
define ('BAZ_NOM_AUTEUR_REQUIS', 'L\'auteur est requis');
define ('BAZ_EDITEUR', 'Editeur');
define ('BAZ_TYPE_PARUTION', 'Type de parution');
define ('BAZ_ANNONCE','Annonce');
define ('BAZ_CAPACITE_ACCUEIL', 'Capacit&eacute; d\'accueil' );
define ('BAZ_CAPACITE_ACCUEIL_REQUIS', 'La capacit&eacute; d\'accueil est requise!!') ;
define ('BAZ_NB_ANIMATEURS', 'Nombre d\'animateurs' );
define ('BAZ_NB_ANIMATEURS_REQUIS', 'Le nombre d\'animateurs est requis!!') ;
define ('BAZ_TARIF', 'Tarif (en euros)' );
define ('BAZ_TARIF_REQUIS', 'Le tarif est requis!!') ;
define ('BAZ_LISTE_TRANCHES_AGES', 'Tranches d\'&agrave;ges' );
define ('BAZ_LISTE_TRANCHES_AGES_REQUIS', 'Les tranches d\'&agrave;ges sont requises!!') ;
define ('BAZ_LISTE_URL', 'Liens associ&eacute;s &agrave; l\'annonce: ');
define ('BAZ_PAS_URL', 'Pour l\'instant, pas de lien associ&eacute; &agrave; la fiche, vous pouvez en ajouter ci-dessous.');
define ('BAZ_AJOUTER_URL', 'Ajouter un lien (URL) &agrave la fiche');
define ('BAZ_LIEN', 'Lien' );
define ('BAZ_LIEN_INTERNET', 'Liens Internet' );
define ('BAZ_URL_LIEN', 'Adresse du lien (URL)' );
define ('BAZ_URL_LIEN_REQUIS', 'L\'adresse  du lien (URL) est requise!!') ;
define ('BAZ_URL_TEXTE', 'Texte du lien' );
define ('BAZ_URL_TEXTE_REQUIS', 'Le texte du lien est requis!!') ;
define ('BAZ_AJOUTER_FICHIER_JOINT','Ajouter un fichier joint &agrave la fiche');
define ('BAZ_FICHIER_JOINT', 'Fichier joint' );
define ('BAZ_LISTE_FICHIERS_JOINTS', 'Fichiers associ&eacute;s &agrave; la fiche ');
define ('BAZ_PAS_DE_FICHIERS_JOINTS', 'Pour l\'instant, pas de fichier associ&eacute; &agrave; la fiche, vous pouvez en ajouter ci-dessous.');
define ('BAZ_FICHIER','Fichier');
define ('BAZ_FICHIER_JOINT_REQUIS', 'Le fichier joint est requis!!') ;
define ('BAZ_FICHIER_DESCRIPTION', 'Description du fichier' );
define ('BAZ_FICHIER_TEXTE_REQUIS', 'Le texte du fichier est requis!!') ;
define ('BAZ_FICHIER_LABEL', 'Label du fichier') ;
define ('BAZ_FICHIER_LABEL_REQUIS', 'Le label du fichier est requis!!') ;
define ('BAZ_FICHIER_EXISTANT', 'Il existe d&eacute;j&agrave; un fichier du m&ecirc;me nom sur le site.<br />Votre fiche a &eacute;t&eacute; associ&eacute;e avec le fichier existant d&eacute;j&agrave;.');
define ('BAZ_ACCEPTE_CONDITIONS', 'J\'accepte les conditions de saisie de la fiche');
define ('BAZ_ACCEPTE_CONDITIONS_REQUIS', 'Vous devez accepter les conditions de saisie de la fiche');

//================Textes pour les emplois=======================================
define ('BAZ_INTITULE_POSTE', 'Intitul&eacute; du poste');
define ('BAZ_INTITULE_POSTE_REQUIS', 'L\'intitul&eacute; du poste est requis');
define ('BAZ_DESCRIPTION_STRUCTURE', 'Description de la structure qui embauche');
define ('BAZ_DESCRIPTION_STRUCTURE_REQUIS', 'La description de la structure qui embauche est requise');
define ('BAZ_CP_LIEU_TRAVAIL', 'Code postal du lieu de travail');
define ('BAZ_LIEU_TRAVAIL', 'Lieu de travail');
define ('BAZ_MISSIONS', 'Missions du poste');
define ('BAZ_PROFIL', 'Profil du poste');
define ('BAZ_PROFIL_REQUIS', 'Le profil du poste est requis');
define ('BAZ_NIVEAU_DIPLOME_DEMANDE', 'Niveau de diplome demand&eacute;');
define ('BAZ_ELEGIBILITE', 'Elegibilit&eacute; (CES, Emploi jeune,...)');
define ('BAZ_TYPE_CONTRAT', 'Type de contrat');
define ('BAZ_TYPE_CONTRAT_REQUIS', 'Type de contrat requis');
define ('BAZ_FORME_CANDIDATURE', 'Moyen de candidature (envois de CV par mail, lettre manucrite,...)');
define ('BAZ_INDICE_SALAIRE', 'Indice salaire');
define ('BAZ_SALAIRE_BRUT_MENSUEL', 'Salaire brut mensuel');
define ('BAZ_SALAIRE_BRUT_MENSUEL_REQUIS', 'Le salaire brut mensuel est requis');
define ('BAZ_ECHEANCE_CANDIDATURE', 'Date d\'&eacute;ch&eacute;ance de candidature');
define ('BAZ_DATE_DEBUT_EMBAUCHE', 'Date de d&eacute;but d\'embauche');

//================Textes pour les ressources=======================================
define ('BAZ_THEME','Th&egrave;me');
define ('BAZ_THEME_REQUIS','Le th&egrave;me est requis');
define ('BAZ_TRANCHES_AGES','Tranches d\'&acirc;ge');
define ('BAZ_TRANCHES_AGES_REQUIS','les tranches d\'&acirc; soTRUCTUREnt requises');
define ('BAZ_PERIODE', 'P&eacute;riode');
define ('BAZ_PERIODE_REQUIS', 'P&eacute;riode requise');
define ('BAZ_NIVEAU_SCOLAIRE', 'Niveau scolaire');
define ('BAZ_DATE_DEBUT_SEJOUR', 'Date de d&eacute;but du s&eacute;jour');
define ('BAZ_DATE_DEBUT_SEJOUR_REQUIS', 'Date de d&eacute;but du s&eacute;jour requise');
define ('BAZ_DATE_FIN_SEJOUR', 'Date de fin du s&eacute;jour');
define ('BAZ_DATE_FIN_SEJOUR_REQUIS', 'Date de fin du s&eacute;jour requise');
define ('BAZ_LIEU', 'Lieu');
define ('BAZ_LIEU_REQUIS', 'Lieu requis');
define ('BAZ_MILIEU_DOMINANT', 'Milieu dominant');
define ('BAZ_HEBERGEMENT', 'H&eacute;bergement');
define ('BAZ_HEBERGEMENT_REQUIS', 'H&eacute;bergement requis');
define ('BAZ_NOMBRE_PLACES', 'Nombre de places');
define ('BAZ_NOMBRE_PLACES_REQUIS', 'Nombre de places requis');
define ('BAZ_NOMBRE_ANIMS', 'Nombre d\'animateurs');
define ('BAZ_NOMBRE_ANIMS_REQUIS', 'Nombre d\'animateurs requis');
define ('BAZ_QUALIF_ANIMS', 'Qualification des animateurs');
define ('BAZ_QUALIF_ANIMS_REQUIS', 'Qualification des animateurs requise');
define ('BAZ_AGREMENTS', 'Agr&eacute;ments');
define ('BAZ_ACTIVITES_DOMINANTES', 'Activit&eacute;s dominantes (en 3 mots cl&eacute;s)');
define ('BAZ_ACTIVITES_DOMINANTES_REQUIS', 'Activit&eacute;s dominantes requises');
define ('BAZ_PRECISION_PRIX', 'Pr&eacute;cision sur le prix');
define ('BAZ_PRECISION_PRIX_REQUIS', 'Pr&eacute;cision sur le prix requise');
define ('BAZ_VOYAGE_COMPRIS', 'Voyage compris');
define ('BAZ_VOYAGE_COMPRIS_REQUIS', 'Voyage compris requis');
define ('BAZ_AIDES_POSSIBLES', 'Aides possibles');
define ('BAZ_CHOISIR', 'Choisir...');
define ('BAZ_CHOISIR_OBLIGATOIRE', 'Il faut choisir une option dans la liste ');
define ('BAZ_INDIFFERENT','Indiff&eacute;rent');

define ('BAZ_COORDONNEES','Coordonn&eacute;es');
define ('BAZ_ANNEE_PARUTION','Ann&eacute;e de parution');
define ('BAZ_LANGUE','Langue');
define ('BAZ_THEMES','Th&ecirc;mes');
define ('BAZ_PAS_D_APPROPRIATION','Aucune structure ne s\'est appropri&eacute; cette ressource pour l\'instant.');
define ('BAZ_STRUCTURE_POSSEDANT', 'structure poss&eacute;dant cette ressource.');
define ('BAZ_STRUCTURES_POSSEDANT', 'structures poss&eacute;dant cette ressource.');
define ('BAZ_SI_POSSEDE_RESSOURCE', 'Si vous poss&egrave;dez cette ressource dans votre structure:');
define ('BAZ_POSSEDE_DEJA_RESSOURCE', 'Vous poss&egrave;dez cette ressource.');
define ('BAZ_CLIQUER_POUR_VOUS_ENLEVER', 'cliquez ici pour vous enlever de la liste.');
define ('BAZ_IL_FAUT_ETRE_STRUCTURE', 'Seules les structures identifi&eacute;es peuvent s\'approprier cette ressource.');
define ('BAZ_IL_FAUT_ETRE_IDENTIFIE_STRUCTURE', 'En vous identifiant ou ou vous inscrivant en tant que structure, vous pouvez vous associer cette ressource.');
define ('BAZ_CLIQUER_POUR_APPARAITRE', 'cliquez ici pour appaitre dans la liste.');
define ('BAZ_NON_PRECISE','Non pr&eacute;cis&eacute;');


define ('BAZ_IDENTIFIEZ_VOUS_POUR_SAISIR', 'Pour saisir des fiches, voir vos fiches ou s\'abonner:<br /> veuillez vous identifier ou vous inscrire.');
define ('BAZ_IDENTIFIEZ_VOUS_POUR_VOIR_VOS_FICHES', 'Pour voir vos fiches, veuillez vous identifier.');
define ('BAZ_EST_SUPERADMINISTRATEUR', 'Cette personne est un super-administrateur.<br />Il peut modifier le droits des utilisateurs et administrer toutes les rubriques de fiches.');
define ('BAZ_CHANGER_SUPERADMINISTRATEUR', 'Changer ses droits de super-administrateur pour en faire un utilisateur sans pouvoir');
define ('BAZ_AUCUN_DROIT', 'utilisateur sans pouvoir');
define ('BAZ_LABEL_REDACTEUR', 'r&eacute;dacteur');
define ('BAZ_DROIT_ADMIN', 'administrateur');
define ('BAZ_PASSER_SUPERADMINISTRATEUR', 'Passer la personne en super administrateur');
define ('BAZ_ENLEVER_DROIT', 'Passer en utilisateur sans pouvoir');
define ('BAZ_TYPE_ANNONCES', 'Types de fiche');
define ('BAZ_DROITS_ACTUELS', 'Droits actuels');
define ('BAZ_PASSER_EN', 'Passer en');
define ('BAZ_OU_PASSER_EN', 'ou passer en');


define ('BAZ_CHECKBOX_SUPERADMIN', 'Super administrateur : ');
define ('BAZ_RADIO_AUCUN', 'aucun &nbsp;');
define ('BAZ_RADIO_REDACTEUR', 'r&eacute;dacteur &nbsp;');
define ('BAZ_RADIO_ADMINISTRATEUR', 'administrateur &nbsp;');

//================Textes pour les sejours==================================
define ('BAZ_TITRE_SEJOUR','Titre du s&eacute;jour');
define ('BAZ_TITRE_SEJOUR_REQUIS','Le titre du s&eacute;jour est obligatoire, veuillez le saisir');
define ('BAZ_MAX_60_CAR', '(maximum 60 caract&eagrave;res)');

// ================ Texte pour les structures =============================
define ('BAZ_FICHE_STRUCTURE', 'Fiche structure') ;
define ('BAZ_NOM_STRUCTURE', 'Nom de la structure');
define ('BAZ_NOM_STRUCTURE_REQUIS', 'Le nom de la structure est requis');
define ('BAZ_RAISON_SOCIALE', 'Raison sociale');
define ('BAZ_RAISON_SOCIALE_REQUIS', 'Raison sociale requise');
define ('BAZ_OBJET', 'Objet');
define ('BAZ_OBJET_REQUIS', 'Objet requis');
define ('BAZ_ACTIONS', 'Actions');
define ('BAZ_PRODUCTIONS', 'Productions');
define ('BAZ_RESEAUX', 'R&eacute;seaux');
define ('BAZ_ADRESSE', 'Adresse');
define ('BAZ_ADRESSE_REQUIS', 'Adresse requise');
define ('BAZ_FAX', 'Fax');
//define ('BAZ_DERNIERES_FICHES', 'Derni&egrave;res fiches enregistr&eacute;es');
define ('BAZ_FICHE_NUMERO', 'Fiche n&deg;');
define ('BAZ_ECRITE', '&nbsp;&eacute;crite par&nbsp;');

//================ Administration de Bazar ================================
define ('BAZ_CONFIG', 'Configuration du bazar');
define ('BAZ_ENREGISTRER_ET_QUITTER', 'Enregistrer et quitter');

//================ Calendrier Bazar =======================================
define ('BAZ_AFFICHE_TITRES_COMPLETS', 'Afficher les titres complets des &eacute;v&eacute;nements');
define ('BAZ_TRONQUER_TITRES', 'Tronquer les titres des &eacute;v&eacute;nements');
define ('BAZ_CALENDRIER','Calendrier');
define ('BAZ_LUNDI','Lundi');
define ('BAZ_MARDI','Mardi');
define ('BAZ_MERCREDI','Mercredi');
define ('BAZ_JEUDI','Jeudi');
define ('BAZ_VENDREDI','Vendredi');
define ('BAZ_SAMEDI','Samedi');
define ('BAZ_DIMANCHE','Dimanche');

define ('BAZ_LUNDI_COURT','Lun');
define ('BAZ_MARDI_COURT','Mar');
define ('BAZ_MERCREDI_COURT','Mer');
define ('BAZ_JEUDI_COURT','Jeu');
define ('BAZ_VENDREDI_COURT','Ven');
define ('BAZ_SAMEDI_COURT','Sam');
define ('BAZ_DIMANCHE_COURT','Dim');

define ('BAZ_JANVIER','Janvier');
define ('BAZ_FEVRIER','F&eacute;vrier');
define ('BAZ_MARS','Mars');
define ('BAZ_AVRIL','Avril');
define ('BAZ_MAI','Mai');
define ('BAZ_JUIN','Juin');
define ('BAZ_JUILLET','Juillet');
define ('BAZ_AOUT','Ao&uacute;t');
define ('BAZ_SEPTEMBRE','Septembre');
define ('BAZ_OCTOBRE','Octobre');
define ('BAZ_NOVEMBRE','Novembre');
define ('BAZ_DECEMBRE','D&eacute;cembre');


/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: baz_langue_fr.inc.php,v $
* Revision 1.5  2009/08/01 17:01:58  mrflos
* nouvelle action bazarcalendrier, correction bug typeannonce, validité html améliorée
*
* Revision 1.4  2008/08/27 17:19:40  mrflos
* correction bug moteur de recherche, ajout des flux rss
*
* Revision 1.3  2008/08/27 13:18:57  mrflos
* maj générale
*
* Revision 1.2  2008/07/29 17:32:25  mrflos
* maj générale
*
* Revision 1.1  2008/07/07 18:00:47  mrflos
* maj carto plus calendrier
*
* Revision 1.2  2008/03/06 00:15:40  mrflos
* correction des bugs bazar, ajout de fichiers d'images
*
* Revision 1.1  2008/02/18 09:12:48  mrflos
* Premiere release de 3 extensions en version alpha (bugs nombreux!) des plugins bazar, e2gallery, et templates
*
* Revision 1.61.2.3  2008-01-29 09:39:39  alexandre_tb
* utilisation d entite
*
* Revision 1.61.2.2  2007-12-04 09:08:06  alexandre_tb
* modification de label
*
* Revision 1.61.2.1  2007-11-21 10:01:22  alexandre_tb
* encodage
*
* Revision 1.61  2007-10-10 13:27:40  alexandre_tb
* correction encodage
*
* Revision 1.60  2007-10-01 12:05:07  alexandre_tb
* orthographe
*
* Revision 1.59  2007-09-28 14:07:01  jp_milcent
* Correction pour le num�ro d'une page.
*
* Revision 1.58  2007-08-28 09:41:47  alexandre_tb
* correction orthographe
*
* Revision 1.57  2007-08-27 12:29:09  alexandre_tb
* quelques constantes rendus modifiable, avec if defined ...
*
* Revision 1.56  2007-07-02 13:46:45  alexandre_tb
* nouveaux labels
*
* Revision 1.55  2007-06-25 12:15:06  alexandre_tb
* merge from narmer
*
* Revision 1.54  2007-06-25 09:57:51  alexandre_tb
* nouveaux labels
*
* Revision 1.53  2007-06-04 15:26:42  alexandre_tb
* nouveaux labels
*
* Revision 1.52  2007/04/20 12:47:42  florian
* correction bugs suite au merge
*
* Revision 1.51  2007/04/20 09:57:21  florian
* correction bugs suite au merge
*
* Revision 1.50  2007/04/11 08:30:12  neiluj
* remise en état du CVS...
*
* Revision 1.42.2.2  2007/03/07 17:20:47  jp_milcent
* Ajout d'une majuscule accentu�e.
*
* Revision 1.42.2.1  2007/01/29 10:54:25  alexandre_tb
* Mise en place de la constante BAZ_DERNIERES_FICHES pour remplacer le label en francais dans baz_liste
*
* Revision 1.42  2006/10/05 08:53:50  florian
* amelioration moteur de recherche, correction de bugs
*
* Revision 1.41  2006/09/21 14:19:39  florian
* amélioration des fonctions liés au wikini
*
* Revision 1.40  2006/07/19 12:50:00  alexandre_tb
* Un accent
*
* Revision 1.39  2006/06/29 10:30:26  florian
* correction de BAZ_MOT_CLE
*
* Revision 1.38  2006/05/19 13:53:45  florian
* stabilisation du moteur de recherche, corrections bugs, lien recherche avancee
*
* Revision 1.37  2006/04/28 12:46:14  florian
* integration des liens vers annuaire
*
* Revision 1.36  2006/03/14 17:10:21  florian
* ajout des fonctions de syndication, changement du moteur de recherche
*
* Revision 1.35  2006/03/02 20:36:52  florian
* les entrees du formulaire de saisir ne sont plus dans les constantes mias dans des tables qui gerent le multilinguisme.
*
* Revision 1.34  2006/03/01 16:23:22  florian
* modifs textes fr et correction bug "undefined index"
*
* Revision 1.33  2006/03/01 16:00:17  florian
* ajout de certains mots par rapport aux formulaires de saisie de ressources
*
* Revision 1.32  2006/02/09 18:00:17  florian
* ajout des trois mots, je sais c'est peu de choses!!
*
* Revision 1.31  2006/01/18 10:53:28  florian
* corrections bugs affichage fiche
*
* Revision 1.30  2006/01/05 16:28:25  alexandre_tb
* prise en chage des checkbox, reste la mise � jour � g�rer
*
* Revision 1.29  2006/01/04 15:31:46  alexandre_tb
* ajout de label
*
* Revision 1.28  2006/01/03 10:19:31  florian
* Mise à jour pour accepter des parametres dans papyrus: faire apparaitre ou non le menu, afficher qu'un type de fiches, définir l'action par défaut...
*
* Revision 1.27  2006/01/02 13:24:23  alexandre_tb
* ajout de label
*
* Revision 1.26  2005/11/30 13:58:45  florian
* ajouts graphisme (logos, boutons), changement structure SQL bazar_fiche
*
* Revision 1.25  2005/11/24 16:17:13  florian
* corrections bugs, ajout des cases à cocher
*
* Revision 1.24  2005/11/08 16:09:26  florian
* modifs séjours
*
* Revision 1.23  2005/11/08 16:06:41  florian
* modifs séjours
*
* Revision 1.22  2005/11/07 17:30:36  florian
* ajout controle sur les listes pour la saisie
*
* Revision 1.21  2005/11/07 17:05:46  florian
* amélioration validation conditions de saisie, ajout des règles spécifiques de saisie des formulaires
*
* Revision 1.20  2005/11/04 17:13:49  florian
* ajout des textes et du formulaire pour les annonces
*
* Revision 1.19  2005/10/31 17:10:56  ddelon
* Reintegration des commits mysterieusement disparus
*
* Revision 1.18  2005/10/24 09:42:21  florian
* mise a jour appropriation
*
* Revision 1.17  2005/10/21 16:15:04  florian
* mise a jour appropriation
*
* Revision 1.13  2005/10/13 14:43:42  florian
* corrections pb accents
*
* Revision 1.12  2005/10/12 16:28:37  florian
* corrections fautes orthographes
*
* Revision 1.11  2005/10/12 16:27:22  florian
* ajout textes des Ressources
*
* Revision 1.10  2005/10/12 16:17:37  florian
* corrections pb accents
*
* Revision 1.9  2005/10/12 16:12:34  florian
* corrections pb accents
*
* Revision 1.8  2005/10/12 15:31:43  florian
* corrections pb accents
*
* Revision 1.7  2005/10/10 16:22:06  alexandre_tb
* Modification de label pour les rendre plus générique
*
* Revision 1.6  2005/09/30 23:04:31  ddelon
* calendrier bazar
*
* Revision 1.5  2005/09/30 12:22:54  florian
* Ajouts commentaires pour fiche, modifications graphiques, maj SQL
*
* Revision 1.3  2005/07/21 19:03:12  florian
* nouveautés bazar: templates fiches, correction de bugs, ...
*
* Revision 1.1.1.1  2005/02/17 18:05:11  florian
* Import initial de Bazar
*
* Revision 1.1.1.1  2005/02/17 11:09:50  florian
* Import initial
*
* Revision 1.1.1.1  2005/02/16 18:06:35  florian
* import de la nouvelle version
*
* Revision 1.6  2004/07/06 16:21:50  florian
* d&eacute;buggage modification + MAJ flux RSS
*
* Revision 1.5  2004/07/05 15:13:53  florian
* changement interface de saisie
*
* Revision 1.4  2004/07/02 14:51:41  florian
* ajouts de labels divers
*
* Revision 1.3  2004/07/01 16:37:14  florian
* ajout de labels
*
* Revision 1.2  2004/07/01 13:00:24  florian
* modif Florian
*
* Revision 1.1  2004/06/23 09:58:32  alex
* version initiale
*
* Revision 1.1  2004/06/18 09:00:46  alex
* version initiale
*
*
* +-- Fin du code ----------------------------------------------------------------------------------------+
*/
?>
