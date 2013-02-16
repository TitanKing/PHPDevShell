<?php

/**
 * Build array from get url.
 *
 * TODO: it's probably faster to use PHP build-in function (array_merge...)
 *
 * @param array $myGET
 * @param array $includeInGet
 * @param array $excludeFromGet
 * @return array
 *
 * @date 20110809 (v1.0.1) (greg) fixed a typo with $includeInGet
 * @author greg
 */
function PU_BuildGETArray(array $myGET, $includeInGet = null, $excludeFromGet = null)
{
	if (!is_null($includeInGet)) {
		if (!is_array($includeInGet))
			$includeInGet = array($includeInGet);
		foreach ($includeInGet as $index => $value)
			$myGET[$index] = $value;
	}
	if (!is_null($excludeFromGet)) {
		if (!is_array($excludeFromGet))
			$excludeFromGet = array($excludeFromGet);
		foreach ($excludeFromGet as $param)
			unset($myGET[$param]);
	}
	return $myGET;
}

/**
 * Creates a (string) url to be used with GET, including encoding
 *
 * @param array $myGET
 * @param string $glue
 * @return string
 */
function PU_BuildGETString(array $myGET, $glue = '&amp;')
{
	$url = '';
	if (count($myGET)) {
		$params = array();
		foreach ($myGET as $index => $value)
			$params[] .= rawurlencode($index) . '=' . rawurlencode($value);
		$url = '?' . implode($glue, $params);
	}
	return $url;
}

/**
 * Build GET part of a url
 *
 * @param $includeInGet	(optional) array of pairs: parameters to add as GET in the url
 * @param $excludeFromGet (optional) array of strings: parameters to remove from GET in the url
 * @return string the whole parameter part of the url (including '?') ; maybe empty if there are no parameters
 */
function PU_BuildGET($includeInGet = null, $excludeFromGet = null, $glue = '&amp;')
{
	return PU_BuildGETString(PU_BuildGETArray($_GET, $includeInGet, $excludeFromGet), $glue);
}

/**
 * Build a xml-style attributes string based on an array
 *
 * @version 1.1
 * @date 20091203
 *  @date 20110203 (v1.1) (greg) added the glue parameter
 * @param $attributes array, the attribute array to compile
 * @param $glue string, a piece of string to insert between the values
 * @return string
 */
function PU_BuildAttrString(array $attributes = null, $glue = '')
{
	$result = '';
	if (is_array($attributes))
		foreach ($attributes as $key => $value) {
			if ($result && $glue)
				$result .= $glue;
			$result .= " $key=\"$value\"";
		}
	return $result;
}

/**
 * Builds a parsed url.
 *
 * @param array $p
 * @return string
 */
function PU_buildParsedURL($p)
{
	if (!is_array($p))
		return $p;

	if (empty($p['scheme']))
		$p['scheme'] = 'http';
	if (empty($p['host']))
		$p['host'] = $_SERVER["HTTP_HOST"];
	if (empty($p['port']))
		$p['port'] = '';
	else
		$p['port'] = ':' . $p['port'];
	if (empty($p['user']))
		$p['user'] = '';
	if (empty($p['pass']))
		$p['pass'] = '';
	if (empty($p['path']))
		$p['path'] = $_SERVER["PHP_SELF"];
	if (empty($p['query']))
		$p['query'] = '';
	if (empty($p['fragment']))
		$p['fragment'] = '';
	else
		$p['fragment'] = '#' . $p['fragment'];

	$auth = ($p['user'] || $p['pass']) ? $p['user'] . ':' . $p['pass'] . '@' : '';

	if ($p['query'] && ('?' != substr($p['query'], 0, 1)))
		$p['query'] = '?' . $p['query'];

	if ('/' == substr($p['path'], 0, 1))
		$url = $p['scheme'] . '://' . $auth . $p['host'] . $p['port'] . $p['path'] . $p['query'] . $p['fragment'];
	else
		$url = $p['path'] . $p['query'] . $p['fragment'];

	return $url;
}

/**
 * Build a url with GET parameters
 *
 * @param string|array $target (optional) string: the target script url (current script if missing)
 * @param array $includeInGet	(optional) array of pairs: parameters to add as GET in the url
 * @param array $excludeFromGet (optional) array of strings: parameters to remove from GET in the url
 * @return string the built url
 *
 * @version 1.1
 * @author greg
 *
 * @date 20100930 (v1.1) (greg) $target parameter can now be an array resulting from php's parse_url function
 */
