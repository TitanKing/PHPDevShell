<!DOCTYPE HTML>
<?php
	$this->notif->clear();
?>
<html lang="<?php $template->outputLanguage() ?>">
	<head>
		<title><?php echo ___('Error 403') ?></title>
        <?php require_once 'themes/default/include.php'; ?>
        <!-- Custom Head Added -->
        <?php $template->outputHead() ?>
	</head>
    <!-- Main Body -->
    <body id="container">
        <div id="wrap">
            <?php require_once 'themes/default/nav.php' ?>
            <div id="bg" class="container-fluid">
                <!-- OUTPUT CONTROLLER AREA -->
                <div class="row">
                    <div class="span12">
                        <h1>403</h1>
                        <h2><?php echo ___('You are not authorized to view this page.') ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <!-- FOOTER AREA -->
        <?php $template->outputDebugInfo() ?>
        <?php $template->outputLoader() ?>
        <?php $template->outputFooterJS() ?>
    </body>
</html>