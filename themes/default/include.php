<?php
(! empty($aurl)) ? $url = $aurl : $url = $template->outputAbsoluteURL('return');
if (empty($skin)) $skin = $template->outputSkin('return');
?>
        <link rel="stylesheet" href="<?php echo $url ?>/themes/default/bootstrap/css/bootstrap.css?v=4.0.0" type="text/css">
        <link rel="stylesheet" href="<?php echo $url ?>/themes/default/bootstrap/css/bootstrap-responsive.css?v=4.0.0" type="text/css">
        <link rel="stylesheet" href="<?php echo $url ?>/themes/default/css/default.css?v=400" type="text/css">
        <script type="text/javascript" src="<?php echo $url ?>/themes/default/jquery/js/jquery.js?v=1.8.3"></script>
        <script type="text/javascript" src="<?php echo $url ?>/themes/default/js/default.js?v=4.0.0"></script>
        <script type="text/javascript" src="<?php echo $url ?>/themes/default/bootstrap/js/bootstrap.js?v=2.2.2"></script>