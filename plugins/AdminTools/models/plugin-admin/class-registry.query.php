<?php

/**
 * CLass Registry - Write new classes to database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_writeCoreClassQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_plugin_classes (class_id, class_name, alias, plugin_folder, enable, rank)
		VALUES
			%s
    ";

	public function invoke($parameters = null)
	{
		$s = $this->security->post;

		$write_sd = '';
		if (!empty($s['class_name_'])) {
			$write_sd .= "('', '{$s['class_name_']}', '{$s['alias_']}', '{$s['plugin_folder_']}', 1, '{$s['rank_']}'),";
		}

		if (! empty($s['class_id'])) {
			foreach ($s['class_id'] as $sd) {
				if (!empty($sd)) {
					$write_sd .= "('$sd', '{$s['class_name'][$sd]}', '{$s['alias'][$sd]}', '{$s['plugin_folder'][$sd]}', {$s['enable'][$sd]}, '{$s['rank'][$sd]}'),";
				} else {
					$this->template->warning(__('You must provide all fields to identify the class'));
					break;
				}
			}
		}

		if (!empty($write_sd)) {
			$write_sd = rtrim($write_sd, ',');
			if (parent::invoke(array($write_sd))) {
				$this->template->ok(__('Class changes were saved.'));
			}
		} else {
			$this->template->warning(__('No class data was changed.'));
		}
	}
}

/**
 * Class Registry - Enable a single class.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_enableClass extends PHPDS_query
{
	protected $sql = "
		UPDATE
			_db_core_plugin_classes
		SET
			enable = %u
		WHERE
			class_id = %u
    ";
}

/**
 * Class Registry - Read available class registry from Database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readCoreClassQuery extends PHPDS_query
{
	protected $sql = "
        SELECT
            t1.class_id, t1.class_name, t1.alias, t1.plugin_folder, t1.enable, t1.rank
        FROM
            _db_core_plugin_classes t1
    ";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$core = $this->core;
		$template = $this->template;
		$navigation = $this->navigation;
		$configuration = $this->configuration;

		$del_url_ = $navigation->buildURL(false, 'dc=');
		$enable_url_ = $navigation->buildURL(false, 'enable=');
		$disable_url_ = $navigation->buildURL(false, 'disable=');
		$pagination = $this->factory('pagination');

		$pagination->columns = array(
			_('Class ID') => 'class_id',
			_('Class Name') => 'class_name',
			_('Alias Call') => 'alias',
			_('Resides in Plugin') => 'plugin_folder',
			_('Rank') => 'rank',
			_('Files') => '',
			_('Enabled') => '',
			_('Delete') => ''
		);

		$select_classes = $pagination->query($this->sql);
		$RESULTS['pagination'] = $pagination->navPages();
		$RESULTS['searchForm'] = $pagination->searchForm();
		$RESULTS['th'] = $pagination->th();

		// Icons.
		$delete_icon = $template->icon('cross-script', __('Delete'));
		$enabled_icon = $template->icon('tick-circle', __('Disable on Click'));
		$disabled_icon = $template->icon('cross-white', __('Enable on Click'));

		$i = 0;

		foreach ($select_classes as $ca) {
			$class_file = $configuration['absolute_path'] . 'plugins/' . $ca['plugin_folder'] . '/includes/' . $ca['class_name'] . '.class.php';
			$query_file = $configuration['absolute_path'] . 'plugins/' . $ca['plugin_folder'] . '/models/' . $ca['class_name'] . '.query.php';
			if (is_file($class_file)) {
				$class_found = $template->icon('tick', __('Class file available : ') . $class_file);
			} else {
				$class_found = $template->icon('exclamation', __('Class file not found : ') . $class_file);
			}
			if (is_file($query_file)) {
				$query_found = $template->icon('database--plus', __('Model file available : ') . $query_file);
			} else {
				$query_found = $template->icon('database--exclamation', __('No model file available : ') . $query_file);
			}

			if (! empty($ca['enable'])) {
				$enabled_icon = $enabled_icon;
				$enabled_url = $disable_url_;
			} else {
				$enabled_url = $enable_url_;
				$enabled_icon = $disabled_icon;
			}

			$RESULTS['list'][] = array(
				'class_id' => $ca['class_id'],
				'class_name' => $ca['class_name'],
				'alias' => $ca['alias'],
				'plugin_folder' => $ca['plugin_folder'],
				'rank' => $ca['rank'],
				'enable' => $ca['enable'],
				'found' => $class_found,
				'query_found' => $query_found,
				'enabled' => "<a href=\"{$enabled_url}{$ca['class_id']}\" {$core->confirmLink(sprintf(__('This could break your system, are you sure you want to MODIFY : %s'), $ca['class_name']))} class=\"button\">" . $enabled_icon . "</a>",
				'delete' => "<a href=\"{$del_url_}{$ca['class_id']}\" {$core->confirmLink(sprintf(__('This could break your system, are you sure you want to DELETE : %s'), $ca['class_name']))} class=\"button\">" . $delete_icon . "</a>"
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
