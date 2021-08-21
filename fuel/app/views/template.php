<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="">
        <meta name="author" content="">

        <title><?php echo isset($title) ? $title.' | ' : ''; ?> <?php echo __('title.primary_title'); ?></title>

        <!-- CSS -->
        <?php echo Asset::render('css'); ?>

        <script>
            var base_url = '<?php echo Uri::base(); ?>';
            var language = JSON.parse('<?php echo $language; ?>');
        </script>
    </head>
    <body>
        <?php require_once 'common/header.php'; ?>
        <!--box to show error, success, warning message-->
        <?php if (Session::get_flash('success')): ?>
            <div class="alert alert-success" role="alert" style="color:green"><?php echo Security::clean(Session::get_flash('success'),array('htmlentities','xss_clean')) ?></div>
        <?php endif; ?>
        <?php if (Session::get_flash('error')): ?>
            <div class="alert alert-danger" role="alert" style="color:red"><?php echo Security::clean(Session::get_flash('error'),array('htmlentities','xss_clean')) ?></div>
        <?php endif; ?>

        <?php if (Session::get_flash('warning')): ?>
            <div class="alert alert-warning" role="alert" style="color:orange"><?php echo Security::clean(Session::get_flash('warning'),array('htmlentities','xss_clean')) ?></div>
        <?php endif; ?>
        <?php echo isset($content) ? $content : ''; ?>
        <?php require_once 'common/footer.php'; ?>
        <?php echo Asset::render('js'); ?>
    </body>
    <?php
        $cache = Session::get_flash('cache');
        if ($cache == 'del') {
            Controller_Base::delete_files(APPPATH . '/cache/auth/');
        }
    ?>
</html>