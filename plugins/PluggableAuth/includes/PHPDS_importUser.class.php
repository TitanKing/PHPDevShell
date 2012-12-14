<?php

/**
<<<<<<< TREE
 * PHPDevShell is a RAD Framework aimed at developing administrative applications.
 *
 * @package PHPDevShell
 * @link http://www.phpdevshell.org
 * @copyright Copyright (C) 2007 Jason Schoeman, All rights reserved.
 * @license GNU/LGPL, see readme/licensed_under_lgpl or http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html
 *
 * Copyright notice: See readme/notice
 * By using PHPDevShell you agree to notice and license, if you dont agree to this notice/license you are not allowed to use PHPDevShell.
 *
 */

require_once dirname(__FILE__).'/../models/PHPDS_importUser.query.php';

/**
=======
>>>>>>> MERGE-SOURCE
 * A class representing a user in PHPDevShell.
 *
 * Can be used to examine / modify an existing user, or create / import  a new user
 *
 * @date 20091125
 * @version 1.0.1
 * @author Greg
 */
class PHPDS_importUser extends PHPDS_user
{
	//for flexibily all user data are stored in an array
	protected $data = array();  // main table
	protected $data_ex = array(); // overflow table
	protected $ready = false; // if the user object is actually ready to be used (i.e. fields are valid)
	protected $userid = false; // if the user is know to exist in the database, here is his user ID
	protected $dirty = false; // if the user data in memory are different from the user data in the database
	protected $tokenid = false; // token to authorize the user
	protected $overflowtable = false; // table to hold extended data
	protected $primary_group = null;
	protected $primary_role = null;
	protected $extra_groups = array(); // associative array: [groupID] = true
	protected $extra_roles = array(); // associative array: [roleID] = true
	protected $lookupQuery; // PHPDS_FindUsersQuery
	/**
	 * Constructor
	 *
	 * Initialize a User object and optionnaly tries to load a given user's data
	 *
	 * @version 								1.0.2
	 * @author	greg
	 * @date        20100921              (v1.0.2) (greg) moved to new style constructor, removing the $dependancy parameter
	 * @date			20100412				(v1.0.1) (greg) removed tokenid
	 * @date			20091126				created
	 * @return 									the user obejct
	 */

	public function construct($username = null)
	{
		$this->init();

		if ($username) $this->load($username);

		return $this;
	}

	/**
	 * Magic getter
	 * Used to directly  access (read only) the user's data
	 *
	 * @version 								1.0
	 * @date			20091126				created
	 * @see current/includes/PHPDS_dependant#__get($name)
	 */
	public function __get($field)
	{
		if (isset($this->data[$field])) return $this->data[$field];
		if (isset($this->data_ex[$field])) return $this->data_ex[$field];
		return parent::__get($field);
	}

	/**
	 * Initialize the object with some default values
	 *
	 * For all load/import methods, this is what the new data will be merge onto
	 *
	 * @version 								1.0
	 * @date			20091126				created
	 * @return 									the user obejct
	 */
	public function init()
	{
		$this->data = array();
		$this->data_ex = array();

		$this->extra_groups = array();
		$this->extra_roles = array();

		$this->ready = false;
		$this->dirty = false;
		$this->userid = false;

		// these fields are to be filled
		$this->data['user_id'] = 0;
		$this->data['user_name'] = _('MISSING???');
		$this->data['user_display_name'] = _('MISSING???');
		$this->data['user_email'] = _('MISSING???');
		$this->data['user_password'] = 'password';

		$settings = $this->db->getSettings(array('registration_group', 'registration_role'), 'PHPDevShell');
		// these fields have default values

		//$this->PHPDS_dependance->copyArray($settings, &$this->data, array('registration_group'));

		$this->data['user_group'] = $settings['registration_group'];
		$this->data['user_role'] = $settings['registration_role'];
		$this->data['date_registered'] = $this->configuration['time'];
		$this->data['language'] = $this->configuration['language'];
		if (isset($this->configuration['user_timezone'])) {
			$this->data['user_timezone'] = $this->configuration['user_timezone'];
			$this->data['timezone'] = $this->configuration['user_timezone'];
		} else {
			$this->data['user_timezone'] = '';
			$this->data['timezone'] = '';
		}
		$this->data['region'] = $this->configuration['region'];

		return $this;
	}

