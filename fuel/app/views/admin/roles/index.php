<div class="register_account">
    <div class="wrap">
        <h4 class="title"><?= __('common.auth'); ?></h4>
        <table id="customers">
            <tr>
                <th><?= __('common.auth'); ?></th>
                <th><?= __('title.actions'); ?></th>
            </tr>
            <?php foreach($auths as $key => $val):?>
            <tr>
                <td><?= $val; ?></td>
                <td>
                    <?php if ($key > 2): ?>
                        <?php echo Html::anchor('admin/roles/edit/'   . $key, __('common.edit')) ?> |
                        <?php echo Html::anchor('admin/roles/delete/' . $key, __('common.delete'),  array('onclick' => 'return confirm("'.__('message.deleteyn').'");')) ?>
                    <?php else : ?>
                        |-(.)(.)-|
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <?php echo Html::anchor('admin/roles/register', __('button.add_auth'), array('class' => 'button button-right')); ?>
        <?php echo Html::anchor('admin', __('common.cancel'), array('class' => 'button button-right')); ?>
        <br>
    </div>
</div>