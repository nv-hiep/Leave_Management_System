<div class="contents">
    <div class="row">
        <h3 class="page-header">CSVファイル</h3>
    </div>
    <div id="master">
        <?php echo Form::open(array("class"=>"form-horizontal", 'enctype' => 'multipart/form-data')); ?>
            <div class="form-group">
                <?php echo Form::file('csv', array('class' => 'form-control')); ?>
                <p class="red_font"><?php echo isset($err) ? $err : ''; ?></p>
            </div>

            <div class="form-group">
                <?php echo Html::anchor('account', __('common.cancel'), array('class' => 'btn btn-danger')); ?>
                <?php echo Form::button('submit', 'Upload', array('class' => 'btn btn-primary', 'value' => 'upload')); ?>
                <?php echo Form::button('gallery', 'Gallery', array('class' => 'btn btn-success', 'value' => 'gallery')); ?>
            </div>
        <?php echo Form::close(); ?>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Image(<?php echo count($imgs); ?>)</th>
                        <th>Path/name</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($imgs) == 0): ?>
                    <tr><td>No file found</td></tr>
                    <?php endif;?>

                    <?php foreach ($imgs as $img): ?>
                        <?php $img_info = explode('.', $img); ?>
                        <tr>
                            <?php if ($img_info[1] == 'csv') : ?>
                            <td><?php echo Html::img(Uri::base().'assets/img/app.png', array("title" => $img, "alt" => $img, 'class' => "", 'height' => 100, 'width' => 100)); ?></td>
                            <?php else: ?>
                            <td><?php echo Html::img(Uri::base().'files/'.$img, array("title" => $img, "alt" => $img, 'class' => "", 'height' => 100, 'width' => 100)); ?></td>
                            <?php endif; ?>
                            <td><?php echo $img; ?></td>
                            <td><a href="<?php echo Uri::base().'account/upload/'.$img_info[0].'/'.$img_info[1]; ?>" onclick="return confirm('Do you want to delete this file?')">Del</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <h4 class="page-header">CSV content</h3>
        <?php
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        ?>
    </div>
</div>