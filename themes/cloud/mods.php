<?php

class themeMods extends PHPDS_dependant
{

	public function logo($url, $src, $alt, $title)
	{
		return "
			<a href=\"{$url}\">
				<img src=\"{$src}\" class=\"logo\" alt=\"{$alt}\" title=\"{$title}\">
			</a>

		";
	}

	public function logoText($title)
	{
		return '<h1>' . $title . '</h1>
			';
	}

	public function title($title, $appname)
	{
		return "{$title} - {$appname}";
	}

	public function role($role, $name)
	{
		return "<strong class=\"ui-state-error-text\">{$name}&#44;&nbsp;</strong>";
	}

	public function group($group, $name)
	{
		return "<strong>{$name}&#44;&nbsp;</strong>";
	}

	public function cssFileToHead($href, $media)
	{
		return '<link rel="stylesheet" href="' . $href . '" media="' . $media . '" />
			';
	}

	public function jsFileToHead($src)
	{
		return '<script type="text/javascript" src="' . $src . '"></script>
			';
	}

	public function addToHead($head)
	{
		return "<!-- Dynamically Added to Head -->
				$head

				";
	}

	public function addJsToHead($js)
	{
		return '<script type="text/javascript">' . $js . '</script>
			';
	}

	public function addCssToHead($css)
	{
		return '<style>' . $css . '</style>
			';
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

	public function loginForm($action, $username_label, $password_label, $redirect_page, $lost_password, $lost_password_text, $not_registered_yet, $not_registered_yet_text, $remember, $security, $login_label, $user_name)
	{
		if (!empty($remember)) {
			$remember = '<label>' . $remember . '<input tabindex="3" type="checkbox" name="user_remember" value="remember" title="' . $lost_password_text . '"></label>';
		} else {
			$remember = '';
		}

		if (!empty($not_registered_yet)) {
			$not_registered_yet = '<a href="' . $not_registered_yet . '">' . $not_registered_yet_text . '</a>';
		} else {
			$not_registered_yet = '';
		}

		$HTML = <<<HTML
			<form id="login" action="{$action}" method="post" class="validate">
					<div class="row">
							<div class="column grid_4">
								<fieldset>
									<legend>{$login_label}</legend>
									<p><label>{$username_label}<input tabindex="1" type="text" size="20" name="user_name" value="{$user_name}" title="$username_label"></label></p>
									<p><label>{$password_label}<input tabindex="2" type="password" size="20" name="user_password" title="$password_label"></label></p>
									{$redirect_page}
									<p>{$remember}</p>
									<p>
										<button type="submit" name="login"><span class="ui-icon ui-icon-key left"></span><span>{$login_label}</span></button>
										<a href="{$lost_password}">{$lost_password_text}</a>
										{$not_registered_yet}
									</p>
									<input type="hidden" name="login" value="login">
									{$security}
								</fieldset>
							</div>
					</div>
			</form>

HTML;
		return $HTML;
	}

	public function heading($heading_text)
	{
		$HTML = <<<HTML
			<div id="heading" class="ui-widget-header ui-corner-all">$heading_text</div>

HTML;
		return $HTML;
	}

	public function info($text)
	{
		$HTML = <<<HTML

			<div class="ui-corner-all info">
				<span class="ui-icon ui-icon-info right"></span>
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
		$HTML = <<<HTML
			<div class="ui-state-default ui-corner-all scripthead">
				<span class="ui-icon ui-icon-tag left"></span>
				{$text}
			</div>

HTML;
		return $HTML;
	}

	public function error($text)
	{
		$HTML = <<<HTML

			<div class="ui-corner-all error">
				<span class="ui-icon ui-icon-circle-close right"></span>
				{$text}
			</div>

HTML;
		return $HTML;
	}

	public function warning($text)
	{
		$HTML = <<<HTML

			<div class="ui-corner-all warning">
				<span class="ui-icon ui-icon-notice right"></span>
				{$text}
			</div>

HTML;
		return $HTML;
	}

	public function critical($text)
	{
		$HTML = <<<HTML

			<div class="ui-corner-all critical">
				<span class="ui-icon ui-icon-alert right"></span>
				{$text}
			</div>

HTML;
		return $HTML;
	}

	public function notice($text)
	{
		$HTML = <<<HTML

			<div class="ui-corner-all notice">
				<span class="ui-icon ui-icon-lightbulb right"></span>
				{$text}
			</div>

HTML;
		return $HTML;
	}

	public function busy($text)
	{
		$HTML = <<<HTML

			<div class="ui-corner-all busy">
				<span class="ui-icon ui-icon-flag right"></span>
				{$text}
			</div>

HTML;
		return $HTML;
	}

	public function message($text)
	{
		$HTML = <<<HTML

			<div class="ui-corner-all message">
				<span class="ui-icon ui-icon-comment right"></span>
				{$text}
			</div>

HTML;
		return $HTML;
	}

	public function note($text)
	{
		$HTML = <<<HTML

			<div class="ui-corner-all note">
				<span class="ui-icon ui-icon-pencil right"></span>
				{$text}
			</div>

HTML;
		return $HTML;
	}

	public function ok($text)
	{
		$HTML = <<<HTML

			<div class="ui-corner-all ok">
				<span class="ui-icon ui-icon-check right"></span>
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
			<a href="#" title="{$text}" class="tooltip"><span class="ui-icon ui-icon-info"></span></a>

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
				<span>
					{$text}
				</span>
			</a>

HTML;
		return $HTML;
	}

	/**
	 * The username/logout box at the top right
	 *
	 * By default display the name of the logged-in user, being a link to logout directly
	 *
	 * @param string $href url of the direct logout link
	 * @param string $text the user display name
	 * @return string, the html snippet
	 */
	public function loggedInInfo($href, $text)
	{
		return "<a href=\"{$href}\"><div id=\"logged-in\" class=\"loginlink\"><span></span>{$text}</div></a>
		";
	}

	public function logInInfo($href, $inoutpage)
	{
		return "<a href=\"{$href}\"><div id=\"logged-out\" class=\"loginlink\"><span></span>{$inoutpage}</div></a>
		";
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
		return "
			<th>{$th_}{$sort_html}</th>
				";
	}

	public function paginationNav($url, $class)
	{
		return "
				<li class=\"paginationicon\">
					<a href=\"{$url}\">
						<span class=\"{$class}\"></span>
					</a>
				</li>
				";
	}

	public function paginationNavEmpty($class)
	{
		return "
				<li class=\"paginationicondisabled\">
					<span class=\"{$class}\"></span>
				</li>
				";
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

		/**
		 * Class types:
		 * nav-grand
		 * nav-parent
		 * child
		 */
		if (empty($class))
			$span = '';
		else
			$span = "<span class=\"{$class}\"></span>
			";

		return "
				<a href=\"{$url}\" target=\"{$target}\" {$noclick}>
					{$span}{$mr['menu_name']}
				</a>
				";
	}

	public function menuUlParent($tree, $class)
	{
		/**
		 * Class types:
		 * navparent
		 * ulchild
		 * breadparent
		 */
		return "
				<ul class=\"{$class}\">
					{$tree}
				</ul>
				";
	}

	public function menuUlChild($tree, $class)
	{
		/**
		 * Class types:
		 * navparent
		 * ulchild
		 * breadparent
		 */
		return "
				<ul class=\"{$class}\">
					{$tree}
				</ul>
				";
	}

	public function menuLiParent($tree, $link, $class, $menu_data = null)
	{
		/**
		 * Class types:
		 * parent
		 * grandparent
		 * current
		 * inactive
		 */
		$id = empty($menu_data['menu_id']) ? '' : ' id="menu_'.PU_safeName($menu_data['menu_id']).'"';
		return "
				<li class=\"{$class}\"$id>
					{$link}
						{$tree}
				</li>
				";
	}

	public function menuLiChild($link, $class, $menu_data = null)
	{
		/**
		 * Class types:
		 * current
		 * inactive
		 */
		$id = empty($menu_data['menu_id']) ? '' : ' id="menu_'.PU_safeName($menu_data['menu_id']).'"';
		return "
				<li class=\"{$class}\"$id>
					{$link}
				</li>
				";
	}

	public function menuLiJump($href, $name)
	{
		return "
				<li class=\"jump\">
					<a href=\"{$href}\">
						<span></span>{$name}
					</a>
				</li>
				";
	}

	public function menuLiUp($href, $name)
	{
		return "
			<li class=\"up\">
				<a href=\"{$href}\">
					<span></span>{$name}
				</a>
			</li>
				";
	}

	public function menuLiHome($href, $name, $jump_menu)
	{
		return "
			<li class=\"home\">
				<a href=\"{$href}\">
					<span></span>{$name}
				</a>
			</li>
				{$jump_menu}
				";
	}

	public function menuRedirect($url, $time)
	{
		return '
			<META HTTP-EQUIV="refresh" CONTENT="' . $time . '; URL=' . $url . '">
			';
	}

	public function securityToken($token)
	{
		return '<input type="hidden" name="token_validation" value="' . $token . '">';
	}

	public function searchToken($search_token)
	{
		return '
			<input type="hidden" name="search_token_validation" value="' . $search_token . '">
			';
	}

	public function debug($queries, $memory, $other='')
	{
		return "
				<div id=\"queries\">
					(Queries Used : {$queries}) - (PHP Memory Used : {$memory} Mb) - (Page Load Time : {$other} ms)
				</div>
					";
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
				$errorStyling = '<div class="ui-state-error ui-corner-all warning"><span class="ui-icon ui-icon-alert right"></span>' . $error['message'] . '</div>';
				$message .= "$('$errorStyling').insertBefore('{$forType}');";
			}
		}