	/**
	 * Set/Get the user ID
	 *
	 * @version 								1.0
	 * @date			20091126				created
	 * @author									greg
	 * @param 	$userID					(optional)
	 * @return 	integer					the user ID
	 */
	protected function ID($userID = null)
	{
		$ID = intval($userID);
		if ($ID > 0) {
			$this->userid = $ID;
			$this->data['user_id'] = $ID;
		}
		return $this->userid;
	}

	/**
	 * Return an associative array with all user data
	 *
	 * @version 								1.0
	 * @author									greg
	 * @return array
	 */
	public function data()
	{
		return array_merge($this->data, $this->data_ex);
	}

	/**
	 *
	 * @return PHPDS_query
	 */
	protected function lookupQuery()
	{
		if (empty($this->lookupQuery)) {
			$this->lookupQuery = $this->db->makeQuery('PHPDS_FindUserQuery');
		}
		return $this->lookupQuery;
	}

	/**
	 * Lookup a name in the database, optionaly loading the data
	 *
	 * Note: if the data is loaded, the object is reset before importing. If the data is not loaded, the object stays untouched
	 *
	 * @version 								1.0
	 * @date			20091126				created
	 * @author									greg
	 * @param 	$username			(optional) "user_name" to lookup (if empty, taken from the user's data)
	 * @param 	$load						(optional, default is not to load) boolean, do we load the data into the object
	 * @return 	integer					the user ID (or false if not found)
	 */
	public function lookup($username = null, $load = false)
	{
		$ID = false;
		$exists = false;

		$existing_user = $this->lookupQuery()->invoke($username);
		$exists = !empty($existing_user['user_id']);

		if ($exists) $ID = intval($existing_user['user_id']);

		if ($exists && $load) {
			$this->init();
			$this->ID($existing_user['user_id']);

			$this->importArray($existing_user);

			$this->primary_group = $existing_user['user_group'];
			$this->primary_role = $existing_user['user_role'];

			$groups = $this->db->invokeQuery('PHPDS_UserGroupsQuery', $ID);
			if ($groups)
					foreach ($groups as $group)
					$this->addGroup($group['user_group_id']);

			$roles = $this->db->invokeQuery('PHPDS_UserRolesQuery', $ID);
			if ($roles)
					foreach ($roles as $role)
					$this->addRole($role['user_role_id']);

			$this->ready = true;
			$this->dirty = false;
		}

		$this->log('Looking up user "'.$username.'" gave id '.$ID);
		return $ID;
	}

	/**
	 * Add the user to a group
	 *
	 * @param $groupID
	 * @return unknown_type
	 */
	public function addGroup($groupID)
	{
		//array_push($this->extra_groups, intval($groupID));
		$this->extra_groups[intval($groupID)] = true;
	}
	
	public function removeGroup($groupID)
	{
		//array_push($this->extra_groups, intval($groupID));
		unset($this->extra_groups[intval($groupID)]);
	}

	/**
	 * Return an array of the roles given to the user
	 *
	 * @return unknown_type
	 */
	public function roles()
	{
		return array_keys($this->extra_roles);
	}

	/**
	 * Return an array of the possible roles for the user
	 *
	 * @return unknown_type
	 */
	public function possibleRoles()
	{
		return $this->db->invokeQuery('PHPDS_RolesQuery');
	}

	/**
	 * Return an array of the groups the user belongs to
	 *
	 * @return unknown_type
	 */
	public function groups()
	{
		return array_keys($this->extra_groups);
	}

	/**
	 * Add a role to the user
	 *
	 * @param $roleID
	 * @return unknown_type
	 */
	public function addRole($roleID)
	{
		$this->extra_roles[intval($roleID)] = true;
	}

	/**
	 * Import some data from an array into the object
	 *
	 * @version 								1.0
	 * @date			20091126				created
	 * @param $import_data			associative array of user data
	 * @return 									the user object
	 */
	public function importArray(array $import_data)
	{
		$this->ready = false;
		
		foreach ($import_data as $field => $value) {
			if (isset($this->data[$field])) $this->data[$field] = $value;
			else $this->data_ex[$field] = $value;
		}

		$this->dirty = true;
		$this->ready = true;

		return $this;
	}
	
	public function import(array $import_array)
	{
		$this->ready = false;
		
		foreach ($import_array as $field => $value) {
			$import_data[$field] = $this->valuePrep($field, $value);
		}

		$this->importPrep($import_data);

		return $this;
		
	}

