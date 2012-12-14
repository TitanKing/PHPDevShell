<!DOCTYPE HTML>
<html lang="<?php $template->outputLanguage() ?>">
	<head>
		<title><?php $template->outputTitle() ?></title>
		<meta charset=<?php $template->outputCharset() ?>>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="keywords" content="<?php $template->outputMetaKeywords() ?>">
		<meta name="description" content="<?php $template->outputMetaDescription() ?>">
		<?php require_once 'themes/cloud/include.php'; ?>
		<?php $template->outputNotifications() ?>
		<!-- Custom Head Added -->
		<?php $template->outputHead(); ?>
		<script type="text/javascript"><?php echo $template->mod->styleForms() ?></script>
		<script type="text/javascript"><?php echo $template->mod->styleButtons() ?></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('body').fadeIn('fast');
				PHPDS_documentReady();
			});
		</script>
	</head>
	<!-- Main Body -->
	<body id="container" class="ui-widget ui-widget-content" style="<?php echo $configuration['elegant_loading'] ?>">
		<header>
			<!-- LOGO & LOGIN DETAILS -->
			<div id="logo">
				<?php $template->outputLogo() ?>
			</div>
			<div id="info">
				<div id="logindetail">
					<?php $template->outputLoginLink() ?>
				</div>
				<div id="datetime">
					<?php $template->outputTime() ?>
				</div>
			</div>
		</header>
		<nav>
			<div id="menu">
				<!-- MENU AREA -->
				<ul id="nav">
					<?php $template->outputMenu() ?>
				</ul>
			</div>
			<!-- BREADCRUMB MENU AREA -->
			<div id="scripticon">
				<?php $template->outputScriptIcon() ?>
			</div>
			<div id="history">
				<ul id="bread">
					<?php $template->outputBreadcrumbs() ?>
				</ul>
			</div>
		</nav>
		<div id="bg">
			<!-- OUTPUT CONTROLLER AREA -->
			<?php $template->loginFormHeading() ?>
			<?php $template->loginForm() ?>
		</div>
		<!-- FOOTER AREA -->
		<footer class="ui-state-disabled">
			<div id="footername">
				<?php $template->outputTextLogo() ?>
			</div>
			<div id="footernotes">
				<?php $template->outputFooter() ?>
			</div>
		</footer>
	</body>
</html>