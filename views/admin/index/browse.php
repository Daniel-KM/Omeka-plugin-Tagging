<?php
$pageTitle = __('Taggings (%d total)', $total_results);
queue_css_file('tagging');
queue_js_file('tagging');
queue_js_file('tagging-browse');
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'taggings browse',
));
?>
<?php echo flash(); ?>
<?php if (!Omeka_Captcha::isConfigured()): ?>
    <p class="alert"><?php echo __("You have not entered your %s API keys under %s. We recommend adding these keys, or the tagging form will be vulnerable to spam.", '<a href="http://recaptcha.net/">reCAPTCHA</a>', "<a href='" . url('security#recaptcha_public_key') . "'>" . __('security settings') . "</a>");?></p>
<?php endif; ?>
    <p class="info"><?php echo __('Use <a href="%s">items page</a> to manage taggings by item.', url('items')); ?></p>

<?php if ($total_results): ?>
    <?php echo pagination_links(); ?>

    <form action="<?php echo html_escape(url('tagging/index/batch-edit')); ?>" method="post" accept-charset="utf-8">
        <div class="table-actions batch-edit-option">
            <?php if (is_allowed('Tagging_Tagging', 'update')): ?>
            <input type="submit" class="small green batch-action button" name="submit-batch-approve" value="<?php echo __('Approve'); ?>">
            <?php endif; ?>
            <?php if (is_allowed('Tagging_Tagging', 'update')): ?>
            <input type="submit" class="small green batch-action button" name="submit-batch-reject" value="<?php echo __('Reject'); ?>">
            <?php endif; ?>
            <?php if (is_allowed('Tagging_Tagging', 'delete')): ?>
            <input type="submit" class="small red batch-action button" name="submit-batch-delete" value="<?php echo __('Delete'); ?>">
            <?php endif; ?>
        </div>

        <?php echo common('quick-filters'); ?>

        <table id="taggings">
        <thead>
            <tr>
                <?php if (is_allowed('Tagging_Tagging', 'update')): ?>
                <th class="batch-edit-heading"><?php echo __('Select'); ?>
                </th>
                <?php endif; ?>
                <?php
                $browseHeadings[__('Title')] = null;
                $browseHeadings[__('Tag')] = 'name';
                $browseHeadings[__('Status')] = 'status';
                $browseHeadings[__('User')] = 'user_id';
                $browseHeadings[__('Date Added')] = 'added';
                echo browse_sort_links($browseHeadings, array('link_tag' => 'th scope="col"', 'list_tag' => ''));
                ?>
            </tr>
        </thead>
        <tbody>
            <?php $key = 0; ?>
            <?php foreach (loop('taggings') as $tagging): ?>
                <?php $record = get_record_by_id($tagging->record_type, $tagging->record_id); ?>
            <tr class="tagging <?php if(++$key%2==1) echo 'odd'; else echo 'even'; ?>">
                <?php if (is_allowed('Tagging_Tagging', 'update')): ?>
                <td class="batch-edit-check" scope="row">
                    <input type="checkbox" name="taggings[]" value="<?php echo $tagging->id; ?>" />
                </td>
                <?php endif; ?>
                <td class="record-info">
                    <?php // Currently, taggable records are items in Omeka.
                    // echo link_to($record, 'show', metadata($record, array('Dublin Core', 'Title')));
                    echo link_to_item(null, array(), 'show', $record); ?>
                </td>
                <td class="tagging-name">
                    <?php echo html_escape($tagging->name); ?>
                </td>
                <td class="tagging-status">
                    <?php switch ($tagging->status) {
                        case 'proposed': $status = __('Proposed'); break;
                        case 'allowed': $status = __('Allowed'); break;
                        case 'approved': $status = __('Approved'); break;
                        case 'rejected': $status = __('Rejected'); break;
                        default: $status = __('Undefined');
                    } ?>
                    <a href="<?php echo ADMIN_BASE_URL; ?>" id="tagging-<?php echo $tagging->id; ?>" class="tagging toggle-status status <?php echo $tagging->status; ?>"><?php echo $status; ?></a>
                </td>
                <td>
                    <?php echo html_escape(metadata($tagging, 'added_username')); ?>
                </td>
                <td>
                    <?php echo html_escape(format_date($tagging->added, Zend_Date::DATETIME_SHORT)); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        </table>

        <div class="table-actions batch-edit-option">
            <?php if (is_allowed('Tagging_Tagging', 'update')): ?>
            <input type="submit" class="small green batch-action button" name="submit-batch-approve" value="<?php echo __('Approve'); ?>">
            <?php endif; ?>
            <?php if (is_allowed('Tagging_Tagging', 'update')): ?>
            <input type="submit" class="small green batch-action button" name="submit-batch-reject" value="<?php echo __('Reject'); ?>">
            <?php endif; ?>
            <?php if (is_allowed('Tagging_Tagging', 'delete')): ?>
            <input type="submit" class="small red batch-action button" name="submit-batch-delete" value="<?php echo __('Delete'); ?>">
            <?php endif; ?>
        </div>

        <?php echo common('quick-filters'); ?>
    </form>

    <?php echo pagination_links(); ?>

    <script type="text/javascript">
        Omeka.messages = jQuery.extend(Omeka.messages,
            {'tagging':{
                'proposed':<?php echo json_encode(__('Proposed')); ?>,
                'allowed':<?php echo json_encode(__('Allowed')); ?>,
                'approved':<?php echo json_encode(__('Approved')); ?>,
                'rejected':<?php echo json_encode(__('Rejected')); ?>,
                'confirmation':<?php echo json_encode(__('Are your sure to remove these taggings?')); ?>
            }}
        );
        Omeka.addReadyCallback(Omeka.TaggingsBrowse.setupBatchEdit);
    </script>

<?php else: ?>
    <?php if (total_records('Tagging') == 0): ?>
    <h2><?php echo __('There is no tagging yet.'); ?></h2>
    <?php else: ?>
    <p><?php echo __('The query searched %d items and returned no results.', total_records('Tagging')); ?></p>
    <p><a href="<?php echo url('tagging'); ?>"><?php echo __('See all taggings.'); ?></a></p>
    <?php endif; ?>
<?php endif; ?>
<?php echo foot(); ?>