function PU_BuildURL($target = null, $includeInGet = null, $excludeFromGet = null, $glue = '&amp;')
{
	if (is_null($target))
		$target = $_SERVER["REQUEST_URI"];
	if (!is_array($target))
		$target = parse_url($target);

	if (empty($target['query']))
		$tarGET = $_GET;
	else {
		parse_str($target['query'], $tarGET);
		$tarGET = array_merge($_GET, $tarGET);
	}
	$myGET = PU_BuildGETArray($tarGET, $includeInGet, $excludeFromGet);
	$target['query'] = PU_BuildGETString($myGET, $glue);
	$target = PU_buildParsedURL($target);
	return $target;
}

/**
 * Build a html link (A+HREF html tag) with label and url and GET parameters
 *
 * @version 1.0.1
 * @date 20091203: added $attrs parameter
 * @param $label string: the text of the link
 * @param $includeInGet (optional) array of pairs: parameters to add as GET in the url
 * @param $excludeFromGet (optional) array of strings: parameters to remove from GET in the url
 * @param $target (optional) string: the target script url (current script if missing)
 * @return string the complete html link
 *
 * TODO: support attrs!!!
 */
function PU_BuildHREF($label, $includeInGet = null, $excludeFromGet = null, $target = null, array $attrs = null)
{
	$url = PU_BuildURL($target, $includeInGet, $excludeFromGet);
	$href = '<a href="' . $url . '">' . $label . '</a>';
	return $href;
}

/**
 * Clean a string from possibly harmful chars
 *
 * These are removed: single and double quotes, backslashes, optionnaly html tags (everything between < and >)
 *
 * A cleaned string should be safe to include in an html output
 *
 * @param $string the string to clean
 * @param $clean_htlm if true, HTML tags are deleted too
 *
 * @return string
 */
function PU_CleanString($string, $clean_htlm = false)
{
	$string = preg_replace('/["\'\\\\]/', '', $string);
	if ($clean_htlm)
		$string = preg_replace('/<.+>/', '', $string);
	return $string;
}

/**
 * Convert a string to UTF8 (default) or to HTML
 *
 * @version 1.1
 * @date 20120309 (v1.1) (greg) $htmlize can now specify a target encoding
 *
 * @param $string the string to convert
 * @param $htmlize if true the string is converted to HTML, if nul to UTF8; otherwise specified encoding
 *
 * @return string
 */
function PU_MakeString($string, $htmlize = false)
{
	if (!empty($string)) {
		$from = mb_detect_encoding($string, 'HTML-ENTITIES, UTF-8, ISO-8859-1, ISO-8859-15', true);
		$to = is_null($htmlize) ? 'UTF-8' : (($htmlize === true ) ? 'HTML-ENTITIES' : $htmlize);
		//$to = ($htmlize ? 'HTML-ENTITIES' : 'UTF-8');
		$string = mb_convert_encoding($string, $to, $from);
	}
	return $string;
}

/**
 * Search for array values inside array and returns key.
 *
 * @param array $needle
 * @param array $haystack
 * @return mixed
 */
function PU_ArraySearch($needle, $haystack)
{
	if (empty($needle) || empty($haystack)) {
		return false;
	}

	foreach ($haystack as $key => $value) {
		$exists = 0;
		foreach ($needle as $nkey => $nvalue) {
			if (!empty($value[$nkey]) && $value[$nkey] == $nvalue) {
				$exists = 1;
			} else {
				$exists = 0;
			}
		}
		if ($exists)
			return $key;
	}

	return false;
}

/**
 * Create gettext functions.
 */
