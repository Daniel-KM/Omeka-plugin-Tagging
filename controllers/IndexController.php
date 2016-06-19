<?php

/**
 * The Tagging index controller class.
 *
 * @package Tagging
 */
class Tagging_IndexController extends Omeka_Controller_AbstractActionController
{
    /**
     * Controller-wide initialization. Sets the underlying model to use.
     */
    public function init()
    {
        $this->_helper->db->setDefaultModelName('Tagging');
    }

    public function indexAction()
    {
        // Always go to browse.
        $this->_helper->redirector('browse');
        return;
    }

    public function addAction()
    {
        $data = $_POST;

        $destination = $data['path'];

        $form = new Tagging_Form_Tagging();
        $valid = $form->isValid($this->getRequest()->getPost());
        if (!$valid) {
            $taggingSession = new Zend_Session_Namespace('tagging');
            $taggingSession->post = serialize($_POST);
            $this->_helper->redirector->gotoUrl($destination . '#tagging-form');
        }

        // Currently, tags are allowed only on items.
        if ($data['record_type'] != 'Item') {
            $this->_helper->flashMessenger(__('This record does not accept tags.'), 'warning');
            $this->_helper->redirector->gotoUrl($destination);
        }

        // Security check.
        $record = get_record_by_id($data['record_type'], (integer) $data['record_id']);
        if (!$record) {
            $this->_helper->flashMessenger(__('Record does not exist.'), 'warning');
            $this->_helper->redirector->gotoUrl($destination);
        }

        // Moderation or not.
        $user = current_user();
        // If the user can moderate, the proposition is automatically approved.
        $moderationRoles = unserialize(get_option('tagging_moderate_roles'));
        if (in_array($user->role, $moderationRoles)) {
            $status = 'approved';
        }
        // Else check if a moderation is required.
        else {
            if (empty($user)) {
                $user_id = 0;
                $requireModeration = (boolean) get_option('tagging_public_require_moderation');
            }
            else {
                $user_id = $user->id;
                $requireModerationRoles = unserialize(get_option('tagging_require_moderation_roles'));
                $requireModeration = in_array($user->role, $requireModerationRoles);
            }
            $status = $requireModeration ? 'proposed' : 'allowed';
        }

        // Default values for tagging.
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $data['status'] = $status;
        // Need getValue to run the filter.
        $userTagging = $form->getElement('tagging')->getValue();
        $proposedTaggingsNames = explode(get_option('tag_delimiter'), $userTagging);

        // Prepare checks of existing tags.
        $db = get_db();
        $recordTags = $record->getTags();
        $recordTaggings = $db->getTable('Tagging')->findByRecord($record);
        $recordTagsNames = $this->_getTagsNames($recordTags);
        $recordTaggingsNames = $this->_getTagsNames($recordTaggings);

        // There is one tagging by tag to simplify management.
        $tagsToAdd = array();
        $approvedExistingTags = array();
        foreach ($proposedTaggingsNames as $proposedTag) {
            // Name sanitization is done later, in beforeSave().
            $data['name'] = $proposedTag;
            $tagging = new Tagging;
            $tagging->user_id = $user_id;
            $tagging->setArray($data);
            $sanitizedName = $tagging->sanitizeName();

            // Check the quality of tag.
            if (!$sanitizedName) {
                continue;
            }
            // Check if this tagging is not a duplicate.
            if (in_array($sanitizedName, $tagsToAdd)) {
                continue;
            }
            // Check if this tagging is not already set.
            if (in_array($sanitizedName, $recordTagsNames)) {
                continue;
            }
            // Check size of a tag.
            if (strlen($sanitizedName) > get_option('tagging_max_length_tag')) {
                $this->_helper->flashMessenger(__('A proposed tag cannot be longer than %d characters.', get_option('tagging_max_length_tag')), 'error');
                continue;
            }

            // Check if this tagging is not already saved.
            if (in_array($sanitizedName, $recordTaggingsNames)) {
                $existingTagging = $recordTaggings[array_search($sanitizedName, $recordTaggingsNames)];
                // Check status.
                // Normally, an existing approved tagging is already an item tag.
                if ($tagging->status == 'approved') {
                    $existingTagging->status = 'approved';
                    $existingTagging->save();
                    $approvedExistingTags[] = $sanitizedName;
                }
                // In all other cases (already approved or rejected), the
                // old tagging is kept in place of the new one.
                continue;
            }

            $tagsToAdd[] = $sanitizedName;
            // Taggings are automatically added to item if they are appoved.
            $tagging->save();
        }

        // Information for user.
        if (count($approvedExistingTags)) {
            $this->_helper->flashMessenger(__('Proposed tags "%s" have been approved.', implode(', ', $approvedExistingTags)), 'success');
        }
        if (count($tagsToAdd) == 0 && count($approvedExistingTags) == 0) {
            $this->_helper->flashMessenger(__('Your proposition "%s"  is already proposed or is not correctly formatted.', $userTagging), 'warning');
        }
        else {
            if ($requireModeration) {
                $this->_helper->flashMessenger(__('Your proposition "%s" is awaiting approbation.', $userTagging), 'success');
            }
            else {
                if (count($tagsToAdd) == 0) {
                    // In that case, this is approved existing tags.
                }
                elseif (count($tagsToAdd) == 1) {
                    $this->_helper->flashMessenger(__('Your tag "%s" have been added.', implode(', ', $tagsToAdd)), 'success');
                }
                else {
                    $this->_helper->flashMessenger(__('Your tags "%s" have been added.', implode(', ', $tagsToAdd)), 'success');
                }
            }
        }

        $this->_helper->redirector->gotoUrl($destination);
    }

    /**
     * Get all names of a list of tags or taggings objects.
     *
     * @param array $tags Array of tag or tagging objects.
     * @return array of string
     * List of names of tags or taggings.
     */
    protected function _getTagsNames($tags)
    {
        $result = array();
        foreach ($tags as $tag) {
            $result[] = $tag->name;
        }
        return $result;
    }

    public function browseAction()
    {
        if (!$this->hasParam('sort_field')) {
            $this->setParam('sort_field', 'added');
        }

        if (!$this->hasParam('sort_dir')) {
            $this->setParam('sort_dir', 'd');
        }

        parent::browseAction();
    }

    /**
     * Retrieve the number of records to display on any given browse page.
     * This can be modified as a query parameter provided that a user is
     * actually logged in.
     *
     * @return integer
     */
    protected function _getBrowseRecordsPerPage($pluralName = null)
    {
        return is_admin_theme()
            ? (int) get_option('per_page_admin')
            : (int) get_option('per_page_public');
    }
}
