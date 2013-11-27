<?php echo js_tag('vendor/tiny_mce/tiny_mce'); ?>
<script type="text/javascript">
jQuery(window).load(function () {
  Omeka.wysiwyg({
    mode: 'specific_textareas',
    editor_selector: 'html-editor'
  });
});
</script>
<?php $view = get_view(); ?>
<fieldset id="fieldset-tagging-form"><legend><?php echo __('Tagging Form'); ?></legend>
    <div class='field'>
        <div class="two columns alpha">
            <label><?php echo __('Class to add to the form'); ?></label>
        </div>
        <div class='inputs five columns omega'>
            <div class='input-block'>
                <?php echo $view->formText('tagging_form_class', get_option('tagging_form_class')); ?>
            </div>
        </div>
    </div>
</fieldset>
<fieldset id="fieldset-tagging-form"><legend><?php echo __('Proposed Tagging'); ?></legend>
    <div class='field'>
        <div class="two columns alpha">
            <label><?php echo __('Max length of the proposition of tagging'); ?></label>
        </div>
        <div class='inputs five columns omega'>
            <div class='input-block'>
                <?php echo $view->formText('tagging_max_length_total', get_option('tagging_max_length_total')); ?>
            </div>
        </div>
    </div>
    <div class='field'>
        <div class="two columns alpha">
            <label><?php echo __('Max length of a proposed tag'); ?></label>
        </div>
        <div class='inputs five columns omega'>
            <div class='input-block'>
                <?php echo $view->formText('tagging_max_length_tag', get_option('tagging_max_length_tag')); ?>
            </div>
        </div>
    </div>
</fieldset>
<fieldset id="fieldset-tagging-public"><legend><?php echo __('Public Rights'); ?></legend>
    <div class='field'>
        <div class="two columns alpha">
            <label><?php echo __('Legal agreement'); ?></label>
        </div>
        <div class='inputs five columns omega'>
            <div class='input-block'>
                <?php echo $view->formTextarea(
                    'tagging_legal_text',
                    get_option('tagging_legal_text'),
                    array(
                        'rows' => 5,
                        'cols' => 60,
                        'class' => array('textinput', 'html-editor')
                     )
                ); ?>
                <p class="explanation">
                    <?php echo __('This text will be shown beside the legal checkbox.'
                        . " Let empty if you don't want to use a legal agreement."); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="tagging_public_allow_tag"></label>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formCheckbox('tagging_public_allow_tag', true,
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
            <?php echo get_view()->formCheckbox('tagging_public_require_moderation', true,
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
            <label><?php echo __("Roles that can tag"); ?></label>
        </div>
        <div class="inputs five columns omega">
            <div class="input-block">
                <?php
                    $currentRoles = unserialize(get_option('tagging_tag_roles'));
                    $userRoles = get_user_roles();
                    unset($userRoles['super']);
                    echo '<ul>';
                    foreach ($userRoles as $role => $label) {
                        echo '<li>';
                        echo $view->formCheckbox('tagging_tag_roles[]', $role, array(
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
            <label><?php echo __("Roles that require moderation"); ?></label>
        </div>
        <div class="inputs five columns omega">
            <div class="input-block">
                <?php
                    $currentRoles = unserialize(get_option('tagging_require_moderation_roles'));
                    $userRoles = get_user_roles();
                    unset($userRoles['super']);
                    echo '<ul>';
                    foreach ($userRoles as $role => $label) {
                        echo '<li>';
                        echo $view->formCheckbox('tagging_require_moderation_roles[]', $role, array(
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
            <label><?php echo __("Roles that can moderate"); ?></label>
        </div>
        <div class="inputs five columns omega">
            <div class="input-block">
                <?php
                    $currentRoles = unserialize(get_option('tagging_moderate_roles'));
                    $userRoles = get_user_roles();
                    unset($userRoles['super']);
                    echo '<ul>';
                    foreach ($userRoles as $role => $label) {
                        echo '<li>';
                        echo $view->formCheckbox('tagging_moderate_roles[]', $role, array(
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
