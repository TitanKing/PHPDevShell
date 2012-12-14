<!DOCTYPE html>
<html lang="<?php $template->outputLanguage() ?>">
    <head>
        <title><?php $template->outputTitle() ?></title>
        <meta charset=<?php $template->outputCharset() ?>>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="<?php $template->outputMetaKeywords() ?>">
        <meta name="description" content="<?php $template->outputMetaDescription() ?>">
        <?php require_once 'themes/default/include.php'; ?>
        <!-- Custom Head Added -->
        <?php $template->outputNotifications() ?>
        <?php $template->outputHead() ?>
    </head>
    <!-- Main Body -->
    <body id="container">
        <div id="wrap">
            <nav>
                <div id="menu" class="navbar navbar-static-top navbar-inverse">
                    <div class="navbar-inner">
                        <!-- MENU AREA -->
                        <div class="nav-collapse">
                            <ul id="nav" class="nav">
                                <?php $template->outputMenu() ?>
                            </ul>
                            <ul class="nav pull-right">
                                <?php $template->outputAltNav() ?>
                                <li class="dropdown">
                                    <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                                        <i class="icon-user icon-white"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <?php $template->outputLogin() ?>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- BREADCRUMB MENU AREA -->
                <div id="history" class="navbar navbar-static-top">
                    <div class="navbar-inner">
                        <div id="subnav-history">
                            <div class="nav-collapse">
                                <ul class="nav">
                                    <?php $template->outputAltHome() ?>
                                    <?php $template->outputName() ?>
                                </ul>
                                <ul class="nav pull-right">
                                    <?php $template->outputSubnav() ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
            <div id="loader" class="container" style="<?php echo $configuration['elegant_loading'] ?>">
                <p id="wrap-loader">
                    <img src="http://jason/projects/phpdev/themes/default/images/loader.gif" title="Loading..." />
                </p>
            </div>
            <div id="notify" class="notifications"></div>
            <div id="bg" class="container" style="<?php echo $configuration['elegant_loading'] ?>">
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