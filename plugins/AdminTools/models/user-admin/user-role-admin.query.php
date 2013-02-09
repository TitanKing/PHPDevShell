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
 * User Role Admin - Read Role Menu Permission.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readRoleMenuQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			menu_id
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
		foreach ($r as $role_per_menu_array) {
			$selected_menu[$role_per_menu_array['menu_id']] = 'on';
		}
		if (!empty($selected_menu)) {
			return $selected_menu;
		} else {
			return $selected_menu = array();
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
			_db_core_user_role_permissions (user_role_id, menu_id)
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
			foreach ($permission as $menu_id => $on) {
				$cols[] = array($user_role_id, $menu_id);
				// Also set selected role menu items.
			}
			$user_role_id_db = $this->rows($cols);
		}

		// Set new assigned value.
		if (!empty($user_role_id_db)) {
			// Insert menu permissions.
			return parent::invoke(array($user_role_id_db));
		} else {
			return false;
		}
	}
}

/**
 * User Role Admin - Read Role Menu Permission.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readMenusQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.menu_id, t1.menu_name, t1.parent_menu_id, t1.menu_link, t1.menu_type,
			t2.is_parent
		FROM
			_db_core_menu_items t1
		LEFT JOIN
			_db_core_menu_structure t2
		ON
			t1.menu_id = t2.menu_id
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
			$selected_menu = $parameters[0];
		} else {
			$selected_menu = array();
		}

		$r = parent::invoke();

        foreach ($r as $mnr) {
            $menur[$mnr['menu_id']]['menu_id']             = $mnr['menu_id'];
            $menur[$mnr['menu_id']]['parent_menu_id']      = $mnr['parent_menu_id'];
            $menur[$mnr['menu_id']]['menu_name']           = $nav->determineMenuName($mnr['menu_name'], $mnr['menu_link'], $mnr['menu_id']);
            $menur[$mnr['menu_id']]['is_parent']           = $mnr['is_parent'];
            if (! empty($mnr['parent_menu_id'])) {
                $childr[$mnr['parent_menu_id']][]          = $mnr['menu_id'];
            }

            if (empty($selected_menu[$mnr['menu_id']])) {
                $menur[$mnr['menu_id']]['checked'] = '';
            } else {
                $menur[$mnr['menu_id']]['checked'] = 'checked';
            }
        }

        $nodeul = '';

		foreach ($menur as $menu_id => $menu) {
            if (((string) $menu['parent_menu_id'] == '0')) {
                if ($menu['is_parent'] == 1) {

                    $family = $this->callFamily($menu_id, $menur, $childr);
                    if (! empty($family)) {
                        $family = $mod->ulCheckbox($family);
                    } else {
                        $family = '';
                    }
                    $nodeul .= $mod->liCheckbox($menu_id, 'permission', $menu['menu_name'], $menu['checked']);
                    $nodeul .= $family;

                } else {
                    $nodeul .= $mod->liCheckbox($menu_id, 'permission', $menu['menu_name'], $menu['checked']);
                }
            }

		}
		if ($nodeul) {
			return $nodeul;
		} else {
			return array();
		}
	}

    public function callFamily($menu_id, $menur, $childr)
    {
        $mod = $this->template->mod;
        $nodeul = '';

        if (! empty($childr[$menu_id])) {
            $child = $childr[$menu_id];
            foreach ($child as $m) {
                if ($menur[$m]['is_parent'] == 1) {

                    $family = $this->callFamily($m, $menur, $childr);
                    if (! empty($family)) {
                        $family = $mod->ulCheckbox($family);
                    } else {
                        $family = '';
                    }
                    $nodeul .= $mod->liCheckbox($m, 'permission', $menur[$m]['menu_name'], $menur[$m]['checked']);
                    $nodeul .= $family;

                } else {
                    $nodeul .= $mod->liCheckbox($m, 'permission', $menur[$m]['menu_name'], $menur[$m]['checked']);
                }
            }
        }

        return $nodeul;
    }
}