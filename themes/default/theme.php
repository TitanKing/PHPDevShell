<!DOCTYPE html>
<html lang="<?php $template->outputLanguage() ?>">
    <head>
        <title><?php $template->outputTitle() ?></title>
        <?php require_once 'themes/default/include.php'; ?>
        <!-- Custom Head Added -->
        <?php $template->outputNotifications() ?>
        <?php $template->outputHead() ?>
        <?php $template->outputLoader() ?>
    </head>
    <!-- Main Body -->
    <body id="container">
        <div id="wrap">
            <?php require_once 'themes/default/nav.php' ?>
            <div id="notify" class="notifications"></div>
            <div id="bg" class="container-fluid">
                <!-- OUTPUT CONTROLLER AREA -->
                <?php $template->outputController() ?>
            </div>
        </div>
        <!-- FOOTER AREA -->
        <?php $template->outputDebugInfo() ?>
        <?php $template->outputFooterJS() ?>
    </body>
</html>