		if (! empty($forOk)) {
			$forOk = rtrim($forOk,",");
			$okClass = '$("' . $forOk . '").removeAttr("required").addClass("okField");';
		}

		if (! empty($forError)) {
			$forError = rtrim($forError,",");
			$errorClass = '$("' . $forError . '").removeAttr("required").addClass("invalidField");';
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

		$option = "
			<option value=\"{$value}\" {$selected}>{$name}</option>
		";
		return $option;
	}

	public function formCheckbox($name, $value, $label, $checked='')
	{
		if ($checked)
			$checked = 'checked';
		else
			$checked = '';

		$checkbox = "
			<label>
				<input type=\"checkbox\" name=\"$name\" value=\"$value\" $checked title=\"$label\">$label
			</label><br>
		";
		return $checkbox;
	}

	public function formRadio($name, $value, $label, $checked='')
	{
		if ($checked)
			$checked = 'checked';
		else
			$checked = '';

		$checkbox = "
			<label>
				<input type=\"radio\" name=\"$name\" value=\"$value\" $checked title=\"$label\">$label
			</label><br>
		";
		return $checkbox;
	}

	public function botBlockFields($fakefieldname)
	{
		$fakefields = <<<HTML

			<div style="display: none;">
				<input type="text" name="text_$fakefieldname" value="" title="$fakefieldname">
				<input type="checkbox" name="check_$fakefieldname" title="$fakefieldname">
			</div>

HTML;

		return $fakefields ;
	}

