<div class="register_account">
    <div class="wrap">
        <h4 class="title"><?= __('title.languages'); ?></h4>
        <table id="customers">
            <tr>
                <th><?= __('title.languages'); ?></th>
                <th><?= __('title.abbr'); ?></th>
                <th><?= __('title.actions'); ?></th>
            </tr>
            <?php foreach ($langs as $key => $lang): ?>
            <tr>
                <td><?= $lang; ?></td>
                <td><?= $key; ?></td>
                <td>
                    <?php echo Html::anchor('language/edit/'   . $key, __('common.edit')) ?> |
                    <?php echo Html::anchor('language/delete/' . $key, __('common.delete'),  array('onclick' => 'return confirm("'.__('message.delete_lang').'");')) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <?php echo Html::anchor('language/add', __('button.add_lang'), array('class' => 'button button-right')) ?>
        <br>
    </div>
</div>