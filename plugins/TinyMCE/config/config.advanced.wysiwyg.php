<?php

// This is a typical config file for TinyMCE wysiwyg,
// you may edit this file to your liking or create a new config file and call the wysiwyg class with the custom config file.
// Please read more about what plugins can be enabled at : http://tinymce.moxiecode.com/
// http://wiki.moxiecode.com/index.php/TinyMCE:Plugins
// Some available plugins:
// plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",
?>
<script type="text/javascript" src="plugins/TinyMCE/resources/tiny_mce/tiny_mce_gzip.js"></script>
<script type="text/javascript">
tinyMCE_GZ.init({
	plugins 		: "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",
	skin 			: "cirkuit",
	languages 		: 'en',
	disk_cache 		: true,
	debug 			: false
});
</script>
<script language="javascript" type="text/javascript" src="plugins/TinyMCE/resources/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
	tinyMCE.init
	({
		theme                              		: 'advanced',
		mode                               		: 'textareas',
		plugins 								: "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",
		skin                                    : "cirkuit",
		theme_advanced_buttons1 				: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 				: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 				: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 				: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
		theme_advanced_toolbar_location    		: "top",
		theme_advanced_toolbar_align       		: "left",
		theme_advanced_statusbar_location  		: "bottom",
		theme_advanced_resizing            		: true,
		extended_valid_elements 				: "@[id|class|style|title|dir<ltr?rtl|lang|xml::lang|onclick|ondblclick|"
                                    			+ "onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|"
                                    			+ "onkeydown|onkeyup],a[rel|rev|charset|hreflang|tabindex|accesskey|type|"
                                    			+ "name|href|target|title|class|onfocus|onblur],strong/b,em/i,strike,u,"
                                    			+ "#p[align],-ol[type|compact],-ul[type|compact],-li,br,img[longdesc|usemap|"
                                    			+ "src|border|alt=|title|hspace|vspace|width|height|align],-sub,-sup,"
                                    			+ "-blockquote,-table[border=0|cellspacing|cellpadding|width|frame|rules|"
                                    			+ "height|align|summary|bgcolor|background|bordercolor],-tr[rowspan|width|"
                                    			+ "height|align|valign|bgcolor|background|bordercolor],tbody,thead,tfoot,"
                                    			+ "#td[colspan|rowspan|width|height|align|valign|bgcolor|background|bordercolor"
                                    			+ "|scope],#th[colspan|rowspan|width|height|align|valign|scope],caption,-div,"
                                    			+ "-span,-code,-pre,address,-h1,-h2,-h3,-h4,-h5,-h6,hr[size|noshade],-font[face"
                                    			+ "|size|color],dd,dl,dt,cite,abbr,acronym,del[datetime|cite],ins[datetime|cite],"
                                    			+ "object[classid|width|height|codebase|*],param[name|value|_value],embed[type|width"
                                    			+ "|height|src|*],script[src|type],map[name],area[shape|coords|href|alt|target],bdo,"
                                    			+ "button,col[align|char|charoff|span|valign|width],colgroup[align|char|charoff|span|"
                                    			+ "valign|width],dfn,fieldset,form[action|accept|accept-charset|enctype|method],"
                                    			+ "input[accept|alt|checked|disabled|maxlength|name|readonly|size|src|type|value],"
                                    			+ "kbd,label[for],legend,noscript,optgroup[label|disabled],option[disabled|label|selected|value],"
                                    			+ "q[cite],samp,select[disabled|multiple|name|size],small,"
                                    			+ "textarea[cols|rows|disabled|name|readonly],tt,var,big"
	});
</script>