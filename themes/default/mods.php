<?php

class themeMods extends PHPDS_dependant
{

    public function loader()
	{
		return <<<HTML

        <script type="text/javascript">
            $(document).ready(function() {
                $('#bg').fadeIn('slow');
                $('#nav').click(function () {
                    $('#bg').fadeOut('fast', function () {
                        $('#loader').fadeIn('fast');
                    });
                });
                $('#bg').load(function () {
                    $('#loader').fadeOut();
                });
            });
            PHPDS_documentReady();
        </script>

HTML;
	}

	public function logo($url, $src, $alt, $title)
	{
		return <<<HTML

			<a href="{$url}" class="brand">
				<img src="{$src}" class="logo" alt="{$alt}" title="{$title}" />
			</a>

HTML;
	}

	public function logoText($title)
	{
		return <<<HTML

            <h1>{$title}</h1>

HTML;
	}

	public function title($title, $appname)
	{
		return "{$title} - {$appname}";
	}

	public function role($role, $name)
	{
		return "{$name}&nbsp;$role";
	}

	public function group($group, $name)
	{
		return "{$name}&nbsp;$group";
	}

	public function cssFileToHead($href, $media)
	{
        if (empty($href)) return '';
		return <<<HTML

            <link rel="stylesheet" href="{$href}" media="{$media}">

HTML;
	}

	public function jsFileToHead($src)
	{
        if (empty($src)) return '';
		return <<<HTML

            <script type="text/javascript" src="{$src}"></script>

HTML;
	}

	public function addToHead($head)
	{
        if (empty($head)) return '';
		return <<<HTML

			<!-- Dynamically Added to Head -->
			$head

HTML;
	}

	public function addJsToHead($js)
	{
        if (empty($js)) return '';
		return <<<HTML

            <script type="text/javascript">{$js}</script>

HTML;
	}

	public function addCssToHead($css)
	{
        if (empty($css)) return '';
		return <<<HTML

            <style>{$css}</style>

HTML;
	}

	public function iFrame($src, $height, $width)
	{
		$HTML = <<<HTML

			<iframe src="{$src}" name="iframe" height="{$height}" frameborder="0" scrolling="auto" width="{$width}" seamless></iframe>

HTML;
		return $HTML;
	}

	public function result($colspan, $td_content)
	{
		$HTML = <<<HTML

			<tr class="highlight">
				<td class="no_results" colspan="{$colspan}">
					{$td_content}
				</td>
			</tr>

HTML;
		return $HTML;
	}

	public function loginForm($action, $username_label, $password_label, $redirect_page, $lost_password, $lost_password_text, $not_registered_yet, $not_registered_yet_text, $remember, $security, $login_label, $user_name, $button_name='Submit')
	{
		if (!empty($remember)) {
			$remember = <<<HTML
				<label class="checkbox">
					<input tabindex="3" type="checkbox" name="user_remember" value="remember" title="{$lost_password_text}">
					{$remember}
				</label>

HTML;
		} else {
			$remember = '';
		}

		if (!empty($not_registered_yet)) {
			$not_registered_yet = '<a href="' . $not_registered_yet . '">' . $not_registered_yet_text . '</a>';
		} else {
			$not_registered_yet = '';
		}

		$HTML = <<<HTML
			<div class="form-actions">
				<form id="login" action="{$action}" method="post" class="validate">
					<fieldset>
						<legend>{$login_label}</legend>
						<p>
                            <label for="user_name">{$username_label}</label>
                            <input id="user_name" tabindex="1" type="text" size="20" name="user_name" value="{$user_name}" title="$username_label">
                        </p>
						<p>
                            <label for="password">{$password_label}</label>
                            <input id="password" tabindex="2" type="password" size="20" name="user_password" title="$password_label">
                        </p>
						{$redirect_page}
						<p>{$remember}</p>
						<p>
							<button type="submit" name="login" class="btn btn-primary">{$button_name}</button><br>
						</p>
						<p>
							<a href="{$lost_password}">{$lost_password_text}</a><br>
							{$not_registered_yet}
						</p>
						<input type="hidden" name="login" value="login">
						{$security}
					</fieldset>
				</form>
			</div>

HTML;
		return $HTML;
	}

