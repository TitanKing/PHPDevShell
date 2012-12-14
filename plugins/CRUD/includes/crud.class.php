<?php

/**
 * RedBeanPHP ORM plugin.
 *
 * @author Jason Schoeman, maheshchari.com
 */
class crud extends PHPDS_dependant
{
	/**
	 * Cleaned up $_GET.
	 *
	 * @var mixed
	 */
	public $get;
	/**
	 * Cleaned up $_POST.
	 *
	 * @var mixed
	 */
	public $post;
	/**
	 * Cleaned up $_REQUEST.
	 *
	 * @var mixed
	 */
	public $request;
	/**
	 * Should forms be protected agains possible injection?
	 *
	 * @var boolean
	 */
	public $protect = true;
	/**
	 * Where should data from form be looked for?
	 *
	 * @var string
	 */
	public $from = 'post';
	/**
	 * Register an orm service, this allows direct and easy saving to database.
	 *
	 * @var object
	 */
	public $orm = null;
	/**
	 * Register an orm service, this allows direct and easy saving to database.
	 *
	 * @var object
	 */
	public $f;
	/**
	 * Simply stores last field that was validated.
	 *
	 * @var string
	 */
	public $lastField;
	/**
	 * Store selections that did not have id's yet.
	 *
	 * @var string
	 */
	public $selectWrite;
	/**
	 * Contains arrays of errors.
	 *
	 * @var array
	 */
	public $errorExist = array();

	/**
	 * This method does the actual security check, other security checks are done on a per call basis to this method in specific scripts.
	 * Improved version reduces the cost of queries by 3, I also believe that this is a more secure method.
	 *
	 * @param boolean $validate_crypt_key Set if you would like the system to verify an encryption before accepting global $_POST variables. Use with method send_crypt_key_validation in your form.
	 * @return string
	 * @author Jason Schoeman
	 */
	public function construct ($orm = null)
	{
		if (is_object($orm))
			$this->orm = $orm;
		else
			$this->orm = null;

		$this->f = new field();

		if (! empty($this->security->post))
			$this->post = $this->security->post;

		if (! empty($this->security->get))
			$this->get = $this->security->get;

		if (! empty($this->security->request))
			$this->request = $this->security->request;
	}

	/**
	 * After each form validation methods, use this to compile error fields if any.
	 * @author Jason Schoeman
	 */
	public function errorShow()
	{
		$t = $this->template;
		if (! empty($this->errorExist))
			$t->addJsToHead($t->mod->errorField($this->errorExist));
	}

	/**
	 * Check if the data was submitted ok and make sure there are no errors.
	 * @return boolean
	 * @author Jason Schoeman
	 */
	public function ok()
	{
		foreach($this->errorExist as $r)
			if (! empty($r['type']))
				return false;
		return true;
	}

	/**
	 * Ater each validation, add this as the condition to report the error and its message.
	 * @param string $error_message
	 * @param string $field This should be the field name, else it will auto detect.
	 * @author Jason Schoeman
	 */
	public function error($error_message='', $field='')
	{
		if (empty($field))
			$field = $this->lastField;

		$this->errorExist[] = array('type'=>'error', 'message'=>$error_message, 'field'=>$field);
	}

	/**
	 * For a general form error, this can be used to halt the ok process.
	 * @param string $error_message
	 * @param string $id This should be the element id name, else it will use the form tag.
	 * @author Jason Schoeman
	 */
	public function errorElse($error_message='', $field='FORM')
	{
		$this->errorExist[] = array('type'=>'errorElse', 'message'=>$error_message, 'field'=>$field);
	}

	/**
	 * Allows import of arrays and converts them to properties for easy access.
	 * @param type $array
	 * @author Jason Schoeman
	 */
	public function importFields($array)
	{
		if (! empty($array) && is_array($array)) {
			foreach ($array as $key => $val) {
				$this->f->$key = (string) $val;
			}
		}
	}

