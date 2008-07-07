<?php
/*vim: set expandtab tabstop=4 shiftwidth=4: */
// +------------------------------------------------------------------------------------------------------+
// | PHP version 4.1                                                                                      |
// +------------------------------------------------------------------------------------------------------+
// | Copyright (C) 2004 Tela Botanica (accueil@tela-botanica.org)                                         |
// +------------------------------------------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or                                        |
// | modify it under the terms of the GNU General Public                                                  |
// | License as published by the Free Software Foundation; either                                         |
// | version 2.1 of the License, or (at your option) any later version.                                   |
// |                                                                                                      |
// | This library is distributed in the hope that it will be useful,                                      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of                                       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU                                    |
// | General Public License for more details.                                                             |
// |                                                                                                      |
// | You should have received a copy of the GNU General Public                                            |
// | License along with this library; if not, write to the Free Software                                  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                            |
// +------------------------------------------------------------------------------------------------------+
// CVS : $Id: gestion_wikini.class.php,v 1.1 2008/07/07 18:00:39 mrflos Exp $
/**
* Application projet
*
* La classe gestion_wikini
*
*@package projet
//Auteur original :
*@author        Alexandre Granier <alexandre@tela-botanica.org>
//Autres auteurs :
*@author        Aucun
*@copyright     Tela-Botanica 2000-2004
*@version       $Revision: 1.1 $
// +------------------------------------------------------------------------------------------------------+
*/


// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+


/**
 * class gestion_wikini
 *
 */
class gestion_wikini
{

     /*** Attributes: ***/

    /**
     * Un objet PEAR::DB
     * @access private
     */
    var $_db;

    /**
     * Constructeur
     *
     * @param DB objetDB Une r��ence vers un objet PEAR:DB
     * @return void
     * @access public
     */
    function gestion_wikini( &$objetDB )
    {
        $this->_db = $objetDB ;
    } // end of member function gestion_wikini