    public function loggedInInfo ($name, $logouturl, $logoutname, $role, $group, $menu_data = null)
    {

        if (! empty($menu_data['user-preferences'])) {
            $p     = $menu_data['user-preferences'];
            $prefs = <<<HTML
                <a href="{$p['href']}" class="btn btn-primary options">{$p['menu_name']}</a>
HTML;
        }

            $logout = <<<HTML
                <a href="{$logouturl}" class="btn btn-danger options">{$logoutname}</a>
HTML;

        $HTML = <<<HTML
            <div id="logged-in-info" class="form-actions">
                <form>
                    <fieldset>
                        <legend>{$name}</legend>
                        <p>
                            {$prefs}
                            {$logout}
                        </p>
                        <div class="divider"></div>
                        <p>
                            <span class="label label-success">{$role}</span>
                            <span class="label label-info">{$group}</span>
                        </p>
                    </fieldset>
                </form>
            </div>
HTML;

        return $HTML;
    }

	public function heading($heading_text)
	{
		$HTML = "<h1>$heading_text</h1>";
		return $HTML;
	}

	public function info($text)
	{
		$HTML = <<<HTML

			<div class="alert alert-info fade in">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				{$text}
			</div>

HTML;
		return $HTML;
	}

	public function scriptIcon($src, $title)
	{
		$HTML = <<<HTML

			<img class="img" src="{$src}" title="{$title}" alt="{$title}" />

HTML;
		return $HTML;
	}

	public function scriptHead($text)
	{
		$HTML = "<h2>{$text}</h2>";
		return $HTML;
	}

	public function error($text)
	{
		$HTML = <<<HTML

			<div class="alert alert-error fade in">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				{$text}
			</div>

HTML;
		return $HTML;
	}

	public function warning($text)
	{
		return $this->error($text);
	}

	public function critical($text)
	{
		return $this->error($text);
	}

	public function notice($text)
	{
		$HTML = <<<HTML

			<div class="alert fade in">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				{$text}
			</div>

HTML;
		return $HTML;
	}

	public function busy($text)
	{
		return $this->notice($text);
	}

	public function message($text)
	{
		return $this->notice($text);
	}

	public function note($text)
	{
		return $this->notice($text);
	}

	public function ok($text)
	{
		$HTML = <<<HTML

			<div class="alert alert-success fade in">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				{$text}
			</div>

HTML;
		return $HTML;
	}

	public function icon($src, $class, $title)
	{
		$HTML = <<<HTML
			<img src="{$src}" class="{$class}" title="{$title}" alt="{$title}" />
HTML;
		return $HTML;
	}

	public function toolTip($text)
	{
		$HTML = <<<HTML
            <a href="#" rel="tooltip" title="{$text}"><i class="icon-question-sign"></i></a>
HTML;
		return $HTML;
	}

	public function noResults($text)
	{
		$HTML = <<<HTML

			<div id="norecords" class="ui-state-disabled ui-corner-all">
				{$text}
			</div>

HTML;
		return $HTML;
	}

	public function results($first_page, $rw, $previous_page, $currentPage_, $total_pages_, $current_records_, $totalRows_, $next_page, $ff, $last_page)
	{
		$HTML = <<<HTML

			<div id="pagination">
				<ul id="results" class="ui-widget">
					{$first_page}
					{$rw}
					{$previous_page}
					<li class="pages ui-state-default ui-corner-all"><span class="ui-icon ui-icon-clipboard left"></span>[{$currentPage_}/{$total_pages_}] - [{$current_records_}/{$totalRows_}]</li>
					{$next_page}
					{$ff}
					{$last_page}
				</ul>
			</div>

HTML;
		return $HTML;
	}

	public function search($action, $value, $class, $validate)
	{
		$HTML = <<<HTML

			<form action="{$action}" method="post">
				<div id="searchForm">
					<span class="ui-icon ui-icon-search left"></span><input id="search_field" type="text" size="40" name="search_field" value="{$value}" class="{$class}">
					<input type="hidden" value="Filter" name="search">
					{$validate}
				</div>
			</form>

HTML;
		return $HTML;
	}

	public function infoMark($icon, $text)
	{
		$HTML = <<<HTML

			<a href="#" class="toolpopup">
				{$icon}
				<span>{$text}</span>
			</a>

HTML;
		return $HTML;
	}

	public function logOutInfo($href, $text)
	{
		return <<<HTML

			<a href="{$href}">
				<div id="logged-in" class="loginlink">
					<span></span>{$text}
				</div>
			</a>
HTML;
	}

	public function logInInfo($href, $inoutpage)
	{
		return <<<HTML

			<a href="{$href}">
				<div id="logged-out" class="loginlink">
					<span></span>{$inoutpage}
				</div>
			</a>
HTML;
	}