	/**
	 * Allows system to do general check on specified form receive type.
	 *
	 * @param mixed $key
	 * @param mixed $default
	 * @return mixed
	 * @author Jason Schoeman
	 */
	public function field($key = null, $default = null)
	{
		switch ($this->from) {
			case 'post':
				$r = $this->POST($key, $default);
			break;
			case 'get':
				$r = $this->GET($key, $default);
			break;
			case 'request':
				$r = $this->REQUEST($key, $default);
			break;
			default:
				$r = $this->POST($key, $default);
			break;
		}

		if (!is_array($r)) {
			if (is_object($this->orm))
				$this->orm->$key = (string) trim($r);
			else
				$this->f->$key = (string) trim($r);

			$this->lastField = (string) $key;
		} else {
			$this->lastField = (string) $key . '[]';
		}

		$this->errorExist[$key] = array();

		return $r;
	}

	/**
	 * Return a value from the REQUEST array
	 *
	 * @param string|null $key the name of the post variable to fetch; if null, the entire array is returned
	 * @param mixed|array $default a default value to return when the post variable is not set; when returning the entire array, an array can be given here with default values
	 *
	 * @return scalar|array the content of the post variable or the whole array, possibly with default value(s)
	 * @author Jason Schoeman
	 */
	public function REQUEST($key = null, $default = null)
	{
		($this->protect) ? $r = $this->request : $r = $_REQUEST;

		if (!empty($key)) {
			return (isset($r[$key])) ? $r[$key] : $default;
		} else {
			if (is_array($default)) return array_merge($default, $r);
			else return $r;
		}
	}

	/**
	 * Return a value from the POST array
	 *
	 * @param string|null $key the name of the post variable to fetch; if null, the entire array is returned
	 * @param mixed|array $default a default value to return when the post variable is not set; when returning the entire array, an array can be given here with default values
	 *
	 * @return scalar|array the content of the post variable or the whole array, possibly with default value(s)
	 * @author Jason Schoeman
	 */
	public function POST($key = null, $default = null)
	{
		($this->protect) ? $p = $this->post : $p = $_POST;

		if (!empty($key)) {
			return (isset($p[$key])) ? $p[$key] : $default;
		} else {
			if (is_array($default)) return array_merge($default, $p);
			else return $p;
		}
	}

	/**
	 * Return a value from the GET meta array
	 *
	 * @param string|null $key the name of the get variable to fetch; if null, the entire array is returned
	 * @param mixed|array $default a default value to return when the get variable is not set; when returning the entire array, an array can be given here with default values
	 *
	 * @return scalar|array the content of the get variable or the whole array, possibly with default value(s)
	 * @author Jason Schoeman
	 */
	public function GET($key = null, $default = null)
	{
		($this->protect) ? $g = $this->get : $g = $_GET;

		if (!empty($key)) {
			return (isset($g[$key])) ? $g[$key] : $default;
		} else {
			if (is_array($default)) return array_merge($default, $g);
			else return $g;
		}
	}

	/**
	 * Makes select fields easy to create and maintain.
	 * @param type $options
	 * @param type $selected
	 * @return string
	 * @author Jason Schoeman
	 */
	public function select($options, $selected)
	{
		return $this->selectElements('', $options, $selected, 'select');
	}

	/**
	 * Makes check boxes easy to create and maintain.
	 * @param type $name
	 * @param type $options
	 * @param type $checked
	 * @return string
	 * @author Jason Schoeman
	 */
	public function checkbox($name, $options, $checked)
	{
		return $this->selectElements($name, $options, $checked, 'checkbox');
	}

	/**
	 * Makes radio buttons easy to create and maintain.
	 * @param type $name
	 * @param type $options
	 * @param type $checked
	 * @return string
	 * @author Jason Schoeman
	 */
	public function radio($name, $options, $checked)
	{
		return $this->selectElements($name, $options, $checked, 'radio');
	}

	/**
	 * Maintainer for radio checkboxes and select fields.
	 * @param type $name
	 * @param type $options
	 * @param type $checked
	 * @param type $type
	 * @return string
	 */
	public function selectElements($name, $options, $checked, $type)
	{
		$m = $this->template->mod;
		$option = '';
		if (is_array($options)) {
			foreach ($options as $value => $label) {
				if (! empty($checked) && in_array($value, $checked))
					$select = true;
				else
					$select = null;

				switch ($type) {
					case 'radio':
						$option .= $m->formRadio($name, $value, $label, $select);
					break;
					case 'checkbox':
						$option .= $m->formCheckbox($name, $value, $label, $select);
					break;
					case 'select':
						$option .= $m->formSelect($value, $label, $select);
					break;
				}
			}
		}

		if (empty($option)) {
			return '';
		} else {
			return $option;
		}
	}

