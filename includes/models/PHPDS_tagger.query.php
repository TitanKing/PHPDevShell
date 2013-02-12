<?php

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

class PHPDS_taggerLookupQuery extends  PHPDS_taggerListQuery
{
	protected $singleRow = true;
	protected $focus = 'tagValue';

	public function getResults()
	{
		return $this->asOne(0, $this->focus);
	}
}

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

class PHPDS_taggerListTargetQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			tagID, tagName, tagValue
		FROM
			_db_core_tags
		WHERE
			tagTarget = '%s'
		AND
			tagObject = '%s'
		";
}

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

class PHPDS_deleteStrictTagsQuery extends PHPDS_query
{
    protected $sql = "
		DELETE FROM
			_db_core_tags
		WHERE
			tagObject = '%s'
		AND
			tagTarget = '%s'
	    AND
	        tagName = '%s'
		";
    protected $autoProtect = true;
}

class PHPDS_updateTagsQuery extends PHPDS_query
{
    protected $sql = "
		REPLACE INTO
			_db_core_tags (tagID, tagObject, tagName, tagTarget, tagValue)
		VALUES
	      %s
	";

	public function invoke($parameters = null)
	{
		list($object, $target, $taggernames, $taggervalues, $taggerids, $taggerdeletes) = $parameters;

		if (! empty($target) && ! empty($object)) {
            foreach ($taggernames as $key => $name) {
                if (! empty($name)) {
                    $id     = (! empty($taggerids[$key])) ? $taggerids[$key] : '';
                    $value  = (! empty($taggervalues[$key])) ? $taggervalues[$key] : '';
                    $tag[]  = array($id, $object, $name, $target, $value);
                }
            }

            if (! empty($taggerdeletes)) {
                foreach ($taggerdeletes as $name_) {
                    $this->db->invokeQuery('PHPDS_deleteStrictTagsQuery', $object, $target, $name_);
                }
            }
            if (! empty($tag)) $datarows = $this->rows($tag);
            if (! empty($datarows)) {
                return parent::invoke(array($datarows));
            } else {
                return array();
            }
        }
	}
}
