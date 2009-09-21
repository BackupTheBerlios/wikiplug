<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}

$contenu=$this->LoadPage($this->tag);
if (($this->GetMethod() == "show") && ($act=preg_match_all ("/".'(\\{\\{contact)'.'(.*?)'.'(\\}\\})'."/is", $contenu["body"], $matches) || $act=preg_match_all ("/".'(\\{\\{abonnement)'.'(.*?)'.'(\\}\\})'."/is", $contenu["body"], $matches) || $act=preg_match_all ("/".'(\\{\\{desabonnement)'.'(.*?)'.'(\\}\\})'."/is", $contenu["body"], $matches) )) {

	$plugin_output_new=preg_replace ('/<\/head>/',
	'
	<script type="text/javascript">                                         
   // we will add our javascript code here           
   
$(document).ready(function(){
$("#ajax-contact-form, #ajax-abonne-form, #ajax-desabonne-form").submit(function(){

$(this).addClass(\'form-selected\').prev(".note").addClass(\'note-selected\');

var str = $(this).serialize();

   $.ajax({
   type: "POST",
   url: "tools/contact/libs/contact.php",
   data: str,
   success: function(msg){
    
$(".note-selected").ajaxComplete(function(event, request, settings){

if(msg == \'OK\') // Message Sent? Show the message and hide the form
{
result = \'<div class="notification_ok">Votre message a bien &eacute;t&eacute; envoy&eacute;. Merci!</div>\';
$(".form-selected").hide().removeClass("form-selected");
}
else if(msg == \'abonne\')
{
result = \'<div class="notification_ok">Votre abonnement a bien &eacute;t&eacute; pris en compte. Merci!</div>\';
$(".form-selected").hide().removeClass("form-selected");
}
else if(msg == \'desabonne\')
{
result = \'<div class="notification_ok">Votre d&eacute;sabonnement a bien &eacute;t&eacute; pris en compte. Merci, &agrave; bient&ocirc;t!</div>\';
$(".form-selected").hide().removeClass("form-selected");
}
else
{
result = msg;
}

$(this).html(result);

}).removeClass("note-selected");

}

 });

return false;

});

});

 </script>  

<link rel="stylesheet" type="text/css" href="tools/contact/libs/style.css" />
	</head>   
	',
	$plugin_output_new);
	
}
?>	
