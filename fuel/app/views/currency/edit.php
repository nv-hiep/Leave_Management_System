<div class="register_account">
    <div class="wrap">
        <h4 class="title"><?= __('button.add_currency'); ?></h4>
        <?php echo Form::open(); ?>
        <div class="col_1_of_2 span_1_of_2">
            <div>
                <?php echo Form::label(__('common.name'), 'name', array('class' => 'required')); ?>
                <br>
                <?php echo Form::input('name', Input::post('name', ($curr) ? $curr->name : '')); ?>
                <?php echo Form::error('name', $err); ?>
            </div>

            <div>
                <?php echo Form::label(__('title.code'), 'code', array('class' => 'required')); ?>
                <br>
                <?php echo Form::input('code', Input::post('code', ($curr) ? $curr->code : '')); ?>
                <?php echo Form::error('code', $err); ?>
            </div>
        </div>
        <div class="col_1_of_2 span_1_of_2">
            <div>
                <?php echo Form::label(__('title.symbol'), 'company', array('class' => 'required')); ?>
                <br>
                <?php echo Form::input('symbol', Input::post('symbol', ($curr) ? $curr->symbol : ''), array('placeholder' => '')); ?>
                <?php echo Form::error('symbol', $err); ?>
            </div>
            <div>
                <?php echo Form::label(__('title.rate'), 'rate', array('class' => 'required')); ?>
                <br>
                <?php echo Form::input('rate', Input::post('rate', ($curr) ? $curr->rate : ''), array('placeholder' => '')); ?>
                <?php echo Form::error('rate', $err); ?>
            </div>
        </div>
        <div class="clear"></div>
        <?php echo Form::button('submit', __('common.btn_submit'), array('class' => 'button button-left')); ?>
        <?php echo Html::anchor('currency', __('common.cancel'), array('class' => 'button button-left')); ?>
        <br>
    <?php echo Form::close(); ?>
    </div>
</div>