	/**
	 * Allows you to easily maintain selected fields.
	 * @param string $val
	 * @param int $join_id
	 * @param string $columns
	 * @return array
	 */
	public function multiSelected($val, $join_id=null, $columns = 'join_id,value')
	{
		if (is_object($this->orm)) {
			// User ORM
			return $this->multiSelectedORM($val, $join_id, $columns);
		} else {
			// Use Model
			return $this->multiSelectedModel($val, $join_id, $columns);
		}
	}

	/**
	 * Simple check for multiple options.
	 *
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isMultipleOption($val, $default = null)
	{
		$array = $this->field($val, $default);
		if (! in_array($array, array(null, false, '', array()), true)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Allows you to easily maintain selected fields.
	 * @param string $val
	 * @param int $join_id
	 * @param string $columns
	 * @param mixed The default value that should be used when empty.
	 * @return array
	 */
	public function multiSelectedModel($val, $join_id=null, $columns = 'join_id,value', $default = null)
	{
		if (empty($join_id))
			if (! empty($this->f->id))
				$join_id = $this->f->id;

		$previously_selected = $this->field($val, $default);
		list($join_id_col, $value_col) = explode(',', $columns);

		if (! empty($join_id) && empty($previously_selected)) {
			$previously_selected = $this->db->invokeQuery('CRUD_readMultipleOptions', $value_col, $val, $join_id_col, $join_id);

			if (! empty($previously_selected)) {
				foreach ($previously_selected as $valprev) {
					$array[] = $valprev[$value_col];
				}
				if (! empty($array))
					return $array;
				else
					return array();
			}
		} else {
			if (! empty($previously_selected)) {

				if (! empty($join_id)) {
					$this->db->invokeQuery('CRUD_writeMultipleOptions', $val, $join_id_col, $join_id, $value_col, $previously_selected);
				}

				foreach ($previously_selected as $valprev) {
					$array[] = $valprev;
				}
				if (! empty($array))
					return $array;
				else
					return array();
			}
		}
	}

	/**
	 * Allows you to easily maintain selected fields.
	 * @param string $val
	 * @param int $join_id
	 * @param string $columns
	 * @param mixed The default value that should be used when empty.
	 * @return array
	 */
	public function multiSelectedORM($val, $join_id=null, $columns = 'join_id,value', $default = null)
	{
		if (empty($join_id))
			if (! empty($this->orm->id))
				$join_id = $this->orm->id;

		$previously_selected = $this->field($val, $default);
		list($join_id_col, $value_col) = explode(',', $columns);

		if (! empty($join_id) && empty($previously_selected)) {
			$previously_selected = R::find($val, " {$join_id_col} = {$join_id} ");

			if (! empty($previously_selected)) {
				foreach ($previously_selected as $valprev) {
					$array[] = $valprev[$value_col];
				}
				if (! empty($array))
					return $array;
				else
					return array();
			}
		} else {
			if (! empty($previously_selected)) {

				if (! empty($join_id)) {
					// Delete old selections.
					$replace = R::find($val, " {$join_id_col} = {$join_id} ");

					if (! empty($replace)) {
						foreach ($replace as $valprev) {
							$bean = R::load("$val", $valprev['id']);
							R::trash($bean);
						}
					}

					if (! empty($previously_selected)) {
						foreach ($previously_selected as $value) {
							$multipleORM = R::dispense($val);
							$multipleORM->$join_id_col = $join_id;
							$multipleORM->$value_col = $value;
							R::store($multipleORM);
						}
					}
				}

				foreach ($previously_selected as $valprev) {
					$array[] = $valprev;
				}
				if (! empty($array))
					return $array;
				else
					return array();
			}
		}
	}

	/**
	 * a Clean way to add more variable to crud stack.
	 * @param string expecting form field name
	 * @param mixed a Default value to set the field to if failing.
	 * @param mixed The default value that should be used when empty.
	 * @return mixed
	 */
	public function addField($val, $default = null) {
		$this->field($val, $default);
		return $val;
	}

