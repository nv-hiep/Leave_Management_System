<div class="register_account">
    <div class="wrap">
        <h4 class="title"><?= __('button.edit_auth'); ?></h4>
        <?php echo Form::open(); ?>
        <div class="col_1_of_2 span_1_of_2">
            <div>
                <?php echo Form::label(__('common.auth'), 'authority', array('class' => 'required')); ?>
                <br>
                <?php echo Form::input('auth', Input::post('auth', ($auth) ? $auth->name : '')); ?>
                <?php echo Form::error('auth', $error); ?>
            </div>
        </div>

        <div class="clear"></div>
        <?php echo Form::button('submit', __('common.btn_submit'), array('class' => 'button button-left')); ?>
        <?php echo Html::anchor('admin/roles', __('common.cancel'), array('class' => 'button button-left')); ?>
        <br>
    <?php echo Form::close(); ?>
    </div>
</div>