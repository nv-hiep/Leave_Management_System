<div class="contents">
    <div class="row">
        <div class="col-lg-4">
            <!-- small box -->
            <div class="small-box bg-gray">
                <div class="inner">
                    <h3>
                        <?php echo isset($stat['day_off']) ? $stat['day_off'] : 0; ?>
                        <small><?php echo __('confirm.day'); ?></small>
                    </h3>
                    <p><?php echo __('type.dayoff'); ?></p>
                </div>
                <span class="glyphicon glyphicon-refresh box-icon"></span>
            </div>
        </div>
        <div class="col-lg-4">
            <!-- small box -->
            <div class="small-box bg-gray">
                <div class="inner">
                    <h3>
                        <?php echo isset($stat['vacation']) ? $stat['vacation'] : 0; ?>
                        <small><?php echo __('confirm.day'); ?></small>
                    </h3>
                    <p><?php echo __('type.paid_vacation'); ?></p>
                </div>
                <span class="glyphicon glyphicon-adjust box-icon"></span>
            </div>
        </div>
        <div class="col-lg-4">
            <!-- small box -->
            <div class="small-box bg-gray">
                <div class="inner">
                    <h3>
                        <?php echo isset($stat['overtime_work']) ? $stat['overtime_work'] : 0; ?>
                        <small><?php echo __('confirm.hours'); ?></small>
                    </h3>
                    <p><?php echo __('type.overtime'); ?></p>
                </div>
                <span class="glyphicon glyphicon-time box-icon"></span>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-4">
            <!-- small box -->
            <div class="small-box bg-gray">
                <div class="inner">
                    <h3>
                        <?php echo $day_off; ?>
                        <small><?php echo __('confirm.day'); ?></small>
                    </h3>
                    <p><?php echo __('common.dayoff_not_confirmed'); ?></p>
                </div>
                <span class="glyphicon glyphicon-refresh box-icon"></span>
            </div>
        </div>
        <div class="col-lg-4">
            <!-- small box -->
            <div class="small-box">
                <div class="inner">
                    <h3>
                        <?php echo $paid_vacation; ?>
                        <small><?php echo __('confirm.day'); ?></small>
                    </h3>
                    <p><?php echo __('common.vacation_not_confirmed'); ?></p>
                </div>
                <span class="glyphicon glyphicon-adjust box-icon"></span>
            </div>
        </div>
        <div class="col-lg-4">
            <!-- small box -->
            <div class="small-box">
                <div class="inner">
                    <h3>
                        <?php echo $over_time; ?>
                        <small><?php echo __('confirm.hours'); ?></small>
                    </h3>
                    <p><?php echo __('common.overtime_not_confirmed'); ?></p>
                </div>
                <span class="glyphicon glyphicon-time box-icon"></span>
            </div>
        </div>
    </div>
</div>