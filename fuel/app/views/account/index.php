<div class="register_account">
    <div class="wrap">
        <h4 class="title"><?= __('common.role_perm'); ?></h4>
        <table>
            <tr>
                <th><?= __('title.ID'); ?></th>
                <th><?= __('account.fullname'); ?></th>
                <th><?= __('account.email'); ?></th>
                <th><?= __('account.company'); ?></th>
                <th><?= __('account.address'); ?></th>
                <th><?= __('account.city'); ?></th>
                <th><?= __('account.country'); ?></th>
                <th><?= __('account.code'); ?></th>
                <th><?= __('account.phone'); ?></th>
                <th><?= __('title.actions'); ?></th>
            </tr>
            <?php foreach($users as $user):?>
            <tr class="<?= ($user->lock == true) ? 'lock' : ''; ?>">
                <td><?= $user->username; ?></td>
                <td><?= $user->fullname; ?></td>
                <td><?= $user->email; ?></td>
                <td><?= $user->company; ?></td>
                <td><?= $user->address; ?></td>
                <td><?= $user->city; ?></td>
                <td><?= $user->country; ?></td>
                <td><?= $user->code; ?></td>
                <td><?= $user->phone; ?></td>
                <td>
                    <?php echo Html::anchor('account/edit/'   . $user->id, __('common.edit')) ?> |
                    <?php echo Html::anchor('account/delete/' . $user->id, __('common.delete'),  array('onclick' => 'return confirm("'.__('message.deleteyn').'");')) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <br>
        <?php echo Html::anchor('account/register/0', __('button.add_user'), array('class' => 'button button-right')); ?>
        <?php echo Html::anchor('admin', __('common.cancel'), array('class' => 'button button-right')); ?>
        <br>
        <br>
    </div>
</div>