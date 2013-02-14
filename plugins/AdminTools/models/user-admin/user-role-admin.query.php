<?php

/**
 * User Role Admin - Read Basic Role Information.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readRoleUserQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_role_id, user_role_name, user_role_note
		FROM
			_db_core_user_roles
		WHERE
			user_role_id = %u
		";
	protected $singleRow = true;
}

/**
 * User Role Admin - Read Role Node Permission.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readRoleNodeQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			node_id
		FROM
			_db_core_user_role_permissions
		WHERE
			user_role_id = '%u'
		";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$r = parent::invoke($parameters);
		if (empty($r)) $r = array();
		foreach ($r as $role_per_node_array) {
			$selected_node[$role_per_node_array['node_id']] = 'on';
		}
		if (!empty($selected_node)) {
			return $selected_node;
		} else {
			return $selected_node = array();
		}
	}
}

/**
 * User Role Admin - Write Role Data.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_writeRoleQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_user_roles (user_role_id, user_role_name, user_role_note)
		VALUES
			('%u', '%s', '%s')
		";
	protected $returnId = true;
}

/**
 * User Role Admin - Delete old permission data for rewrite.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_deletePermissionsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_user_role_permissions
		WHERE
			user_role_id = '%u'
		";
	protected $returnId = true;
}

/**
 * User Role Admin - Write Role Data.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_writePermissionsQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_user_role_permissions (user_role_id, node_id)
		VALUES
			%s
		";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return boolean
	 */
	public function invoke($parameters = null)
	{
		$user_role_id = intval($parameters[0]);
		$permission = $parameters[1];

		if (!empty($permission)) {
			// Save permissions.
			foreach ($permission as $node_id => $on) {
				$cols[] = array($user_role_id, $node_id);
				// Also set selected role node items.
			}
			$user_role_id_db = $this->rows($cols);
		}

		// Set new assigned value.
		if (!empty($user_role_id_db)) {
			// Insert node permissions.
			return parent::invoke(array($user_role_id_db));
		} else {
			return false;
		}
	}
}

/**
 * User Role Admin - Read Role Node Permission.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readNodesQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.node_id, t1.node_name, t1.parent_node_id, t1.node_link, t1.node_type,
			t2.is_parent
		FROM
			_db_core_node_items t1
		LEFT JOIN
			_db_core_node_structure t2
		ON
			t1.node_id = t2.node_id
		ORDER BY
			t2.id
		ASC
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
        $mod = $this->template->mod;
        $nav = $this->navigation;

		if (!empty($parameters[0])) {
			$selected_node = $parameters[0];
		} else {
			$selected_node = array();
		}

		$r = parent::invoke();

        foreach ($r as $mnr) {
            $noder[$mnr['node_id']]['node_id']             = $mnr['node_id'];
            $noder[$mnr['node_id']]['parent_node_id']      = $mnr['parent_node_id'];
            $noder[$mnr['node_id']]['node_name']           = $nav->determineNodeName($mnr['node_name'], $mnr['node_link'], $mnr['node_id']);
            $noder[$mnr['node_id']]['is_parent']           = $mnr['is_parent'];
            if (! empty($mnr['parent_node_id'])) {
                $childr[$mnr['parent_node_id']][]          = $mnr['node_id'];
            }

            if (empty($selected_node[$mnr['node_id']])) {
                $noder[$mnr['node_id']]['checked'] = '';
            } else {
                $noder[$mnr['node_id']]['checked'] = 'checked';
            }
        }

        $nodeul = '';

		foreach ($noder as $node_id => $node) {
            if (((string) $node['parent_node_id'] == '0')) {
                if ($node['is_parent'] == 1) {

                    $family = $this->callFamily($node_id, $noder, $childr);
                    if (! empty($family)) {
                        $family = $mod->ulCheckbox($family);
                    } else {
                        $family = '';
                    }
                    $nodeul .= $mod->liCheckbox($node_id, 'permission', $node['node_name'], $node['checked']);
                    $nodeul .= $family;

                } else {
                    $nodeul .= $mod->liCheckbox($node_id, 'permission', $node['node_name'], $node['checked']);
                }
            }

		}
		if ($nodeul) {
			return $nodeul;
		} else {
			return array();
		}
	}

    public function callFamily($node_id, $noder, $childr)
    {
        $mod = $this->template->mod;
        $nodeul = '';

        if (! empty($childr[$node_id])) {
            $child = $childr[$node_id];
            foreach ($child as $m) {
                if ($noder[$m]['is_parent'] == 1) {

                    $family = $this->callFamily($m, $noder, $childr);
                    if (! empty($family)) {
                        $family = $mod->ulCheckbox($family);
                    } else {
                        $family = '';
                    }
                    $nodeul .= $mod->liCheckbox($m, 'permission', $noder[$m]['node_name'], $noder[$m]['checked']);
                    $nodeul .= $family;

                } else {
                    $nodeul .= $mod->liCheckbox($m, 'permission', $noder[$m]['node_name'], $noder[$m]['checked']);
                }
            }
        }

        return $nodeul;
    }
}