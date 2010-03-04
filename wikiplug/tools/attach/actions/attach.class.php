<?php
/*
attach.class.php
Code original de ce fichier : Eric FELDSTEIN
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2002, 2003 Charles NEPOTE
Copyright  2003,2004  Eric FELDSTEIN
Copyright  2003  Jean-Pascal MILCENT
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
# Classe de gestion de l'action {{attach}}
# voir actions/attach.php ppour la documentation
# copyrigth Eric Feldstein 2003-2004

if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}

if (!class_exists('attach')){

class attach {
	var $wiki = '';					//objet wiki courant
   var $attachConfig = array();	//configuration de l'action
   var $file = '';					//nom du fichier
   var $desc = '';					//description du fichier
   var $link = '';					//url de lien (image sensible)
   var $isPicture = 0;				//indique si c'est une image
   var $isAudio = 0;				//indique si c'est un fichier audio
   var $isFreeMindMindMap = 0;		//indique si c'est un fichier mindmap freemind
   var $isWma = 0;					//indique si c'est un fichier wma
   var $classes = '';				//classe pour afficher une image
   var $attachErr = '';				//message d'erreur
   var $pageId = 0;					//identifiant de la page
   var $isSafeMode = false;		//indicateur du safe mode de PHP
   /**
   * Constructeur. Met les valeurs par defaut aux param�tres de configuration
   */
	function attach(&$wiki){
   	$this->wiki = $wiki;
		$this->attachConfig = $this->wiki->GetConfigValue("attach_config");
		if (empty($this->attachConfig["ext_images"])) $this->attachConfig["ext_images"] = "gif|jpeg|png|jpg";
		if (empty($this->attachConfig["ext_audio"])) $this->attachConfig["ext_audio"] = "mp3";
		if (empty($this->attachConfig["ext_wma"])) $this->attachConfig["ext_wma"] = "wma";
		if (empty($this->attachConfig["ext_freemind"])) $this->attachConfig["ext_freemind"] = "mm";
		if (empty($this->attachConfig["ext_flashvideo"])) $this->attachConfig["ext_flashvideo"] = "flv";
		if (empty($this->attachConfig["ext_script"])) $this->attachConfig["ext_script"] = "php|php3|asp|asx|vb|vbs|js";
		if (empty($this->attachConfig['upload_path'])) $this->attachConfig['upload_path'] = 'files';
		if (empty($this->attachConfig['update_symbole'])) $this->attachConfig['update_symbole'] = '*';
		if (empty($this->attachConfig['max_file_size'])) $this->attachConfig['max_file_size'] = 1024*8000;	//8000ko max
		if (empty($this->attachConfig['fmDelete_symbole'])) $this->attachConfig['fmDelete_symbole'] = 'Supr';
		if (empty($this->attachConfig['fmRestore_symbole'])) $this->attachConfig['fmRestore_symbole'] = 'Rest';
		if (empty($this->attachConfig['fmTrash_symbole'])) $this->attachConfig['fmTrash_symbole'] = 'Poubelle';
		$this->isSafeMode = ini_get("safe_mode");
	}
