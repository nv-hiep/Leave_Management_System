<div class="register_account">
    <div class="wrap">
        <h4 class="title"><?= __('title.currency'); ?></h4>
        <table id="customers">
            <tr>
                <th><?= __('title.currency'); ?></th>
                <th><?= __('title.code'); ?></th>
                <th><?= __('title.symbol'); ?></th>
                <th><?= __('title.rate'); ?></th>
                <th><?= __('title.actions'); ?></th>
            </tr>
            <?php foreach ($currs as $key => $val): ?>
            <tr>
                <td><?= $val->name; ?></td>
                <td><?= $val->code; ?></td>
                <td><?= $val->symbol; ?></td>
                <td><?= $val->rate; ?></td>
                <td>
                    <?php echo Html::anchor('currency/edit/'   . $key, __('common.edit')) ?> |
                    <?php echo Html::anchor('currency/delete/' . $key, __('common.delete'),  array('onclick' => 'return confirm("'.__('message.deleteyn').'");')) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <?php echo Html::anchor('currency/add', __('button.add_currency'), array('class' => 'button button-right')) ?>
        <br>
    </div>
</div>