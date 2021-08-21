<div class="register_account">
    <div class="wrap">
        <h4 class="title"><?= __('common.role_perm'); ?></h4>
        <table>
            <tr>
                <th><?= __('common.auth'); ?></th>
                <th><?= __('common.perm'); ?></th>
                <th><?= __('title.actions'); ?></th>
                <th><?= __('title.actions'); ?></th>
            </tr>
            <?php foreach($rps as $val):?>
            <tr>
                <td><?= $val->role->name; ?></td>
                <td><?= $val->perms->area . ' : ' . $val->perms->permission; ?></td>
                <td>
                <?php
                    foreach ($val->acts as $act) {
                        echo $act . '<br>';
                    }
                ?>
                </td>
                <td>
                    <?php echo Html::anchor('admin/roleperm/edit/'   . $val->id, __('common.edit')) ?> |
                    <?php echo Html::anchor('admin/roleperm/delete/' . $val->id, __('common.delete'),  array('onclick' => 'return confirm("'.__('message.deleteyn').'");')) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <br>
        <?php echo Html::anchor('admin/roleperm/register', __('button.add_roleperm'), array('class' => 'button button-right')); ?>
        <?php echo Html::anchor('admin', __('common.cancel'), array('class' => 'button button-right')); ?>
        <br>
        <br>
    </div>
</div>