/******************************************************************************
*	FONCTIONS UTILES
*******************************************************************************/
	/**
	* Cr�ation d'une suite de r�pertoires r�cursivement
	*/
	function mkdir_recursif ($dir) {
		if (strlen($dir) == 0) return 0;
		if (is_dir($dir)) return 1;
		elseif (dirname($dir) == $dir) return 1;
		return ($this->mkdir_recursif(dirname($dir)) and mkdir($dir,0755));
	}
	/**
	* Renvois le chemin du script
	*/
	function GetScriptPath () {
		if (preg_match("/.(php)$/i",$_SERVER["PHP_SELF"])){
			$a = explode('/',$_SERVER["PHP_SELF"]);
			$a[count($a)-1] = '';
			$path = implode('/',$a);
		}else{
			$path = $_SERVER["PHP_SELF"];
		}
		return !empty($_SERVER["HTTP_HOST"])? 'http://'.$_SERVER["HTTP_HOST"].$path : 'http://'.$_SERVER["SERVER_NAME"].$path ;
	}
	/**
	* Calcul le repertoire d'upload en fonction du safe_mode
	*/
	function GetUploadPath(){
		if ($this->isSafeMode) {
			$path = $this->attachConfig['upload_path'];
		}else{
         $path = $this->attachConfig['upload_path'].'/'.$this->wiki->GetPageTag();
			if (! is_dir($path)) $this->mkdir_recursif($path);
		}
		return $path;
	}
	/**
	* Calcule le nom complet du fichier attach� en fonction du safe_mode, du nom et de la date de
	* revision la page courante.
	* Le nom du fichier "mon fichier.ext" attache � la page "LaPageWiki"sera :
	*  mon_fichier_datepage_update.ext
	*     update : date de derniere mise a jour du fichier
	*     datepage : date de revision de la page � laquelle le fichier a ete li�/mis a jour
	*  Si le fichier n'est pas une image un '_' est ajoute : mon_fichier_datepage_update.ext_
	*  Selon la valeur de safe_mode :
	*  safe_mode = on : 	LaPageWiki_mon_fichier_datepage_update.ext_
	*  safe_mode = off: 	LaPageWiki/mon_fichier_datepage_update.ext_ avec "LaPageWiki" un sous-repertoire du r�pertoire upload
	*/
	function GetFullFilename($newName = false){
		$pagedate = $this->convertDate($this->wiki->page['time']);
		//decompose le nom du fichier en nom+extension
		if (preg_match('`^(.*)\.(.*)$`', str_replace(' ','_',$this->file), $match)){
			list(,$file['name'],$file['ext'])=$match;
			if(!$this->isPicture() && !$this->isAudio() && !$this->isFreeMindMindMap() && !$this->isWma()) $file['ext'] .= '_';
		}else{
			return false;
		}
		//recuperation du chemin d'upload
		$path = $this->GetUploadPath($this->isSafeMode);
		//generation du nom ou recherche de fichier ?
		if ($newName){
			$full_file_name = $file['name'].'_'.$pagedate.'_'.$this->getDate().'.'.$file['ext'];
			if($this->isSafeMode){
				$full_file_name = $path.'/'.$this->wiki->GetPageTag().'_'.$full_file_name;
			}else{
				$full_file_name = $path.'/'.$full_file_name;
			}
		}else{
			//recherche du fichier
			if($this->isSafeMode){
				//TODO Recherche dans le cas ou safe_mode=on
				$searchPattern = '`^'.$this->wiki->GetPageTag().'_'.$file['name'].'_\d{14}_\d{14}\.'.$file['ext'].'$`';
			}else{
				$searchPattern = '`^'.$file['name'].'_\d{14}_\d{14}\.'.$file['ext'].'$`';
			}
			$files = $this->searchFiles($searchPattern,$path);

			$unedate = 0;
			foreach ($files as $file){
				//recherche du fichier qui une datepage <= a la date de la page
				if($file['datepage']<=$pagedate){
					//puis qui a une dateupload la plus grande
					if ($file['dateupload']>$unedate){
						$theFile = $file;
						$unedate = $file['dateupload'];
					}
				}
			}
			if (is_array($theFile)){
				$full_file_name = $path.'/'.$theFile['realname'];
			}
		}
		return $full_file_name;
	}
	/**
	* Test si le fichier est une image
	*/
	function isPicture(){
		return preg_match("/.(".$this->attachConfig["ext_images"].")$/i",$this->file)==1;
	}
	/**
	* Test si le fichier est un fichier audio
	*/
	function isAudio(){
		return preg_match("/.(".$this->attachConfig["ext_audio"].")$/i",$this->file)==1;
	}
	/**
	* Test si le fichier est un fichier freemind mind map
	*/
	function isFreeMindMindMap(){
		return preg_match("/.(".$this->attachConfig["ext_freemind"].")$/i",$this->file)==1;
	}
	/**
	* Test si le fichier est un fichier flv Flash video
	*/
	function isFlashvideo(){
		return preg_match("/.(".$this->attachConfig["ext_flashvideo"].")$/i",$this->file)==1;
	}
	/**
	* Test si le fichier est un fichier wma
	*/
	function isWma(){
		return preg_match("/.(".$this->attachConfig["ext_wma"].")$/i",$this->file)==1;
	}

	/**
	* Renvoie la date courante au format utilise par les fichiers
	*/
	function getDate(){
		return date('YmdHis');
	}
	/**
	* convertie une date yyyy-mm-dd hh:mm:ss au format yyyymmddhhmmss
	*/
	function convertDate($date){
		$date = str_replace(' ','', $date);
		$date = str_replace(':','', $date);
		return str_replace('-','', $date);
	}
	/**
	* Parse une date au format yyyymmddhhmmss et renvoie un tableau assiatif
	*/
	function parseDate($sDate){
		$pattern = '`^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$`';
		$res = '';
		if (preg_match($pattern, $sDate, $m)){
			//list(,$res['year'],$res['month'],$res['day'],$res['hour'],$res['min'],$res['sec'])=$m;
			$res = $m[1].'-'.$m[2].'-'.$m[3].' '.$m[4].':'.$m[5].':'.$m[6];
		}
		return ($res?$res:false);
	}
	/**
	* Decode un nom long de fichier
	*/
	function decodeLongFilename($filename){
		$afile = array();
		$afile['realname'] = basename($filename);
		$afile['size'] = filesize($filename);
		$afile['path'] = dirname($filename);
		if(preg_match('`^(.*)_(\d{14})_(\d{14})\.(.*)(trash\d{14})?$`', $afile['realname'], $m)){
			$afile['name'] = $m[1];
			//suppression du nom de la page si safe_mode=on
			if ($this->isSafeMode){
				$afile['name'] = preg_replace('`^('.$this->wiki->tag.')_(.*)$`i', '$2', $afile['name']);
			}
			$afile['datepage'] = $m[2];
			$afile['dateupload'] = $m[3];
			$afile['trashdate'] = preg_replace('`(.*)trash(\d{14})`', '$2', $m[4]);
			//suppression de trashxxxxxxxxxxxxxx eventuel
			$afile['ext'] = preg_replace('`^(.*)(trash\d{14})$`', '$1', $m[4]);
			$afile['ext'] = rtrim($afile['ext'],'_');
			//$afile['ext'] = rtrim($m[4],'_');
		}
		return $afile;
	}
	/**
	* Renvois un tableau des fichiers correspondant au pattern. Chaque element du tableau est un
	* tableau associatif contenant les informations sur le fichier
	*/
	function searchFiles($filepattern,$start_dir){
		$files_matched = array();
		$start_dir = rtrim($start_dir,'\/');
		$fh = opendir($start_dir);
		while (($file = readdir($fh)) !== false) {
			if (strcmp($file, '.')==0 || strcmp($file, '..')==0 || is_dir($file)) continue;
			if (preg_match($filepattern, $file)){
				$files_matched[] = $this->decodeLongFilename($start_dir.'/'.$file);
			}
		}
		return $files_matched;
	}