	/**
	 * Load a user from the database into the object
	 *
	 * @version 								1.0.1
	 * @date			20100412				(v1.0.1) (greg) $username is optional
	 * @date			20091126				created
	 * @author	greg
	 * @param 	$username			(optional) "user_name" to lookup (if empty, taken from the user's data)
	 * @return 	integer					the user ID (or false if not found)
	 */
	public function load($username = null)
	{
		//$this->init();
		if (!$username && isset($this->data['user_name']))
				$username = $this->data['user_name'];
		return $this->lookup($username, true);
	}

	/**
	 * Save the user object  into the database
	 *
	 * @version 								1.1
	 * @date			20091126				created
	 * @date			20100329				heavily modified
	 * @return unknown_type
	 */
	public function save()
	{
		if ($this->userid) {
			$existing_user = $this->userid;
		} else {
			if (isset($this->data['user_name'])) {
				$existing_user = $this->lookup(array('user_name' => $this->data['user_name']));
			}
		}

		if (empty($existing_user)) $existing_user = 'NULL';


		$this->log('About to save user id '.$existing_user.' from id '.$this->userid.' / name '.$this->data['user_name']);
		$db = $this->db;
		$user_id = $db->invokeQueryWith('PHPDS_UserReplace', array_merge($this->data, array('existing_user' => $existing_user)));

		$this->log('Saving user id '.$user_id);

		$db->invokeQuery('PHPDS_UserSetRolesQuery', $user_id, array_keys($this->extra_roles));
		$db->invokeQuery('PHPDS_UserSetGroupsQuery', $user_id, array_keys($this->extra_groups));


		// TODO
		if (empty($this->overflowtable)) $this->log('No overflow table'); else $this->log('Overflow table is '.$this->overflowtable);
		if (!empty($this->overflowtable) && !empty($this->data_ex)) {
			$overflow_values = false;
			foreach ($this->data_ex as $custom_column => $custom_column_value) {
				$custom_column_value_ = $this->db->protect(str_replace(array("\n", "\r", "\r\n", "\n\r", ",", ";", "'", '"'), '', trim($custom_column_value)));
				$overflow_values .= "$custom_column = '$custom_column_value_',";
			}
			$overflow_values = rtrim($overflow_values, ",");
			$sql = "REPLACE INTO
				{$this->overflowtable}
				SET
				user_id =$user_id, $overflow_values";
			$this->db->newQuery($sql);
		}

		$this->ID($user_id);

		return $this;
	}

	/**
	 * Import some data from a csv-like string into the object
	 *
	 * Note: the object is NOT reset before importing
	 *
	 * @version 1.0
	 * @date 20091126 created
	 * @param $line	string, the line to import
	 * @param $fields array, the (ordered) names of the fields
	 * @param $delimiter (optional) string, the csv delimiter
	 * @return boolean false if the line doesn't contains as much fields as the $fields array
	 */
	public function importCsvLine($line, $fields, $delimiter = "\t")
	{
		$values = explode($delimiter, $line);
		if ($import = array_combine($fields, $values)) {
			$this->import($import);
			return true;
		} else return false;
	}

	/**
	 * Import several csv-like lines, and save the corresponding user for each line
	 *
	 * Use $this->import_prep() as a callback for each line
	 *
	 * @param $lines array of strings, the lines to import
	 * @param $fields array, the (ordered) names of the fields
	 * @param $delimiter (optional) string, the csv delimiter
	 */
	public function importCsv($lines, $fields, $delimiter = "\t")
	{
		foreach ($lines as $line) {
			$this->init();
			$this->importCsvLine($line, $fields, $delimiter);
			if ($this->dirty) $this->save();
		}
	}

