<?php

/**
 * Reads the iana subtag registry as provided by iana and then parses it into arrays.
 *
 * @link http://www.iana.org/assignments/language-subtag-registry
 */
class iana extends PHPDS_dependant
{
	/**
	 * Actual method that reads the iana language registry from the local language folder.
	 * It puts it in an array (language_code, description)
	 * @param string $type
	 * @return array
	 */
	public function readIanaRegistry ($type = 'language')
	{
		// Read the iana file as a string into variable.
		$registry = file_get_contents("plugins/AdminTools/includes/iana.$type");
		// Plit blocks of language information up in arrays.
		$registry_items = explode('%%', $registry);
		// Loop blocks of arrays.
		foreach ($registry_items as $item) {
			// Split individual lines inside blocks up in arrays.
			$item_lines = explode("\n", $item);
			// Loop lines.
			foreach ($item_lines as $line) {
				// Check if we met a property + value.
				if (strpos($line, ':') !== false) {
					// Assign array to variables.
					list ($property, $value) = explode(':', $line);
					// If it is a subtag remember value for next loop.
					if ($property == 'Subtag') {
						$subtag = trim($value);
					} // If we have a subtag, write language description.
					else if ($property == 'Description' && ! empty($subtag) && ! empty($value)) {
						$iana_registry[$subtag] = trim($value);
					} // Empty subtag.
					else {
						$subtag = false;
					}
				}
			}
		}
		// Sort and return iana registry array.
		asort($iana_registry);
		return $iana_registry;
	}

	/**
	 * Method generates iana language options.
	 *
	 * @param string $selected_language
	 * @param boolean $sort Whether to sort the list or not
	 * @param boolean $default_first Whether to list the default system language first
	 * @return string
	 */
	public function languageOptions ($selected_language, $sort = true, $default_first = true)
	{
		$language_option = "";
		$languages = $this->languages();
		foreach ($languages as $lang_code => $language) {
			// Check if any language is selected.
			($lang_code == $selected_language) ? $language_selected = 'selected' : $language_selected = '';
			// Create dropdown.
			$language_option .= '<option value="' . $lang_code . '" ' . $language_selected . '>' . $language . '</option>';
		}
		// Do we have anything to list?
		if (empty($language_option)) {
			return false;
		} else {
			return $language_option;
		}
	}

	/**
	 * Method generates an optionally sorted iana language list as an array using the format:
	 * array(code => language). The default language is optionally listed first.
	 *
	 * @param boolean $sort Whether to sort the list or not
	 * @param boolean $default_first Whether to list the default system language first
	 * @return array
	 */
	public function languages($sort = true, $default_first = true)
	{
		$configuration = $this->configuration;
		$db = $this->db;
		$result = array();

		// Get required settings.
		$settings = $db->getSettings(array('languages_available'), 'AdminTools');
		$languages_available_settings = $settings['languages_available'];
		if (! empty($languages_available_settings)) {
			// Get iana array.
			$iana_array = $this->readIanaRegistry('language');
			// Turn into array.
			$languages_available_array = explode(',', $languages_available_settings);
			// Sort array.
			if ($sort) asort($languages_available_array);
			// System default language string.
			if ($default_first) $result[] = sprintf(__('Default (%s)'), $iana_array[$configuration['language']]);

			foreach ($languages_available_array as $lang_code) {
				$result[$lang_code] = $iana_array[$lang_code];
			}
		}

		if (empty($result)) {
			return false;
		} else {
			return $result;
		}
	}

	/**
	 * Method generates iana region options.
	 *
	 * @param string $selected_region
	 * @param boolean $sort Whether to sort the list or not
	 * @param boolean $default_first Whether to list the default system language first
	 * @return string
	 */
	public function regionOptions ($selected_region, $sort = true, $default_first = true)
	{
		$region_option = "";
		$regions = $this->regions();

		foreach ($regions as $region_code => $region) {
			// Check if any region is selected.
			($region_code == $selected_region) ? $region_selected = 'selected' : $region_selected = '';
			// Create dropdown.
			$region_option .= '<option value="' . $region_code . '" ' . $region_selected . '>' . $region . '</option>';
		}

		// Do we have anything to list?
		if (empty($region_option)) {
			return false;
		} else {
			return $region_option;
		}
	}

	/**
	 * Method generates an optionally sorted iana region list as an array using the format:
	 * array(code => region). The default region is optionally listed first.
	 *
	 * @param boolean $sort Whether to sort the list or not
	 * @param boolean $default_first Whether to list the default system region first
	 * @return array
	 */
	public function regions($sort = true, $default_first = true) {
		$configuration = $this->configuration;
		$db = $this->db;
		$result = array();

		// Get required settings.
		$settings = $db->getSettings(array('regions_available'), 'AdminTools');
		$regions_available_settings = $settings['regions_available'];
		if (! empty($regions_available_settings)) {
			// Get iana array.
			$iana_array = $this->readIanaRegistry('region');
			// Turn into array.
			$regions_available_array = explode(',', $regions_available_settings);
			// Sort array.
			if ($sort) asort($regions_available_array);
			// System default region string.
			if ($default_first) $result[] = sprintf(__('Default (%s)'), $iana_array[$configuration['region']]);

			foreach ($regions_available_array as $region_code) {
				$result[$region_code] = $iana_array[$region_code];
			}
		}

		if (empty($result)) {
			return false;
		} else {
			return $result;
		}

	}
}