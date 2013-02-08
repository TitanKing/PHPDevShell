<?php

/**
 * Config Manager - Write new settings to database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_writeCoreSettingsQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_settings (setting_description, setting_value, note)
		VALUES
			%s
    ";

	public function invoke($parameters = null)
	{
		$s = $this->security->post;
		// Write new setting.
		$write_sd = '';
		if (!empty($s['setting_description_'])) {
			$write_sd .= "('{$s['setting_description_']}', '{$s['setting_value_']}', '{$s['note_']}'),";
		}
		// Write everything else.
		if (! empty($s['setting_description'])) {
			foreach ($s['setting_description'] as $sd) {
				if (!empty($sd)) {
					if (empty($s['setting_value'][$sd]))
						$s['setting_value'][$sd] = '';
					if (empty($s['note'][$sd]))
						$s['note'][$sd] = '';
					$write_sd .= "('$sd', '{$s['setting_value'][$sd]}', '{$s['note'][$sd]}'),";
				} else {
					$this->template->warning(__('You must provide a field name to identify the setting by.'));
					break;
				}
			}
		}
		// Do we have anything to write to database.
		if (!empty($write_sd)) {
			// Prepare write.
			$write_sd = rtrim($write_sd, ',');
			// Write to DB.
			if (parent::invoke(array($write_sd))) {
				$this->template->ok(__('Plugin settings were saved.'));
			}
		} else {
			$this->template->warning(__('No plugin settings were saved.'));
		}
	}
}

/**
 * Template Admin List - Read available settings from Database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readCoreSettingsQuery extends PHPDS_query
{
	protected $sql = "
        SELECT
            t1.setting_description, t1.setting_value, t1.note
        FROM
            _db_core_settings t1
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

		$del_url_ = $navigation->buildURL(false, 'ds=');
		$pagination = $this->factory('pagination');
		$pagination->columns = array(
			_('Row') => '',
			_('Setting Name') => 'setting_description',
			_('Notes') => 'note',
			_('Setting Value') => 'setting_value',
			_('Delete') => ''
		);

		$select_settings = $pagination->query($this->sql);
		$RESULTS['pagination'] = $pagination->navPages();
		$RESULTS['searchForm'] = $pagination->searchForm();
		$RESULTS['th'] = $pagination->th();

		// Icons.
		$delete_icon = $template->icon('cross-script', __('Delete'));

		$i = 0;
		// OK Loop the array like you would always do.
		foreach ($select_settings as $select_settings_array) {
			// Create variables for the arrays.
			$setting_description = $select_settings_array['setting_description'];
			$setting_value = $select_settings_array['setting_value'];
			$note = $select_settings_array['note'];
			$i++;
			// Save all the results in $RESULT array.
			$RESULTS['list'][] = array(
				'setting_description' => $setting_description,
				'setting_value' => $setting_value,
				'note' => $note,
				'row' => $i,
				'delete' => "<a href=\"{$del_url_}{$setting_description}\" {$core->confirmLink(sprintf(__('Are you sure you want to DELETE : %s'), $setting_description))} class=\"button\">" . $delete_icon . "</a>"
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
