<?php

/**
 * Reads the GMT timezones for user selection.
 *
 */
class timeZone extends PHPDS_dependant
{

	/**
	 * Generates timezone options html.
	 *
	 * @param The selected timezone option.
	 * @param If set to true only options will be returned.
	 * @return string
	 */
	function timezoneOptions($timezone_id_select)
	{
		// Set.
		$timezone_option = false;
		// Get all timezone identifiers.
		$timezone_array = timezone_identifiers_list();
		// Scroll timezone options and generate html.
		foreach ($timezone_array as $zone) {
			// Check if any timezone is selected.
			($zone == $timezone_id_select) ? $timezone_selected = 'selected' : $timezone_selected = false;
			// Create dropdown.
			$timezone_option .= '<option value="' . $zone . '" ' . $timezone_selected . '>' . $zone . '</option>';
		}

		// Do we have anything to list?
		if (empty($timezone_option)) {
			return false;
		} else {
			// Return Options.
			return $timezone_option;
		}
	}

	/**
	 * Generates an array containing all the available timezones. The key and
	 * the value is the same for each timezone. This method is simply used for
	 * consistency sake between other similar functions such as iana::languages()
	 * and iana::regions()
	 *
	 * @return array
	 */
	public function timezones() {
		$result = array();
		$timezone_array = timezone_identifiers_list();
		foreach ($timezone_array as $zone) {
			$result[$zone] = $zone;
		}
		return $result;
	}

}