/******************************************************************************
*	FONCTIONS D'ATTACHEMENTS
*******************************************************************************/
	/**
	* Test les param�tres pass� � l'action
	*/
	function CheckParams(){
		//recuperation des parametres necessaire
		$this->file = $this->wiki->GetParameter("attachfile");
		if (empty($this->file)) $this->file = $this->wiki->GetParameter("file");
		$this->desc = $this->wiki->GetParameter("attachdesc");
		if (empty($this->desc)) $this->desc = $this->wiki->GetParameter("desc");
		$this->link = $this->wiki->GetParameter("attachlink");//url de lien - uniquement si c'est une image
		if (empty($this->link)) $this->link = $this->wiki->GetParameter("link");
		//test de validit� des parametres
		if (empty($this->file)){
			$this->attachErr = $this->wiki->Format("//action attach : param�tre **file** manquant//---");
		}
		if ($this->isPicture() && empty($this->desc)){
			$this->attachErr .= $this->wiki->Format("//action attach : param�tre **desc** obligatoire pour une image//---");
		}
		if ($this->wiki->GetParameter("class")) {
   		$array_classes = explode(" ", $this->wiki->GetParameter("class"));
   		foreach ($array_classes as $c) { $this->classes = $this->classes . "attach_" . $c . " "; }
   		$this->classes = trim($this->classes);
		}
	}
	/**
	* Affiche le fichier li� comme une image
	*/
	function showAsImage($fullFilename){
		//c'est une image : balise <IMG..../>
		$img =	"<img src=\"".$this->GetScriptPath().$fullFilename."\" ".
					"alt=\"".$this->desc.($this->link?"\nLien vers: $this->link":"")."\" />";
		//test si c'est une image sensible
		if(!empty($this->link)){
			//c'est une image sensible
			//test si le lien est un lien interwiki
			if (preg_match("/^([A-Z][A-Z,a-z]+)[:]([A-Z,a-z,0-9]*)$/s", $this->link, $matches))
			{  //modifie $link pour �tre un lien vers un autre wiki
				$this->link = $this->wiki->GetInterWikiUrl($matches[1], $matches[2]);
			}
			//calcule du lien
			$output = $this->wiki->Format('[['.$this->link." $this->file]]");
			$output = eregi_replace(">$this->file<",">$img<",$output);//insertion du tag <img...> dans le lien
		}else{
			//ce n'est pas une image sensible
			$output = $img;
		}
		$output = ($this->classes?"<span class=\"$this->classes\">$output</span>":$output);
		echo $output;
		$this->showUpdateLink();
	}
	/**
	* Affiche le fichier li� comme un lien
	*/
	function showAsLink($fullFilename){
		$url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
		echo '<a href="'.$url.'">'.($this->desc?$this->desc:$this->file)."</a>";
		$this->showUpdateLink();
	}
	// Affiche le fichier liee comme un fichier audio
	function showAsAudio($fullFilename){
		$output =  '<object type="application/x-shockwave-flash" data="tools/attach/players/dewplayer.swf?son='.$fullFilename.'&amp;bgcolor=EEEEEE&amp;showtime=1" width="200" height="20"><param name="wmode" value="transparent" />
						<param name="movie" value="tools/attach/players/dewplayer.swf?son='.$fullFilename.'&amp;bgcolor=EEEEEE&amp;showtime=1" />
					</object>';
		$output .="[<a href=\"$fullFilename\" title=\"T&eacute;l&eacute;charger le fichier mp3\">mp3</a>]";
		echo $output;
		$this->showUpdateLink();
	}

		// Affiche le fichier liee comme un fichier mind map  freemind
	function showAsFreeMindMindMap($fullFilename){
        $haut=$this->haut;
        $large=$this->large;
        if (!$haut) $haut = "650";
        if (!$large) $large = "100%";
        $mindmap_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");     	
		$output = '<object width="'.$large.'" height="'.$haut.'" type="application/x-shockwave-flash" data="tools/attach/players/visorFreemind.swf">
			<param value="false" name="allowfullscreen"/>
			<param value="always" name="allowscriptaccess"/>
			<param value="high" name="quality"/>
			<param value="false" name="cachebusting"/>
			<param value="middle" name="align"/>
			<param value="opaque" name="wmode"/>
			<param value="openUrl=_blank&amp;initLoadFile='.$fullFilename.'&amp;startCollapsedToLevel=5" name="flashvars"/>
		</object>';
		$output .="[<a href=\"$fullFilename\" title=\"T&eacute;l&eacute;charger le fichier Freemind\">mm</a>]";
		echo $output;
		$this->showUpdateLink();
      }
      
