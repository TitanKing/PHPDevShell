
<?php
(! empty($aurl)) ? $url = $aurl : $url = $template->outputAbsoluteURL('return');
if (empty($skin)) $skin = $template->outputSkin('return');

if ($this->configuration['development']) {
?>

<link rel="stylesheet" href="<?php echo $url ?>/themes/cloud/css/reset.css" type="text/css" media="screen, projection" />
<link rel="stylesheet" href="<?php echo $url ?>/themes/cloud/jquery/css/<?php echo $skin; ?>/jquery-ui.css?v314a" type="text/css" media="screen, projection" />
<link rel="stylesheet" href="<?php echo $url ?>/themes/cloud/css/combined.css?v=314a" type="text/css" media="screen, projection" />
<script type="text/javascript" src="<?php echo $url ?>/themes/cloud/jquery/js/jquery.js?v=314a"></script>
<script type="text/javascript" src="<?php echo $url ?>/themes/cloud/jquery/js/jquery-ui.js?v=314a"></script>

<script type="text/javascript" src="<?php echo $url ?>/themes/cloud/js/medialize-URI.js/src/IPv6.js"></script>
<script type="text/javascript" src="<?php echo $url ?>/themes/cloud/js/medialize-URI.js/src/punycode.js"></script>
<script type="text/javascript" src="<?php echo $url ?>/themes/cloud/js/medialize-URI.js/src/SecondLevelDomains.js"></script>
<script type="text/javascript" src="<?php echo $url ?>/themes/cloud/js/medialize-URI.js/src/URI.js"></script>
<script type="text/javascript" src="<?php echo $url ?>/themes/cloud/js/medialize-URI.js/src/URITemplate.js"></script>

<script type="text/javascript" src="<?php echo $url ?>/themes/cloud/js/PHPDS.js?v=314a"></script>

<?php } else { ?>

<link rel="stylesheet" href="<?php echo $url ?>/themes/cloud/css/reset.min.css" type="text/css" media="screen, projection" />
<link rel="stylesheet" href="<?php echo $url ?>/themes/cloud/jquery/css/<?php echo $skin; ?>/jquery-ui.css?v314a" type="text/css" media="screen, projection" />
<link rel="stylesheet" href="<?php echo $url ?>/themes/cloud/css/combined.css?v=314a" type="text/css" media="screen, projection" />
<script type="text/javascript" src="<?php echo $url ?>/themes/cloud/js/combined-min.js?v=314a"></script>

<?php } ?>


