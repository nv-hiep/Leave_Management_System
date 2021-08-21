<?php $login_port = Session::get('login_port'); ?>
<ul class="nav navbar-nav">

    <!--user's menu-->
    <?php if (Auth::has_access("{$login_port}.request")): ?>
        <li><?php echo Html::anchor('request/history', __('menu.view_history')) ?></li>
        <li><?php echo Html::anchor('request/select_type', __('common.register_request')) ?></li>
    <?php endif; ?>

    <!--manager's menu-->
    <?php if (Auth::has_access("{$login_port}.manager[index]")): ?>
        <li><?php echo Html::anchor('manager', __('manager.master_table')) ?></li>
    <?php endif; ?>

    <!--accounting's menu-->
    <?php if (Auth::has_access("{$login_port}.accounting[select_user]")): ?>
        <li><?php echo Html::anchor('accounting/counter_list', __('menu.counter_list')) ?></li>
        <li><?php echo Html::anchor('accounting/select_user', __('common.confirm_request')) ?></li>
    <?php endif; ?>

    <!--approval's menu-->
    <?php if (Auth::has_access("{$login_port}.allow[select_request]")): ?>
        <li><?php echo Html::anchor('allow/select_request', __('common.approve_request')) ?></li>
    <?php endif; ?>

    <!-- approval view account history menu-->
    <?php if (Auth::has_access("{$login_port}.approver")): ?>
        <?php if (Auth::has_access("{$login_port}.account")): ?>
            <li><?php echo Html::anchor('account/index', __('history.request_history')) ?></li>
        <?php endif; ?>
    <?php endif; ?>

    <!--approval's menu-->
    <?php if (Auth::has_access("{$login_port}.force[select_user]")): ?>
        <li><?php echo Html::anchor('force/select_user', __('common.forced_approval')) ?></li>
    <?php endif; ?>

</ul>
<ul class="nav navbar-nav navbar-right">
    <li><?php echo Html::anchor('base/change_password', '<span class="glyphicon glyphicon-user"></span> ' . __('account.password')) ?></li>
    <li><?php echo Html::anchor('base/logout', '<span class="glyphicon glyphicon-log-out"></span> ' . __('menu.logout')) ?></li>
</ul>