	public function botBlockSecret($secret_field, $dom='form')
	{
		$fakefields = <<<HTML

			$(document).ready(function() {
				$("$dom").append('$secret_field');
			});

HTML;

		return $fakefields ;
	}

	public function stylePagination()
	{
		$H = <<<HTML

				$(document).ready(function() {
					$(".paginationicon").hover(
						function () {
							$(this).addClass("ui-state-hover");
						},
						function () {
							$(this).removeClass("ui-state-hover");
						}
					);
					$("#pagination .paginationicon").addClass("ui-state-default ui-corner-all");
					$("#pagination .paginationicondisabled").addClass("ui-state-disabled ui-corner-all");
					$("#pagination .ff").addClass("ui-icon ui-icon-arrowreturnthick-1-e left");
					$("#pagination .rw").addClass("ui-icon ui-icon-arrowreturnthick-1-w left");
					$("#pagination .first").addClass("ui-icon ui-icon-arrowthickstop-1-w left");
					$("#pagination .previous").addClass("ui-icon ui-icon-arrowthick-1-w left");
					$("#pagination .last").addClass("ui-icon ui-icon-arrowthickstop-1-e left");
					$("#pagination .next").addClass("ui-icon ui-icon-arrowthick-1-e left");
					$("th .order .selectedorder").addClass("ui-state-disabled");
					$("th .order .asc").addClass("ui-icon ui-icon-circle-arrow-n left");
					$("th .order .desc").addClass("ui-icon ui-icon-circle-arrow-s left");
			});

HTML;
		return $H;
	}

