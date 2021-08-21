<?php echo Form::open(); ?>
    <div class="col_1_of_2 span_1_of_2">
        <div>
            <?php echo Form::label(__('account.name'), 'name', array('class' => 'required')); ?>
            <br>
            <?php echo Form::input('name', Input::post('name', (isset($account)) ? $account->username : ''), array('placeholder' => 'Username')); ?>
            <?php echo Form::error('name', $err); ?>
        </div>

        <div>
            <?php echo Form::label(__('account.fullname'), 'fullname', array('class' => 'required')); ?>
            <br>
            <?php echo Form::input('fullname', Input::post('fullname', (isset($account)) ? $account->fullname : ''), array('placeholder' => 'Full name')); ?>
            <?php echo Form::error('fullname', $err); ?>
        </div>
        <div>
            <?php echo Form::label(__('account.company'), 'company', array('class' => 'required')); ?>
            <br>
            <?php echo Form::input('company', Input::post('company', (isset($account)) ? $account->company : ''), array('placeholder' => 'Company Name')); ?>
            <?php echo Form::error('company', $err); ?>
        </div>
        <div>
            <?php echo Form::label(__('account.email'), 'email', array('class' => 'required')); ?>
            <br>
            <?php echo Form::input('email', Input::post('email', (isset($account)) ? $account->email : ''), array('placeholder' => 'E-Mail')); ?>
            <?php echo Form::error('email', $err); ?>
        </div>

        <div>
            <?php echo Form::label(__('account.password'), 'password', array('class' => 'required')); ?>
            <br>
            <?php if (!isset($account)) :?>
            <?php echo Form::input('password', Input::post('password'), array('placeholder' => 'Password', 'type' => 'password')); ?>
            <?php else: ?>
            <?php echo Form::input('password', '', array('placeholder' => '*******', 'type' => 'password', 'disabled')); ?>
            <?php endif; ?>
            <?php echo Form::error('password', $err); ?>
        </div>
    </div>
    <div class="col_1_of_2 span_1_of_2">
        <div>
            <?php echo Form::label(__('account.address'), 'address', array('class' => 'required')); ?>
            <br>
            <?php echo Form::input('address', Input::post('address', (isset($account)) ? $account->address : ''), array('placeholder' => 'Address')); ?>
            <?php echo Form::error('address', $err); ?>
        </div>
        <div>
            <?php echo Form::label(__('account.city'), 'city', array('class' => 'required')); ?>
            <br>
            <?php echo Form::input('city', Input::post('city', (isset($account)) ? $account->city : ''), array('placeholder' => 'City')); ?>
            <?php echo Form::error('city', $err); ?>
        </div>
        <div>
            <?php echo Form::label(__('account.country'), 'country', array('class' => 'required')); ?>
            <br>
            <?php echo Form::select('country', Input::post('country', (isset($account)) ? $account->country : ''), $countries); ?>
            <?php echo Form::error('country', $err); ?>
        </div>
        <div>
            <?php echo Form::label(__('account.lang'), 'Language', array('class' => 'required')); ?>
            <br>
            <?php echo Form::select('lang', Input::post('lang', (isset($account)) ? $account->lang : ''), $languages); ?>
            <?php echo Form::error('lang', $err); ?>
        </div>

        <div>
            <?php echo Form::label(__('account.code'), 'code', array('class' => 'required')); ?>  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            <?php echo Form::label(__('account.phone'), 'phone', array('class' => 'required')); ?>
            <br>
            <?php echo Form::input('code', Input::post('code', (isset($account)) ? $account->code : ''), array('placeholder' => 'Code', 'class' => 'code')); ?> - <?php echo Form::input('phone', Input::post('phone', (isset($account)) ? $account->phone : ''), array('placeholder' => 'Number', 'class' => 'number')); ?>
            <?php echo Form::error('code', $err); ?> &nbsp; <?php echo Form::error('phone', $err); ?>
        </div>

        <?php if ($is_admin) : ?>
        <div>
            <?php $authorities = Input::get_field_value('auth', isset($account) ? $account : null, null, Input::method() == 'POST' ? array() : array(USER_AUTH)); ?>
            <?php
                if(Input::post() and (Input::post('auth') == null)) {
                    $authorities = array();
                }
            ?>
            <?php echo Form::label(__('common.auth'), 'authority', array('class' => 'required')); ?>
            <br>
            <?php foreach ($auths as $key => $auth): ?>
                <div class="checkbox">
                    <label>
                        <?php echo Form::checkbox('auth[]', $key, in_array($key, $authorities), !empty($edit_admin) ? array('disabled') : array()); ?>
                        <?php echo $auth; ?>
                    </label>
                </div>
            <?php endforeach; ?>
            <?php echo Form::error('auth', $err); ?>
        </div>
        <br>
        <div>
            <?php echo Form::label(__('common.lock_account'), 'lock'); ?>
            <br>
            <?php
                $lock = false;
                if (Input::post()) {
                    $lock = (Input::post('lock') != null);
                } else {
                    $lock = (isset($account) and $account->lock == true) ? true : false;
                }
            ?>
            <label>
            <?php echo Form::checkbox('lock', 'lock', $lock, array('id' => 'lock_position')); ?>
            <?php echo __('common.lock') ?>
            </label>
        </div>
        <?php endif; ?>
    </div>
    <div class="clear"></div>
    <?php echo Form::button('submit', __('common.btn_submit'), array('class' => 'button button-left')); ?>
    <?php echo Html::anchor($rdr, __('common.cancel'), array('class' => 'button button-left')); ?>
    <br>
    <br>
<?php echo Form::close(); ?>