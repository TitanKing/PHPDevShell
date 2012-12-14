<?php (! empty($aurl)) ? $url = $aurl : $url = $template->outputAbsoluteURL('return'); ?>

<script type="text/javascript">PHPDS_documentReady('#PHPDS_ajax');</script>

<!-- Custom Head Added -->
<?php $template->outputHead(); ?>
<!-- AJAX -->
<div id="PHPDS_ajax"><?php $template->outputController() ?></div>
<!-- Custom Foot Added -->
<?php $template->outputFooterJS() ?>
