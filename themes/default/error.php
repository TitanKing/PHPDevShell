<?php
/**
 * This page will be displayed whenever an unhandled error or exception occurs in PHPDevShell
 */
$skin = empty($this->configuration['skin']) ? '': $this->configuration['skin'];
$navigation = $this->navigation;
$template = $this->template;
?>
<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>Internal System Error</title>
        <meta charset=UTF-8>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="critical, error">
		<meta name="description" content="We encountered an error">
        <link rel="stylesheet" href="<?php echo $aurl ?>/themes/default/bootstrap/css/bootstrap.css?v=4.0.0" type="text/css">
        <link rel="stylesheet" href="<?php echo $aurl ?>/themes/default/bootstrap/css/bootstrap-responsive.css?v=4.0.0" type="text/css">
        <link rel="stylesheet" href="<?php echo $aurl ?>/themes/default/prettify/prettify.css?v=100" type="text/css">
        <link rel="stylesheet" href="<?php echo $aurl ?>/themes/default/css/default.css?v=400" type="text/css">
        <script type="text/javascript" src="<?php echo $aurl ?>/themes/default/jquery/js/jquery-min.js?v=1.9.1"></script>
        <script type="text/javascript" src="<?php echo $aurl ?>/themes/default/js/default.js?v=4.0.0"></script>
        <script type="text/javascript" src="<?php echo $aurl ?>/themes/default/bootstrap/js/bootstrap.js?v=2.2.2"></script>
        <script type="text/javascript" src="<?php echo $aurl ?>/themes/default/prettify/prettify.js?v=100"></script>
	</head>
	<!-- PHPDevShell Main Body -->
	<body id="container" onload="prettyPrint()">
        <div id="wrap">
            <div id="bg" class="container-fluid">
                <h1>Internal System Error</h1>
                <?php if (!empty($message)) {
                    if (is_a($e, 'PHPDS_exception')) {
                        if ($e->hasCauses()) {
                            @list($msg, $causes, $extra_html) = new PHPDS_array($e->getCauses());
                            ?>
                            <div class="alert"><?php  echo $msg?></div>
                            <h3>Possible causes are:</h3>
                            <dl>
                            <?php
                                foreach($causes as $cause) {
                                    list($title, $text) = $cause;
                                    echo "<dt><strong>$title</strong></dt>";
                                    echo "<dd><i class=icon-chevron-right></i> $text</dd>";
                                }
                                if ($extra_html) echo $extra_html;
                            ?>
                            </dl>
                            <?php
                        }
                    }
                    $config = $this->configuration;
                ?>
                <div class="alert alert-error">
                    <div class="pull-right"><strong><?php echo date('d M Y') ?> <?php echo date('H:i a') ?></strong></div>
                    <?php
                    if (!empty($config['m'])) {
                        echo "Executing <strong>{$config['m']}</strong>" . " invoked class <em>" . get_class($e) . "</em> with code $code";
                    } else {
                        echo '<strong>Internal Error</strong>';
                    }
                    ?>

                    <div class="error-split-line"></div>
                    <?php echo $message; ?>

                    <?php
                    if (! empty($extendedMessage)) {
                        echo "$extendedMessage";
                    }
                    ?>
                </div>
                <?php if (! empty($filefragment)) { ?>
                <p class="text-warning">
                    The error <em><strong>actually</strong></em> occurred in <strong><em><?php echo $filepath?></em></strong> at line <strong><?php echo $lineno?></strong> (see the <a href="#backtrace">backtrace</a>)
                </p>
                <pre><?php echo $filefragment ?></pre>
                <?php } ?>

                <?php if ($ignore >= 0) { ?>
                <p>The origin of the error is <em><strong>probably</strong></em> in file <strong><em><?php echo $frame['file']?></em></strong> at line <strong><?php echo $frame['line']?></strong></p>

                <?php if (! empty($framefragment)) { ?>
                <pre><?php echo $framefragment?></pre>
                <?php } ?>

                <?php }?>
                <h3><a name="backtrace">The Backtrace:</a></h3>
                <p>All relative file paths are relative to the server root namely <em><?php echo $_SERVER['DOCUMENT_ROOT']; ?>/</em></p>
                <table id="backtrace" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>File path (relative)</th>
                            <th>Line</th>
                            <th>Call (with arguments)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $bt; ?>
                    </tbody>
                </table>
                <?php if (! empty($conf)) { ?>
                <h3>Active Configuration</h3>
                <div class="well">
                    <h4>Configuration files used:</h4>
                    <?php echo $conf['used']; ?>
                    <h4>Configuration files which would have been used if they were present:</h4>
                    <?php echo $conf['missing']; ?>
                    <h4>Main database info</h4>
                    <?php
                        $db_settings = PU_GetDBSettings($config);
                        echo "<p>Database <em>{$db_settings['database']}</em> with prefix <em>{$db_settings['prefix']}</em> on host <em>{$db_settings['host']}</em> with user <em>{$db_settings['username']}</em>.</p>";
                    ?>
                    <h4>Other useful configuration settings</h4>
                    <dl>
                    <?php
                        echo "<dt>Menu ID</dt>";
                        echo "<dd><code>" . (empty($config['m']) ? 'unknown' : $config['m']) . "</code></dd>";
                        echo "<dt>SEF URL</dt>";
                        echo "<dd><code>" . (empty($config['sef_url']) ? 'off' : 'on') . "</code></dd>";
                        echo "<dt>Default template</dt>";
                        echo "<dd><code>" . (empty($config['default_template']) ? 'not set' : $config['default_template']) . "</code></dd>";
                        echo "<dt>Guest role</dt>";
                        echo "<dd><code>" . (empty($config['guest_role'])  ? 'not set' : $config['guest_role']) . "</code></dd>";
                        echo "<dt>Guest group</dt>";
                        echo "<dd><code>" . (empty($config['guest_group'])  ? 'not set' : $config['guest_group'])  . "</code></dd>";
                        list($plugin, $menu_link) = $navigation->menuPath();
                        echo "<dt>Active plugin</dt>";
                        $menu_link_ = (empty($menu_link)) ? '' : ' (path is ' . $menu_link . ')';
                        echo "<dd><code>" . (empty($plugin) ? 'not set' : $plugin . $menu_link_) . "</code></dd>";
                        echo "<dt>Basepath</dt>";
                        echo "<dd><code>" . BASEPATH . "</code></dd>";
                        echo "<dt>Working directory</dt>";
                        echo "<dd><code>" . getcwd() . "</code></dd>";
                    ?>
                    </dl>
                </div>
                <div>
                    <h3>Variable Registry</h3>
                    <pre><?php echo PU_dumpArray($this->classFactory->PluginClasses); ?></pre>
                </div>
                <?php
                    echo PHPDS_backtrace::phpInfo();
                ?>
                <?php } ?>
                <div class="alert alert-success">
                    <strong>End of Report</strong><br>
                    PHP <?php echo phpversion(); ?>
                </div>
            <?php } else { ?>
                    <p class="lead">
                        An error has occurred while trying to provide you with the requested resource. The site administrator has been informed and will take the appropriate action.</p>
                    <p><a href="<?php echo $aurl ?>" class="btn btn-inverse"><i class="icon-home icon-white"></i> Home</a></p>
                </div>
            <?php } ?>
            </div>
        </div>
	</body>
</html>
