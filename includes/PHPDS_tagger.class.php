<?php

class PHPDS_tagger extends PHPDS_dependant
{
	const tag_user = 'user';
	const tag_menu = 'menu';
	const tag_role = 'role';
	const tag_group = 'group';

	/**
	 * Generic setter/getter for the tags.
	 * All parameters must be given explicitly
	 *
	 * As a setter, all 4 params must be given (for the [$object;$target] set the tag name $name to value $value)
	 * As a getter, don't give the value ; a single value will be returned
	 *
	 * @param string $object
	 * @param string $name
	 * @param string $target
	 * @param string $value (optional)
	 * @return string|array|nothing
	 */
	public function tag($object, $name, $target, $value = null)
	{
		$parameters = array('object' => $object, 'name' => $name, 'target' => $target);
		if (!is_null($value)) {
			$parameters['value'] = $value;
			return $this->db->invokeQueryWith('PHPDS_taggerMarkQuery', $parameters);
		} else {
			return $this->db->invokeQueryWith('PHPDS_taggerLookupQuery', $parameters);
		}
	}
	
	/**
	 * Lookup tags based on criterias ; returns an array
	 * 
	 * @param string $object (optional)
	 * @param string $name (optional)
	 * @param string $target (optional)
	 * @param string $value (optional)
	 * 
	 * @return string|nothing
	 * 
	 * @version 1.0
	 * @author greg
	 * @date 20120105 (v1.0) (greg) added
	 */
	public function tagLookup($object = null, $name = null, $target = null, $value = null)
	{
		$parameters = array('object' => $object, 'name' => $name, 'target' => $target, 'value' => $value);
		
		return $this->db->invokeQueryWith('PHPDS_taggerLookupQuery', $parameters);
	}

	/**
	 * List of [object;target] for the given tag (optionaly restricted to the given $object/$target)
	 *
	 * @param $object
	 * @param $target
	 */
	public function tagList($name, $object, $target = null)
	{
		$parameters = array('object' => $object, 'name' => $name, 'target' => $target);
		$result = $this->db->invokeQueryWith('PHPDS_taggerListQuery', $parameters);
		if (!is_array($result)) $result = array($result);
		return $result;
	}

	/**
	 * Tag (set/get) the user specified in $target
	 *
	 * @param $name
	 * @param $target
	 * @param $value
	 */
	public function tagUser($name, $target, $value = null)
	{
		return $this->tag(PHPDS_tagger::tag_user, $name, $target, $value);
	}

	/**
	 * Tag (set/get) the current user
	 *
	 * @param $name
	 * @param $value
	 */
	public function tagMe($name, $value = null)
	{
		$me = $this->user->currentUserID();
		if (empty($me)) return false;
		return $this->tag(PHPDS_tagger::tag_user, $name, $me, $value);
	}

	/**
	 * Tag (set/get) the menu specified in $target
	 *
	 * @param $name
	 * @param $target
	 * @param $value
	 */
	public function tagMenu($name, $target, $value = null)
	{
		return $this->tag(PHPDS_tagger::tag_menu, $name, $target, $value);
	}

	/**
	 * Tag (set/get) the current menu
	 *
	 * @param $name
	 * @param $value
	 */
	public function tagHere($name, $value = null)
	{
		$here = $this->navigation->currentMenuID();
		if (empty($here)) return false;
		return $this->tag(PHPDS_tagger::tag_menu, $name, $here, $value);
	}

	/**
	 * Tag (set/get) the role specified in $target
	 *
	 * @param $name
	 * @param $target
	 * @param $value
	 */
	public function tagRole($name, $target, $value = null)
	{
		return $this->tag(PHPDS_tagger::tag_role, $name, $target, $value);
	}

	/**
	 * Tag (set/get) the group specified in $target
	 *
	 * @param $name
	 * @param $target
	 * @param $value
	 */
	public function tagGroup($name, $target, $value = null)
	{
		return $this->tag(PHPDS_tagger::tag_group, $name, $target, $value);
	}

	/**
	 * This function creates tag list which allows a comma separated list of tags.
	 *
	 * @param string $object
	 * @param string $target
	 * @param string $value
	 * @return string|nothing
	 */
	public function tagArea($object, $target, $tagArea = null, $defaultValue = null)
	{
		if (!empty($tagArea)) {
			$this->db->invokeQuery('PHPDS_updateTagsQuery', $object, $target, $defaultValue, $tagArea);
		}

		$taglist = $this->db->invokeQuery('PHPDS_taggerListTargetQuery', $target, $object);

		$tagnames = '';
		if (! empty($taglist)) {
			asort($taglist);
			foreach ($taglist as $tag) {
				$tagname = trim($tag['tagName']);
				$tagvalue = trim($tag['tagValue']);
					if (! empty($tagvalue)) $tagvalue = ':' . $tagvalue; else $tagvalue = '';
				$tagnames .= "$tagname" . $tagvalue . "\r\n";
			}
			$tagnames = rtrim($tagnames, ",");
		}
		return $tagnames;
	}
}