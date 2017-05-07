<?php

/**
 * @package Tagging\models
 */
class Tagging extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    public $id;
    public $record_type;
    public $record_id;
    public $name = '';
    public $status = 'proposed';
    public $user_id;
    public $ip;
    public $user_agent;
    public $added;

    /**
     * Get the user object.
     *
     * @return User
     */
    public function getAddedByUser()
    {
        return $this->getTable('User')->find($this->user_id);
    }

    /**
     * Update item after saving the tagging.
     */
    protected function afterSave($args)
    {
        switch ($this->status) {
            case 'proposed':
                break;

            case 'allowed':
            case 'approved':
                $item = get_record_by_id($this->record_type, $this->record_id);
                $item->addTags($this->name);
                $item->save();
                break;

            case 'rejected':
                $item = get_record_by_id($this->record_type, $this->record_id);
                $item->deleteTags($this->name);
                $item->save();
                break;
        }
    }

    /**
     * Set status and save tagging if needed.
     */
    public function saveStatus($status)
    {
        // Update status and save tagging only if needed.
        if (empty($this->id) || $this->status != $status) {
            $this->status = $status;
            $this->save();
        }
    }

    /**
     * Sanitized name and return it.
     *
     * @return string The sanitized name.
     */
    public function sanitizeName()
    {
        $this->_sanitizeName();
        return $this->name;
    }

    /**
     * Check special variables before saving the tagging.
     */
    protected function _validate()
    {
        $this->_sanitizeName();
        if ($this->name == '') {
            $this->addError('name', __("Can't leave an empty tag!"));
        }
        if (!in_array($this->status, array('proposed', 'allowed', 'approved', 'rejected'))) {
            $this->addError('status', __('This status is not authorized.'));
        }
        if (empty($this->record_type)) {
            $this->addError('record_type', __('Record type cannot be empty.'));
        }
        if (empty($this->record_id)) {
            $this->addError('record_id', __('Record id cannot be empty.'));
        }
    }

    /**
     * Sanitize name.
     *
     * @return void.
     */
    private function _sanitizeName()
    {
        $this->name = $this->_sanitizeString($this->name);
    }

    /**
     * Returns a sanitized string.
     *
     * @param string $string The string to sanitize.
     *
     * @return string The sanitized string.
     */
     private function _sanitizeString($string)
    {
        // Quote is allowed.
        $string = strip_tags($string);
        // The first character is a space and the last one is a no-break space.
        $string = trim($string, ' /\\?<>:*%|"`&; ' . "\t\n\r");
        $string = preg_replace('/[\(\{]/', '[', $string);
        $string = preg_replace('/[\)\}]/', ']', $string);
        $string = preg_replace('/[[:cntrl:]\/\\\?<>\*\%\|\"`\&\;#+\^\$\s]/', ' ', $string);
        return trim(preg_replace('/\s+/', ' ', $string));
    }

    public function getProperty($property)
    {
        switch($property) {
            case 'added_username':
                $user = $this->getAddedByUser();
                return $user
                    ? $user->username
                    : __('Anonymous');
            default:
                return parent::getProperty($property);
        }
    }

    public function getResourceId()
    {
        return 'Taggings';
    }
}