// Affiche le fichier liee comme un fichier mind map  freemind
	function showAsWma($fullFilename){
        $haut=$this->haut;
        $large=$this->large;
        if (!$haut) $haut = "30";
        if (!$large) $large = "100";
        $wma_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
        $output = '<embed src="'.$fullFilename.'" autostart="false" loop="false" console="true" height="'.$haut.'" width="'.$large.'" />';
		$output .="[<a href=\"$fullFilename\" title=\"T&eacute;l&eacute;charger le fichier wma\">wma</a>]";
		echo $output;
		$this->showUpdateLink();
      }
      
		// Affiche le fichier liee comme une video flash
	function showAsFlashvideo($fullFilename){
        $haut=$this->haut;
        $large=$this->large;
        if (!$haut) $haut = "300px";
        if (!$large) $large = "400px";
        $video_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
     	$output = '<a  
						 href="'.$fullFilename.'"  
						 style="display:block;width:'.$large.';height:'.$haut.'"  
						 class="flvplayer"> 
					</a>'."\n";         
		$output .='<script type="text/javascript" src="tools/attach/players/flowplayer-3.0.6.min.js"></script> 
<script>
	flowplayer("a.flvplayer", "tools/attach/players/flowplayer-3.0.7.swf", { 
	    clip:  { 
		autoPlay: false, 
		autoBuffering: true 
	    },
	    plugins:  { 
	        controls: {             
			url: \'tools/attach/players/flowplayer.controls-3.0.4.swf\', 
			autoHide: \'always\', 
			 
			// which buttons are visible and which are not? 
			play:true,      
			volume:true, 
			mute:true,  
			time:true,  
			stop:true, 
			playlist:false,  
			fullscreen:true, 
			 
			// scrubber is a well-known nickname for the timeline/playhead combination 
			scrubber: true         
			 
			// you can also use the "all" flag to disable/enable all controls 
		}
	    } 
	});
</script>';
		echo $output;
		$this->showUpdateLink();
      }

	// End Paste



	/**
	* Affiche le lien de mise � jour
	*/
	function showUpdateLink(){
		echo	" <a href=\"".
				$this->wiki->href("upload",$this->wiki->GetPageTag(),"file=$this->file").
				"\" title='Mise � jour'>".$this->attachConfig['update_symbole']."</a>";
	}
	/**
	* Affiche un liens comme un fichier inexistant
	*/
	function showFileNotExits(){
		echo $this->file."<a href=\"".$this->wiki->href("upload",$this->wiki->GetPageTag(),"file=$this->file")."\">?</a>";
	}
	/**
	* Affiche l'attachement
	*/
	function doAttach(){
		$this->CheckParams();
		if ($this->attachErr) {
			echo $this->attachErr;
			return;
		}
		$fullFilename = $this->GetFullFilename();
		//test d'existance du fichier
		if((!file_exists($fullFilename))||($fullFilename=='')){
			$this->showFileNotExits();
			return;
		}
      //le fichier existe : affichage en fonction du type
      if($this->isPicture()){
      	$this->showAsImage($fullFilename);
      }elseif ($this->isAudio()){
      		$this->showAsAudio($fullFilename);
      }elseif ($this->isFreeMindMindMap()){
      	   	$this->showAsFreeMindMindMap($fullFilename);
	  }elseif ($this->isFlashvideo()){
      	   	$this->showAsFlashvideo($fullFilename);
	  }elseif ($this->isWma()){
      	   	$this->showAsWma($fullFilename);
	  }else {
		   	$this->showAsLink($fullFilename);
	  }
	}