	public function styleTables()
	{
		$H = <<<HTML

			$(document).ready(function() {
				$("tbody tr").hover(
					function () {
						$(this).addClass("ui-state-active");
					},
					function () {
						$(this).removeClass("ui-state-active");
				});
				$(".no_results").addClass("ui-state-error");
				$("thead th").addClass("ui-widget-header ui-corner-all");
			});

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
		return "themes/cloud/js/validator/jquery.validate.min.js";
	}

	public function formsValidate()
	{
		$H = <<<HTML

			$(document).ready(function() {
				$("form").validate({
					errorClass: "error",
					validClass: "ok",
					errorElement: "div"
				});
			});

HTML;
		return $H;
	}

	public function styleForms()
	{
		$H = <<<HTML

			$(document).ready(function() {
				$("input[type=submit], input[type=reset], input.submit").addClass("ui-state-default ui-corner-all");
				$("input[type=submit], input[type=reset], input.submit").hover(
					function () {
						$(this).addClass("ui-state-hover");
					},
					function () {
						$(this).removeClass("ui-state-hover");
				});
				$("input[type=number], input[type=url], input[type=email], input[type=text], input[type=password], input.text, input.title, textarea, select").focus(
					function () {
						$(this).addClass("ui-state-active");
					},
					function () {
						$(this).removeClass("ui-state-active");
				});
				$("input[type=number], input[type=url], input[type=email], input[type=text], input[type=password], input.text, input.title, textarea, select").addClass("ui-widget-content ui-corner-all");
				$("[readonly]").addClass("ui-state-disabled ui-corner-all");
			});

HTML;
		return $H;
	}

	public function styleButtons()
	{
		$H = <<<HTML

			$(document).ready(function() {
				$("button, .button").addClass("ui-state-default ui-corner-all");
				$("button, .button").hover(
					function () {
						$(this).addClass("ui-state-hover");
					},
					function () {
						$(this).removeClass("ui-state-hover");
				});
				$("button .delete").addClass("ui-icon ui-icon-trash left");
				$("button .save").addClass("ui-icon ui-icon-disk left");
				$("button .edit").addClass("ui-icon ui-icon-pencil left");
				$("button .reset").addClass("ui-icon ui-icon-refresh left");
				$("button .submit").addClass("ui-icon ui-icon-check left");
				$("button .update").addClass("ui-icon ui-icon-circle-check left");
				$("button .new").addClass("ui-icon ui-icon-plus left");
			});

HTML;
		return $H;
	}

	public function jqueryEffect($plugin)
	{
		return "themes/cloud/jquery/js/jquery.effects.$plugin.min.js";
	}

	public function jqueryUI($plugin)
	{
		return "themes/cloud/jquery/js/jquery.ui.$plugin.min.js";
	}

	public function notificationsJs()
	{
		return 'themes/cloud/js/pnotify/jquery.pnotify.min.js';
	}

	public function notifications($title, $message, $type='info')
	{

		$notify_type = "";
		$notify_hide = "";

		switch ($type) {
			// notice
			case 'ok':
			case 'notice':
			case 'busy':
				// default option fine
			break;

			// info
			case 'info':
			case 'message':
			case 'note':
			default:
				$notify_type = "pnotify_type: 'info',";
			break;

			// error
			case 'warning':
			case 'critical':
				$notify_type = "pnotify_type: 'error',";
				$notify_hide = "pnotify_hide: false,";
			break;
		}

		// to prevent js errors we need to remove line breaks.
		$message = str_replace(array("\r", "\n"), '', $message);

		$H = <<<HTML

			$(document).ready(function() {
				$.pnotify.defaults.pnotify_delay += 500;
				$.pnotify({
					pnotify_title: '{$title}',
					pnotify_text: '{$message}',
					$notify_type
					$notify_hide
					pnotify_opacity: 0.9,
					pnotify_closer_hover: false,
					pnotify_sticker_hover: false
				});
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
		return 'themes/cloud/js/ericselect/jquery.multiselect.js';
    }

    public function styleSelectHeader()
    {
		$H = <<<HTML

            $(document).ready(function() {
				$(".multiselect").multiselect({
					selectedList: 24,
					header: true,
					noneSelectedText: '...',
					minWidth: 350
				}).multiselectfilter();

				$(".select").multiselect({
					multiple: false,
					selectedList: 1,
					header: true,
					noneSelectedText: '...',
					minWidth: 350
				}).multiselectfilter();
            });

HTML;
		return $H;
    }

	// end of mod class
}
