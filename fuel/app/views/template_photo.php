<!DOCTYPE html>
<html lang="jp">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title><?php echo isset($title)?$title.' | ' : ''; ?> <?php echo __('title.primary_title'); ?></title>

        <!-- CSS -->
        <?php echo Asset::render('css'); ?>
        <script>
            var base_url = '<?php echo Uri::base(); ?>';
        </script>
    </head>

    <body>
        <?php echo isset($content) ? $content : ''; ?>
        <!-- Placed at the end of the document so the pages load faster -->
        <?php echo Asset::render('js'); ?>
    </body>
</html>
