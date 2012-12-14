<?php

/**
 * File Manager - Read files available and uploaded.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class FM_readFilesLogsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			file_id,
			sub_id,
			menu_id,
			alias,
			original_filename,
			new_filename,
			relative_path,
			thumbnail,
			resized,
			extention,
			mime_type,
			file_desc,
			group_id,
			user_id,
			date_stored,
			file_size,
			file_explained
		FROM
			_db_core_upload_logs
			%s
			%s
    ";

	/**
	 * Initiate invoke query.
	 */
	public function invoke($parameters = null)
	{
		list($query_grouped, $file_id, $order, $limit) = $parameters;
		if (empty($file_id)) {
			// Create order and limit query.
			$order_by = " ORDER BY $order LIMIT $limit";
		} else {
			$order_by = '';
		}
		return parent::invoke(array($query_grouped, $order_by));
	}
}

/**
 * File Manager - Read files available and uploaded.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class FM_countFilesLogsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			COUNT(file_id)
		FROM
			_db_core_upload_logs
			%s
    ";
	protected $singleValue = true;
}

/**
 * File Manager - Delete files available and uploaded.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class FM_deleteFilesLogsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_upload_logs
			%s
    ";
}

/**
 * File Manager - Delete files available and uploaded.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class FM_buildFileQuery extends PHPDS_query
{
	/**
	 * Initiate invoke query.
	 */
	public function invoke($parameters = null)
	{
		list($file_id, $alias, $sub_id, $menu_id) = $parameters;
		// Define.
		$query_grouped = false;
		// Check if we need a single file.
		if (!empty($file_id)) {
			$query_grouped = " WHERE file_id = '$file_id' ";
		} else {
			// Group WHERE queries first level.
			if (!empty($alias)) {
				$query_grouped .= " WHERE alias = '$alias' ";
			} else if (!empty($sub_id)) {
				$query_grouped .= " WHERE sub_id = '$sub_id' ";
				$sub_id_set = true;
			} else if (!empty($menu_id)) {
				$query_grouped .= " WHERE menu_id = '$menu_id' ";
				$menu_id_set = true;
			}
			// Group AND queries second level.
			if (!empty($query_grouped)) {
				if (!empty($sub_id) && empty($sub_id_set)) {
					$query_grouped .= " AND sub_id = '$sub_id' ";
				}
				if (!empty($menu_id) && empty($menu_id_set)) {
					$query_grouped .= " AND menu_id = '$menu_id' ";
				}
			} else {
				// We need no queries here.
				$query_grouped = '';
			}
		}
		// Return results.
		return $query_grouped;
	}
}

/**
 * File Manager - Write file uploads logs registry.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class FM_writeFilesLogsQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_upload_logs (file_id, sub_id, menu_id, alias, original_filename, new_filename, relative_path, thumbnail, resized, extention, mime_type, file_desc, group_id, user_id, date_stored, file_size, file_explained)
		VALUES
			('%u', '%u', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%u', '%u', '%s', '%u', '%s')
    ";
}