	/**
	 * check if field empty string ,orject,array
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function is($val, $default = null) {
		$val = $this->field($val, $default);
		return ! in_array($val, array(null, false, '', array()), true);
	}

	/**
	 * Returns fields value
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isField($val, $default = null) {
		$val = $this->field($val, $default);
		return $val;
	}

	/**
	 * check a number optional -,+,. values
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isNumeric($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $val);
	}

	/**
	 * valid email
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isEmail($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) (preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i", $val));
	}

	/**
	 * Valid URL or web address
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isUrl($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/^((((https?|ftps?|gopher|telnet|nntp):\/\/)|(mailto:|news:))(%[0-9A-Fa-f]{2}|[-()_.!~*';\/?:@&=+$,A-Za-z0-9])+)([).!';\/?:,][[:blank:]])?$/", $val);
	}

	/**
	 * Valid IP address
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isIpAddress($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/", $val);
	}

	/**
	 * Matches only alpha letters
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isAlpha($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/^([a-zA-Z])+$/i", $val);
	}

	/**
	 * Matches alpha and numbers only
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isAlphaNumeric($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/^([a-zA-Z0-9])+$/i", $val);
	}

	/**
	 * Matches alpha ,numbers,-,_ values
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isAlphaNumericDash($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/^([-a-zA-Z0-9_-])+$/i", $val);
	}

	/**
	 * Matches alpha and dashes like -,_
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isAlphaDash($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/^([A-Za-z_-])+$/i", $val);
	}

	/**
	 * Matches exactly number
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isInteger($val, $default = null) {
		$val = $this->field($val, $default);
		return is_int($val);
	}

	/**
	 * Valid Credit Card
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isCreditCard($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/^((4\d{3})|(5[1-5]\d{2})|(6011)|(7\d{3}))-?\d{4}-?\d{4}-?\d{4}|3[4,7]\d{13}$/", $val);
	}

	/**
	 * check given string length is between given range
	 * @param   string expecting form field name
	 * @param	int min
	 * @param	int max
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isRangeLength($val, $min = 0, $max = 0, $default = null) {
		$val = $this->field($val, $default);
		return (strlen($val) >= $min and strlen($val) <= $max);
	}

	/**
	 * Check the string length has minimum length
	 * @param   string expecting form field name
	 * @param	int min
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isMinLength($val, $min, $default = null) {
		$val = $this->field($val, $default);
		return (strlen($val) >= (int) $min);
	}

	/**
	 * check string length exceeds maximum length
	 * @param   string expecting form field name
	 * @param	int max
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isMaxLength($val, $max, $default = null) {
		$val = $this->field($val, $default);
		return (strlen($val) <= (int) $max);
	}

	/**
	 * check given number exceeds max values
	 * @param   string expecting form field name
	 * @param	int max
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isMaxValue($val, $max, $default = null) {
		$number = $this->field($val, $default);
		return ($number >= $max);
	}

	/**
	 * check given number below value
	 * @param   string expecting form field name
	 * @param	int min
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isMinValue($val, $min, $default = null) {
		$number = $this->field($val, $default);
		return ($number <= $min);
	}

	/**
	 * check given number between given values
	 * @param   string expecting form field name
	 * @param	int min
	 * @param	int max
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isRangeValue($val, $min, $max, $default = null) {
		$number = $this->field($val, $default);
		return ($number >= $min and $number <= $max);
	}

	/**
	 * check for exactly length of string
	 * @param   string expecting form field name
	 * @param	int expecting lenght of string
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isLength($val, $length, $default = null) {
		$val = $this->field($val, $default);
		return (strlen($val) == (int) $length);
	}

	/**
	 * check decimal with . is optional and after decimal places up to 6th precision
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isDecimal($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) pregMatch("/^\d+(\.\d{1,6})?$/'", $val);
	}

	/**
	 * Valid hexadecimal color ,that may have #,
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isHexColor($val, $default = null) {
		$color = $this->field($val, $default);
		return (bool) preg_match('/^#?+[0-9a-f]{3}(?:[0-9a-f]{3})?$/i', $color);
	}

	/**
	 * Matches  againest given regular expression ,including delimeters
	 * @param   string expecting form field name
	 * @param	string regular expression string to compare against
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isRegex($val, $expression, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match($expression, (string) $val);
	}

	/**
	 * compares two any kind of values ,stictly
	 * @param   string expecting form field name
	 * @param	mixed expecting string to compare too
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isMatches($val, $value, $default = null) {
		$val = $this->field($val, $default);
		return ($val === $value);
	}

	/**
	 * check if field empty string ,orject,array
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isEmpty($val, $default = null) {
		$val = $this->field($val, $default);
		return in_array($val, array(null, false, '', array()), true);
	}

	/**
	 * Check if given string matches any format date
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isDate($val, $default = null) {
		$val = $this->field($val, $default);
		return (strtotime($val) !== false);
	}

	/**
	 * check given string againest given array values
	 * @param   string expecting form field name
	 * @param	array
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isEnum($val, $arr, $default = null) {
		$val = $this->field($val, $default);
		return in_array($val, $arr);
	}

	/**
	 * Checks that a field matches a v2 md5 string
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isMd5($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/[0-9a-f]{32}/i", $val);
	}

	/**
	 * Matches base64 enoding string
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isBase64($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) !preg_match('/[^a-zA-Z0-9\/\+=]/', $val);
	}

	/**
	 * check if array has unique elements,it must have  minimum one element
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isUnique($val, $default = null) {
		$arr = $this->field($val, $default);
		$arr = (array) $arr;
		$count1 = count($arr);
		$count2 = count(array_unique($arr));
		return (count1 != 0 and (count1 == $count2));
	}

	/**
	 * Check is rgb color value
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isRgb($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/^(rgb\(\s*\b([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\b\s*,\s*\b([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\b\s*,\s*\b([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\b\s*\))|(rgb\(\s*(\d?\d%|100%)+\s*,\s*(\d?\d%|100%)+\s*,\s*(\d?\d%|100%)+\s*\))$/", $val);
	}

	/**
	 * is given field is boolean value or not
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isBoolean($val, $default = null) {
		$val = $this->field($val, $default);
		$booleans = array(1, 0, '1', '0', true, false, true, false);
		$literals = array('true', 'false', 'yes', 'no');
		foreach ($booleans as $bool) {
			if ($val === $bool)
				return true;
		}

		return in_array(strtolower($val), $literals);
	}

	/**
	 * A token that don't have any white space
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isToken($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) !preg_match('/\s/', $val);
	}

	/**
	 * Checks that a field is exactly the right length.
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @link  http://php.net/checkdnsrr  not added to Windows until PHP 5.3.0
	 * @return  boolean
	 */
	public function isEmailDomain($val, $default = null) {
		$email = $this->field($val, $default);
		return (bool) checkdnsrr(preg_replace('/^[^@]++@/', '', $email), 'MX');
	}

