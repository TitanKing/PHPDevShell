<?php

/**
 * Template Admin List - Write Template to Database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_writeTemplateQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_templates (template_id, template_folder)
		VALUES
			('%s', '%s')
    ";
}

/**
 * Template Admin List - Update Template to Database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateTemplateQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE
			_db_core_node_items
		SET
			template_id = '%s'
		WHERE
			template_id = '%s'
    ";
}

/**
 * Template Admin List - Count available templates.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_countTemplateQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			COUNT(template_id)
		FROM
			_db_core_node_items
		WHERE
			template_id = '%s'
	";
	protected $singleValue = true;
}

/**
 * Template Admin List - Select available templates.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_selectTemplateQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			template_id, template_folder
		FROM
			_db_core_templates
		ORDER BY
			template_folder
	";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		list($setting_template_id) = $parameters;
		// Call query.
		$select_template = parent::invoke();
		// Set.
		$template_option_ = false;
		// Template list
		if (empty($select_template)) $select_template = array();
		foreach ($select_template as $select_template_array) {
			if ($setting_template_id == $select_template_array['template_id']) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			// Create template dropdown.
			$template_option_ .= '<option value="' . $select_template_array['template_id'] . '" ' . $selected . '>' . $select_template_array['template_folder'] . '</option>';
			$template_id_db[$select_template_array['template_id']] = $select_template_array['template_folder'];
		}
		if (!empty($template_option_)) {
			$template_option_ar['dropdown'] = $template_option_;
			$template_option_ar['selected'] = $template_id_db;
			return $template_option_ar;
		} else {
			return array();
		}
	}
}

/**
 * Template Admin List - Read template directory.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readTemplateDir extends PHPDS_query
{

	/**
	 * Initiate query invoke command.
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$core = $this->core;
		$template = $this->template;
		$navigation = $this->navigation;

		// Set self url.
		$page_install = $navigation->buildURL(false, 'it=');
		$page_uninstall = $navigation->buildURL(false, 'ut=');
		$template_id_db = $parameters[0];
		//////////////////////////////////
		// Template lookup starts here. //
		//////////////////////////////////
		$directory = $this->configuration['absolute_path'] . '/themes';
		$level_deduct = substr_count($directory . '/', '/') - 1;
		$original_base = $directory;
		$base = $directory . '/';
		$subdirectories = opendir($base);
		$level = substr_count($base, '/') - ($level_deduct);

		// Icons.
		$found_icon = $template->icon('television-image', __('Found'));
		$not_found_icon = $template->icon('cross-circle', __('Not Found'));
		$installed_icon = $template->icon('tick-circle', __('Installed'));
		$uninstall_icon = $template->icon('disk--minus', __('Uninstall'));
		$not_installed = $template->icon('disk--exclamation', __('NOT Installed'));
		$install_icon = $template->icon('disk--plus', __('Install'));
		$critical_icon = $template->icon('cross-circle', __('Not Found'));
		$remove_icon = $template->icon('cross-script', __('Remove'));

		// Start reading and loading directory structure.
		while (false !== ($object = readdir($subdirectories))) {
			$path = $base . $object;
			if (($object != '.') && ($object != '..') && ($object != '.svn')) {
				// Unset previous configuration files.
				unset($template_config);
				// Generate template values.
				$template_id = $core->nameToId($object);
				$template_folder = $object;
				if ($level == 1 && is_dir('themes/' . $object)) {
					// Read Config Installation File.
					if (file_exists("themes/$object/config/theme.config.xml")) {
						// Require config.
						$xml = simplexml_load_file("themes/$object/config/theme.config.xml");
						// Assign config file values.
						$template_config['name'] = $xml->name;
						$template_config['version'] = $xml->version;
						$template_config['description'] = $xml->description;
						$template_config['founder'] = $xml->founder;
						$template_config['author'] = $xml->author;
						$template_config['email'] = $xml->email;
						$template_config['homepage'] = $xml->homepage;
						$template_config['date'] = $xml->date;
						$template_config['copyright'] = $xml->copyright;
						$template_config['license'] = $xml->license;
						// Generate template detail.
						$t['date'] = $template_config['date'];
						$t['name'] = $template_config['name'];
						$t['version'] = $template_config['version'];
						$t['author'] = $template_config['author'];
						$t['email'] = $template_config['email'];
						$t['description'] = $template_config['description'];
						$t['copyright'] = $template_config['copyright'];
						$t['homepage'] = $template_config['homepage'];
						$t['license'] = $template_config['license'];
					} else {
						// Show no detail.
						$t = false;
					}

					// Check if we can find file.
					if (file_exists('themes/' . $template_folder . '/theme.php')) {
						$found = $found_icon;
					} else {
						$found = $not_found_icon;
					}
					// Check if template is installed.
					if (isset($template_id_db[$template_id])) {
						// Nice we know this one is installed so lets remove it then.
						unset($template_id_db[$template_id]);
						// Show installed icon.
						$installed = $installed_icon;
						// Uninstall should display.
						$action_ = <<<HTML
							<a href="{$page_uninstall}{$template_folder}" {$core->confirmLink(sprintf(__('You are about to uninstall template %s?'), $template_folder))} class="button">
								{$uninstall_icon}
							</a>
HTML;
					} else {
						// Show no installed icon.
						$installed = $not_installed;
						// Install should display.
						$action_ = <<<HTML
							<a href="{$page_install}{$template_folder}" class="button">
								{$install_icon}
							</a>
HTML;
					}
					$RESULTS[] = array(
						'template_id' => $template_id,
						'template_folder' => $template_folder,
						'found' => $found,
						'installed' => $installed,
						't' => $t,
						'set_to' => "<input name=\"set_to\" type=\"radio\" value=\"$template_id\">",
						'action_' => $action_
					);
					// Unset xml object.
					unset($xml);
				} else {
					continue;
				}
			}
		}
		// Close directories.
		closedir($subdirectories);
		// We can now check if we have any infants around.
		$RESULTS_ = false;
		if (!empty($template_id_db)) {
			// Oops we have infants! Thats bad, lets give the user the option to delete it.
			foreach ($template_id_db as $id_ => $name_) {
				$RESULTS_[] = array(
					'id_' => $id_,
					'name_' => $name_,
					'critical_icon' => $critical_icon,
					'tna_notice' => $template->notice(__('Could not locate template install file, this could be a broken template.'), true),
					'page_uninstall' => "<a href=\"{$page_uninstall}{$name_}\" {$core->confirmLink(sprintf(__('You are about to uninstall broken template %s?'), $name_))} class=\"button\">{$remove_icon}</a>"
				);
			}
		}
		if (!empty($RESULTS)) {
			$R[0] = $RESULTS;
		} else {
			$R[1] = array();
		}
		if (!empty($RESULTS_)) {
			$R[1] = $RESULTS_;
		} else {
			$R[1] = array();
		}
		return $R;
	}
}
