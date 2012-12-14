<!DOCTYPE HTML>
<?php
	$this->notif->clear();
?>
<html lang="<?php $template->outputLanguage() ?>">
	<head>
		<title><?php echo ___('Error 418') ?></title>
		<meta charset=<?php $template->outputCharset() ?>>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="keywords" content="<?php $template->outputMetaKeywords() ?>">
		<meta name="description" content="<?php $template->outputMetaDescription() ?>">
		<?php require_once 'themes/default/include.php'; ?>
		<!-- Custom Head Added -->
		<?php $template->outputHead(); ?>
	</head>
	<!-- PHPDevShell Main Body -->
	<body id="container" class="ui-widget ui-widget-content" style="display: none;">
		<header>
			<!-- LOGO & LOGIN DETAILS -->
			<div id="logo">
				<?php $template->outputLogo() ?>
			</div>
		</header>
		<nav>
			<!-- BREADCRUMB MENU AREA -->
			<div id="history">
				<ul id="bread">
					<?php $template->outputBreadcrumbs() ?>
				</ul>
			</div>
		</nav>
		<div class="center">
			<!-- OUTPUT CONTROLLER AREA -->
			<div class="pagenotfound">
				<h1>418</h1>
				<h2><?php echo ___('I\'m a teapot and you might be a spambot.') ?></h2>
				<p><?php echo ___('If you are a human and not a spambot, then we are very sorry, please try giving it some time before submitting same form again.') ?></p>
				<?php
					(! empty($this->core->haltController['message'])) ? $message = $this->core->haltController['message'] : $message = '';
				?>
				<p>Error : <?php echo $message ?></p>
				<p><a href="<?php echo $_SERVER['REQUEST_URI'] ?>"><strong>[ Retry ]</strong></a></p>
			</div>
		</div>
	</body>
</html>