	/**
	 * Matches a phone number that length optional numbers 7,10,11
	 * @param   string expecting form field name
	 * @param	int expecting number lenght
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isPhone($val, $lengths = null, $default = null) {
		$number = $this->field($val, $default);
		if (!is_array($lengths)) {
			$lengths = array(7, 10, 11);
		}
		$number = preg_replace('/\D+/', '', $number);
		return in_array(strlen($number), $lengths);
	}

	/**
	 * check given sting is UTF8
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isUtf8($val, $default = null) {
		$val = $this->field($val, $default);
		return preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]
        |\xE0[\xA0-\xBF][\x80-\xBF]
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
        |\xED[\x80-\x9F][\x80-\xBF]
        |\xF0[\x90-\xBF][\x80-\xBF]{2}
        |[\xF1-\xF3][\x80-\xBF]{3}
        |\xF4[\x80-\x8F][\x80-\xBF]{2}
        )+%xs', $val);
	}

	/**
	 * Given sting is lower cased
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isLower($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/^[a-z]+$/", $val);
	}

	/**
	 * Given string is upper cased?
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isUpper($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/^[A-Z]+$/", $val);
	}

	/**
	 * Checks that given value matches following country pin codes.
	 * at = austria
	 * au = australia
	 * ca = canada
	 * de = german
	 * ee = estonia
	 * nl = netherlands
	 * it = italy
	 * pt = portugal
	 * se = sweden
	 * uk = united kingdom
	 * us = united states
	 * @param String expecting form field name
	 * @param String expecting country code
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isPincode($val, $country = 'us', $default = null) {
		$val = $this->field($val, $default);
		$patterns = array('at' => '^[0-9]{4,4}$', 'au' => '^[2-9][0-9]{2,3}$', 'ca' =>
			'^[a-zA-Z].[0-9].[a-zA-Z].\s[0-9].[a-zA-Z].[0-9].', 'de' => '^[0-9]{5,5}$', 'ee' =>
			'^[0-9]{5,5}$', 'nl' => '^[0-9]{4,4}\s[a-zA-Z]{2,2}$', 'it' => '^[0-9]{5,5}$',
			'pt' => '^[0-9]{4,4}-[0-9]{3,3}$', 'se' => '^[0-9]{3,3}\s[0-9]{2,2}$', 'uk' =>
			'^([A-Z]{1,2}[0-9]{1}[0-9A-Z]{0,1}) ?([0-9]{1}[A-Z]{1,2})$', 'us' =>
			'^[0-9]{5,5}[\-]{0,1}[0-9]{4,4}$');
		if (!array_key_exists($country, $patterns))
			return false;
		return (bool) preg_match("/" . $patterns[$country] . "/", $val);
	}

	/**
	 * Check given url really exists?
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isUrlExists($val, $default = null) {
		$link = $this->field($val, $default);
		if (!$this->isUrl($link))
			return false;
		return (bool) @fsockopen($link, 80, $errno, $errstr, 30);
	}

	/**
	 * Check given sting has script tags
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isJsSafe($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) (!preg_match("/<script[^>]*>[\s\r\n]*(<\!--)?|(-->)?[\s\r\n]*<\/script>/", $val));
	}

	/**
	 * given sting has html tags?
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isHtmlSafe($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) (!preg_match("/<(.*)>.*</$1>/", $val));
	}

	/**
	 * check given sring has multilines
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isMultiLine($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/[\n\r\t]+/", $val);
	}

	/**
	 * check given array key element exists?
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isExists($val, $arr, $default = null) {
		$val = $this->field($val, $default);
		return isset($arr[$val]);
	}

	/**
	 * is given string is ascii format?
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isAscii($val, $default = null) {
		$val = $this->field($val, $default);
		return !preg_match('/[^\x00-\x7F]/i', $val);
	}

	/**
	 * Checks given value again MAC address of the computer
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isMacAddress($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match('/^([0-9a-fA-F][0-9a-fA-F]:){5}([0-9a-fA-F][0-9a-fA-F])$/', $val);
	}

	/**
	 * Checks given value matches us citizen social security number
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isUsssn($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/^\d{3}-\d{2}-\d{4}$/", $val);
	}

	/**
	 * Checks given value matches date de
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isDateDE($val, $default = null) {
		$date = $this->field($val, $default);
		return (bool) preg_match("/^\d\d?\.\d\d?\.\d\d\d?\d?$/", $date);
	}

	/**
	 * Checks given value matches us citizen social security number
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isDateISO($val, $default = null) {
		$date = $this->field($val, $default);
		return (bool) preg_match("/^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/", $date);
	}

	/**
	 * Checks given value matches a time zone
	 * +00:00 | -05:00
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isTimezone($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/^[-+]((0[0-9]|1[0-3]):([03]0|45)|14:00)$/", $val);
	}

	/**
	 * Time in 24 hours format with optional seconds
	 * 12:15 | 10:26:59 | 22:01:15
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isTime24($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/", $val);
	}

	/**
	 * Time in 12 hours format with optional seconds
	 * 08:00AM | 10:00am | 7:00pm
	 * @param   string expecting form field name
	 * @param	mixed The default value that should be used when empty.
	 * @return  boolean
	 */
	public function isTime12($val, $default = null) {
		$val = $this->field($val, $default);
		return (bool) preg_match("/^([1-9]|1[0-2]|0[1-9]){1}(:[0-5][0-9][aApP][mM]){1}$/", $val);
	}

}

class field
{
	public function __get($name)
	{
		return $this->$name = null;
	}

	public function __set($name, $value)
	{
		return $this->$name = $value;
	}
}