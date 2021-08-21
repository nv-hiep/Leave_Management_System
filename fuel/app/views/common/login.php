<div class="login">
    <div class="wrap">
        <div class="col_1_of_login span_1_of_login">
            <h4 class="title">New Customers</h4>
            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan</p>
            <div class="button1">
                <?php echo Html::anchor('account/register', __('menu.create_account')) ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="col_1_of_login span_1_of_login">
            <div class="login-title">
                <h4 class="title">Registered Customers</h4>
                <div id="loginbox" class="loginbox">
                    <?php echo Form::open(array('id' => 'login-form')); ?>
                        <fieldset class="input">
                            <p id="login-form-username">
                                <label for="modlgn_username">Username</label>
                                <?php echo Form::input('account', Input::post('account'), array('autocomplete'=>"off", 'size'=>"18", 'id'=>"modlgn_username", 'class'=>"inputbox")); ?>
                                <?php echo Form::error('account', $err) ?>
                            </p>
                            <p id="login-form-password">
                                <label for="modlgn_passwd">Password</label>
                                <?php echo Form::input('passwd', Input::post('passwd'), array('type'=>"password", 'autocomplete'=>"off", 'size'=>"18", 'id'=>"modlgn_passwd", 'class'=>"inputbox")); ?>
                                <?php echo Form::error('passwd', $err) ?>
                            </p>
                            <div class="remember">
                                <p id="login-form-remember">
                                    <label for="modlgn_remember"><a href="#">Forget Your Password ? </a></label>
                                </p>
                                <input type="submit" name="Submit" class="button" value="Login"><div class="clear"></div>
                            </div>
                        </fieldset>
                    <?php echo Form::close(); ?>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>