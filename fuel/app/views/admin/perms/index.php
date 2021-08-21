<div class="register_account">
    <div class="wrap">
        <h4 class="title"><?= __('common.perm'); ?></h4>
        <table id="customers">
            <tr>
                <th style="width:10%"><?= __('common.area'); ?></th>
                <th style="width:10%"><?= __('common.perm'); ?></th>
                <th style="width:65%"><?= __('common.method'); ?></th>
                <th><?= __('title.actions'); ?></th>
            </tr>
            <?php foreach($perms as $key => $val):?>
                <tr>
                    <td><?php echo $val->area; ?></td>
                    <td><?php echo $val->permission; ?></td>
                    <td title="<?= $val->actions; ?>"><?php echo Str::truncate($val->actions, 130); ?></td>
                    <td>
                        <?php echo Html::anchor('admin/perms/edit/'   . $key, __('common.edit')) ?> |
                        <?php echo Html::anchor('admin/perms/delete/' . $key, __('common.delete'),  array('onclick' => 'return confirm("'.__('message.deleteyn').'");')) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <?php echo Html::anchor('admin/perms/register', __('button.add_perms'), array('class' => 'button button-right')); ?>
        <?php echo Html::anchor('admin', __('common.cancel'), array('class' => 'button button-right')); ?>
        <br>
    </div>
</div>