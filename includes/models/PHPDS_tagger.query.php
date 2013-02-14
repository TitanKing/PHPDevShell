<?php

class PHPDS_taggerListQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			tag_id, tag_object, tag_name, tag_target, tag_value
		FROM
			_db_core_tags";

	protected $where = '1';
	protected $autoProtect = true;

	public function extraBuild($parameters = null)
	{
		if (!empty($parameters['object'])) $this->addWhere("tag_object = '%(object)s'");
		if (!empty($parameters['name'])) $this->addWhere("tag_name = '%(name)s'");
		if (!empty($parameters['target'])) $this->addWhere("tag_target = '%(target)s'");
		if (!empty($parameters['value'])) $this->addWhere("tag_value = '%(value)s'");

		return parent::extraBuild($parameters);
	}

}

class PHPDS_taggerLookupQuery extends  PHPDS_taggerListQuery
{
	protected $singleRow = true;
	protected $focus = 'tag_value';

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
			tag_object = '%(object)s', tag_name = '%(name)s', tag_target = '%(target)s'
	";
	protected $autoProtect = true;

	public function checkParameters(&$parameters = null)
	{
		if (!isset($parameters['value']) || is_null($parameters['value'])) {
			$parameter['value'] = 'NULL';
			$this->sql .= ', tag_value = NULL';
		} else {
			$this->sql .= ", tag_value = '%(value)s'";
		}
		return true;
	}
}

class PHPDS_taggerListTargetQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			tag_id, tag_name, tag_value
		FROM
			_db_core_tags
		WHERE
			tag_target = '%s'
		AND
			tag_object = '%s'
		";
}

class PHPDS_deleteTagsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_tags
		WHERE
			tag_object = '%s'
		AND
			tag_target = '%s'
		";
	protected $autoProtect = true;
}

class PHPDS_deleteStrictTagsQuery extends PHPDS_query
{
    protected $sql = "
		DELETE FROM
			_db_core_tags
		WHERE
			tag_object = '%s'
		AND
			tag_target = '%s'
	    AND
	        tag_name = '%s'
		";
    protected $autoProtect = true;
}

class PHPDS_updateTagsQuery extends PHPDS_query
{
    protected $sql = "
		REPLACE INTO
			_db_core_tags (tag_id, tag_object, tag_name, tag_target, tag_value)
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