	public function paginationOrder($order_url, $asc, $desc)
	{
		$HTML = <<<HTML

			<div class="order">
				<a href="{$order_url}&order=asc"><span class="asc {$asc}"></span></a>
				<a href="{$order_url}&order=desc"><span class="desc {$desc}"></span></a>
			</div>

HTML;
		return $HTML;
	}

	public function paginationTh($th_, $sort_html)
	{
		return "<th>{$th_}{$sort_html}</th>";
	}

	public function paginationNav($url, $class)
	{
		return <<<HTML

			<li class="paginationicon">
				<a href="{$url}"><span class="{$class}"></span></a>
			</li>

HTML;
	}

	public function paginationNavEmpty($class)
	{
		return <<<HTML

				<li class="paginationicondisabled">
					<span class="{$class}"></span>
				</li>

HTML;
	}

	public function activeName ($name)
	{
		return '<li class="active-name">' . $name . '</li>';
	}

	public function menuA($mr, $class='')
	{
		// Check if we have a place marker.
		if ($mr['menu_type'] == 6) {
			$noclick = 'onclick="return false;"';
			// Create URL.
			$url = "&#35;";
		} else {
			$noclick = '';
			// Last check if it is a link item that should be jumped to.
			if ($mr['menu_type'] == 5) {
				$url = $mr['menu_link'];
			} else {
				$url = $mr['href'];
			}
		}
		($mr['new_window'] == 1) ? $target = '_blank' : $target = '_self';
		$extra = ($class == 'nav-grand') ? 'data-toggle="dropdown" class="dropdown-toggle"' : '';

		return <<<HTML
				<a tabindex="-1" href="{$url}" target="{$target}" {$extra} {$noclick}>{$mr['menu_name']}</a>
HTML;
	}

	public function menuUlParent($tree)
	{
		return <<<HTML

			<ul class="dropdown-menu">
				{$tree}
			</ul>

HTML;
	}

	public function menuUlChild($tree)
	{
		return $tree;

		// You could also make a tree type menu, but remember users with touch screens will find it hard to navigate, they can't hover.
		/*
		return <<<HTML
				<ul class="dropdown-menu">
					{$tree}
				</ul>
HTML;
		*/
	}

	public function menuLiParent($tree, $link, $class, $menu_data = null)
	{
		$id = empty($menu_data['menu_id']) ? '' : ' id="menu_'.PU_safeName($menu_data['menu_id']).'"';
		return <<<HTML

			<li class="{$class} dropdown" {$id}>
				{$link}
					{$tree}
			</li>

HTML;
	}

	public function subMenuLiParent($tree, $link, $class, $menu_data = null)
	{
		$id = empty($menu_data['menu_id']) ? '' : ' id="menu_'.PU_safeName($menu_data['menu_id']).'"';

		return <<<HTML

			<li class="{$class} nav-header" {$id}>{$menu_data['menu_name']}</li>
			{$tree}

HTML;
		// You could also make a tree type menu, but remember users with touch screens will find it hard to navigate, they can't hover.
		/*
		return <<<HTML
				<li class="{$class} dropdown-submenu" {$id}>
					{$link}
						{$tree}
				</li>
HTML;
		*/
	}

	public function menuLiChild($link, $class, $menu_data = null)
	{
        /**
		 * Class types:
		 * current
		 * inactive
		 */
		$id = empty($menu_data['menu_id']) ? '' : ' id="menu_'.PU_safeName($menu_data['menu_id']).'"';
		return <<<HTML

			<li class="{$class}" {$id}>{$link}</li>

HTML;
	}

	public function subMenuLiChild($link, $class, $menu_data = null)
	{
		/**
		 * Class types:
		 * current
		 * inactive
		 */
		$id = empty($menu_data['menu_id']) ? '' : ' id="menu_'.PU_safeName($menu_data['menu_id']).'"';
		return <<<HTML

			<li class="{$class}" {$id}>{$link}</li>

HTML;
	}

	public function menuASubNav($mr)
	{
		return '<a href="' . $mr['href'] . '">' . $mr['menu_name'] . '</a>';
	}

	public function subNavMenuLi($link, $class, $menu_data = null)
	{
		/**
		 * Class types:
		 * active
		 * inactive
		 */
		$id = empty($menu_data['menu_id']) ? '' : ' id="menu_'.PU_safeName($menu_data['menu_id']).'"';
		return <<<HTML

			<li class="{$class}" {$id}>{$link}</li>

HTML;
	}

