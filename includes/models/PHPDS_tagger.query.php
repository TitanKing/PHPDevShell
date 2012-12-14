<?php

/**
 * Tagger - Search for tags.
 * @author Greg
 *
 * @version 1.1
 * @date 20120105 (v1.1) (greg) added search by value
 */
class PHPDS_taggerListQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			tagID, tagObject, tagName, tagTarget, tagValue
		FROM
			_db_core_tags"; // WHERE tagTarget = '%s', tagObject = '%s', tagName = '%s', tagValue = '%s' ";
	protected $where = '1';
	protected $autoProtect = true;

	public function extraBuild($parameters = null)
	{
		if (!empty($parameters['object'])) $this->addWhere("tagObject = '%(object)s'");
		if (!empty($parameters['name'])) $this->addWhere("tagName = '%(name)s'");
		if (!empty($parameters['target'])) $this->addWhere("tagTarget = '%(target)s'");
		if (!empty($parameters['value'])) $this->addWhere("tagValue = '%(value)s'");

		return parent::extraBuild($parameters);
	}

}

/**
 * Tagger - Look for tags.
 * @author Greg
 */
class PHPDS_taggerLookupQuery extends  PHPDS_taggerListQuery
{
	protected $singleRow = true;
	protected $focus = 'tagValue';

	public function getResults()
	{
		return $this->asOne(0, $this->focus);
	}
}

/**
 * Tagger - Add tags
 * @author Greg
 */
class PHPDS_taggerMarkQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_tags
		SET
			tagObject = '%(object)s', tagName = '%(name)s', tagTarget = '%(target)s'
	";
	protected $autoProtect = true;

	public function checkParameters(&$parameters = null)
	{
		if (!isset($parameters['value']) || is_null($parameters['value'])) {
			$parameter['value'] = 'NULL';
			$this->sql .= ', tagValue = NULL';
		} else {
			$this->sql .= ", tagValue = '%(value)s'";
		}
		return true;
	}
}

/**
 * Tagger - List available tags.
 * @author Jason Schoeman
 */
class PHPDS_taggerListTargetQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			tagName, tagValue
		FROM
			_db_core_tags
		WHERE
			tagTarget = '%s'
		AND
			tagObject = '%s'
		";
	protected $autoProtect = true;
}

/**
 * Tagger - Delete old tags.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteTagsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_tags
		WHERE
			tagObject = '%s'
		AND
			tagTarget = '%s'
		";
	protected $autoProtect = true;
}

/**
 * Tagger - Update tags to database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateTagsQuery extends PHPDS_query
{
    protected $sql = "
		REPLACE INTO
			_db_core_tags (tagID, tagObject, tagName, tagTarget, tagValue)
		VALUES
			%s
	";

	/**
	 * Initiate query invoke command.
	 */
	public function invoke($parameters = null)
	{
		list($object, $target, $value_original, $names) = $parameters;
		if (! empty($target)) {
			// Lets just clean up old tags before we save.
			if (! empty($names)) {
				$new_tags_array = explode("\r\n", $names);
				$new_tags = '';
				foreach ($new_tags_array as $tags) {
					$tags = trim($tags);
					$value = trim($value_original);
					$splittags = strpos($tags, ":");
					if (! empty($splittags)) {
						list($tags, $value) = explode(":", $tags);
						if (empty($value)) {
							$value = trim($value_original);
						}
					}
					if ($tags) {
						$values = $this->protectArray(array($object, $tags, $target, $value), '"');
						$new_tags .= "\r\n(NULL, ".implode(', ', $values).'),';
					}
				}
				if (! empty($new_tags)) {
					$new_tags = rtrim($new_tags,",");
					return parent::invoke(array($new_tags));
				}
			} else {
				return false;
			}
		}
	}
}