	/**
	 * Import a whole csv-like file into the database
	 *
	 * Use $this->import_prep() as a callback for each line
	 *
	 * @param $filename				full pathname of the file
	 * @param $fields						array, the (ordered) names of the fields
	 * @param $delimiter				(optional) string, the csv delimiter
	 * @return unknown_type
	 */
	public function importCsvFile($filename, $fields, $delimiter = "\t")
	{
		$content = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES | FILE_TEXT);
		if ($content) return $this->importCsv($content, $fields, $delimiter);
		else return false;
	}

	/**
	 * This function will be called for each line when importing a file. It's meant to be overriden.
	 * Here is a very simple example.
	 *
	 * No paramaters or return value: everything is done with the object's data
	 *
	 * Note 1: it's currently only used when importing csv data
	 * Note 2: values have already been "prepared"
	 */
	public function importPrep($import_data)
	{
		if (($import_data['user_display_name'] == _('MISSING???')) || empty($import_data['user_display_name'])) {
			$import_data['user_display_name'] = $import_data['user_name'];
		}

		$this->importArray($import_array);
		
		return $this;
	}

	/**
	 * This function is called for each value when importing data
	 *
	 * Its goal is to "prepare" a value before actually importing it
	 * This basic implementation just cleans values which are surrounded by double quotes. You are supposed to override it according to your data format
	 *
	 * @param	$field			name of the field containing the value
	 * @param 	$value		the value to prepare
	 * @return 						mixed, the prepare valued
	 */
	public function valuePrep($field, $value)
	{
		$matches = array();
		if (preg_match('/"(.*)"/', $value, $matches)) {
			$value = $matches[1];
		}
		return $value;
	}

	/**
	 * Dump the object content
	 *
	 * @return unknown_type
	 */
	public function dump()
	{
		print "\nUserID ";
		print_r($this->userid);
		print "\ndirty ";
		print_r($this->dirty);
		print "\nread ";
		print_r($this->ready);
		print "\ndata ";
		print_r($this->data);
		print "\ndata_ex ";
		print_r($this->data_ex);
		print "\nprimary_group ";
		print_r($this->primary_group);
		print "\nextra_groups ";
		print_r(array_keys($this->extra_groups));
		print "\nprimary_role ";
		print_r($this->primary_role);
		print "\nextra_roles ";
		print_r(array_keys($this->extra_roles));
	}

	/**
	 *
	 * @param <type> $name
	 * @param <type> $value
	 */
	public function __set($name, $value)
	{
		$setter = 'set_' . $name;
		// if there is an explicit method to set this value, let's call it
		if (method_exists($this, $setter)) $this->$setter($value);
		else {
			// no explicit method, so let's try what we can do
			if (isset($this->data[$name])) $this->data[$name] = $value;
			elseif (isset($this->data['user_' . $name]))
					$this->data['user_' . $name] = $value;
			else $this->data_ex[$name] = $value;
		}
	}

	/**
	 *
	 * @param <type> $password
	 * @return PHPDS_user
	 */
	public function setPassword($password)
	{
		$this->data['user_password'] = md5($password);
		return $this;
	}

	/**
	 * Lookup all users which are members of any of the current user's groups
	 *
	 * @return array
	 */
	public function siblings()
	{
		return $this->db->invokeQuery('PHPDS_FindUsersQuery');
	}
}

/**
 * A specific tree for groups handling
 *
 * Usage:
 *
 * 		$tree = PHPDS_GroupTree::singleton($dependance);
 * 		echo '<form action="" method="post"><ul id="example">';
 * 		echo $tree->make_html();
 * 		echo '<input type="submit" name="submit" value="Send" .>';
 * 		echo '</ul></form>';
 *
 * Can also be used to find a subgroup for a query:
 *
	$this->where = $tree->user_groups_sql(35); // group 35 and all groups inside it

 * Note: it fit in the PHPDS_dependant system
 *
 * @author greg
 *
 */
class PHPDS_groupTree extends PU_tree
{
	protected static $instance;
	protected static $dependance;

	/** constructor is private to ensure singleton
	 *
	 * @param $dependance
	 * @return irrelevant
	 */
	private function __construct($dependance)
	{
		$this->dependance = $dependance;
	}

	/**
	 * Return the single instance
	 *
	 * @param PHPDS_dependant $dependance
	 * @return PHPDS_GroupTree
	 */
	public static function singleton($dependance = null)
	{
		if (!isset(PHPDS_groupTree::$instance)) {
			PHPDS_groupTree::$instance = new PHPDS_groupTree($dependance);
		}
		return PHPDS_groupTree::$instance;
	}

	/**
	 * Load (only once) all the groups into the tree
	 *
	 * @return this
	 */
	public function load()
	{
		if (empty($this->descendants)) {
			$groups = $this->dependance->db->invokeQuery('PHPDS_AllGroupsQuery');
			if (false == $groups) throw new Exception('Error loading group list');

			foreach ($groups as $group) {
				$this->add($group['user_group_id'], $group['parent_group_id'], $group['user_group_name']);
			}

			$this->climb();
		}
		return $this;
	}

	/**
	 * Build an html representation of the groups selector (including INPUTs)
	 *
	 * Result is something like:
	 *
	 * 		<ul>
	 * 			<li>
	 * 				<input type="checkbox" id="group_2"  value="2" checked /><label for="group_2">Registered<label>
	 * 				<ul>
	 * 					<li>
	 * 						<input type="checkbox" id="group_6"  value="6" checked /><label for="group_6">Clients<label><input type="checkbox" id="group_7"  value="7" checked />
	 * 					</li>
	 * 				</ul>
	 * 			</li>
	 * 		</ul>
	 *
	 * @param integer $branch starting branch (optional, usually 0)
	 * @return string	html
	 */
	public function makeHtml($branch = 0)
	{
		$this->load();
		$cuts = $this->userGroups($branch, true);
		$selected = $cuts;
		return $this->makeHtmlBranch($branch, $cuts, $selected);
	}

