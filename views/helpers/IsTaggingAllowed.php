<?php

class Tagging_View_Helper_IsTaggingAllowed extends Zend_View_Helper_Abstract
{

    /**
     * Helper to determine if tagging is enabled on current page or not.
     */
    public function isTaggingAllowed()
    {
        static $isAllowed = null;
        if (is_null($isAllowed)) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
            // TODO Set this in config form.
            // if (($request->getControllerName() == 'items' || $request->getControllerName() == 'files' )
            if ($request->getControllerName() == 'items'
                && $request->getActionName() == 'show'
                && ((get_option('tagging_public_allow_tag') == 1)
                    || is_allowed('Tagging_Tagging', 'add'))
                ) {
                $isAllowed = true;
            }
            else {
                $isAllowed = false;
            }
        }
        return $isAllowed;
    }
}
