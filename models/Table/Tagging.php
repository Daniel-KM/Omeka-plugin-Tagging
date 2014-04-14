<?php

/**
 * @package Tagging\models\Table
 */
class Table_Tagging extends Omeka_Db_Table
{
    public function findByRecord($record)
    {
        return $this->findBy(array(
            'record_type' => get_class($record),
            'record_id' => $record->id,
        ));
    }

    public function findByRecordAndName($record, $name)
    {
        $result = $this->findBy(array(
            'record_type' => get_class($record),
            'record_id' => $record->id,
            'name' => $name,
        ));
        return reset($result);
    }

    /**
     * Find by record and one or multiple status.
     *
     * @param Record $record
     * @param string|array $status One status or a array of status.
     * @return array of taggings.
     */
    public function findByRecordAndStatus($record, $status)
    {
        if (empty($status)) {
            return array();
        }

        // TODO Use the OR Zend clause.
        if (is_array($status)) {
            if (count($status) == 1) {
                $status = reset($status);
            }
            else {
                $statusSql =str_repeat(' OR status = ?', count($status) - 1);
                $bind = array();
                $bind[] = get_class($record);
                $bind[] = $record->id;
                $bind = array_merge($bind, $status);
                return $this->findBySql(
                    'record_type = ? AND record_id = ? AND (status = ?'. $statusSql . ')',
                    $bind
                );
            }
        }

        return $this->findBy(array(
            'record_type' => get_class($record),
            'record_id' => $record->id,
            'status' => $status,
        ));
    }

    public function findModeratedByRecord($record)
    {
        return $this->findByRecordAndStatus($record, array('approved', 'rejected'));
    }

    /**
     * Find by one or multiple status.
     *
     * @param string|array $status One status or a array of status.
     * @return array of taggings.
     */
    public function findByStatus($status)
    {
        if (empty($status)) {
            return array();
        }

        // TODO Use the OR Zend clause.
        if (is_array($status)) {
            return $this->findBySql('status in (?)',
                array(
                    implode(', ', $status),
            ));
        }

        return $this->findBy(array(
            'status' => $status,
        ));
    }

    public function findModerated()
    {
        return $this->findByStatus(array('approved', 'rejected'));
    }

    /**
     * Find by one or multiple status.
     *
     * @param integer|User $user User object or user id.
     * @return array of taggings.
     */
    public function findByUser($user)
    {
        $user_id = is_object($user) ? $user->id : $user;
        return $this->findBy(array(
            'user_id' => $user_id,
        ));
    }

    public function findByAnonymous()
    {
        return $this->findBy(array(
            'user_id' => 0,
        ));
    }

    /**
     * Filter taggings by record.
     *
     * @todo As Omeka, manages only items.
     *
     * @see self::applySearchFilters()
     * @param Omeka_Db_Select
     * @param integer $record Record id.
     */
    public function filterByRecord($select, $record)
    {
        $alias = $this->getTableAlias();
        $select->where($alias . '.record_type = ?', 'Item');
        $select->where($alias . '.record_id = ?', $record);
    }

    /**
     * Filter taggings by name.
     *
     * @see self::applySearchFilters()
     * @param Omeka_Db_Select
     * @param string $record name.
     */
    public function filterByName($select, $name)
    {
        $alias = $this->getTableAlias();
        $select->where($alias . '.name = ?', $name);
    }

    /**
     * Filter records on their status.
     *
     * @see self::applySearchFilters()
     * @param Omeka_Db_Select
     * @param array|string $status Status to filter by.
     */
    public function filterByStatus($select, $status)
    {
        $alias = $this->getTableAlias();
        if (is_array($status)) {
            $select->where($alias . '.status in (?)', $status);
        }
        else {
            $select->where($alias . '.status = ?', $status);
        }
    }

    /**
     * @param Omeka_Db_Select
     * @param array
     * @return void
     */
    public function applySearchFilters($select, $params)
    {
        $boolean = new Omeka_Filter_Boolean;
        foreach ($params as $key => $value) {
            if ($value === null || (is_string($value) && trim($value) == '')) {
                continue;
            }
            switch ($key) {
                // Currently, Omeka manage only Item taggings.
                case 'record':
                case 'record_id':
                    $this->filterByRecord($select, $value);
                    break;
                case 'tag':
                case 'name':
                    $this->filterByName($select, $value);
                    break;
                case 'status':
                    switch ($value) {
                        case 'moderated':
                            $this->filterByStatus($select, array('approved', 'rejected'));
                            break;
                        case 'not moderated':
                            $this->filterByStatus($select, array('proposed', 'allowed'));
                            break;
                        default:
                            $this->filterByStatus($select, $value);
                            break;
                    }
                    break;
                case 'user_id':
                    $this->filterByUser($select, $value, 'user_id');
                    break;
                case 'added_since':
                    $this->filterBySince($select, $value, 'added');
                    break;
            }
        }

        // If we returning the data itself, we need to group by the record id.
        $select->group('taggings.id');
    }
}
