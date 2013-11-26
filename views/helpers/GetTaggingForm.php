<?php

class Tagging_View_Helper_GetTaggingForm extends Zend_View_Helper_Abstract
{

    public function getTaggingForm($item = null)
    {
        if (get_view()->isTaggingAllowed()) {
            if (empty($item)) {
                $item = get_current_record('item');
            }

            require_once PLUGIN_DIR . '/Tagging/forms/TaggingForm.php';

            $taggingSession = new Zend_Session_Namespace('tagging');
            $form = new Tagging_TaggingForm();
            if ($taggingSession->post) {
                $form->isValid(unserialize($taggingSession->post));
            }
            unset ($taggingSession->post);

            return $form;
        }
    }
}
