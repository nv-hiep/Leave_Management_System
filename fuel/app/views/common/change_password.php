<div class="login">
    <div class="wrap">
        <div class="col_1_of_login span_1_of_login">
            <h4 class="title"><?= __('account.new_customers'); ?></h4>
            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan</p>
            <div class="button1">
                <?php echo Html::anchor('account/register', __('menu.create_account'), array('class' => 'button button-right')) ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="col_1_of_login span_1_of_login">
            <div class="login-title">
                <h4 class="title"><?php echo __('common.change_password'); ?></h4>
                <div id="loginbox" class="loginbox">
                    <?php echo Form::open(array('id' => 'login-form')); ?>
                        <fieldset class="input">
                            <p id="login-form-username">
                                <label for="modlgn_username" class="required"><?= __('account.old_password'); ?></label>
                                <?php echo Form::input('old_password', null, array('type'=>'password', 'autocomplete'=>"off", 'size'=>"18", 'id'=>"modlgn_username", 'class'=>"inputbox")); ?>
                                <?php echo Form::error('old_password', $err); ?>
                            </p>
                            <p id="login-form-password">
                                <label for="modlgn_passwd" class="required"><?= __('account.new_password'); ?></label>
                                <?php echo Form::input('new_password', null, array('type'=>'password', 'autocomplete'=>"off", 'size'=>"18", 'id'=>"modlgn_username", 'class'=>"inputbox")); ?>
                                <?php echo Form::error('new_password', $err); ?>
                            </p>
                            <p id="login-form-password">
                                <label for="modlgn_passwd" class="required"><?= __('account.confirm_password'); ?></label>
                                <?php echo Form::input('confirm_password', null, array('type'=>'password', 'autocomplete'=>"off", 'size'=>"18", 'id'=>"modlgn_username", 'class'=>"inputbox")); ?>
                                <?php echo Form::error('confirm_password', $err); ?>
                            </p>
                            <div class="remember">
                                <?php echo Form::button('submit', __('common.btn_submit'), array('class' => 'button button-right')); ?>
                                <?php echo Html::anchor('user/info', __('common.cancel'), array('class' => 'button button-right')); ?>
                                <div class="clear"></div>
                            </div>
                        </fieldset>
                    <?php echo Form::close(); ?>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>