if (function_exists('gettext')) {

	/**
	 * Wrapper for $core->__() method.
	 * Converts text to use gettext PO system. Does the same as $core->__();
	 * @author Jason Schoeman
     *
	 * @param string $gettext what The string required to output or convert.
	 * @param string $domain Override textdomain that should be looked under for this text string.
     *
	 * @return string Will return converted string or same string if not available.
	 */
	function __($gettext, $domain = '')
	{
		if (empty($domain)) {
			return gettext($gettext);
		} else {
			return dgettext($domain, $gettext);
		}
	}

	/**
	 * Specifically meant for core translation domain.
	 *
	 * @param string $gettext The string required to output or convert.
     *
	 * @return string
	 */
	function ___($gettext)
	{
		return dgettext('core.lang', $gettext);
	}

	/**
	 * This function echos the returning text.
	 *
	 * @param string $text
	 */
	function _e($text)
	{
		echo gettext($text);
	}

	/**
	 * This function echos the returning text inside a domain.
	 *
	 * @param string $text
     * @oaram string $domain
	 */
	function __e($text, $domain)
	{
		echo dgettext($domain, $text);
	}
} else {

	function ___($gettext)
	{
		return dgettext('core.lang', $gettext);
	}

	function gettext($text)
	{
		return $text;
	}

	function dgettext($domain, $text)
	{
		return $text;
	}

	function _($text)
	{
		return $text;
	}

	function __($gettext, $domain = false)
	{
		return $gettext;
	}

	function _e($text)
	{
		echo $text;
	}

	function __e($text)
	{
		echo $text;
	}

	function textdomain($textdomain)
	{
		return '';
	}

}

/**
 * Outputs an array in html
 * A slightly better version of print_r()
 * Note: this output is html
 *
 * @version 2.0
 * @author greg
 *
 * @date 20100825 (greg) (v1.1) updated to deal with associative arrays
 * @date 20110211 (greg) (v1.2) added $htmlize parameter
 * @data 20120630 (greg) (v2.0) made it recursive; improved html
 *
 * @param array $a
 * @param string $title
 * @param boolean $htmlize (default to false) if true html is escaped to be displayed as source
 *
 * @return string
 */
function PU_dumpArray($a, $title = '', $htmlize = false)
{
	$s = $title ? "<p>$title</p>" : '';

	if (!(is_array($a) || is_object($a))) {
		$a = array($a);
	}

	if (count($a) == 0) {
		$s .= '(empty array)';
	} else {
		$s .= '<ul class="array_dump">';
		foreach ($a as $k => $e) {
			$t = gettype($e);
			switch ($t) {
				case 'array': $t .= ', '.count($e).' elements'; break;
				case 'string':
						$t .= ', '.strlen($e).' chars, '.mb_detect_encoding($e);

					break;
				case 'object': $t .= ' of class "'.get_class($e).'"'; break;
			}
			$s .= '<li>'
				.'<span class="array_key"><span class="array_grey">[&nbsp;</span>'.$k.'<span class="array_grey">&nbsp;]&nbsp;=&gt;</span></span>'
				.'&nbsp;<span class="array_type">('.$t.')</span>&nbsp;';
			if (is_array($e) || is_object($e)) {
				$e = PU_dumpArray($e, null, $htmlize);
			} else if ($htmlize) {
				$e = htmlentities($e);
			}
			$s .= '<span class="array_value">'.(string)$e.'</li>';
		}
		$s .= '</ul>';
	}

	return $s;
}

/**
 * Get rid of all buffer, optionaly flushing (i.e. writing to the browser)
 * Default behavior is to ignore.
 *
 * @param boolean $flush do we flush or ignore?
 */
function PU_cleanBuffers($flush = false)
{
	try {
		for ($loop = ob_get_level(); $loop; $loop--) {
			$flush ? ob_end_flush() : ob_end_clean();
		}

		// these catches are only there to mask the exception and prevent it from bubbling
	} catch (Exception $e) {
		$a = 0;
	} catch (ErrorException $e) {
		$a = 0;
	} catch (PHPDS_fatalError $e) {
		$a = 0;
	}
}

/**
 * Add a header if and only if headers have not been sent yet
 *
 * @param string $header the header string to add
 * @return nothing
 *
 * @version 1.0
 * @since 3.0.6
 * @date 20110809 (v1.0) (greg) added
 * @author greg <greg@phpdevshell.org>
 */
function PU_silentHeader($header)
{
	if (!headers_sent()) {
		header($header);
	}
}

/**
 * Determines if the current request has been made by some kind of ajax call (i.e. XMLHttpRequest)
 *
 * @param boolean $json set to true if you want to force the request's result as json
 * @return boolean
 *
 * @version 1.0.1
 * @date 20110809 (v1.0.1) (greg) use PU_silentHeader to prevent unit tests from failing
 * @author greg <greg@phpdevshell.org>
 */
