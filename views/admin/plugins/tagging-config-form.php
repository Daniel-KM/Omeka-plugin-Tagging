<?php $view = get_view(); ?>
<fieldset id="fieldset-tagging-form"><legend><?php echo __('Tagging Form'); ?></legend>
    <div class='field'>
        <div class="two columns alpha">
            <label><?php echo __("Class to add to the form"); ?></label>
        </div>
        <div class='inputs five columns omega'>
            <div class='input-block'>
                <?php echo $view->formText('tagging_form_class', get_option('tagging_form_class')); ?>
            </div>
        </div>
    </div>
</fieldset>
<fieldset id="fieldset-tagging-public"><legend><?php echo __('Public Rights'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <label for="tagging_public_allow_tag"></label>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formCheckbox('tagging_public_allow_tag', true,
                array('checked'=>(boolean) get_option('tagging_public_allow_tag'))); ?>
                <?php echo __('Allow public to tag'); ?>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="tagging_public_require_moderation"></label>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formCheckbox('tagging_public_require_moderation', true,
                array('checked'=>(boolean) get_option('tagging_public_require_moderation'))); ?>
                <?php echo __('Require approbation for public tags'); ?>
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