    /**
     *
     *
     * @param string prefixe Le pr�ixe des tables.
     * @return void
     * @access public
     */
    function creation_tables( $prefixe, $root_page = 'PagePrincipale' )
    {
            // Connection �la base de donn� de wikini
            $prefixe .= '_' ;            
        $this->_db->query(
                "CREATE TABLE `".$prefixe."pages` (".
                    "id int(10) unsigned NOT NULL auto_increment,".
                    "tag varchar(50) NOT NULL default '',".
                    "time datetime NOT NULL default '0000-00-00 00:00:00',".
                    "body text NOT NULL,".
                    "body_r text NOT NULL,".
                    "owner varchar(50) NOT NULL default '',".
                    "user varchar(50) NOT NULL default '',".
                    "latest enum('Y','N') NOT NULL default 'N',".
                    "handler varchar(30) NOT NULL default 'page',".
                    "comment_on varchar(50) NOT NULL default '',".
                    "PRIMARY KEY  (id),".
                    "FULLTEXT KEY tag (tag,body),".
                    "KEY idx_tag (tag),".
                    "KEY idx_time (time),".
                    "KEY idx_latest (latest),".
                    "KEY idx_comment_on (comment_on)".
                    ") TYPE=MyISAM;");
                    
        $this->_db->query(
            "CREATE TABLE `".$prefixe."acls` (".
                "page_tag varchar(50) NOT NULL default '',".
                "privilege varchar(20) NOT NULL default '',".
                "list text NOT NULL,".
                "PRIMARY KEY  (page_tag,privilege)".
                ") TYPE=MyISAM");
        $this->_db->query(
            "CREATE TABLE `".$prefixe."links` (".
                "from_tag char(50) NOT NULL default '',".
                "to_tag char(50) NOT NULL default '',".
                "UNIQUE KEY from_tag (from_tag,to_tag),".
                "KEY idx_from (from_tag),".
                "KEY idx_to (to_tag)".
                ") TYPE=MyISAM");
                
        $this->_db->query(
            "CREATE TABLE `".$prefixe."referrers` (".
                "page_tag char(50) NOT NULL default '',".
                "referrer char(150) NOT NULL default '',".
                "time datetime NOT NULL default '0000-00-00 00:00:00',".
                "KEY idx_page_tag (page_tag),".
                "KEY idx_time (time)".
                ") TYPE=MyISAM");
        $this->_db->query(
            "CREATE TABLE `".$prefixe."users` (".
                "name varchar(80) NOT NULL default '',".
                "password varchar(32) NOT NULL default '',".
                "email varchar(50) NOT NULL default '',".
                "motto text NOT NULL,".
                "revisioncount int(10) unsigned NOT NULL default '20',".
                "changescount int(10) unsigned NOT NULL default '50',".
                "doubleclickedit enum('Y','N') NOT NULL default 'Y',".
                "signuptime datetime NOT NULL default '0000-00-00 00:00:00',".
                "show_comments enum('Y','N') NOT NULL default 'N',".
                "PRIMARY KEY  (name),".
                "KEY idx_name (name),".
                "KEY idx_signuptime (signuptime)".
                ") TYPE=MyISAM");
       $this->_db->query("INSERT INTO `".$prefixe."pages` (`id`, `tag`, `time`, `body`, `body_r`, `owner`, `user`, `latest`, `handler`, `comment_on`) VALUES (56, 'PageMemo', '2006-09-21 10:38:12', '[[AideWikiNi Retour]]\n\n======M�mo d''utilisation du site Internet Wikini======\n\n=====Pr�ambule, en r�gle g�n�ral :=====\n\n	- N''oubliez pas de vous **identifier** pour avoir acc�s � toutes les pages qui vous concernent.\n\n	- Veillez � ne pas effacer le contenu d''une page mis en ligne pr�c�demment. \nEn mode \" �dition \" une page n''est pas forc�ment tr�s lisible, **prenez le temps de vous habituer � la syntaxe, � trouver o� vous voulez �crire.**\n\n=====Ecrire sur une page, enregistrer ses modifications ou faire un aper�u de la page :=====\n\n	- Pour �crire sur une page, il vous suffit de **double cliquer sur la zone d''�criture** ou de cliquer en bas de la page sur le lien : **\" Editer cette page \"**\nAttention, lorsque vous avez termin� d�crire, n''oubliez pas de **sauvegarder vos modifications** en bas de la page en cliquant sur le bouton **Aper�u** puis sur **Sauver**\n\n=====Pour Mettre en page :=====\n\n	- Pour mettre en page vos textes vous pouvez **utiliser la barre d''outil** situ�e en haut de la page quand vous �tes en mode �dition.\n\n	- Sinon, vous pouvez utiliser des codes pr� configur�s � partir de **symboles de syntaxe.**\nDans tous les cas il faudra utiliser deux symboles juste avant la premi�re lettre � mettre en page et deux autres juste apr�s la derni�re lettre � mettre en page.\nPar exemple, si vous tapez : \"\"**Le COCM est une association r�gie sous la loi 1901**\"\"\nA l''�cran le texte appara�tra ainsi : **Le COCM est une **association** r�gie sous la loi 1901**\n\n        - **Guide des r�gles de formatage**\n__R�gles de base :__\n\n-       \"\"##texte � espacement fixe## --->\"\" ##texte � espacement fixe##\n\n-	Mettre en gras : \"\"**Texte en gras !** --->\"\" Texte en gras !\n\n-	Souligner : \"\"Texte __soulign�__ ! --->\"\" __Texte soulign� !__ (2 fois le symbole du tiret bas, touche 8 du clavier)\n\n-	Barrer un texte :  \"\"@@Texte barr�@@ ! --->\"\" @@Texte barr� !@@ (deux arobaz)\n\n-	Mettre en Italique :  \"\"//Texte en italique.// --->\"\" //Texte en italique.// (deux slash)\n\n-       \"\"======Grand titre (T1):======(6 signes \" �gal \") -->\"\" ======Grand titre (T1):======\n\n-	\"\"=====Grand titre (T2) :===== -->\"\"  =====Grand titre (T2) :===== (5 signes \" �gal \")\n\n-	\"\"====Grand titre (T3) :==== -->\"\"  ====Grand titre (T3) :==== (4 signes \" �gal \")\n\n-	S�parateur horizontal : il suffit de taper au moins 4 \"-\", au dela de 4 tirets c''est le meme effet :  \"\"----\"\" (4 tiret, touche 6 du clavier)\nExemple : ----\n\nRetour de ligne forc� :   \"\" ---\"\" \nExemple : \"\"le--- wikini --->\"\"le --- wikini\n\n=====Faire un lien vers un autre site ou une autre page du site :=====\n\n	- **Pour faire un lien vers un autre site**, il suffit d''**�crire l''adresse du site enti�re** sur la page, le wikini reconna�t automatique que c''est un lien. Rappel, une adresse compl�te commence par [[http://]]\n\n        - **Si vous souhaitez q''un autre texte apparaisse**, vous devrez �crire l''adresse du lien entre des crochets avec le texte que vous voulez. \nPar exemple : \"\"[[http://cocmathl�tisme.org Visitez le site du club de Mauguio !]]\"\" le texte que le public verra sera : [[http://cocmathl�tisme.org Visitez le site du club de Mauguio !]]\n\n         - **Pour faire un lien d''une page du site vers une autre page du m�me site**, pas besoin de mettre toute l''adresse, il vous suffit d''indiquer le dernier mot du chemin (celui avec les deux majuscules) Par exemple pour faire un lien vers cette page, on utilisera seulement le mot �crit en bleu : http://www.cocmathletisme.org/wikini/wakka.php?wiki=AdministraTeurs\nVous pourrez �galement utiliser un autre texte grâce aux crochets comme expliqu� plus haut. (rappel : \"\"[[ParametresUtilisateur Identification ]]\"\" donnera aux visiteurs : [[ParametresUtilisateur Identification ]]\n\n=====Cr�er une nouvelle page :=====\n\n        - **Une nouvelle page se cr�e** en �crivant un mot qui contient 2 majuscules **CommeCeci**.\nLorsque vous sauvez vos modifications, le mot appara�t � l''�cran avec une majuscule en bout de mot : **CommeCeci?** En cliquant sur le point d''interrogation, une nouvelle page blanche appara�t, en y ins�rant du texte et en sauvant, cette page devient active.\n\n        - **Conseil** : Lorsque vous cr�er la page, il est pr�f�rable d''**en informer l''administrateur principal** afin qu''il puisse g�rer les droits d''�criture et de lecture de la page (notamment si cette page est cr��e dans la rubrique des administrateurs)\n\n=====Mettre une image dans le corps d''une page :=====\n\n1) **dimensionner votre photo** de mani�re � ce qu''elle ne soit pas trop grande (rester sur un format 10X15 cm maxi), v�rifier que votre photo ne soit pas trop lourde sinon, elle n''appara�tra pas. Configurer par exemple **la r�solution** qui, pour �tre sur le Web, doit �tre de 72 dpi.\n\n2) Quant vous �tes en train de modifier la page, il faut utiliser la barre d''outil en haut de la page, sur la 2�me ligne de la barre, il y a un ic�ne image, avant de cliquez dessus, vous allez informer les cases suivantes : **fichier, description et alignement**\n\n3) **Fichier** : indiquer un nom de fichier et surtout son **extension** (.doc,.jpg,.img ,.gif....) par exemple pour le logo de cocm vous pourrez indiquer **logo.jpg**\n\n4) **Description** : ici vous d�crivez la photo par exemple : Le logo du cocm\n\n5) **Alignement** : indiquer si vous souhaitez que l''image soit centr�e, � gauche ou � droite de l''�cran.\n\n6) **Cliquez enfin sur le bouton image** de la barre d''outil puis **sauver vos modifications**, vous verrez appara�tre � c�t� de la description de votre ficher un point d''interrogation : le logo du  cocm?\n\n7/ Cliquez sur le point d''interrogation, le site vous propose de **transf�rer votre image** via le formulaire d''envois de fichier cliquez sur \" parcourir \". **Pointez l''image** et cliquez sur **envoyer**.\n\n=====Mettre un fichier en t�l�chargement sur le site :=====\n\n	- **Idem que pour mettre une image**, suivez les instructions du point 2/ � 7/\nRappel : ne vous trompez pas sur l''extension de votre fichier :\nQuelques extensions possibles : \nFichier Word : .doc\nPower point : .pps ou . ppt\nExcel : .xls\n\n**Attention, lorsque un fichier est trop lourd, le serveur vous envoie un message d''erreur. Veillez � ne pas trop surcharger le site, en effet, l''espace d''un site n''est pas extensible.**', '', '', 'webmaster', 'Y', 'page', ''),
(2, 'DerniersChangements', '2006-09-20 12:18:59', '** Retour : ** [[TableauDeBord Tableau de bord]]\n----\n{{RecentChanges}}', '', '', 'webmaster', 'Y', 'page', ''),
(3, 'DerniersCommentaires', '2006-09-20 12:18:59', '** Retour : ** [[TableauDeBord Tableau de bord]]\n----\n{{RecentlyCommented}}', '', '', 'webmaster', 'Y', 'page', ''),
(5, 'PagesACreer', '2006-09-20 12:18:59', '** Retour : ** [[TableauDeBord Tableau de bord]]\n----\n=== Liste des pages &agrave; cr&eacute;er : ===\n\n{{WantedPages}}', '', '', 'webmaster', 'Y', 'page', ''),
(6, 'PagesOrphelines', '2006-09-20 12:18:59', '** Retour : ** [[TableauDeBord Tableau de bord]]\n----\n=== Liste des pages orphelines ===\n\n{{OrphanedPages}}', '', '', 'webmaster', 'Y', 'page', ''),
(7, 'RechercheTexte', '2006-09-20 12:18:59', '** Retour : ** [[PagePrincipale Page principale]] > [[TableauDeBord Tableau de bord]]\n----\n{{TextSearch}}', '', '', 'webmaster', 'Y', 'page', ''),
(9, 'ListeUtilisateurs', '2006-09-20 12:18:59', '** Retour : ** [[TableauDeBord Tableau de bord]]\n----\n=== Liste des utilisateurs ===\n\n... du premier au dernier.\n\n{{Listusers}}', '', '', 'webmaster', 'Y', 'page', ''),
(10, 'ListeUtilisateursInverse', '2006-09-20 12:18:59', '** Retour : ** [[TableauDeBord Tableau de bord]]\n----\n=== Liste des utilisateurs ===\n\n... du dernier au premier.\n\n{{Listusers/last}}', '', '', 'webmaster', 'Y', 'page', ''),
(11, 'PlanDuSite', '2006-09-20 12:18:59', '**Retour : ** [[TableauDeBord tableau de bord]]\n----\n=== Plan du site : ===\n\n{{ListPages/tree}}', '', '', 'webmaster', 'Y', 'page', ''),
(12, 'IndexDesPages', '2006-09-20 12:18:59', '**Retour : ** [[TableauDeBord tableau de bord]]\n----\n=== Liste des pages : ===\n\n{{ListPages}}', '', '', 'webmaster', 'Y', 'page', ''),
(13, 'IndexAlphabetiqueDesPages', '2006-09-20 12:18:59', '**Retour : ** [[TableauDeBord tableau de bord]]\n----\n=== Liste des pages par ordre alphab�tique : ===\n\n{{pageindex}}', '', '', 'webmaster', 'Y', 'page', ''),
(14, 'DerniersChangementsRSS', '2006-09-20 12:18:59', '**Retour : ** [[TableauDeBord tableau de bord]]\n----\nCette page renvoie au fils RSS des derniers changement. Pour savoir comment l''utiliser voir la page \"\"<a href=\"http://www.wikini.net/wakka.php?wiki=WikiniEtLesFluxRSS\" target=\"_blank\" title=\"Wikini et les flux RSS\">Wikini et les flux RSS</a>\"\".\n\n\"\"<!--\n\n{{recentchangesrss/link=\"DerniersChangements\"}}\n\n-->\"\"', '', '', 'webmaster', 'Y', 'page', ''),
(15, 'BacASable', '2006-09-20 12:18:59', '** Retour : ** [[PagePrincipale Page pincipale]]\n----\nUtilisez cette page pour faire vos tests !', '', '', 'webmaster', 'Y', 'page', ''),
(16, 'TableauDeBord', '2006-09-20 12:18:59', '** Retour : ** [[PagePrincipale Page Principale]]\n----\n===== Tableau de bord =====\n\n	- Listes des utilisateurs : [[ListeUtilisateurs par ordre de cr�ation ]] ou [[ListeUtilisateursInverse par ordre inverse de cr�ation ]].\n\n	- [[DerniersChangements Derni�res modifications sur les pages]]\n	- [[DerniersCommentaires Derni�res modifications sur les commentaires]]\n\n\n	- [[PagesOrphelines Pages orphelines]]\n	- [[PagesACreer Pages � cr�er]]\n\n	- [[RechercheTexte Recherche texte]]\n\n	- [[PlanDuSite Plan du site]]\n	- [[IndexDesPages Index des pages avec noms des propri�taires]]\n	- [[IndexAlphabetiqueDesPages Index des pages par classement alphab�tique]]\n\n	- [[DerniersChangementsRSS La page permettant le flux RSS]]\n\n----\n==== 5 derniers comptes utilisateurs ====\n{{Listusers last=\"5\"}}\n\n==== 5 derni�res pages modifi�es ====\n{{recentchanges max=\"5\"}}\n----', '', '', 'webmaster', 'Y', 'page', ''),
(17, 'NomWiki', '2006-09-20 12:18:59', '** Retour : ** [[PagePrincipale Page Principale]]\n----\nUn NomWiki est un nom qui est &eacute;crit \"\"CommeCela\"\".\n\nUn NomWiki est transform� automatiquement en lien. Si la page correspondante n''existe pas, un ''?'' est affich� � c�t� du mot.', '', '', 'webmaster', 'Y', 'page', ''),
(47, 'ParametresUtilisateur', '2006-09-20 17:52:04', '** Retour : ** [[PagePrincipale page principale]]\n----\n** Note : ** L''id&eacute;al pour la cr&eacute;ation d''un nom Wiki est d''accoler son pr�nom et son nom de cette fa�on : \"\"\"PrenomNom\"\"\". \n----\n==== Mes param�tres ====\n\n{{UserSettings}}\n\n----', '', '', 'webmaster', 'Y', 'page', ''),
(50, 'ListeDesActionsWikini', '2006-09-20 17:58:42', 'Wikini propose par d&eacute;faut les \"Actions\" suivantes.\n\n  - \"\"ActionBacklinks\"\" ins&eacute;re la liste des pages faisant r�f�rence � la page courante\n  - \"\"ActionInclude\"\" : inclusion d''une page au sein d''une autre\n  - \"\"ActionListPages\"\" : liste la totalit� des pages du site (param�tres possibles)\n  - \"\"ActionListUsers\"\" : liste la totalit� des pages du site (tri /last).\n  - \"\"ActionMyChanges\"\" : donne les derni�res pages modifi�es par l''utilisateur courant (force son identification)\n  - \"\"ActionMyPages\"\" : donne la liste des pages dont l''utilisateur courant est le propri�taire\n  - \"\"ActionOrphanedPages\"\" : recherche les pages qui ne sont pas cibles de liens (pages orphelines)\n  - \"\"ActionPageIndex\"\" : liste toutes les pages class�es par ordre alphab�tique et regroup�es par lettres.\n  - \"\"ActionRecentChanges\"\" : affiche la liste des pages r�cemment modifi�es (voir DerniersChangements )\n  - \"\"ActionRecentChangesRSS\"\" : automatise la diffusion pr RSS (voir la page DerniersChangementsRss ) \n  - \"\"ActionRecentComments\"\" : donne la liste des pages comment�es tri�es par date anti-chronologique (cf. ci-dessous) \n  - \"\"ActionRecentlyCommented\"\" : affiche une liste (born�e par max= ) des pages ayant �t� r�cemment comment�es (voir DerniersCommentaires ).\n  - \"\"ActionRedirect\"\" : redirection d''une page vers une autre\n  - \"\"ActionResetPassword\"\" : r�-initialise le mot de passe de l''utilisateur (utilis� dans ParametresUtilisateur ) \n  - \"\"ActionTextSearch\"\" : recherche d''une cha�ne de caract�res dans l''ensemble des pages de Wikini\n  - \"\"ActionTrail\"\" : permet de lier des pages entres elles et de passer de l''une � l''autre avec un petit navigateur style \"pr�c�dente/suivante\"\n  - \"\"ActionUserSettings\"\" : cr�ation de compte et connexion (voir ParametresUtilisateur ).\n  - \"\"ActionWantedPages\"\" : liste les pages de Mot Wiki devant �tre �crites (voir PagesACreer ).\n.', '', '', 'webmaster', 'Y', 'page', ''),
(51, 'ControlerLAccesAuxPages', '2006-09-20 18:03:17', 'Chaque page poss&egrave;de trois niveaux de contr&ocirc;le d''acc&eacute;s :\n - lecture de la page\n - �criture/modification de la page\n - commentaire de la page\n\nLes contr�les d''acc�s ne peuvent �tre modifi�s que par le propri�taire de la page -- l''administrateur technique peut aussi manuellement modifier ces contr�les en travaillant directement sur la base de donn�es.\nLe propri�taire d''une page voit appara�tre, dans la page dont il est propri�taire, l''option \"Éditer permissions\" : cette option lui permet de modifier les contr�les d''acc�s.\nCes contr�les sont mat�rialis�s par des colonnes o� le propri�taire va ajouter ou supprimer des informations.\nLe propri�taire peut compl�ter ces colonnes par les informations suivantes, s�par�es par des espaces :\n - le nom d''un ou plusieurs utilisateurs : par exemple \"\"CharlesNepote\"\" ou \"\"DavidDelon\"\"\n - le caract�re ***** d�signant tous les utilisateurs\n - le caract�re **+** d�signant les utilisateurs enregistr�s\n - le caract�re **!** signifiant la n�gation : par exemple !\"\"CharlesNepote\"\" signifie que \"\"CharlesNepote\"\" **ne doit pas** avoir acc�s � cette page\n\n===== Droits d''acc�s par d�faut =====\nPour toute nouvelle page cr��e, [[http://www.wikini.net WikiNi]] applique des droits d''acc�s par d�faut. Ces droits d''acc�s sont configurables via le fichier /wakka.config.php.\nIl faut renseigner les trois variables de configuration suivantes :\n##\n	\"default_write_acl\" => \"*\",\n	\"default_read_acl\" => \"*\",\n	\"default_comment_acl\" => \"*\",\n##\n\nPar exemple, vous pouvez souhaiter que, par d�faut, seuls les utilisateurs enregistr�s puisse modifier des pages. Vous utiliserez alors :\n##\n	\"default_write_acl\" => \"+\",\n	\"default_read_acl\" => \"*\",\n	\"default_comment_acl\" => \"*\",\n##', '', '', 'webmaster', 'Y', 'page', ''),
(61, '". $root_page ."', '2006-09-21 10:53:19', '======Bienvenue sur L''espace de r&eacute;daction collaboratif !======\n\n----En tant que participant, vous pouvez **cr&eacute;er vos propres pages** ----\n\n=====Quelques pistes pour utiliser le wiki :=====\n\n  - Double-cliquez sur la page pour r&eacute;diger.\n  - Pour apprendre &agrave; r&eacute;diger, mettre des photos, des liens sur l''espace projet, cliquez [[http://www.wikini.net/wakka.php?wiki=DocumentationUtilisateur ici]] ', '', '', 'webmaster', 'Y', 'page', ''),
(64, 'ReglesDeFormatage', '2006-09-21 12:59:40', '[[AccueiL Retour]]\n\n====== Guide des r&egrave;gles de formatage ======\n\nLes r&egrave;gles de formatage avec Wakka diff�rent l�g�rement des autres Wikis. (Voir par exemple [[http://c2.com/cgi/wiki?TextFormattingRules les r�gles de formatage de WikiWikiWeb]], le premier Wiki connu.)\nTout texte plac� entre deux guillemets doubles - \" - est pr�sent� tel que.\n\nVous pouvez effectuer vos propres tests dans le BacASable : c''est un endroit fait pour �a.\n\n=== R�gles de base : ===\n	\"\"**Texte en gras !** -----\"\"> **Texte en gras !**\n	\"\"//Texte en italique.// -----\"\"> //Texte en italique.//\n	\"\"Texte __soulign�__ ! -----\"\"> Texte __soulign�__ !\n	\"\"##texte � espacement fixe## -----\"\"> ##texte � espacement fixe##\n	\"\"%%code%%\"\"\n	\"\"%%(php) PHP code%%\"\"\n\n=== Liens forc�s : ===\n	\"\"[[http://www.mon-site.org]]\"\"\n	\"\"[[http://www.mon-site.org Mon-site]]\"\"\n	\"\"[[P2P]]\"\"\n	\"\"[[P2P Page sur le P2P]]\"\"\n\n=== Liens dans Wikini ===\n	Pour r�aliser un lien dans wikini qui apparaisse avec un style normal utilisez cette �criture :\n	\"\"[[ReglesDeFormatage R�gles de Formatage]]\"\"\n	Le lien appara�tra de cette mani�re [[ReglesDeFormatage R�gles de Formatage]].\n\n=== En-t�tes : ===\n	\"\"====== En-t�te �norme ======\"\" ====== En-t�te �norme ======\n	\"\"===== En-t�te tr�s gros =====\"\" ===== En-t�te tr�s gros =====\n	\"\"==== En-t�te gros ====\"\" ==== En-t�te gros ====\n	\"\"=== En-t�te normal ===\"\" === En-t�te normal ===\n	\"\"== Petit en-t�te ==\"\" == Petit en-t�te ==\n\n=== S�parateur horizontal : ===\n	\"\"----\"\"\n\n=== Retour de ligne forc� : ===\n	\"\"---\"\"\n=== Indentation : ===\nL''indentation de textes se fait avec la touche \"TAB\". Vous pouvez aussi cr�er des listes � puces ou num�rot�es :\n	\"\"- liste � puce\"\"\n	\"\"1) liste num�rot�e (chiffres arabes)\"\"\n	\"\"A) liste num�rot�e (capitales alphab�tiques)\"\"\n	\"\"a) liste num�rot�e (minuscules alphab�tiques)\"\"\n	\"\"i) liste num�rot�e (chiffres romains)\"\"\n\n=== Inclure une image ===\n\n - Pour inclure un lien sur une image (sans l''inclure � la page):\n   \"\"[[http://wiki.tela-botanica.org/eflore/bibliotheque/images/table.gif]]\"\"(ne pas indiquer de texte alternatif).\n   Ce qui donne : [[http://wiki.tela-botanica.org/eflore/bibliotheque/images/table.gif]]\n\n - Pour inclure une image sans indiquer de texte alternatif :\n   \"\"[[http://wiki.tela-botanica.org/eflore/bibliotheque/images/table.gif]]\"\"(laisser 3 espaces blancs avant la fermeture des crochets).\n   Ce qui donne quand l''image est trouv�e : [[http://wiki.tela-botanica.org/eflore/bibliotheque/images/table.gif]]\n   Quand l''image n''est pas trouv�e : [[http://wiki.tela-botanica.org/eflore/bibliotheque/table.gif]]\n\n - Pour inclure une image en indiquant un texte alternatif :\n   \"\"[[http://wiki.tela-botanica.org/eflore/bibliotheque/images/table.gif une puce ]]\"\"\n   Ce qui donne quand l''image est trouv�e : [[http://wiki.tela-botanica.org/eflore/bibliotheque/images/table.gif une puce ]]\n   Quand l''image n''est pas trouv�e : [[http://wiki.tela-botanica.org/eflore/bibliotheque/table.gif une puce ]]\n\n//Note :// le texte alternatif est affich� � la place de l''image s''il y a une erreur lors de l''affichage de celle-ci.\n\n=== Outils avanc�s : ===\n\n        \"\"{{Backlinks}}\"\" permet de cr�er un lien vers la page pr�c�dente. \n        \"\"{{Listusers}}\"\" affiche la liste des utilisateurs du site wikini.\n        \"\"{{OrphanedPages}}\"\" affiche les pages orphelines du site wikini.\n        \"\"{{ListPages/tree}}\"\" affiche le plan du site wikini.\n        \"\"{{pageindex}}\"\" affiche un index des pages du site class�es par lettres alphab�tiques.\n        \"\"{{ListPages}}\"\"  affiche un index des pages du site avec le nom de leur propri�taire.\n        \"\"{{WantedPages}}\"\" affiche la liste des pages restant � cr�er. Elles apparaissent dans le site avec un ? � la suite de leur nom.\n        \"\"{{RecentChanges}}\"\" affiche la liste des sites faisant r�f�rence au site wikini.\n        \"\"{{RecentlyCommented}}\"\" affichage de la liste des derniers commentaires.\n        \"\"{{TextSearch}}\"\" recherche de texte dans les pages du site.\n\n**Note :** � cause d''un [[http://bugzilla.mozilla.org/show_bug.cgi?id=10547 bogue dans son moteur de rendu]], les listes, utilisant la touche TAB, ne fonctionnent pas (encore) sous Mozilla.\nUne astuce consiste � r�aliser une tabulation dans un �diteur de texte puis de la copier. On peut ensuite coller la tabulation dans la zone de saisie de Wikini.\nVous pouvez �galement indenter du texte en utilisant des caract�res espace au lieu de la touche \"TAB\", les exemples ci-dessus restent valables mais attention � ne pas m�langer des \"TAB\" et des espaces dans la m�me �num�ration.\n\n---', '', '', 'webmaster', 'Y', 'page', ''),
(66, 'PageMenu', '2006-09-21 14:30:48', '======Menu======\n\n  - [[". $root_page ." Page d''accueil]]\n\n\"\"<div class=\"centrage\"><a id=\"rss\" href=\"wakka.php?wiki=DerniersChangementsRSS/xml\"><img src=\"http://webmaster.org/images/rss.png\" alt=\"Syndication RSS\" /></a></div>\"\"', '', '', 'webmaster', 'Y', 'page', '');
");
    } // end of member function creation_tables

    /**
     *
     *
     * @param string prefixe Le prefixe des tables a supprimer
     * @return void
     * @access public
     */
    function suppression_tables( $prefixe )
    {
    	$resultat = $this->_db->query("DROP TABLE   `".$prefixe."_acls` ,`".$prefixe."_links` ,`".$prefixe."_pages` ,`".
							          $prefixe."_referrers` ,`".$prefixe."_users`") ;
        if (DB::isError ($resultat)) {
            echo ('Echec de la requete de suppression <br />'.$resultat->getMessage()) ;
        }
    } // end of member function suppression_tables





} // end of gestion_wikini
?>