function PU_isAJAX($json = false)
{
	$ajax = !empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"] == 'XMLHttpRequest');
	if ($ajax && $json) {
		PU_cleanBuffers();
		PU_silentHeader('Content-Type: application/json');
	}
	return $ajax;
}

/**
 * Checks for a json context and if so, outputs data
 *
 * @author greg <greg@phpdevshell.org>
 * @version 1.1
 * @since v3.0.1
 * @date 20110309 (v1.0) (greg) added
 * @date 20110316 (v1.1) (greg) returns instead of printing (for buffering)
 *
 * @param $data mixed, the data to be encoded and sent
 * @param $force boolean, (optionnal) do we pretend it's json context even if it's not?
 * @return boolean, false if it's not JSON, or the encoded data
 */
function PU_isJSON($data, $force = false)
{
	$json = $force || (isset($_SERVER["HTTP_X_REQUESTED_TYPE"]) && ($_SERVER["HTTP_X_REQUESTED_TYPE"] == 'json'));
	if ($json && PU_isAJAX(true)) {
		return json_encode($data);
	}
	return false;
}

/**
 * OBSOLETE don't use
 *
 * @param $data
 */
function PU_exitToAJAX($data)
{
	PU_isAJAX(true);
	print json_encode($data);
	exit;
}

/**
 * Get rid of null values inside an array
 *
 * All values which are null in the array are remove, shortening the array
 *
 * @version 2.0
 * @date 20121010 (v2.0) (greg) rewrote using a simple loop as clever array_walk didn't work
 *
 * @param array $a the array to compact
 * @return array
 */
function PU_array_compact(array $a)
{
	foreach($a as $k => $v) {
		if (is_null($a[$k])) unset($a[$k]);
	}
	return $a;
}

/**
 * version of sprintf for cases where named arguments are desired (python syntax)
 *
 * with sprintf: sprintf('second: %2$s ; first: %1$s', '1st', '2nd');
 *
 * with sprintfn: sprintfn('second: %(second)s ; first: %(first)s', array(
 *  'first' => '1st',
 *  'second'=> '2nd'
 * ));
 * @author nate at frickenate dot com
 *
 * @version 1.0.1
 * @date 20120724 (v1.0.1) (greg) cleaned up exception
 *
 * @param string $format sprintf format string, with any number of named arguments
 * @param array $args array of [ 'arg_name' => 'arg value', ... ] replacements to be made
 * @return string|false result of sprintf call, or bool false on error
 */
function PU_sprintfn($format, array $args = array())
{
	try {
		// map of argument names to their corresponding sprintf numeric argument value
		$arg_nums = array_slice(array_flip(array_keys(array(0 => 0) + $args)), 1);

		// find the next named argument. each search starts at the end of the previous replacement.
		for ($pos = 0; preg_match('/(?<=%)\(([a-zA-Z_]\w*)\)/', $format, $match, PREG_OFFSET_CAPTURE, $pos);) {
			$arg_pos = $match[0][1];
			$arg_len = strlen($match[0][0]);
			$arg_key = $match[1][0];

			// programmer did not supply a value for the named argument found in the format string
			if (!array_key_exists($arg_key, $arg_nums)) {
				throw new PHPDS_sprintfnException(array($format, $arg_key), $args);
			}

			// replace the named argument with the corresponding numeric one
			$format = substr_replace($format, $replace = $arg_nums[$arg_key] . '$', $arg_pos, $arg_len);
			$pos = $arg_pos + strlen($replace); // skip to end of replacement for next iteration
		}

		$result = vsprintf($format, array_values($args));
		return $result;
	} catch (Exception $e) {
		throw new PHPDS_sprintfnException($format, $args, $e);
	}
}

/**
 * Create a html string of <options> from an associative array
 *
 * @version 1.0.1
 * @date 20101021 (v1.0.1) (greg) added checks on $a ; removed htmlentities
 * @author greg
 *
 * @param array $a
 * @param string|array $selected which key(s) should be marked as "selected" (optional)
 * @return string the html to display
 */
function PU_buildHTMLoptions($a, $selected = null)
{
	$options = '';
	if (is_array($a) && (count($a) > 0))
		foreach ($a as $key => $value) {
			$options .= '<option value="' . htmlspecialchars($key) . '"';
			if (($key == $selected) || (is_array($selected) && in_array($key, $selected)))
				$options .=' selected ';
			$options .= '>' . ($value) . '</option>';
		}
	return $options;
}

