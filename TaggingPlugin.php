<?php
/**
 * Tagging
 *
 * Allows users to add tags with or without approbation to create a folksonomy.
 *
 * @copyright Copyright Daniel Berthereau, 2013-2014
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2.1-en.txt
 * @package Tagging
 *
 * @todo Use mixin_owner
 */

/**
 * The Tagging plugin.
 * @package Omeka\Plugins\Tagging
 */
class TaggingPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'initialize',
        'install',
        'upgrade',
        'uninstall',
        'config_form',
        'config',
        'define_acl',
        'admin_head',
        'public_head',
        'admin_items_browse',
        'admin_items_browse_simple_each',
        'admin_items_browse_detailed_each',
        'admin_items_show_sidebar',
        'public_items_show',
        'after_delete_item',
        'remove_item_tag',
    );

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        'admin_navigation_main',
    );

    /**
     * @var array Options and their default values.
     */
    protected $_options = array(
        'tagging_form_class' => '',
        'tagging_max_length_total' => 400,
        'tagging_max_length_tag' => 40,
        'tagging_legal_text' => '',
        // Without roles.
        'tagging_public_allow_tag' => true,
        'tagging_public_require_moderation' => true,
        // With roles, in particular if Guest User is installed.
        // serialize(array()) = 'a:0:{}'.
        'tagging_tag_roles' => 'a:0:{}',
        'tagging_require_moderation_roles' => 'a:0:{}',
        'tagging_moderate_roles' => 'a:0:{}',
    );

    /**
     * Add the translations.
     */
    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . '/languages');
    }

    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        $db = $this->_db;
        // Currently, in Omeka, tags are allowed only for items, but the code
        // allows to tag anything.
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->Tagging` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `record_type` varchar(50) collate utf8_unicode_ci NOT NULL DEFAULT '',
            `record_id` int unsigned NOT NULL,
            `name` varchar(255) collate utf8_unicode_ci NOT NULL DEFAULT '',
            `status` enum('proposed', 'allowed', 'approved', 'rejected') NOT NULL DEFAULT 'proposed',
            `user_id` int(10) DEFAULT NULL,
            `ip` tinytext COLLATE utf8_unicode_ci,
            `user_agent` tinytext COLLATE utf8_unicode_ci,
            `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `record_type_record_id` (`record_type`, `record_id`),
            KEY (`name`),
            KEY (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";
        $db->query($sql);

        $html = '<p>';
        $html .= __('I agree with %s terms of use %s and I accept to free my contribution under the licence %s CC BY-SA %s.',
            '<a rel="licence" href="#" target="_blank">', '</a>',
            '<a rel="licence" href="https://creativecommons.org/licenses/by-sa/3.0/" target="_blank">', '</a>'
        );
        $html .= '</p>';
        $this->_options['tagging_legal_text'] = $html;

        $this->_installOptions();
    }

    /**
     * Upgrade the plugin.
     */
    public function hookUpgrade($args)
    {
        $oldVersion = $args['old_version'];
        $newVersion = $args['new_version'];
        $db = $this->_db;

        if (version_compare($oldVersion, '2.1', '<')) {
            $sql = "
                ALTER TABLE `{$db->Tagging}`
                CHANGE `status` `status` enum('proposed', 'allowed', 'approved', 'rejected') COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'proposed' AFTER `name`
            ";
            $db->query($sql);
        }
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        $db = $this->_db;
        $sql = "DROP TABLE IF EXISTS `$db->Tagging`";
        $db->query($sql);

        $this->_uninstallOptions();
    }

    /**
     * Shows plugin configuration page.
     */
    public function hookConfigForm($args)
    {
        $view = get_view();
        echo $view->partial(
            'plugins/tagging-config-form.php'
        );
    }

    /**
     * Processes the configuration form.
     *
     * @return void
     */
    public function hookConfig($args)
    {
        $post = $args['post'];
        foreach ($this->_options as $optionKey => $optionValue) {
            if (in_array($optionKey, array(
                    'tagging_tag_roles',
                    'tagging_require_moderation_roles',
                    'tagging_moderate_roles',
                ))) {
               $post[$optionKey] = serialize($post[$optionKey]) ?: serialize(array());
            }
            if (isset($post[$optionKey])) {
                set_option($optionKey, $post[$optionKey]);
            }
        }
    }

    /**
     * Defines the plugin's access control list.
     *
     * @param object $args
     */
    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $acl->addResource('Tagging_Tagging');
        $acl->allow(null, 'Tagging_Tagging', array('show', 'flag'));

        if (get_option('tagging_public_allow_tag')) {
            $acl->allow(null, 'Tagging_Tagging', array('add'));
        }
        else {
            $roles = unserialize(get_option('tagging_tag_roles'));
            // Check that all the roles exist, in case a plugin-added role has
            // been removed (e.g. GuestUser).
            foreach ($roles as $role) {
                if ($acl->hasRole($role)) {
                    $acl->allow($role, 'Tagging_Tagging', 'add');
                }
            }
        }

        // This role is used only inside addAction().
        // $requireModerationRoles = unserialize(get_option('tagging_require_moderation_roles'));

        // Moderation is available even if public can tag without moderation.
        $moderateRoles = unserialize(get_option('tagging_moderate_roles'));
        foreach ($moderateRoles as $role) {
            if ($acl->hasRole($role)) {
                $acl->allow($role, 'Tagging_Tagging', array(
                    'browse',
                    'delete',
                    'update',
                ));
            }
        }
    }

    public function hookAdminHead($args)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if ($controller == 'items'
                && ($action == 'browse' || $action == 'show')
            ) {
            queue_css_file('tagging');
            queue_js_file('tagging');
        }
    }

    public function hookPublicHead($args)
    {
        if ($args['view']->isTaggingAllowed()) {
            queue_css_file('tagging');
        }
    }

    public function hookAdminItemsBrowse($args)
    { ?>
<script type="text/javascript">
    Omeka.messages = jQuery.extend(Omeka.messages,
        {'tagging':{
            'proposed':<?php echo json_encode(__('Proposed')); ?>,
            'allowed':<?php echo json_encode(__('Allowed')); ?>,
            'approved':<?php echo json_encode(__('Approved')); ?>,
            'rejected':<?php echo json_encode(__('Rejected')); ?>
        }}
    );
</script>
    <?php
    }

    public function hookAdminItemsBrowseSimpleEach($args)
    {
        $view = $args['view'];
        $item = $args['item'];

        $taggings = $this->_db->getTable('Tagging')->findByRecord($item);
        if (!count($taggings)) {
            echo __('No proposed taggings');
        }
        else {
            $moderatedTaggings = $this->_db->getTable('Tagging')->findModeratedByRecord($item);
            echo __('Taggings: %d proposed (%d not moderated)', count($taggings), count($taggings) - count($moderatedTaggings));
        }
    }

    public function hookAdminItemsBrowseDetailedEach($args)
    {
        $item = $args['item'];
        $html = '<p><strong>' . __('Taggings:') . '</strong></p>';
        $html .= $this->_displayTaggingsOfItem($item);
        echo $html;
    }

    private function _displayTaggingsOfItem($item)
    {
        $html = '';
        $taggings = $this->_db->getTable('Tagging')->findByRecord($item);
        if (count($taggings)) {
            $html .= '<ul class="taggings-list">';
            foreach ($taggings as $tagging) {
                $html .= $this->_displayTaggingForModeration($tagging);
            }
            $html .= '</ul>';
        }
        else {
            $html .= __('No taggings.');
        }
        return $html;
    }

    private function _displayTaggingForModeration($tagging)
    {
        $html = '<li>';
        $html .= '<span href="" id="tagging-edit-%d" class="tag-edit-tag">%s</span>';
        $html .= '<a href="' . ADMIN_BASE_URL . '" id="tagging-%d" class="tagging toggle-status status %s"></a>';
        $html .= '</li>';
        $args = array();
        $args[] = $tagging->id;
        $args[] = html_escape($tagging->name);
        $args[] = $tagging->id;
        $args[] = $tagging->status;
        return vsprintf($html, $args);
    }

    public function hookAdminItemsShowSidebar($args)
    {
        $item = $args['item'];
        $html = '<div class="panel">';
        $html .= '<h4>' . __('Taggings') . '</h4>';
        $html .= $this->_displayTaggingsOfItem($item);
        $html .= '</div>';
        echo $html;
    }

    /**
     * Hook to append html to items/show page.
     */
    public function hookPublicItemsShow($args)
    {
        $view = $args['view'];
        $item = $args['item'];
        if ($view->isTaggingAllowed()) {
            echo $view->getTaggingForm($item);
        }
    }

    /**
     * Hook used when an item is removed.
     */
    public function hookAfterDeleteItem($args)
    {
        $item = $args['record'];
        $taggings = $this->_db->getTable('Tagging')->findByRecord($item);
        foreach ($taggings as $tagging) {
            $tagging->delete();
        }
    }

    /**
     * Hook used when an item tag is removed.
     */
    public function hookRemoveItemTag($args)
    {
        $item = $args['record'];
        $removed = $args['removed'];
        foreach ($removed as $tag) {
            // Check if this tag is a tagging.
            $tagging = $this->_db->getTable('Tagging')->findByRecordAndName($item, $tag->name);
            if ($tagging) {
                $tagging->saveStatus('rejected');
            }
        }
    }

    public function filterAdminNavigationMain($nav)
    {
        if (is_allowed('Tagging_Tagging', 'browse')) {
            $nav[] = array(
                'label' => __('Taggings'),
                'uri' => url('tagging'),
            );
        }

        return $nav;
    }
}
