<?php

/**
 * Fileupload Logs - Reset logs internal pointer.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_resetFileuploadLogsQuery extends PHPDS_query
{
	protected $sql = "
		ALTER TABLE
			_db_core_upload_logs
		AUTO_INCREMENT = 0;
	";
}

/**
 * Fileupload Logs - Clear Fileupload Logs
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_clearFileuploadLogsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_upload_logs
	";

	/**
	 * Initiate query invoke command.
	 */
	public function invoke($parameters = null)
	{
		// Delete all logs.
		parent::invoke();

		// Reset auto increment counter.
		$this->db->invokeQuery('PHPDS_resetFileuploadLogsQuery');

		$this->template->ok(_('Logs table cleared.'));
	}
}

/**
 * Fileupload Logs - Delete File Upload
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_deleteFileuploadQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.file_id, t1.new_filename, t1.relative_path, t1.thumbnail, t1.resized
		FROM
			_db_core_upload_logs t1
		WHERE
			t1.file_id = %u
	";
	protected $singleRow = true;

	/**
	 * Initiate query invoke command.
	 * @param string
	 */
	public function invoke($parameters = null)
	{
		$file_id = $parameters[0];
		$deleted_filelog = $this->db->deleteQuick('_db_core_upload_logs', 'file_id', $file_id, 'original_filename');
		if ($deleted_filelog) {
			// Loop and delete.
			$delete_farr = parent::invoke(array($file_id));
			// Start deleting.
			$path_to_delete = $this->configuration['absolute_path'] . $delete_farr['relative_path'] . $delete_farr['new_filename'];
			if (is_writable($path_to_delete)) unlink($path_to_delete);
			$path_to_delete = $this->configuration['absolute_path'] . $delete_farr['resized'];
			if (is_writable($path_to_delete)) unlink($path_to_delete);
			$path_to_delete = $this->configuration['absolute_path'] . $delete_farr['thumbnail'];
			if (is_writable($path_to_delete)) unlink($path_to_delete);

			$this->template->ok(sprintf(_('File %s was deleted.'), $deleted_filelog));
		} else {
			$this->template->warning(sprintf(_('No file "%s" to delete.'), $this->security->get['df']));
		}
	}
}

/**
 * Fileupload Logs - Get All upload logs
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_getAllUploadLogsQuery extends PHPDS_query
{
	protected $sql = "
			SELECT
				t1.file_id, t1.sub_id, t1.menu_id, t1.alias, t1.original_filename, t1.new_filename, t1.relative_path,
				t1.thumbnail, t1.resized, t1.extention, t1.group_id, t1.user_id, t1.date_stored, t1.file_size,
				t2.menu_name,
				t3.user_display_name
			FROM
				_db_core_upload_logs t1
			LEFT JOIN
				_db_core_menu_items t2
			ON
				t1.menu_id = t2.menu_id
			LEFT JOIN
				_db_core_users t3
			ON
				t1.user_id = t3.user_id";

	/**
	 * Initiate query invoke command.
	 * @param string
	 */
	public function invoke($parameters = null)
	{
		$core = $this->core;
		$template = $this->template;
		$navigation = $this->navigation;

		$filemanager = $parameters[0];
		$page_delete_file = $this->navigation->buildURL(false, 'df=');

		$pagination = $this->factory('pagination');
		$pagination->columns = array(
			_('Uploaded In') => 't2.menu_name',
			_('File Name') => 't1.original_filename',
			_('Thumbnail') => 't1.thumbnail',
			_('Resized') => 't1.resized',
			_('Date Stored') => 't1.date_stored',
			_('Original Size') => 't1.file_size',
			_('Delete') => ''
		);
		$pagination->dateColumn = 't1.date_stored';
		$get_logs = $pagination->query($this->sql);
		$RESULTS['pagination'] = $pagination->navPages();
		$RESULTS['searchForm'] = $pagination->searchForm();
		$RESULTS['th'] = $pagination->th();

		// Icons.
		$resized_icon = $template->icon('image-resize', _('Resized'));
		$no_resized_icon = $template->icon('cross', _('No Resized'));
		$thumb_icon = $template->icon('image-small', _('Resized'));
		$no_thumb_icon = $template->icon('cross-small', _('No Thumb'));
		$delete_file_icon = $template->icon('box--minus', _('Delete File'));

		foreach ($get_logs as $la) {
			// Create path urls.
			$file = "<a href=\"{$la['relative_path']}{$la['new_filename']}\" target=\"_blank\"><small>{$la['original_filename']}</small></a>";
			// Create resized zoom.
			if (!empty($la['resized'])) {
				$resized = "<a href=\"{$la['resized']}\" target=\"_blank\">" . $resized_icon . '</a>';
			} else {
				$resized = $no_resized_icon;
			}
			// Create thumb zoom.
			if (!empty($la['thumbnail'])) {
				$thumbnail = "<a href=\"{$la['thumbnail']}\" target=\"_blank\">" . $thumb_icon . '</a>';
			} else {
				$thumbnail = $no_thumb_icon;
			}
			// Get filesize.
			if (!empty($la['file_size'])) {
				$size = $filemanager->displayFilesize($la['file_size']);
			} else {
				$size = '0';
			}
			$RESULTS['list'][] = array(
				'menu_name_url' => "<a href=\"{$navigation->buildURL($la['menu_id'])}\">{$la['menu_name']}</a>",
				'file' => $file,
				'thumbnail' => $thumbnail,
				'resized' => $resized,
				'date_stored_format' => $core->formatTimeDate($la['date_stored']),
				'size' => $size,
				'delete' => "<a href=\"{$page_delete_file}{$la['file_id']}\" {$core->confirmLink(sprintf(_('Delete Filename %s?'), $la['original_filename']))} class=\"button\">" . $delete_file_icon . "</a>"
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