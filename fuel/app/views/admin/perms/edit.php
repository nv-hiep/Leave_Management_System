<div class="register_account">
    <div class="wrap">
        <h4 class="title"><?= __('button.add_auth'); ?></h4>
        <?php echo Form::open(); ?>
        <div class="col_1_of_2 span_1_of_2">
            <div>
                <?php echo Form::label(__('common.area'), 'area', array('class' => 'required')); ?>
                <br>
                <?php echo Form::input('area', Input::post('area', ($perm) ? $perm->area : ''), array('placeholder' => 'som')); ?>
                <?php echo Form::error('area', $error); ?>
            </div>
            <div>
                <?php echo Form::label(__('common.perm'), 'area', array('class' => 'required')); ?>
                <br>
                <?php echo Form::select('perm', Input::post('perm', ($perm) ? $perm->permission : ''), $perms, array('id' => 'permission')); ?>
                <?php echo Form::error('perm', $error); ?>
            </div>
            <div>
                <?php echo Form::label(__('common.desc'), 'area', array('class' => '')); ?>
                <br>
                <?php echo Form::input('desc', Input::post('desc', ($perm) ? $perm->description : '')); ?>
                <?php echo Form::error('desc', $error); ?>
            </div>
            <div>
                <?php
                    $act = isset($perm) ? $perm->actions : '';
                    $act = (Input::post('act')) ? Controller_Admin_Perms::serialise($controllers[Input::post('perm')]) : $act;
                    if ((Input::method() == 'POST') and (empty(Input::post('act')))) {
                        $act = '';
                    }
                ?>
                <?php echo Form::label(__('title.actions'), 'actions', array('class' => 'required')); ?>
                <br>
                <span id="actions" value='<?= $act; ?>' prim-perm='<?= $perm->permission; ?>'></span>
                <?php echo Form::error('act', $error); ?>
            </div>
        </div>

        <div class="clear"></div>
        <?php echo Form::button('submit', __('common.btn_submit'), array('class' => 'button button-left')); ?>
        <?php echo Html::anchor('admin/perms', __('common.cancel'), array('class' => 'button button-left')); ?>
        <br>
    <?php echo Form::close(); ?>
    </div>
</div>