<?php

/**
 * This page will be displayed whenever an unhandled error or exception occurs in PHPDevShell
 */
	$skin = empty($this->configuration['skin']) ? '': $this->configuration['skin'];
	$navigation = $this->navigation;
?>
<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>Serious Error Encountered</title>
		<meta charset=UTF-8>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="keywords" content="critical, error">
		<meta name="description" content="We encountered an error">
		<link rel="stylesheet" href="<?php echo $aurl ?>/themes/cloud/css/reset.css" type="text/css" media="screen, projection" />
		<link rel="stylesheet" href="<?php echo $aurl ?>/themes/cloud/jquery/css/ui-lightness/jquery-ui.css?v314a" type="text/css" media="screen, projection" />
		<link rel="stylesheet" href="<?php echo $aurl ?>/themes/cloud/css/combined.css?v=314a" type="text/css" media="screen, projection" />
		<script type="text/javascript" src="<?php echo $aurl ?>/themes/cloud/js/combined-min.js?v=314a"></script>
		<script type="text/javascript" src="<?php echo $aurl ?>/themes/cloud/js/showhide/jquery.showhide.js"></script>
		<style>
			#backtrace td, #backtrace th{
				border: none;
			}
			.bt-line-number {
				color: #F93;
			}
			.causes_list {
				list-style-type: square;
			}
			H1 {
				font-size: 3em !important;
				text-align: center;
			}
			H2 {
				font-size: 2em !important;
			}
			H3 {
				font-size: 1.5em !important;
			}
			#support {
				position: absolute;
				right: 10px;
				top: 10px;
				font-size: 120%;
				text-align: center;
			}
			a:link {
				text-decoration: underline !important;
			}
		</style>
	</head>
	<!-- PHPDevShell Main Body -->
	<body class="ui-widget">
		<?php if (!empty($message)) {
			?>
			<h1>An error occured</h1>
			<p class="note">This page will try to provide as much information as possible so you can track down (and hopefully fix) the problem.</p>
			<?php
				if (is_a($e, 'PHPDS_exception')) {
					if ($e->hasCauses()) {
						@list($msg, $causes, $extra_html) = new PHPDS_array($e->getCauses());
						?>

						<article class="ui-widget-content ui-corner-all" style="margin:2em; padding:2em;">
						<h3 class="warning"><?php  echo $msg?></h3>
						<p>Possible causes are:</p>
						<ul id="causes_list">
						<?php
							foreach($causes as $cause) {
								list($title, $text) = $cause;
								echo "<li><strong>$title</strong><br />$text</li>";
							}
							if ($extra_html) echo $extra_html;
						?>
						</ul>
						</article>
						<?php
					}
					if ($e->hasMoreInfo()) {
						?>
						<article class="ui-widget-content ui-corner-all" style="margin:2em; padding:2em;">
						<h3 class="warning">More information</h3>
						<p><?php echo $e->getMoreInfo() ?></p>
						</article>
						<?php
					}
				}

				$config = $this->configuration;
			?>
		<article class="ui-widget-content ui-corner-all" style="margin:2em; padding:2em;">

			<div style="display: none" >
				<p class="warning">WARNING! several errors were caught in the Exception Handler itself:</p>
				<blockquote>
					<pre id="crumbs"><crumbs/></pre>
				</blockquote>
				<script>
					$(function(){
						var crumbs = $('#crumbs');
						if (crumbs.html()) crumbs.parents('DIV').show();
					});
				</script>
			</div>

			<h2>The error</h2>

			<p>The error class is "<tt><?php echo get_class($e) ?>"</tt> and the error code is <?php echo $code ?>.</p>

			<p>The message of the error is as follow:</p>
			<blockquote>
			<p class="critical"><?php echo $message; ?></p>
			<p><?php echo $extendedMessage; ?></p>
			</blockquote>

			<p>The error occured on <?php echo date('Y-M-d') ?> at <?php echo date('H:s') ?>.
			<p>
			<?php
			if (!empty($config['m'])) {
				echo 'The current menu id is '.$config['m'];
				echo '. <a href="'.$navigation->buildURL(3440897808, 'em='.$config['m']).'">'._('Edit this menu').'</a>';
			} else {
				echo 'It happened outside a menu.';
			}
			?>
			</p>
			</p>

			<p>The error <em><strong>actually</strong></em> occurred in file <strong><tt><?php echo $filepath?></tt></strong> at line <strong><?php echo $lineno?></strong> (see the <a href="#backtrace">backtrace</a>)</p>
			<blockquote>
			<p><?php echo $filefragment?></p>
			</blockquote>
			<?php if ($ignore >= 0) { ?>
				<p>The origin of the error is <em><strong>probably</strong></em> in file <strong><tt><?php echo $frame['file']?></tt></strong> at line <strong><?php echo $frame['line']?></strong></p>
				<blockquote>
				<p><?php echo $framefragment?></p>
				</blockquote>
			<?php }?>

			<h2><a name="backtrace">The Backtrace:</a></h2>
			<p>All relative file pathes are relative to the server root (namely <tt><?php echo $_SERVER['DOCUMENT_ROOT']; ?>/ )</tt></p>
			<p>Click on the Book icon to access online documentation.</p>
			<table id="backtrace">
				<thead>
					<tr>
						<th>File path (relative)</th>
						<th>Line</th>
						<th>Call (with arguments)</th>
					</tr>
				</thead>
				<tbody>
					<?php echo $bt; ?>
				</tbody>
			</table>


			<h2>The configuration</h2>
			<div class="info">
				<h3>Configuration files actually used:</h3>
				<?php echo $conf['used']; ?>
				<h3>Configuration files which would have been used if they were present:</h3>
				<?php echo $conf['missing']; ?>
				<h3>Main database info</h3>
				<?php
					$db_settings = PU_GetDBSettings($config);
					echo "<p>Database <i>{$db_settings['database']}</i> with prefix <i>{$db_settings['prefix']}</i> on host <i>{$db_settings['host']}</i> with user <i>{$db_settings['username']}</i>.</p>";
				?>
				<h3>Other useful configuration settings</h3>
				<?php
					echo '<p>Sef URL is <i>'.($config['sef_url'] ? 'on' : 'off').'</i> ; default template is '.($config['default_template'] ? '<i>'.$config['default_template'].'</i>' : '<b>not set</b>').'.</p>';
					echo '<p>Guest role  is '.(empty($config['guest_role'])  ? '<b>not set</b>' : '<i>'.$config['guest_role'].'</i>').' ; guest group is '.(empty($config['guest_group'])  ? '<b>not set</b>' : '<i>'.$config['guest_group'].'</i>').'.</p>';
					list($plugin, $menu_link) = $navigation->menuPath();
					echo empty($plugin) ? '<p>No plugin currently selected</p>' : '<p>Current plugin is <strong>'.$plugin.'</strong> (path is '.$menu_link.')</p>';
					echo '<p>BASEPATH is "<tt>'.BASEPATH.'</tt>" - Current working directory is "<tt>'.getcwd().'</tt>".</p>';
				?>
			</div>

			<h2>Class Registry</h2>
			<div class="info">
				<?php
					echo PU_dumpArray($this->classFactory->PluginClasses);
				?>
			</div>

			<h2>PHP info</h2>
			<blockquote>
			<div style="overflow: hidden;">
			<?php phpinfo(); ?>
			</div>
			</blockquote>

		</article>
		<div id="support" class="critical">
			Need support?<br>
			The <a href="http://www.phpdevshell.org/support" target="www.phpdevshell.org">support page</a> of our website is here for you.
		</div>
		<?php } else { ?>
		<article class="ui-widget-content ui-corner-all" style="margin:2em; padding:2em;">
			<h1 class="ui-state-error ui-corner-all">An error has occured...</h1>
			<p>An error has occurred while trying to provide you with the requested resource.</p>
			<p>The site administrator have been informed and will fix the problem as soon as possible.</p>
			<p>Sorry for the inconvenience, please come back later...</p>
		</article>
		<?php }?>
	</body>
</html>