    public function altHome ($home)
    {
        if (! empty($home['href'])) {
            return <<<HTML
                <li class="backhome"><a href="{$home['href']}"><i class="icon-home icon-white"></i></a></li>
HTML;
        } else {
            return '';
        }
    }

    public function altNav ($nav)
    {
        if (! empty($nav['contact-admin'])) {
            return <<<HTML
                <li><a href="{$nav['contact-admin']['href']}"><i class="icon-envelope icon-white"></i></a></li>
HTML;
        } else {
            return '';
        }
    }

	public function menuRedirect($url, $time)
	{
		return <<<HTML

			<META HTTP-EQUIV="refresh" CONTENT="{$time}; URL={$url}" >

HTML;
	}

	public function debug($queries, $memory, $other='')
	{
		return <<<HTML

                <div id="debug">
                    <div class="container">
                        <p class="muted credit">(Queries Used : {$queries}) - (PHP Memory Used : {$memory} Mb) - (Page Load Time : {$other} ms)</p>
                    </div>
				</div>

HTML;
	}

	public function widget($widget_url, $element_id, $text='', $options = '')
	{

		$JS = <<<JS

					$(document).ready(function() {
						$.ajax({
						  url: "{$widget_url}",
						  dataType: 'html',
						  {$options}
						  data: "widget=true",
						  beforeSend: function() {
							 $("#{$element_id}").append('<img id="loading_{$element_id}" src="themes/cloud/images/widget-loader.gif" title="{$text}" alt="{$text}" />');
						  },
						  complete: function(){
							 $("#loading_{$element_id}").hide()
						  },
						  success: function(html){
							$("#{$element_id}").append(html).hide().fadeIn('slow');
						  }
						});
					});

JS;
		return $JS;
	}

	public function ajax($ajax_url, $element_id, $text='', $options = '')
	{

		$JS = <<<JS

					$(document).ready(function() {
						$.ajax({
						  url: "{$ajax_url}",
						  dataType: 'html',
						  {$options}
						  data: "ajax=true",
						  beforeSend: function() {
							 $("#{$element_id}").append('<img id="loading_{$element_id}" src="themes/cloud/images/ajax-loader.gif" title="{$text}" alt="{$text}" />');
						  },
						  complete: function(){
							 $("#loading_{$element_id}").hide()
						  },
						  success: function(html){
							$("#{$element_id}").append(html).hide().fadeIn('slow');
						  }
						});
					});

JS;
		return $JS;
	}

	public function lightBoxScript()
	{
		return "themes/cloud/js/fancybox/jquery.fancybox.pack.js";
	}

	public function lightBoxCss()
	{
		return "themes/cloud/js/fancybox/jquery.fancybox.css";
	}

	public function lightBox($element_id, $options = '')
	{

		$JS = <<<JS

				$(document).ready(function() {
					$("#{$element_id}").fancybox({
						{$options}
						'ajax' : { data: "lightbox=true" }
					});
				});

JS;
		return $JS;
	}

	public function errorField($errorExist)
	{
		$forError = '';
		$forOk = '';
		$message = '';
		$okClass = '';
		$errorClass = '';

		foreach ($errorExist as $error) {
			if (! empty($error['field'])) {
				$field = $error['field'];
			} else {
				$field = 'FORM';
				$error['type'] = 'errorElse';
			}
			if (empty($error['type'])) {
				$forOk .= "[name=\'{$field}\'],";
			} else if ($error['type'] == 'error') {
				$forError .= "[name=\'{$field}\'],";
				$forType = "[name=\'{$field}\']";
			} else if ($error['type'] == 'errorElse') {
				if ($field == 'FORM')
					$forType = "form";
				else
					$forType = $field;
			}

			if (!empty($error['message']) && !empty($error['type'])) {
				$errorStyling = '<div class="control-group error"><label class="control-label">' . $error['message'] . '</label></div>';
				$message .= "$('$errorStyling').insertBefore('{$forType}');";
			}
		}

		if (! empty($forOk)) {
			$forOk = rtrim($forOk,",");
			$okClass = '$("' . $forOk . '").addClass("ok-field");';
		}

		if (! empty($forError)) {
			$forError = rtrim($forError,",");
			$errorClass = '$("' . $forError . '").addClass("error-field");';
		}

		$JS = <<<JS

				$(document).ready(function() {
					{$okClass}
					{$errorClass}
					{$message}
				});

JS;
		return $JS;
	}

