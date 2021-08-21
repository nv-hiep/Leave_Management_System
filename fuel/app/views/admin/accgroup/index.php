<div class="register_account">
    <div class="wrap">
        <h4 class="title"><?= __('common.account_group'); ?></h4>
        <table id="customers">
            <tr>
                <th style="width:60%"><?= __('common.group'); ?></th>
                <th><?= __('title.actions'); ?></th>
            </tr>
            <?php foreach($groups as $key => $val):?>
                <tr>
                    <td><?php echo $val->name; ?></td>
                    <td>
                        <?php echo Html::anchor('admin/accgroup/edit/'   . $key, __('common.edit')) ?> |
                        <?php echo Html::anchor('admin/accgroup/delete/' . $key, __('common.delete'),  array('onclick' => 'return confirm("'.__('message.deleteyn').'");')) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <?php echo Html::anchor('admin/accgroup/register', __('button.add_accgroup'), array('class' => 'button button-right')); ?>
        <?php echo Html::anchor('admin', __('common.cancel'), array('class' => 'button button-right')); ?>
        <br>
    </div>
</div>