/**
 * Add an include path to check in for classes.
 *
 * @version 1.1
 * @author greg <greg@phpdevshell.org>
 *
 * @date 20120606 (v1.1) (greg) ensure the given path actually exists before adding it
 *
 * @param string $path
 */
function PU_addIncludePath($path)
{
	if (!empty($path) && file_exists($path)) {
		return set_include_path(get_include_path() . PATH_SEPARATOR . $path);
	}
	return false;
}

/**
 * Better GI than print_r or var_dump -- but, unlike var_dump, you can only dump one variable.
 * Added htmlentities on the var content before echo, so you see what is really there, and not the mark-up.
 *
 * Also, now the output is encased within a div block that sets the background color, font style, and left-justifies it
 * so it is not at the mercy of ambient styles.
 *
 * Inspired from:     PHP.net Contributions
 * Stolen from:       [highstrike at gmail dot com]
 * Modified by:       stlawson *AT* JoyfulEarthTech *DOT* com
 *
 * @param mixed $var  -- variable to dump
 * @param string $var_name  -- name of variable (optional) -- displayed in printout making it easier to sort out what variable is what in a complex output
 * @param string $indent -- used by internal recursive call (no known external value)
 * @param unknown_type $reference -- used by internal recursive call (no known external value)
 */
function PU_printr(&$var, $var_name = NULL, $indent = NULL, $reference = NULL)
{
	$do_dump_indent = "<span style='color:#666666;'>|</span> &nbsp;&nbsp; ";
	$reference = $reference . $var_name;
	$keyvar = 'the_do_dump_recursion_protection_scheme';
	$keyname = 'referenced_object_name';

	// So this is always visible and always left justified and readable
	echo "<div style='text-align:left; background-color:white; font: 100% monospace; color:black;'>";

	if (is_array($var) && isset($var[$keyvar])) {
		$real_var = &$var[$keyvar];
		$real_name = &$var[$keyname];
		$type = ucfirst(gettype($real_var));
		echo "$indent$var_name <span style='color:#666666'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
	} else {
		$var = array($keyvar => $var, $keyname => $reference);
		$avar = &$var[$keyvar];

		$type = ucfirst(gettype($avar));
		if ($type == "String")
			$type_color = "<span style='color:green'>";
		elseif ($type == "Integer")
			$type_color = "<span style='color:red'>";
		elseif ($type == "Double") {
			$type_color = "<span style='color:#0099c5'>";
			$type = "Float";
		} elseif ($type == "Boolean")
			$type_color = "<span style='color:#92008d'>";
		elseif ($type == "NULL")
			$type_color = "<span style='color:black'>";

		if (is_array($avar)) {
			$count = count($avar);
			echo "$indent" . ($var_name ? "$var_name => " : "") . "<span style='color:#666666'>$type ($count)</span><br>$indent(<br>";
			$keys = array_keys($avar);
			foreach ($keys as $name) {
				$value = &$avar[$name];
				PU_printr($value, "['$name']", $indent . $do_dump_indent, $reference);
			}
			echo "$indent)<br>";
		} elseif (is_object($avar)) {
			echo "$indent$var_name <span style='color:#666666'>$type</span><br>$indent(<br>";
			foreach ($avar as $name => $value)
				PU_printr($value, "$name", $indent . $do_dump_indent, $reference);
			echo "$indent)<br>";
		} elseif (is_int($avar))
			echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> $type_color" . htmlentities($avar) . "</span><br>";
		elseif (is_string($avar))
			echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> $type_color\"" . htmlentities($avar) . "\"</span><br>";
		elseif (is_float($avar))
			echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> $type_color" . htmlentities($avar) . "</span><br>";
		elseif (is_bool($avar))
			echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> $type_color" . ($avar == 1 ? "TRUE" : "FALSE") . "</span><br>";
		elseif (is_null($avar))
			echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> {$type_color}NULL</span><br>";
		else
			echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> " . htmlentities($avar) . "<br>";

		$var = $var[$keyvar];
	}

	echo "</div>";
}

/**
 * This method creates a random string with mixed alphabetic characters.
 *
 * @param integer $length The lenght the string should be.
 * @param boolean $uppercase_only Should the string be uppercase.
 * @return string Will return required random string.
 * @author Andy Shellam, andy [at] andycc [dot] net
 */
