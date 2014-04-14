<?php

class Tagging_View_Helper_GetTaggingForm extends Zend_View_Helper_Abstract
{

    public function getTaggingForm($record = null)
    {
        if (get_view()->isTaggingAllowed()) {
            require_once PLUGIN_DIR . '/Tagging/forms/TaggingForm.php';

            $taggingSession = new Zend_Session_Namespace('tagging');
            $form = new Tagging_TaggingForm($record);
            if ($taggingSession->post) {
                $form->isValid(unserialize($taggingSession->post));
            }
            unset ($taggingSession->post);

            return $form;
        }
    }
}
