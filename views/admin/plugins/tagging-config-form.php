<?php echo js_tag('vendor/tiny_mce/tiny_mce'); ?>
<script type="text/javascript">
jQuery(window).load(function () {
  Omeka.wysiwyg({
    mode: 'specific_textareas',
    editor_selector: 'html-editor'
  });
});
</script>
<fieldset id="fieldset-tagging-form"><legend><?php echo __('Tagging Form'); ?></legend>
    <div class='field'>
        <div class="two columns alpha">
            <?php echo $this->formLabel('tagging_message',
                __('Message to invite to tag')); ?>
        </div>
        <div class='inputs five columns omega'>
            <div class='input-block'>
                <?php echo $this->formText('tagging_message', get_option('tagging_message') ?: '+'); ?>
            </div>
            <p class="explanation">
                <?php echo __('The text to click to display the tag form (a simple "+" by default, customizable in the theme).'); ?>
            </p>
        </div>
    </div>
    <div class='field'>
        <div class="two columns alpha">
            <?php echo $this->formLabel('tagging_form_class',
                __('Class to add to the form')); ?>
        </div>
        <div class='inputs five columns omega'>
            <div class='input-block'>
                <?php echo $this->formText('tagging_form_class', get_option('tagging_form_class')); ?>
            </div>
        </div>
    </div>
</fieldset>
<fieldset id="fieldset-tagging-form"><legend><?php echo __('Proposed Tagging'); ?></legend>
    <div class='field'>
        <div class="two columns alpha">
            <?php echo $this->formLabel('tagging_max_length_total',
                __('Max length of the proposition of tagging')); ?>
        </div>
        <div class='inputs five columns omega'>
            <div class='input-block'>
                <?php echo $this->formText('tagging_max_length_total', get_option('tagging_max_length_total')); ?>
            </div>
        </div>
    </div>
    <div class='field'>
        <div class="two columns alpha">
            <?php echo $this->formLabel('tagging_max_length_tag',
                __('Max length of a proposed tag')); ?>
        </div>
        <div class='inputs five columns omega'>
            <div class='input-block'>
                <?php echo $this->formText('tagging_max_length_tag', get_option('tagging_max_length_tag')); ?>
            </div>
        </div>
    </div>
</fieldset>
<fieldset id="fieldset-tagging-public"><legend><?php echo __('Public Rights'); ?></legend>
    <div class='field'>
        <div class="two columns alpha">
            <?php echo $this->formLabel('tagging_legal_text',
                __('Legal agreement')); ?>
        </div>
        <div class='inputs five columns omega'>
            <div class='input-block'>
                <?php echo $this->formTextarea(
                    'tagging_legal_text',
                    get_option('tagging_legal_text'),
                    array(
                        'rows' => 5,
                        'cols' => 60,
                        'class' => array('textinput', 'html-editor'),
                     )
                ); ?>
                <p class="explanation">
                    <?php echo __('This text will be shown beside the legal checkbox.'); ?>
                    <?php echo ' ' . __("Let empty if you don't want to use a legal agreement."); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="tagging_public_allow_tag"></label>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox('tagging_public_allow_tag', true,
                array('checked'=>(boolean) get_option('tagging_public_allow_tag'))); ?>
            <p class="explanation">
                <?php echo __('Allow public to tag'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="tagging_public_require_moderation"></label>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox('tagging_public_require_moderation', true,
                array('checked'=>(boolean) get_option('tagging_public_require_moderation'))); ?>
            <p class="explanation">
                <?php echo __('Require approbation for public tags'); ?>
            </p>
        </div>
    </div>
</fieldset>
<fieldset id="fieldset-tagging-roles"><legend><?php echo __('Roles'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('tagging_tag_roles',
                __('Roles that can tag')); ?>
        </div>
        <div class="inputs five columns omega">
            <div class="input-block">
                <?php
                    $currentRoles = unserialize(get_option('tagging_tag_roles')) ?: array();
                    $userRoles = get_user_roles();
                    unset($userRoles['super']);
                    echo '<ul>';
                    foreach ($userRoles as $role => $label) {
                        echo '<li>';
                        echo $this->formCheckbox('tagging_tag_roles[]', $role, array(
                            'checked'=> in_array($role, $currentRoles) ? 'checked' : '',
                        ));
                        echo $label;
                        echo '</li>';
                    }
                    echo '</ul>';
                ?>
            </div>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('tagging_require_moderation_roles',
                __('Roles that require moderation')); ?>
        </div>
        <div class="inputs five columns omega">
            <div class="input-block">
                <?php
                    $currentRoles = unserialize(get_option('tagging_require_moderation_roles')) ?: array();
                    $userRoles = get_user_roles();
                    unset($userRoles['super']);
                    echo '<ul>';
                    foreach ($userRoles as $role => $label) {
                        echo '<li>';
                        echo $this->formCheckbox('tagging_require_moderation_roles[]', $role, array(
                            'checked'=> in_array($role, $currentRoles) ? 'checked' : '',
                        ));
                        echo $label;
                        echo '</li>';
                    }
                    echo '</ul>';
                ?>
            </div>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('tagging_moderate_roles',
                __('Roles that can moderate')); ?>
        </div>
        <div class="inputs five columns omega">
            <div class="input-block">
                <?php
                    $currentRoles = unserialize(get_option('tagging_moderate_roles')) ?: array();
                    $userRoles = get_user_roles();
                    unset($userRoles['super']);
                    echo '<ul>';
                    foreach ($userRoles as $role => $label) {
                        echo '<li>';
                        echo $this->formCheckbox('tagging_moderate_roles[]', $role, array(
                            'checked'=> in_array($role, $currentRoles) ? 'checked' : '',
                        ));
                        echo $label;
                        echo '</li>';
                    }
                    echo '</ul>';
                ?>
            </div>
        </div>
    </div>
</fieldset>
