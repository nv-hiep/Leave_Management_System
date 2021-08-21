<div class="register_account">
    <div class="wrap">
        <h4 class="title"><?= __('title.set_role_perm'); ?></h4>
        <?php echo Form::open(); ?>
        <div class="col_1_of_2 span_1_of_2">
            <div>
                <?php echo Form::label(__('common.role'), 'role', array('class' => 'required')); ?>
                <br>
                <?php echo Form::select('role', Input::post('role', ($rp) ? $rp->role_id : ''), $auths, array('id' => 'role')); ?>
                <?php echo Form::error('role', $error); ?>
            </div>
            <div>
                <?php echo Form::label(__('common.perm'), 'permission', array('class' => 'required')); ?>
                <br>
                <?php echo Form::select('perm', Input::post('perm', ($rp) ? $rp->perms->permission : ''), $perms, array('id' => 'permission')); ?>
                <?php echo Form::error('perm', $error); ?>
            </div>
        </div>

        <div class="col_1_of_2 span_1_of_2">
            <div>
                <?php
                    $act = isset($rp) ? $rp->actions : '';
                    $act = (Input::post('act')) ? Controller_Admin_Perms::serialise($controllers[Input::post('perm')]) : $act;
                    if ((Input::method() == 'POST') and (empty(Input::post('act')))) {
                        $act = '';
                    }
                ?>
                <?php echo Form::label(__('title.actions'), 'actions', array('class' => 'required')); ?>
                <br>
                <span id="actions" value='<?= $act; ?>' prim-perm='<?= $rp->perms->permission; ?>'></span>
                <?php echo Form::error('act', $error); ?>
            </div>
        </div>

        <div class="clear"></div>
        <?php echo Form::button('submit', __('common.btn_submit'), array('class' => 'button button-left')); ?>
        <?php echo Html::anchor('admin/roleperm', __('common.cancel'), array('class' => 'button button-left')); ?>
        <br>
        <br>
    <?php echo Form::close(); ?>
    </div>
</div>
