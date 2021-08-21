<!DOCTYPE html>
<html lang="jp">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../favicon.ico">

        <title><?php echo isset($title)?$title.' | ' : ''; ?> <?php echo __('title.primary_title'); ?></title>

        <!-- CSS -->
        <?php echo Asset::render('css'); ?>

    </head>

    <body>
        <div class="back back-color-<?php if (Request::active()->controller == 'Controller_User') {
            echo '2';
        } else {
            echo '3';
        } ?>">
            <?php if (Session::get_flash('success')): ?>
                <div class="message-area">
                    <div class="alert alert-danger container" role="alert"><?php echo Session::get_flash('success') ?></div>
                </div>
            <?php endif; ?>
            <?php if (Session::get_flash('error')): ?>
                <div class="message-area">
                    <div class="alert alert-danger container" role="alert"><?php echo Session::get_flash('error') ?></div>
                </div>
                <?php endif; ?>
        </div>

        <div class="container">
            <div class="contents">
                <?php echo $content; ?>
            </div> <!-- /contents -->
        </div> <!-- /container -->


        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
<?php echo Asset::render('js'); ?>
    </body>
</html>