/******************************************************************************
*	FONTIONS D'UPLOAD DE FICHIERS
*******************************************************************************/
	/**
	* Traitement des uploads
	*/
	function doUpload(){
		$HasAccessWrite=$this->wiki->HasAccess("write");
		if ($HasAccessWrite){
         switch ($_SERVER["REQUEST_METHOD"]) {
         	case 'GET' : $this->showUploadForm(); break;
         	case 'POST': $this->performUpload(); break;
         	default : echo $this->wiki->Format("//Methode de requete invalide//---");
			}
		}else{
			echo $this->wiki->Format("//Vous n'avez pas l'acc�s en �criture � cette page//---");
			echo $this->wiki->Format("Retour � la page ".$this->wiki->GetPageTag());
		}
	}
	/**
	* Formulaire d'upload
	*/
	function showUploadForm(){
		echo $this->wiki->Format("====Formulaire d'envois de fichier====\n---");
		$this->file = $_GET['file'];
		echo 	$this->wiki->Format("**Envois du fichier $this->file :**\n")
				."<form enctype=\"multipart/form-data\" name=\"frmUpload\" method=\"POST\" action=\"".$_SERVER["PHP_SELF"]."\">\n"
				."	<input type=\"hidden\" name=\"wiki\" value=\"".$this->wiki->GetPageTag()."/upload\" />\n"
				."	<input TYPE=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".$this->attachConfig['max_file_size']."\" />\n"
				."	<input type=\"hidden\" name=\"file\" value=\"$this->file\" />\n"
				."	<input type=\"file\" name=\"upFile\" size=\"50\" /><br />\n"
				."	<input type=\"submit\" value=\"Envoyer\" />\n"
				."</form>\n";
	}
	/**
	* Execute l'upload
	*/
	function performUpload(){
		$this->file = $_POST['file'];

		$destFile = $this->GetFullFilename(true);	//nom du fichier destination
		//test de la taille du fichier recu
		if($_FILES['upFile']['error']==0){
			$size = filesize($_FILES['upFile']['tmp_name']);
			if ($size > $this->attachConfig['max_file_size']){
				$_FILES['upFile']['error']=2;
			}
		}
		switch ($_FILES['upFile']['error']){
			case 0:
				$srcFile = $_FILES['upFile']['tmp_name'];
				if (move_uploaded_file($srcFile,$destFile)){
					chmod($destFile,0644);
					header("Location: ".$this->wiki->href("",$this->wiki->GetPageTag(),""));
				}else{
					echo $this->wiki->Format("//Erreur lors du d�placement du fichier temporaire//---");
				}
				break;
			case 1:
				echo $this->wiki->Format("//Le fichier t�l�charg� exc�de la taille de upload_max_filesize, configur� dans le php.ini.//---");
				break;
			case 2:
				echo $this->wiki->Format("//Le fichier t�l�charg� exc�de la taille de MAX_FILE_SIZE, qui a �t� sp�cifi�e dans le formulaire HTML.//---");
				break;
			case 3:
				echo $this->wiki->Format("//Le fichier n'a �t� que partiellement t�l�charg�.//---");
				break;
			case 4:
				echo $this->wiki->Format("//Aucun fichier n'a �t� t�l�charg�.//---");
				break;
		}
		echo $this->wiki->Format("Retour � la page ".$this->wiki->GetPageTag());
	}
