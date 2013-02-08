<!DOCTYPE html>
<html lang="<?php $template->outputLanguage() ?>">
    <head>
        <title><?php $template->outputTitle() ?></title>
        <?php require_once 'themes/default/include.php'; ?>
        <!-- Custom Head Added -->
        <?php $template->outputNotifications() ?>
        <?php $template->outputHead() ?>
    </head>
    <!-- Main Body -->
    <body id="container">
        <div id="wrap">
            <?php require_once 'themes/default/nav.php' ?>
            <div id="notify" class="notifications"></div>
            <div id="bg" class="container-fluid">
                <!-- OUTPUT CONTROLLER AREA -->
                <div class="row">
                    <div class="span12">
                        <?php $template->loginFormHeading() ?>
                        <?php $template->loginForm() ?>
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