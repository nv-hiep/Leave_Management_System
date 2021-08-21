<div class="register_account">
    <div class="wrap">
        <h4 class="title"><?= __('button.add_auth'); ?></h4>
        <?php echo Form::open(); ?>
        <div class="col_1_of_2 span_1_of_2">
            <div>
                <?php echo Form::label(__('common.area'), 'area', array('class' => 'required')); ?>
                <br>
                <?php echo Form::input('area', Input::post('area'), array('placeholder' => 'som')); ?>
                <?php echo Form::error('area', $error); ?>
            </div>
            <div>
                <?php echo Form::label(__('common.perm'), 'area', array('class' => 'required')); ?>
                <br>
                <?php echo Form::select('perm', Input::post('perm'), $perms, array('id' => 'permission')); ?>
                <?php echo Form::error('perm', $error); ?>
            </div>
            <div>
                <?php echo Form::label(__('common.desc'), 'area', array('class' => '')); ?>
                <br>
                <?php echo Form::input('desc', Input::post('desc')); ?>
                <?php echo Form::error('desc', $error); ?>
            </div>
            <div>
                <?php
                    $act = (Input::post('act')) ? Controller_Admin_Perms::serialise($controllers[Input::post('perm')]) : '';
                ?>
                <?php echo Form::label(__('title.actions'), 'actions', array('class' => 'required')); ?>
                <br>
                <span id="actions" value='<?= $act; ?>'></span>
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