function PU_createRandomString ($length = 4, $uppercase_only = false)
{
	if ($uppercase_only == true) {
		$template = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	} else {
		$template = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}
	$length = $length - 1;
	$rndstring = false;
	$a = 0;
	$b = 0;
	settype($length, 'integer');
	settype($rndstring, 'string');
	settype($a, 'integer');
	settype($b, 'integer');
	for ($a = 0; $a <= $length; $a ++) {
		$b = rand(0, strlen($template) - 1);
		$rndstring .= $template[$b];
	}
	return $rndstring;
}

/**
 * Function formats locale according to logged in user settings else will default to system.
 *
 * @param boolean $charset Whether the charset should be included in the format.
 * @return string Will return formatted locale.
 * @author Jason Schoeman
 */
function PU_formatLocale ($charset = true, $user_language = false, $user_region = false)
{
	$configuration = $this->configuration;
	if (empty($configuration['charset_format'])) $configuration['charset_format'] = false;
	if (! empty($user_language)) $configuration['user_language'] = $user_language;
	if (! empty($user_region)) $configuration['user_region'] = $user_region;
	if (empty($configuration['user_language'])) $configuration['user_language'] = $configuration['language'];
	if (empty($configuration['user_region'])) $configuration['user_region'] = $configuration['region'];
	if ($charset && ! empty($configuration['charset_format'])) {
		$locale_format = preg_replace('/\{charset\}/', $configuration['charset_format'], $configuration['locale_format']);
		$locale_format = preg_replace('/\{lang\}/', $configuration['user_language'], $locale_format);
		$locale_format = preg_replace('/\{region\}/', $configuration['user_region'], $locale_format);
		$locale_format = preg_replace('/\{charset\}/', $configuration['charset'], $locale_format);
		return $locale_format;
	} else {
		$locale_format = preg_replace('/\{lang\}/', $configuration['user_language'], $configuration['locale_format']);
		$locale_format = preg_replace('/\{region\}/', $configuration['user_region'], $locale_format);
		$locale_format = preg_replace('/\{charset\}/', '', $locale_format);
		return $locale_format;
	}
}

/**
 * Strip a string from the end of a string.
 * Is there no such function in PHP?
 *
 * @param string $str      The input string.
 * @param string $remove   OPTIONAL string to remove.
 *
 * @return string the modified string.
 */
function PU_rightTrim ($str, $remove = null)
{
	$str = (string) $str;
	$remove = (string) $remove;
	if (empty($remove)) {
		return rtrim($str);
	}
	$len = strlen($remove);
	$offset = strlen($str) - $len;
	while ($offset > 0 && $offset == strpos($str, $remove, $offset)) {
		$str = substr($str, 0, $offset);
		$offset = strlen($str) - $len;
	}
	return rtrim($str);
}

/**
 * This method simply renames a string to safe unix standards.
 *
 * @param string $name
 * @param string $replace Replace odd characters with what?
 *
 * @return string
 */
function PU_safeName ($name, $replace = '-')
{
	$search = array('--', '&trade;', '&quot;', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '{', '}', '|', ':', '"', '<', '>', '?', '[', ']', '\\', ';', "'", ',', '.', '/', '*', '+', '~', '`', '=', ' ');
	$new_replaced_name = strtolower(str_replace($search, $replace, $name));
	if (! empty($new_replaced_name)) {
		return $new_replaced_name;
	} else {
		return false;
	}
}

/**
 * Replaces accents with plain text for a given string.
 *
 * @param string $string
 */
function PU_replaceAccents($string)
{
	return str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $string);
}

/**
 * This is a handy little function to strip out a string between two specified pieces of text.
 * This could be used to parse XML text, bbCode, or any other delimited code/text for that matter.
 * Can also return all text with replaced string between tags.
 *
 * @param string $string
 * @param string $start
 * @param string $end
 * @param string $replace Use %s to be replaced with the string between tags.
 * @return string
 */
function PU_SearchAndReplaceBetween ($string, $start, $end, $replace = '', $replace_char='%')
{
	$ini = strpos($string, $start);
	if ($ini === false) return $string;
	$ini += strlen($start);
	$len = strpos($string, $end, $ini) - $ini;
	$string_between = substr($string, $ini, $len);
	if (! empty($replace)) {
		if ($replace_char == '%') {
			$replaced_text = sprintf($replace, $string_between);
			return str_replace($start . $string_between . $end, $replaced_text, $string);
		} else {
			$replaced_text = str_replace($replace_char, $string_between, $replace);
			return str_replace($start . $string_between . $end, $replaced_text, $string);
		}
		return $string;
	} else {
		return $string_between;
	}
}

