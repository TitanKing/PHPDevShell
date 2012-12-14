<?php

/**
 * This is the wysiwyg editor class, PHPDevShell uses TinyMCE as we found it well tested, if you choose to use your own wysiwyg editor, simply replace this class file
 * with your own class and settings to your wysiwyg editor. You may visit the TinyMCE website to find usage of how you could add more functionality to TinyMCE.
 *
 * @link http://tinymce.moxiecode.com/ TinyMCE
 */
class wysiwygEditor extends PHPDS_dependant
{
	/**
	 * This method automaticly converts textareas to a TinyMCE wysiwyg editor, you may check the confic.x.wysiwyg.php for customizing the editor.
	 * @deprecated
	 * @param string $wysiwyg_load_config This will load the config file of your choice for the active WYSIWYG.
	 */
	public function wysiwyg_load ($wysiwyg_load_config = false)
	{
		$this->wysiwygLoad($wysiwyg_load_config);
	}

	/**
	 * This method automaticly converts textareas to a TinyMCE wysiwyg editor, you may check the confic.x.wysiwyg.php for customizing the editor.
	 *
	 * @param string $wysiwyg_load_config This will load the config file of your choice for the active WYSIWYG.
	 */
	public function wysiwygLoad ($wysiwyg_load_config = false)
	{
		// Check if we need to find a custom wysiwyg config script.
		if (! empty($wysiwyg_load_config)) {
			// First load from active plugin, see if it exists.
			$load_active_config = 'plugins/' . $this->core->activePlugin() . '/config/config.' . $wysiwyg_load_config . '.wysiwyg.php';
			// Load configuration.
			if (file_exists($load_active_config)) {
				// Load required config file.
				if (require_once ($load_active_config)) return true;
			}
			$load_config = 'plugins/TinyMCE/config/config.' . $wysiwyg_load_config . '.wysiwyg.php';
		} else {
			// Load default.
			$load_config = 'plugins/TinyMCE/config/config.simple.wysiwyg.php';
		}
		// Load configuration.
		if (! file_exists($load_config)) return false;
		// Load required config file.
		if (require_once ($load_config)) return true;
	}
}
