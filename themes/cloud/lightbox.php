<?php (! empty($aurl)) ? $url = $aurl : $url = $template->outputAbsoluteURL('return'); ?>

<script type="text/javascript">PHPDS_documentReady('#PHPDS_lightbox');</script>
<!-- Custom Head Added -->
<?php $template->outputHead(); ?>
<!-- LIGHTBOX -->
<div id="PHPDS_lightbox"><?php $template->outputController() ?></div>
<!-- Custom Foot Added -->
<?php $template->outputFooterJS() ?>

