<!DOCTYPE HTML>
<?php
	$this->notif->clear();
?>
<html lang="<?php $template->outputLanguage() ?>">
	<head>
		<title><?php echo ___('Error 418') ?></title>
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
            </div>
        </div>
        <!-- FOOTER AREA -->
        <?php $template->outputDebugInfo() ?>
        <?php $template->outputLoader() ?>
        <?php $template->outputFooterJS() ?>
    </body>
</html>