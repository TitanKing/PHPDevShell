<!DOCTYPE HTML>
<?php
	$this->notif->clear();
?>
<html lang="<?php $template->outputLanguage() ?>">
	<head>
		<title><?php echo ___('Error 404') ?></title>
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
                        <h1>404</h1>
                        <h2><?php echo ___('Requested page could not be found.') ?></h2>

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