/**
 * Returns an array containing the current database settings as per the system configuration. This function
 * handles both old legacy settings and the new multi-db setup configuration.
 *
 * TODO: rethink all this
 *
 * @version  1.1
 * @date 20121010 (1.1) (greg) changed to comply with the databases settings move inside a single array $configuration['databases']
 *
 * @param array $configuration The configuration array. This is required since we don't necessarily have access to it otherwise.
 * @param string $db Specifies the database configuration to use, leave blank if not sure.
 * @return array The database settings
 */
function PU_GetDBSettings($configuration, $db = '')
{
	if (!empty($configuration['database_name'])) {
		// Return with legacy database settings
		return array(
			'dsn' => '',
			'database' => $configuration['database_name'],
			'host' => (!empty($configuration['server_address']) ? $configuration['server_address'] : 'localhost'),
			'username' => (!empty($configuration['database_user_name']) ? $configuration['database_user_name'] : 'root'),
			'password' => (!empty($configuration['database_password']) ? $configuration['database_password'] : 'root'),
			'prefix' => (!empty($configuration['database_prefix']) ? $configuration['database_prefix']: 'pds_'),
			'persistent' => (isset($configuration['persistent_db_connection']) ? $configuration['persistent_db_connection'] : false),
			'charset' => (!empty($configuration['database_charset']) ? $configuration['database_charset'] : 'utf8'));
	} else {
		// Return with new style database settings
		$databases = $configuration['databases'];
		if (empty($db)) {
			$db = $configuration['master_database'];
		}

		if (isset($configuration['databases'][$db])) {
			return $configuration['databases'][$db];
		}
	}
	throw new PHPDS_databaseException(_('Unable to provide the required database settings'));
}

/**
 * Pack all available environnement variable into a DB safe string
 * Useful mainly for log functions
 *
 * @return string
 */
function PU_PackEnv()
{
	$env = array(
			'POST' => $_POST,
			'GET' => $_GET,
			'REQUEST' => $_REQUEST,
			'SERVER' => $_SERVER,
			'COOKIE' => $_COOKIE,
			'SESSION' => $_SESSION,
			'ENV' => $_ENV
	);
	$env = addslashes(serialize($env));
	return $env;
}

/**
 * Logs the specified string to the specified file.
 *
 * @param string $text The text you wish to log.
 * @param string $filename The filename to which to log the string to. "debug.log" is used if not specified.
 */
function PU_DebugLog($text, $filename = '')
{
	if (empty($filename)) $filename = 'write/logs/debug.log';
	error_log(date('Y-m-d H:i:s') . ' - ' . $text . "\n", 3, $filename);
}

/**
 * Flattens the given $path and ensure it's below the given root
 *
 * The goal is to avoid getting access to files outside the web site tree
 *
 * @version 1.1
 *
 * @date 20120607 (v1.1) (greg) added support for both absolute path and relative path (relative to the given root)
 *
 * @param string $path
 * @param string $root
 * @return string|false the actual path or false
 */
function PU_SafeSubpath($path, $root)
{
	if (substr($path, 0, 1) != '/') {
		$path = $root.'/'.$path;
	}
	error_log('testing '.$path.' against '.$root);
	$path = realpath($path);
	$result = (substr($path, 0, strlen($root)) == $root) ? $path : false;

	return $result;
}


/**
 * Returns the numerical value of the given value
 * Equivalent of intval() but safe for large number
 *
 * @param mixed $value
 */
function numval($value)
{
	return is_numeric($value) ? $value : 0;
}





/**
 * A class to deal with tree-structured data (such as groups)
 *
 * Usage:
 * $tree = new PU_tree
 * $tree->add(1, 0); // add a root node
 * $tree->add(2, 1, 'leaf'); // add a named leaf to the root node
 * $tree->climb(); // study the tree YOU HAVE TO DO THAT BEFORE ACTUALLY USING THE TREE
 *
 * @author greg
 * @date 20100514
 * @version	1.0
 *
 */
class PU_tree
{

	/**
	 * Associative array of the nodes (element ref => element, usually a label)
	 *
	 * @var array
	 */
	protected $elements = array(0);

