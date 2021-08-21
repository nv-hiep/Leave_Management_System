<div class="register_account">
    <div class="wrap">
        <h4 class="title"><?= __('button.add_lang'); ?></h4>
        <?php echo Form::open(); ?>
        <div class="col_1_of_2 span_1_of_2">
            <div>
                <?php echo Form::label(__('account.lang'), 'Language', array('class' => 'required')); ?>
                <br>
                <?php echo Form::select('lang', Input::post('lang'), $langs); ?>
                <?php echo Form::err($err); ?>
            </div>
        </div>
        <div class="clear"></div>
        <?php echo Form::button('submit', __('common.btn_submit'), array('class' => 'button button-left')); ?>
        <?php echo Html::anchor('language', __('common.cancel'), array('class' => 'button button-left')); ?>
        <br>
    <?php echo Form::close(); ?>
    </div>
</div>