	/**
	 * Sub for the previous function - do not use
	 *
	 * @param unknown_type $branch
	 * @param array $cuts
	 * @param array $selected
	 * @param unknown_type $propagate
	 * @return unknown_type
	 */
	public function makeHtmlBranch($branch, array $cuts, array $selected, $propagate = false)
	{
		$html1 = '';
		$html2 = '';

		$propagate = $propagate || in_array($branch, $cuts);
		$display = $propagate || (0 == $branch);

		if ($display) {
			if ($branch) {
				$id = 'group_' . $branch;
				$html1 .= '<input type="checkbox" id="' . $id . '" ';
				if (isset($this->elements[$branch])) $html1 .= ' value="' . $branch . '"';
				if (in_array($branch, $selected)) $html1 .= ' checked ';
				$html1 .= '/>';
				$html1 .= '<label for="' . $id . '">' . $this->elements[$branch] . '<label>';
			} else {
				$html1 = 'all';
			}
		}

		if (!empty($this->descendants[$branch])) {
			foreach ($this->descendants[$branch] as $node)
				$html2 .= $this->makeHtmlBranch($node, $cuts, $selected, $propagate);
		}

		// this is tricky, since we may have to display a group nested inside another group which is not displayed
		$html = '';
		if ($html1) $html .= "<li>$html1";
		if ($html1 && $html2) $html .= '<ul>';
		if ($html2) $html .= $html2;
		if ($html1 && $html2) $html .= '</ul>';
		if ($html1) $html .= "</li>\n";

		return $html;
	}

	/**
	 * Returns all groups the user belongs to, which are children of given branch
	 *
	 * Note: this is used to deal with implicit membership, as we a user of a given group can be considered member of all its subgroups
	 *
	 * @param unknown_type $branch
	 * @param unknown_type $as_array
	 * @return unknown_type
	 */
	public function userGroups($branch = 0, $as_array = false)
	{
		$this->load();

		$descendants = array_unique($this->descendants($branch, true));
		$mygroups = $this->dependance->db->getGroups(false, true);

		$groups = array_unique(array_intersect($descendants, $mygroups));

		return $as_array ? $groups : implode(',', $groups);
	}

	/**
	 * Returns a sql snippet based on the previous method
	 *
	 * @param unknown_type $branch
	 * @return unknown_type
	 */
	public function userGroupsSql($branch = 0)
	{
		$groups = $this->userGroups($branch, false);
		return $groups ? " user_group_id IN ($groups) OR user_group IN ($groups) " : "false";
	}

	/**
	 * Find all groups the user belongs to or is a subgroup (a group inside a group the user belongs to)
	 *
	 * @see stable/phpdevshell/includes/PU_tree#descendants($node, $as_array)
	 * @return	array of id
	 */
	public function descendants($branch = 0, $as_array = true)
	{
		$this->load();
		$descendants = parent::descendants($branch, true);
		$children = $descendants;
		foreach ($descendants as $descendant)
			$children = array_merge($this->descendants($descendant, true), $children);
		$children[] = $branch;

		return $as_array ? $children : implode(',', $children);
	}

	/**
	 * Find all groups hierachy from the given group up to the top
	 *
	 * @see stable/phpdevshell/includes/PU_tree#ascendants($node, $as_array)
	 * @return	array of id
	 */
	public function ascendants($branch = 0, $as_array = true)
	{
		$this->load();
		$elders = parent::ascendants($branch, true);
		$elders[] = $branch;
		return $as_array ? $elders : implode(',', $elders);
	}

	/**
	 * Find all groups hierachy from the given group up to the top
	 *
	 * @see stable/phpdevshell/includes/PU_tree#ascendants($node, $as_array)
	 * @return	array of id
	 */
	public function userGroupNames($branch)
	{
		$nodes = $this->userGroups($branch, true);
		return array_intersect_key($this->elements, array_flip($nodes));
	}

	/**
	 * Returns an array of names, either the whole tree, or only the groups which IDs are listed in the filter
	 *
	 * @param array $filter
	 * @return array
	 */
	public function nodes(array $filter = null)
	{
		$this->load();
		return parent::nodes($filter);
	}
}

