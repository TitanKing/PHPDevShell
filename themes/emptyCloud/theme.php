<!DOCTYPE HTML>
<html lang="<?php $template->outputLanguage() ?>">
	<head>
		<title><?php $template->outputTitle() ?></title>
		<meta charset=<?php $template->outputCharset() ?>>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta name="keywords" content="<?php $template->outputMetaKeywords() ?>" />
		<meta name="description" content="<?php $template->outputMetaDescription() ?>" />
		<?php require_once 'themes/cloud/include.php'; ?>
		<!-- Custom Head Added -->
		<?php $template->outputHead(); ?>
	</head>
	<!-- PHPDevShell Main Body -->
	<body id="container" class="ui-widget ui-widget-content">
		<!-- OUTPUT CONTROLLER AREA -->
		<div id="bg">
			<!-- OUTPUT CONTROLLER AREA -->
			<?php $template->outputController() ?>
		</div>
	</body>
</html>