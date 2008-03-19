<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}

if ($this->HasAccess("write") && $this->HasAccess("read"))
{
	// preview?
	if ($_POST["submit"] == "Aperçu")
	{
		// Rien
	}
	else
	{
		$ACbuttonsBar = "
		<div id=\"toolbar\"> 
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'**','**');\" src=\"tools/aceditor/ACEdImages/bold.gif\" title=\"Passe le texte sélectionné en gras  ( Ctrl-Maj-b )\" alt=\"Gras\"></img>
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'//','//');\" src=\"tools/aceditor/ACEdImages/italic.gif\" title=\"Passe le texte sélectionné en italique ( Ctrl-Maj-t )\" alt=\"Italique\"></img>
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'__','__');\" src=\"tools/aceditor/ACEdImages/underline.gif\" title=\"Souligne le texte sélectionné ( Ctrl-Maj-u )\" alt=\"Souligné\"></img>
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'@@','@@');\" src=\"tools/aceditor/ACEdImages/strike.gif\" title=\"Barre le texte sélectionné\" alt=\"Barre\"></img>
		    
		<img class=\"buttons\"  src=\"tools/aceditor/ACEdImages/separator.gif\" alt=\"\" ></img>
		    
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'======','======\\n');\" src=\"tools/aceditor/ACEdImages/t1.gif\" title=\" En-tête énorme\" alt=\"\">    </img>
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'=====','=====\\n');\" src=\"tools/aceditor/ACEdImages/t2.gif\" title=\"  En-tête très gros\" alt=\"\"> </img>
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'====','====\\n');\" src=\"tools/aceditor/ACEdImages/t3.gif\" title=\"  En-tête gros\" alt=\"\">    </img>
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'===','===\\n');\" src=\"tools/aceditor/ACEdImages/t4.gif\" title=\"  En-tête normal\" alt=\"\">    </img>
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'==','==');\" src=\"tools/aceditor/ACEdImages/t5.gif\" title=\"  Petit en-tête\" alt=\"\">        </img>
		<img class=\"buttons\"  src=\"tools/aceditor/ACEdImages/separator.gif\" alt=\"\" ></img>
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelectionWithLink(thisForm.body);\" src=\"tools/aceditor/ACEdImages/link.gif\" title=\"Ajoute un lien au texte sélectionné\" alt=\"\"></img>
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'\\t-&nbsp;','');\" src=\"tools/aceditor/ACEdImages/listepuce.gif\" title=\"Liste\" alt=\"\"></img>
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'\\t1)&nbsp;','');\" src=\"tools/aceditor/ACEdImages/listenum.gif\" title=\"Liste numérique\" alt=\"\"></img>
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'\\ta)&nbsp;','');\" src=\"tools/aceditor/ACEdImages/listealpha.gif\" title=\"Liste alphabéthique\" alt=\"\"></img>
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelectionBis(thisForm.body,'\\n---','');\" src=\"tools/aceditor/ACEdImages/crlf.gif\" title=\"Insère un retour chariot\" alt=\"\"></img>
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelectionBis(thisForm.body,'\\n------','');\" src=\"tools/aceditor/ACEdImages/hr.gif\" title=\"Insère une ligne horizontale\" alt=\"\"> </img>
		
		
		<img class=\"buttons\"  src=\"tools/aceditor/ACEdImages/separator.gif\" alt=\"\" ></img>
		      
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'%%','%%');\" src=\"tools/aceditor/ACEdImages/code.gif\" title=\"Code\" alt=\"\"></img>
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'%%(php)','%%');\" src=\"tools/aceditor/ACEdImages/php.gif\" title=\"Code PHP\" alt=\"\"></img>
		</div>
		<div id=\"toolbar_suite\">   
		   
		<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelectionWithImage(thisForm.body);\" src=\"tools/aceditor/ACEdImages/image.gif\"    title=\"insère un tag image \" alt=\"\">  </img>
		
		<span class=\"texteChampsImage\">
		&nbsp;&nbsp;Fichier&nbsp;<input type=\"text\" name=\"filename\" class=\"ACsearchbox\" size=\"10\"/>&nbsp;&nbsp;Description&nbsp;<input type=\"text\" name=\"description\" class=\"ACsearchbox\" size=\"10\"/>
		&nbsp;&nbsp;Alignement&nbsp;<select id=\"alignment\" class=\"ACsearchbox\">
		<option value=\"left\">Gauche</option>
		<option value=\"center\">Centré</option>
		<option value=\"right\">Droite</option>
		</select>
		</span>
		</div>";
		
			if (substr(WIKINI_VERSION,2,1)<=4) {

			$plugin_output_new=preg_replace ('/\<textarea onkeydown/',
			$ACbuttonsBar.
			'<textarea onkeydown',
			$plugin_output_new);
		
			}
			else  {
				if (substr(WIKINI_VERSION,2,1)>=5) {
						$plugin_output_new=preg_replace ('/\<textarea id="body"/',
						$ACbuttonsBar.
						'<textarea id="body"',
						$plugin_output_new);
					
				}
			}
		
		
		
	}
}