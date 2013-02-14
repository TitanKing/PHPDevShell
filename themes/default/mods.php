<?php

class themeMods extends PHPDS_dependant
{

    public function loader()
	{
		return <<<HTML

        <script type="text/javascript">
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
			$not_registered_yet = '<a href="' . $not_registered_yet . '" class="click-elegance">' . $not_registered_yet_text . '</a>';
		} else {
			$not_registered_yet = '';
		}

		$HTML = <<<HTML

			<div class="login-actions">
				<form id="login" action="{$action}" method="post" class="validate click-elegance">
					<fieldset>
						<legend>{$login_label}</legend>
						<p>
                            <label for="user_name">{$username_label}</label>
                            <input id="user_name" tabindex="1" type="text" name="user_name" value="{$user_name}" title="$username_label">
                        </p>
						<p>
                            <label for="password">{$password_label}</label>
                            <input id="password" tabindex="2" type="password" name="user_password" title="$password_label">
                        </p>
						{$redirect_page}
						<p>{$remember}</p>
						<p>
							<button type="submit" name="login" class="btn btn-primary"><i class="icon-ok icon-white"></i> {$button_name}</button><br>
						</p>
						<p>
							<a href="{$lost_password}" class="click-elegance">{$lost_password_text}</a><br>
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

    public function loggedInInfo ($name, $logouturl, $logoutname, $role, $group, $node_data = null)
    {

        if (! empty($node_data['user-preferences'])) {
            $p     = $node_data['user-preferences'];
            $prefs = <<<HTML
                <a href="{$p['href']}" class="btn btn-primary options">{$p['node_name']}</a>
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

			<div id="norecords">
				{$text}
			</div>

HTML;
		return $HTML;
	}

	public function results($first_page, $rw, $previous_page, $currentPage_, $total_pages_, $current_records_, $totalRows_, $next_page, $ff, $last_page)
	{
		$HTML = <<<HTML

			<div id="pagination" class="pagination">
				<ul id="results">
					{$first_page}
					{$rw}
					{$previous_page}
					<li><a href="#" class="muted">[{$currentPage_}/{$total_pages_}] - [{$current_records_}/{$totalRows_}]</a></li>
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

            <div id="search-field-outer">
                <form action="{$action}" method="post" class="click-elegance">
                    <div id="searchForm">
                        <div class="input-append">
                            <input id="search_field" type="text" name="search_field" value="{$value}" class="{$class}">
                            <button class="btn" type="submit"><i class="icon-search"></i></button>
                        </div>
                        <input type="hidden" value="Filter" name="search">
                        {$validate}
                    </div>
                </form>
			</div>

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

	public function paginationTh($th_, $order_url=null, $asc=null, $desc=null)
	{
        if (! empty($order_url)) {
            if (! empty($asc)) {
                $asc_ = '&darr;';
                $filter = 'desc';
            } else {
                $asc_ = '';
                $filter = 'asc';
            }

            if (! empty($desc)) {
                $desc_ = '&uarr;';
                $filter = 'asc';
            } else {
                $desc_ = '';
                $filter = 'desc';
            }

            if (empty($asc) && empty($desc)) {
                return '<th><a href="' . $order_url . '&order=asc" class="click-elegance">' . $th_ . '</a></th>';
            } else {
                return '<th><a href="' . $order_url . '&order=' . $filter . '" class="click-elegance">' . $th_ . $asc_ . $desc_ . '</a></th>';
            }
        } else {
            return "<th>{$th_}</th>";
        }
	}

	public function paginationNav($url, $class)
	{

        switch ($class) {
            case 'ff':
                $name = '<i class="icon-forward"></i>';
            break;

            case 'rw':
                $name = '<i class="icon-backward"></i>';
            break;

            case 'next':
                $name = '<i class="icon-chevron-right"></i>';
            break;

            case 'last':
                $name = '<i class="icon-fast-forward"></i>';
            break;

            case 'first':
                $name = '<i class="icon-fast-backward"></i>';
            break;

            case 'previous':
                $name = '<i class="icon-chevron-left"></i>';
            break;
        }

		return <<<HTML

			<li><a href="{$url}" class="click-elegance">{$name}</a></li>

HTML;
	}

	public function paginationNavEmpty($class)
	{

        // This allows you to also style buttons that are disabled,
        // however lets leave this to show nothing as default.
        switch ($class) {
            case 'ff':
                $name = '<i class="icon-forward"></i>';
                break;

            case 'rw':
                $name = '<i class="icon-backward"></i>';
                break;

            case 'next':
                $name = '<i class="icon-chevron-right"></i>';
                break;

            case 'last':
                $name = '<i class="icon-fast-forward"></i>';
                break;

            case 'first':
                $name = '<i class="icon-fast-backward"></i>';
                break;

            case 'previous':
                $name = '<i class="icon-chevron-left"></i>';
                break;
        }

        return '';
	}

	public function activeName ($name)
	{
		return '<li class="active-name">' . $name . '</li>';
	}

	public function menuA($mr, $class='')
	{
		// Check if we have a place marker.
		if ($mr['node_type'] == 6) {
			$noclick = 'onclick="return false;"';
			// Create URL.
			$url = "&#35;";
		} else {
			$noclick = '';
			// Last check if it is a link item that should be jumped to.
			if ($mr['node_type'] == 5) {
				$url = $mr['node_link'];
			} else {
				$url = $mr['href'];
			}
		}
		($mr['new_window'] == 1) ? $target = '_blank' : $target = '_self';
		$extra = ($class == 'nav-grand') ? 'data-toggle="dropdown" class="dropdown-toggle"' : '';

		return <<<HTML
				<a tabindex="-1" href="{$url}" target="{$target}" {$extra} {$noclick}>{$mr['node_name']}</a>
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

	public function menuLiParent($tree, $link, $class, $node_data = null)
	{
		$id = empty($node_data['node_id']) ? '' : ' id="menu_'.PU_safeName($node_data['node_id']).'"';
		return <<<HTML

			<li class="{$class} dropdown" {$id}>
				{$link}
					{$tree}
			</li>

HTML;
	}

	public function subMenuLiParent($tree, $link, $class, $node_data = null)
	{
		$id = empty($node_data['node_id']) ? '' : ' id="menu_'.PU_safeName($node_data['node_id']).'"';

		return <<<HTML

			<li class="{$class} nav-header" {$id}>{$node_data['node_name']}</li>
			{$tree}

HTML;
		// You could also make a tree type node, but remember users with touch screens will find it hard to navigate, they can't hover.
		/*
		return <<<HTML
				<li class="{$class} dropdown-submenu" {$id}>
					{$link}
						{$tree}
				</li>
HTML;
		*/
	}

	public function menuLiChild($link, $class, $node_data = null)
	{
        /**
		 * Class types:
		 * current
		 * inactive
		 */
		$id = empty($node_data['node_id']) ? '' : ' id="node_'.PU_safeName($node_data['node_id']).'"';
		return <<<HTML

			<li class="{$class}" {$id}>{$link}</li>

HTML;
	}

	public function subMenuLiChild($link, $class, $node_data = null)
	{
		/**
		 * Class types:
		 * current
		 * inactive
		 */
		$id = empty($node_data['node_id']) ? '' : ' id="menu_'.PU_safeName($node_data['node_id']).'"';
		return <<<HTML

			<li class="{$class}" {$id}>{$link}</li>

HTML;
	}

	public function menuASubNav($mr)
	{
		return '<a href="' . $mr['href'] . '">' . $mr['node_name'] . '</a>';
	}

	public function subNavMenuLi($link, $class, $node_data = null)
	{
		/**
		 * Class types:
		 * active
		 * inactive
		 */
		$id = empty($node_data['node_id']) ? '' : ' id="menu_'.PU_safeName($node_data['node_id']).'"';
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

			if ($error['type'] == 'error') {
				$errorStyling = '<div class="control-group error"><label class="control-label error-label">' . $error['message'] . '</label></div>';
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

			<label class="checkbox">
				<input type="checkbox" name="{$name}" value="{$value}" {$checked} title="{$label}"> {$label}
			</label>

HTML;
		return $checkbox;
	}


    public function ulCheckbox($tree)
    {
        $treehtml = <<<HTML

            <ul>
                {$tree}
            </ul>

HTML;

        return $treehtml;
    }

    public function liCheckbox($node_id, $inputname, $node_name, $checked)
    {
        $checkbox = <<<HTML

            <li>
                <label class="checkbox">
                    <input type="checkbox" name="{$inputname}[{$node_id}]" $checked> {$node_name}
                </label>
            </li>
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

			<label class="radio">
				<input type="radio" name="{$name}" value="{$value}" {$checked} title="{$label}"> {$label}
			</label>

HTML;
		return $checkbox;
	}

    public function taggerArea($taglist, $tagnametext, $tagvaluetext)
    {

        $existingtags   = (string) '';

        if (! empty($taglist)) {
            asort($taglist);
            foreach ($taglist as $tag) {
                $tagname    = trim($tag['tagName']);
                $tagvalue   = trim($tag['tagValue']);
                $tagid      = $tag['tagID'];
                if ((empty($tagname) && empty($tagvalue)) || empty($tagid)) continue;

                $existingtags .= <<<HTML

                    <div>
                        <p class="delete-tag pull-right">
                            <button type="button" class="btn btn-warning"><i class="icon-minus icon-white"></i></button>
                        </p>
                        <p>
                            <input type="hidden" name="tagger_id[{$tagid}_update]" value="{$tagid}">
                            <input type="text" name="tagger_name[{$tagid}_update]" value="{$tagname}" placeholder="{$tagnametext}"><br>
                            <textarea id="tagger" name="tagger_value[{$tagid}_update]" placeholder="{$tagvaluetext}">{$tagvalue}</textarea>
                        </p>
                    </div>

HTML;

            }
        } else {
            $existingtags = '';
        }

        $HTML = <<<HTML
            <p id="moretags" class="pull-right">
                <button id="addtag" type="button" class="btn btn-info"><i class="icon-plus icon-white"></i></button>
            </p>
            <div class="clonetags">
                <p>
                    <input type="text" name="tagger_name[]" value="" placeholder="{$tagnametext}"><br>
                    <textarea id="tagger" name="tagger_value[]" value="" placeholder="{$tagvaluetext}"></textarea>
                </p>
            </div>

            <hr>
            {$existingtags}
			<script type="text/javascript">
                $(function() {
                    $("#addtag").click(function () {
                        $(".clonetags p").clone().insertAfter("#moretags");
                    });

                    $(".delete-tag button").click(function() {
                        $(this).parent().parent().fadeTo('slow', '0.5', function() {
                            $("button", this).addClass("disabled");
                            $("i", this).removeClass('icon-minus icon-white').addClass('icon-trash');
                            $("input", this).attr('name', 'tagger_delete[]').attr("readonly", "readonly");
                            $("textarea", this).attr("readonly", "readonly");
                        });
                    });
                });
            </script>
HTML;

        return $HTML;
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