	public function formSelect($value, $name, $selected='')
	{
		if ($selected)
			$selected = 'selected';
		else
			$selected = '';

		$option = <<<HTML

			<option value="{$value}" {$selected}>{$name}</option>

HTML;
		return $option;
	}

	public function formCheckbox($name, $value, $label, $checked='')
	{
		if ($checked)
			$checked = 'checked';
		else
			$checked = '';

		$checkbox = <<<HTML

			<label>
				<input type="checkbox" name="{$name}" value="{$value}" {$checked} title="{$label}">{$label}
			</label><br>

HTML;
		return $checkbox;
	}

	public function formRadio($name, $value, $label, $checked='')
	{
		if ($checked)
			$checked = 'checked';
		else
			$checked = '';

		$checkbox = <<<HTML

			<label>
				<input type="radio" name="{$name}" value="{$value}" {$checked} title="{$label}">{$label}
			</label><br>

HTML;
		return $checkbox;
	}

    public function securityToken($token)
	{
		return '<input type="hidden" name="token_validation" value="' . $token . '">';
	}

	public function searchToken($search_token)
	{
		return '<input type="hidden" name="search_token_validation" value="' . $search_token . '">';

	}

	public function botBlockFields($fakefieldname)
	{
        // This string CANT be nicely formatted as it will give an error with jqeury appen in botBlockSectret.
		$fakefields = '<div style="display: none;"><input type="text" name="text_' . $fakefieldname . '" value="" title="' . $fakefieldname . '"><input type="checkbox" name="check_' . $fakefieldname . '" title="' . $fakefieldname . '"></div>';

		return $fakefields;
	}

	public function botBlockSecret($secret_field, $dom='form')
	{

		$fakefields = <<<HTML

			$(document).ready(function() {
				$("{$dom}").append('{$secret_field}');
			});

HTML;

		return $fakefields;
	}

	public function stylePagination()
	{
		$H = <<<HTML

HTML;
		return $H;
	}

	public function styleTables()
	{
		$H = <<<HTML

HTML;
		return $H;
	}

	public function styleFloatHeadersScript()
	{
		return "themes/cloud/js/floatheader/jquery.floatheader.js";
	}

	public function styleFloatHeaders()
	{
		$H = <<<HTML

			$(document).ready(function() {
				$("table.floatHeader").floatHeader();
			});

HTML;
		return $H;
	}

	public function formsValidateJs()
	{
		return '';
	}

	public function formsValidate()
	{
		return '';
	}

	public function styleForms()
	{
		return '';
	}

	public function styleButtons()
	{
		return '';
	}

	public function jqueryEffect($plugin)
	{
		return "themes/default/jquery/js/jquery.effects.$plugin.min.js";
	}

	public function jqueryUI($plugin)
	{
		return "themes/default/jquery/js/jquery.ui.$plugin.min.js";
	}

	public function notificationsJs()
	{
		return '';
	}

	public function notifications($title, $message, $type='info')
	{

		$notify_type = 'info';
        $fadeout = '';

		switch ($type) {
			// notice
			case 'ok':
                $notify_type = 'alert-success';
                $fadeout = 0;
			break;

			// info
			case 'info':
                $notify_type = 'alert-info';
                $fadeout = 0;
            break;

			// error
			case 'warning':
			case 'critical':
				$notify_type = 'alert-error';
                $fadeout = 0;
			break;

        	default:
				$notify_type = 'alert-notice';
                $fadeout = 8000;
			break;
		}

        if (! empty($fadeout)) {
            $fadeout = "$('.{$notify_type}').delay({$fadeout}).fadeOut('slow')";
        } else {
            $fadeout = "";
        }

		// to prevent js errors we need to remove line breaks.
		$message = str_replace(array("\r", "\n"), '', $message);


		$H = <<<HTML

            $(document).ready(function() {
                $('#notify').append('<div class="alert {$notify_type} fade in"><button type="button" class="close" data-dismiss="alert">&times;</button><h4 class="alert-heading">{$title}</h4>{$message}</div>');
                {$fadeout}
            });

HTML;

		return $H;
	}

	public function formatTimeDate($time_stamp, $format_type_or_custom = 'default', $custom_timezone = false)
	{
		return $this->core->formatTimeDate($time_stamp, $format_type_or_custom, $custom_timezone);
	}

	public function styleSelectJs()
    {
		return '';
    }

    public function styleSelectHeader()
    {
		$H = <<<HTML

HTML;
		return $H;
    }

	// end of mod class
}
