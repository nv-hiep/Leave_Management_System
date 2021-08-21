<div class="register_account">
    <div class="wrap">
        <h4 class="title"><?= __('button.edit_lang'); ?> -- [<?= $lang_editing;?> - <?= $abbr;?>]</h4>
        <?php echo Form::open(); ?>
        <div class="col_1_of_2 col_1_of_2 span_1_of_2">
            <div>
                <?php echo Form::label(__('common.content'), 'content', array('class' => 'required')); ?>
                <br>
                <br>
                <?php echo Form::textarea('content', Input::post('content', ($content) ? $content : ''), array('placeholder' => 'Content', 'rows' => "40", 'cols' => "215")); ?>
                <?php echo Form::err($errc); ?>
            </div>
        </div>
        <div class="col_1_of_2 col_1_of_2 span_1_of_2">
            <div>
                <?php echo Form::label(__('common.valid_content'), 'valid_content', array('class' => 'required')); ?>
                <br>
                <br>
                <?php echo Form::textarea('val_content', Input::post('val_content', ($val_content) ? $val_content : ''), array('placeholder' => 'Content', 'rows' => "40", 'cols' => "215")); ?>
                <?php echo Form::err($errv); ?>
            </div>
        </div>
        <div class="clear"></div>
        <?php echo Form::button('submit', __('common.btn_submit'), array('class' => 'button button-left')); ?>
        <?php echo Html::anchor('language', __('common.cancel'), array('class' => 'button button-left')); ?>
        <br>
    <?php echo Form::close(); ?>
    </div>
</div>