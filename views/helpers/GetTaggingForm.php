<?php

class Tagging_View_Helper_GetTaggingForm extends Zend_View_Helper_Abstract
{

    public function getTaggingForm($record = null)
    {
        if (get_view()->isTaggingAllowed()) {
            $taggingSession = new Zend_Session_Namespace('tagging');
            $form = new Tagging_Form_Tagging($record);
            if ($taggingSession->post) {
                $form->isValid(unserialize($taggingSession->post));
            }
            unset ($taggingSession->post);

            return $form;
        }
        return '';
    }
}