	/**
	 * Associative array: for each node (by ref), what are the nodes upper in the tree
	 *
	 * @var array
	 */
	protected $ascendants = array();

	/**
	 * Associative array: for each node (by ref), what are the nodes lower in the tree
	 *
	 * @var array
	 */
	protected $descendants = array();

	/**
	 * Add an element to the tree. When all elements are added, you MUST call climb()
	 *
	 * @param mixed $leaf the new element ref
	 * @param mixed $node the element onto this new element is stuck
	 * @param mixed $label an optional label to display
	 * @return this
	 */
	public function add($leaf, $node, $label = '')
	{
		$this->elements[$leaf] = $label;
		$this->descendants[$node][] = $leaf;

		return $this;
	}

	/**
	 * Climb the tree in order to fill the descendant array.
	 * Don't call it with parameter
	 *
	 * @param unknown_type $branch
	 * @return this
	 */
	public function climb($branch = 0)
	{
		if (!empty($this->descendants[$branch]) && is_array($this->descendants[$branch])) {
			foreach ($this->descendants[$branch] as $leaf) {
				$this->ascendants[$leaf] = (empty($branch)) ? array() : $this->ascendants[$branch];
				if ($branch)
					$this->ascendants[$leaf][] = $branch;
				$this->climb($leaf);
			}
		}
		return $this;
	}

	/**
	 * Returns the ascendants of the given node, either as array or as a string for sql
	 *
	 * @param $node the node which ascendants are asked
	 * @param $as_array	boolean, do you want an array (true) or a string (false)
	 * @return array or string
	 */
	public function ascendants($node, $as_array = false)
	{
		if ($as_array)
			return (isset($this->ascendants[$node]) ? $this->ascendants[$node] : array());
		else
			return (isset($this->ascendants[$node]) ? implode(',', $this->ascendants[$node]) : '');
	}

	/**
	 * Returns the descendants of the given node, either as array or as a string for sql
	 *
	 * @param $node	the node which ascendants are asked
	 * @param $as_array	boolean, do you want an array (true) or a string (false)
	 * @return array or string
	 */
	public function descendants($node, $as_array = false)
	{
		if ($as_array)
			return (isset($this->descendants[$node]) ? $this->descendants[$node] : array());
		else
			return (isset($this->descendants[$node]) ? implode(',', $this->descendants[$node]) : '');
	}

	/**
	 * Returns an array of nodes, either the whole tree, or only the nodes listed in the filter
	 *
	 * @param array $filter
	 * @return array
	 */
	public function nodes(array $filter = null)
	{
		if ($filter)
			return array_intersect_key($this->elements, $filter);
		else
			return $this->elements;
	}

}

///////////////////////// WINDOWS COMPATIBILITY FUNCTIONS //////////////////////////////
// thanks to me at rowanlewis dot com (http://fr2.php.net/manual/en/function.fnmatch.php)

if (!function_exists('fnmatch')) {
	define('FNM_PATHNAME', 1);
	define('FNM_NOESCAPE', 2);
	define('FNM_PERIOD', 4);
	define('FNM_CASEFOLD', 16);

	function fnmatch($pattern, $string, $flags = 0)
	{
		return pcre_fnmatch($pattern, $string, $flags);
	}

}

function pcre_fnmatch($pattern, $string, $flags = 0)
{
	$modifiers = null;
	$transforms = array(
		'\*' => '.*',
		'\?' => '.',
		'\[\!' => '[^',
		'\[' => '[',
		'\]' => ']',
		'\.' => '\.',
		'\\' => '\\\\'
	);

	// Forward slash in string must be in pattern:
	if ($flags & FNM_PATHNAME) {
		$transforms['\*'] = '[^/]*';
	}

	// Back slash should not be escaped:
	if ($flags & FNM_NOESCAPE) {
		unset($transforms['\\']);
	}

	// Perform case insensitive match:
	if ($flags & FNM_CASEFOLD) {
		$modifiers .= 'i';
	}

	// Period at start must be the same as pattern:
	if ($flags & FNM_PERIOD) {
		if (strpos($string, '.') === 0 && strpos($pattern, '.') !== 0)
			return false;
	}

	$pattern = '#^'
		. strtr(preg_quote($pattern, '#'), $transforms)
		. '$#'
		. $modifiers;

	return (boolean) preg_match($pattern, $string);
}

