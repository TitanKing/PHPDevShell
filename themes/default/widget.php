<?php (! empty($aurl)) ? $url = $aurl : $url = $template->outputAbsoluteURL('return'); ?>

<script type="text/javascript">PHPDS_documentReady('#PHPDS_widget');</script>
<!-- Custom Head Added -->
<?php $template->outputHead(); ?>
<!-- WIDGET -->
<div id="PHPDS_widget" class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-helper-clearfix ajaxwidget" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog">
	<div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
		<span class="ui-dialog-title"><?php $template->outputName() ?></span>
	</div>
	<div class="ui-dialog-content ui-widget-content">
		<?php $template->outputController() ?>
	</div>
</div>
<!-- Custom Foot Added -->
<?php $template->outputFooterJS() ?>