/******************************************************************************
*	FUNCTIONS DE DOWNLOAD DE FICHIERS
*******************************************************************************/
	function doDownload(){
		$this->file = $_GET['file'];
		$fullFilename = $this->GetUploadPath().'/'.basename(realpath($this->file).$this->file);
//		$fullFilename = $this->GetUploadPath().'/'.$this->file;
		if(!file_exists($fullFilename)){
			$fullFilename = $this->GetFullFilename();
			$dlFilename = $this->file;
			$size = filesize($fullFilename);
		}else{
			$file = $this->decodeLongFilename($fullFilename);
			$size = $file['size'];
			$dlFilename =$file['name'].'.'.$file['ext'];
		}
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Content-type: application/force-download");
        header('Pragma: public');
        header("Pragma: no-cache");// HTTP/1.0
        header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
        header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
        header('Content-Transfer-Encoding: none');
        header('Content-Type: application/octet-stream; name="' . $dlFilename . '"'); //This should work for the rest
        header('Content-Type: application/octetstream; name="' . $dlFilename . '"'); //This should work for IE & Opera
        header('Content-Type: application/download; name="' . $dlFilename . '"'); //This should work for IE & Opera
        header('Content-Disposition: attachment; filename="'.$dlFilename.'"');
        header("Content-Description: File Transfer");
        header("Content-length: $size".'bytes');
		readfile($fullFilename);
	}
