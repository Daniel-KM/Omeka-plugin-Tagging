<?php

class Tagging_TaggingForm extends Omeka_Form
{
    public function init()
    {
        parent::init();
        $this->setAction(WEB_ROOT . '/tagging/index/add');
        $this->setAttrib('id', 'tagging-form');
        $user = current_user();

        $this->addElement('text', 'tagging', array(
            'label' => __('Add Tags'),
            'description' => __('Separe multiple tags with a "%s".', get_option('tag_delimiter')),
            'required' => true,
            'size' => 40,
            // An internal validator is used after (allow some non alnum
            // characters).
            // TODO Use the regex validator here?
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(
                    'min' => 1,
                    'max' => get_option('tagging_max_length_total'),
                    'messages' => array(
                        Zend_Validate_StringLength::TOO_SHORT =>
                            __('Proposed tag cannot be empty.'),
                        Zend_Validate_StringLength::TOO_LONG =>
                            __('Proposed tags cannot be longer than %d characters.', get_option('tagging_max_length_total')),
                    ),
                )),
            ),
            'decorators' => array(),
        ));

        // Assume registered users are trusted and don't make them play recaptcha.
        if (!$user && get_option('recaptcha_public_key') && get_option('recaptcha_private_key')) {
            $this->addElement('captcha', 'captcha',  array(
                'class' => 'hidden',
                'label' => __("Please verify you're a human"),
                'captcha' => array(
                    'captcha' => 'ReCaptcha',
                    'pubkey' => get_option('recaptcha_public_key'),
                    'privkey' => get_option('recaptcha_private_key'),
                    // Make the connection secure so IE8 doesn't complain. if
                    // works, should branch around http: vs https:
                    'ssl' => true,
                ),
                'decorators' => array(),
            ));
        }

        // Add some hidden fields to simplify redirection.
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $record_type = ucfirst(Inflector::singularize($request->getControllerName()));
        $record_id = $request->getParam('id');
        $this->addElement('hidden', 'path', array(
            'value' => $request->getPathInfo(),
            'hidden' => true,
            'class' => 'hidden',
            'decorators' => array('ViewHelper'),
        ));
        $this->addElement('hidden', 'record_type', array(
            'value' => $record_type,
            'hidden' => true,
            'class' => 'hidden',
            'decorators' => array('ViewHelper'),
        ));
        $this->addElement('hidden', 'record_id', array(
            'value' => $record_id,
            'hidden' => true,
            'class' => 'hidden',
            'decorators' => array('ViewHelper'),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => __('Tag it'),
        ));
    }
}
