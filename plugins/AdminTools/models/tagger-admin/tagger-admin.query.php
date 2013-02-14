<?php

/**
 * Tags Admin - Update tags to database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateTagsQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_tags (tagID, tagObject, tagName, tagTarget, tagValue)
		VALUES
			%s
    ";

	/**
	 * Initiate query invoke command.
	 */
	public function invoke($parameters = null)
	{
		// Update tags.
		$write_tag = '';
		$t = $this->security->post;

		// Execute saving new tag.
		if (! empty($t['tagObject'][0])) {
			if (!empty($t['tagName'][0]) && !empty($t['tagTarget'][0])) {
				$write_tag .= "('', '{$t['tagObject'][0]}', '{$t['tagName'][0]}', '{$t['tagTarget'][0]}', '{$t['tagValue'][0]}'),";
			} else {
				$this->template->warning(__('To add a new tag please provide both Tag Name and Tag Target.'));
			}
		}
		// We dont need you anymore, go rest somewhere.
		unset($t['tagObject'][0]);
		// Execute updating rows.
		if (is_array($t['tagObject'])) {
			foreach ($t['tagObject'] as $id=>$tag) {
				if (empty($tag) || empty($t['tagName'][$id]) || empty($t['tagTarget'][$id])) {
					$this->template->warning(sprintf(__('Nothing saved, you missed tag values for tag id %u.'), $id));
					break;
				}
				$write_tag .= "('$id', '{$tag}', '{$t['tagName'][$id]}', '{$t['tagTarget'][$id]}', '{$t['tagValue'][$id]}'),";
			}
		}
		if (! empty($write_tag)) {
			$write_tag = rtrim($write_tag, ',');
			$this->template->ok(__('Tags updated.'));
			return parent::invoke(array($write_tag));
		} else {
			return false;
		}
	}
}

/**
 * Tags Admin - Reads all required tags.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readTagsQuery extends PHPDS_query
{
	protected $sql = "
        SELECT
            t1.tagID, t1.tagObject, t1.tagName, t1.tagTarget, t1.tagValue
        FROM
            _db_core_tags t1
    ";

	/**
	 * Initiate query invoke command.
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$core = $this->core;
		$template = $this->template;
		$navigation = $this->navigation;

		$del_url_ = $navigation->buildURL(false, 'dt=');
		$pagination = $this->factory('pagination');
		$pagination->columns = array(
			_('Tag Id') => 'tagID',
			_('Tag Object') => 'tagObject',
			_('Tag Name') => 'tagName',
			_('Tag Target') => 'tagTarget',
			_('Tag Value') => 'tagValue',
			_('Delete') => ''
		);

		$select_settings = $pagination->query($this->sql);
		$RESULTS['pagination'] = $pagination->navPages();
		$RESULTS['searchForm'] = $pagination->searchForm();
		$RESULTS['th'] = $pagination->th();

		// Icons.
		$delete_icon = $template->icon('cross-script', __('Delete'));

		// OK Loop the array like you would always do.
		foreach ($select_settings as $tag) {

			// Save all the results in $RESULT array.
			$RESULTS['list'][] = array(
				'tagID' => $tag['tagID'],
				'tagObject' => tag_field_object($tag['tagObject'], $tag['tagID']),
				'tagName' => $tag['tagName'],
				'tagTarget' => $tag['tagTarget'],
				'tagValue' => $tag['tagValue'],
				'delete' => "<a href=\"{$del_url_}{$tag['tagID']}\" {$core->confirmLink(sprintf(__('Are you sure you want to DELETE : %s'), $tag['tagID']))} class=\"button\">" . $delete_icon . "</a>"
			);
		}
		if (! empty($RESULTS['list'])) {
			return $RESULTS;
		} else {
			$RESULTS['list'] = array();
			return $RESULTS;
		}
	}
}

/**
 * Simple function to allow quick selection of options of tagger objects.
 * @param string $selected
 */
function tag_field_object ($selected='', $id=0)
{
	// Define.
	$user_selected = '';
	$node_selected = '';
	$role_selected = '';
	$group_selected = '';
	$req = 'required="required"';

	switch ($selected) {
		case 'user':
			$user_selected = 'selected';
		break;

		case 'node':
			$node_selected = 'selected';
		break;

		case 'role':
			$role_selected = 'selected';
		break;

		case 'group':
			$group_selected = 'selected';
		break;
	}

	if (empty($id))
		$req = '';

	$object_html = <<<HTML
		<select name="tagObject[$id]" $req>
		  <option value=""></option>
		  <option value="user" $user_selected>User</option>
		  <option value="node" $node_selected>Node</option>
		  <option value="role" $role_selected>Role</option>
		  <option value="group" $group_selected>Group</option>
		</select>
HTML;
	return $object_html;
}