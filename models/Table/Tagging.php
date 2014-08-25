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
     * Find by user.
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
     * @param Omeka_Db_Select
     * @param array
     * @return void
     */
    public function applySearchFilters($select, $params)
    {
        $alias = $this->getTableAlias();
        $boolean = new Omeka_Filter_Boolean;
        $genericParams = array();
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
                    $genericParams['name'] = $value;
                    break;
                case 'status':
                    switch ($value) {
                        case 'moderated':
                            $genericParams['status'] = array('approved', 'rejected');
                            break;
                        case 'not moderated':
                            $genericParams['status'] = array('proposed', 'allowed');
                            break;
                        default:
                            $genericParams['status'] = $value;
                            break;
                    }
                    break;
                case 'user_id':
                    $this->filterByUser($select, $value, 'user_id');
                    break;
                case 'added_since':
                    $this->filterBySince($select, $value, 'added');
                    break;
                default:
                    $genericParams[$key] = $value;
            }
        }

        if (!empty($genericParams)) {
            parent::applySearchFilters($select, $genericParams);
        }

        // If we returning the data itself, we need to group by the record id.
        $select->group("$alias.id");
    }
}