/******************************************************************************
*	FONTIONS DU FILEMANAGER
*******************************************************************************/
	function doFileManager(){
		$do = $_GET['do']?$_GET['do']:'';
		switch ($do){
			case 'restore' :
				$this->fmRestore();
				$this->fmShow(true);
				break;
			case 'erase' :
				$this->fmErase();
				$this->fmShow(true);
				break;
			case 'del' :
				$this->fmDelete();
				$this->fmShow();
				break;
			case 'trash' :
				$this->fmShow(true); break;
			case 'emptytrash' :
				$this->fmEmptyTrash();	//pas de break car apres un emptytrash => retour au gestionnaire
			default :
				$this->fmShow();
		}
	}
	/**
	* Affiche la liste des fichiers
	*/
	function fmShow($trash=false){
		$fmTitlePage = $this->wiki->Format("====Gestion des fichiers attach�s �  la page ".$this->wiki->tag."====\n---");
		if($trash){
			//Avertissement
			$fmTitlePage .= '<div class="prev_alert">Les fichiers effac�s sur cette page le sont d�finitivement</div>';
			//Pied du tableau
			$url = $this->wiki->Link($this->wiki->tag,'filemanager','Gestion des fichiers');
      	$fmFootTable =	'	<tfoot>'."\n".
      						'		<tr>'."\n".
      						'			<td colspan="6">'.$url.'</td>'."\n";
			$url = $this->wiki->Link($this->wiki->tag,'filemanager&do=emptytrash','Vider la poubelle');
      	$fmFootTable.=	'			<td>'.$url.'</td>'."\n".
      						'		</tr>'."\n".
      						'	</tfoot>'."\n";
		}else{
			//pied du tableau
         $url = '<a href="'.$this->wiki->href('filemanager',$this->wiki->GetPageTag(),'do=trash').'" title="Poubelle">'.$this->attachConfig['fmTrash_symbole']."</a>";
      	$fmFootTable =	'	<tfoot>'."\n".
      						'		<tr>'."\n".
      						'			<td colspan="6">'.$url.'</td>'."\n".
      						'		</tr>'."\n".
      						'	</tfoot>'."\n";
		}
		//entete du tableau
		$fmHeadTable = '	<thead>'."\n".
							'		<tr>'."\n".
							'			<td>&nbsp;</td>'."\n".
							'			<td>Nom du fichier</td>'."\n".
							'			<td>Nom r�el du fichier</td>'."\n".
							'			<td>Taille</td>'."\n".
							'			<td>R�vision de la page</td>'."\n".
							'			<td>R�vison du fichier</td>'."\n";
		if($trash){
         $fmHeadTable.= '			<td>Suppression</td>'."\n";
		}
		$fmHeadTable.= '		</tr>'."\n".
							'	</thead>'."\n";
		//corps du tableau
		$files = $this->fmGetFiles($trash);
  		$files = $this->sortByNameRevFile($files);

		$fmBodyTable =	'	<tbody>'."\n";
		$i = 0;
		foreach ($files as $file){
			$i++;
			$color= ($i%2?"tableFMCol1":"tableFMCol2");
			//lien de suppression
			if ($trash){
				$url = $this->wiki->href('filemanager',$this->wiki->GetPageTag(),'do=erase&file='.$file['realname']);
			}else{
				$url = $this->wiki->href('filemanager',$this->wiki->GetPageTag(),'do=del&file='.$file['realname']);
			}
			$dellink = '<a href="'.$url.'" title="Supprimer">'.$this->attachConfig['fmDelete_symbole']."</a>";
			//lien de restauration
			$restlink = '';
			if ($trash){
				$url = $this->wiki->href('filemanager',$this->wiki->GetPageTag(),'do=restore&file='.$file['realname']);
				$restlink = '<a href="'.$url.'" title="Restaurer">'.$this->attachConfig['fmRestore_symbole']."</a>";
			}

			//lien pour downloader le fichier
			$url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=".$file['realname']);
			$dlLink = '<a href="'.$url.'">'.$file['name'].'.'.$file['ext']."</a>";
			$fmBodyTable .= 	'		<tr class="'.$color.'">'."\n".
									'			<td>'.$dellink.' '.$restlink.'</td>'."\n".
									'			<td>'.$dlLink.'</td>'."\n".
									'			<td>'.$file['realname'].'</td>'."\n".
									'			<td>'.$file['size'].'</td>'."\n".
									'			<td>'.$this->parseDate($file['datepage']).'</td>'."\n".
									'			<td>'.$this->parseDate($file['dateupload']).'</td>'."\n";
			if($trash){
         	$fmBodyTable.= '			<td>'.$this->parseDate($file['trashdate']).'</td>'."\n";
			}
			$fmBodyTable .= 	'		</tr>'."\n";
		}
		$fmBodyTable .= '	</tbody>'."\n";
		//pied de la page
		$fmFooterPage = "---\n-----\n[[".$this->wiki->tag." Retour � la page ".$this->wiki->tag."]]\n";
		//affichage
		echo $fmTitlePage."\n";
		echo '<table class="tableFM" border="0" cellspacing="0">'."\n".$fmHeadTable.$fmFootTable.$fmBodyTable.'</table>'."\n";
		echo $this->wiki->Format($fmFooterPage);
	}
	/**
	* Renvoie la liste des fichiers
	*/
	function fmGetFiles($trash=false){
		$path = $this->GetUploadPath();
		if($this->isSafeMode){
			$filePattern = '^'.$this->wiki->GetPageTag().'_.*_\d{14}_\d{14}\..*';
		}else{
			$filePattern = '^.*_\d{14}_\d{14}\..*';
		}
		if($trash){
			$filePattern .= 'trash\d{14}';
		}else{
			$filePattern .= '[^(trash\d{14})]';
		}
		return $this->searchFiles('`'.$filePattern.'$`', $path);
	}
	/**
	* Vide la poubelle
	*/
	function fmEmptyTrash(){
		$files = $this->fmGetFiles(true);
		foreach ($files as $file){
			$filename = $file['path'].'/'.$file['realname'];
			if(file_exists($filename)){
				unlink($filename);
			}
		}
	}
	/**
	* Effacement d'un fichier dans la poubelle
	*/
	function fmErase(){
		$path = $this->GetUploadPath();
		$filename = $path.'/'.($_GET['file']?$_GET['file']:'');
		if (file_exists($filename)){
			unlink($filename);
		}
	}
	/**
	* Met le fichier a la poubelle
	*/
	function fmDelete(){
		$path = $this->GetUploadPath();
		$filename = $path.'/'.($_GET['file']?$_GET['file']:'');
		if (file_exists($filename)){
			$trash = $filename.'trash'.$this->getDate();
			rename($filename, $trash);
		}
	}
	/**
	* Restauration d'un fichier mis a la poubelle
	*/
	function fmRestore(){
		$path = $this->GetUploadPath();
		$filename = $path.'/'.($_GET['file']?$_GET['file']:'');
		if (file_exists($filename)){
			$restFile = preg_replace('`^(.*\..*)trash\d{14}$`', '$1', $filename);
			rename($filename, $restFile);
		}
	}
	/**
	* Tri tu tableau liste des fichiers par nom puis par date de revision(upload) du fichier, ordre croissant
	*/
	function sortByNameRevFile($files){
		if (!function_exists('ByNameByRevFile')){
			function ByNameByRevFile($f1,$f2){
				$f1Name = $f1['name'].'.'.$f1['ext'];
				$f2Name = $f2['name'].'.'.$f2['ext'];
				$res = strcasecmp($f1Name, $f2Name);
				if($res==0){
					//si meme nom => compare la revision du fichier
					$res = strcasecmp($f1['dateupload'], $f2['dateupload']);
				}
				return $res;
			}
		}
		usort($files,'ByNameByRevFile');
		return $files;
	}
}
}
?>
