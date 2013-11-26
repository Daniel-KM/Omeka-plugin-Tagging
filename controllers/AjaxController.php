<?php
/**
 * The Tagging Ajax controller class.
 *
 * @package Tagging
 */
class Tagging_AjaxController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('Tagging');
    }

    /**
     * Handle AJAX requests to update a tagging.
     */
    public function updateAction()
    {
        if (!$this->_checkAjax('update')) {
            return;
        }

        // Handle action.
        try {
            $status = $this->_getParam('status');
            if (!in_array($status, array('proposed', 'approved', 'rejected'))) {
                $this->getResponse()->setHttpResponseCode(400);
                return;
            }

            $id = (integer) $this->_getParam('id');
            $tagging = get_record_by_id('Tagging', $id);
            if (!$tagging) {
                $this->getResponse()->setHttpResponseCode(400);
                return;
            }
            $tagging->saveStatus($status);
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(500);
        }
    }

    /**
     * Handle AJAX requests to delete a tagging.
     */
    public function deleteAction()
    {
        if (!$this->_checkAjax('delete')) {
            return;
        }

        // Handle action.
        try {
            $id = (integer) $this->_getParam('id');
            $tagging = get_record_by_id('Tagging', $id);
            if (!$tagging) {
                $this->getResponse()->setHttpResponseCode(400);
                return;
            }
            $tagging->delete();
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(500);
        }
    }

    /**
     * Check AJAX requests.
     *
     *
     * 403 Forbidden
     * 400 Bad Request
     * 500 Internal Server Error
     *
     * @param string $action
     */
    protected function _checkAjax($action)
    {
        // Don't render the view script.
        $this->_helper->viewRenderer->setNoRender(true);

        // Only allow AJAX requests.
        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest()) {
            $this->getResponse()->setHttpResponseCode(403);
            return false;
        }

        // Allow only valid calls.
        if ($request->getControllerName() != 'ajax'
                || $request->getActionName() != $action
            ) {
            $this->getResponse()->setHttpResponseCode(400);
            return false;
        }

        // Allow only allowed users.
        if (!is_allowed('Tagging_Tagging', $action)) {
            $this->getResponse()->setHttpResponseCode(403);
            return false;
        }

        return true;
    }
}
