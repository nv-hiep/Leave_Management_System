<div class="register_account">
    <div class="wrap">
        <h4 class="title"><?= __('account.info'); ?></h4>
        <form>
        <div class="col_1_of_2 span_1_of_2">
            <div>
                <?php echo Form::label(__('account.name'), 'name'); ?>
                <br>
                <?php echo Form::input('', $account->username, array('readonly' => 'readonly')); ?>
            </div>

            <div>
                <?php echo Form::label(__('account.fullname'), 'fullname'); ?>
                <br>
                <?php echo Form::input('', $account->fullname, array('readonly' => 'readonly')); ?>
            </div>
            <div>
                <?php echo Form::label(__('account.company'), 'company'); ?>
                <br>
                <?php echo Form::input('', $account->company, array('readonly' => 'readonly')); ?>
            </div>
            <div>
                <?php echo Form::label(__('account.email'), 'email'); ?>
                <br>
                <?php echo Form::input('', $account->email, array('readonly' => 'readonly')); ?>
            </div>
            <div>
                <?php echo Form::label(__('account.address'), 'address'); ?>
                <br>
                <?php echo Form::input('', $account->address, array('readonly' => 'readonly')); ?>
            </div>
        </div>
        <div class="col_1_of_2 span_1_of_2">
            <div>
                <?php echo Form::label(__('account.city'), 'city'); ?>
                <br>
                <?php echo Form::input('', $account->city, array('readonly' => 'readonly')); ?>
            </div>
            <div>
                <?php echo Form::label(__('account.country'), 'country'); ?>
                <br>
                <?php echo Form::input('', $countries[$account->country], array('readonly' => 'readonly')); ?>
            </div>
            <div>
                <?php echo Form::label(__('account.lang'), 'Language'); ?>
                <br>
                <?php echo Form::input('', $languages[$account->lang], array('readonly' => 'readonly')); ?>
            </div>

            <div>
                <?php echo Form::label(__('account.code'), 'code'); ?>  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <?php echo Form::label(__('account.phone'), 'phone'); ?>
                <br>
                <?php echo Form::input('', $account->code, array('class' => 'code', 'readonly' => 'readonly')); ?> - <?php echo Form::input('', $account->phone, array('class' => 'number', 'readonly' => 'readonly')); ?>
            </div>
        </div>
        </form>
        <?php echo Html::anchor('user/password', __('menu.change_passwd'), array('class' => 'button button-right')) ?> &nbsp;
        <?php echo Html::anchor('account/edit', __('menu.edit_account'), array('class' => 'button button-right')) ?>
        <div class="clear"></div>
        <br>
    </div>
</div>