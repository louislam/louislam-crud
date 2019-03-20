<?php

use LouisLam\CRUD\Middleware\CSRFGuard;
use LouisLam\Util;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.4 -->
    <link href="<?= Util::res("vendor/almasaeed2010/adminlte/bootstrap/css/bootstrap.min.css") ?>" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="<?= Util::res("vendor/fortawesome/font-awesome/css/font-awesome.min.css") ?>" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="<?= Util::res("vendor/almasaeed2010/adminlte/dist/css/AdminLTE.min.css") ?>" rel="stylesheet" type="text/css"/>

</head>
<body class="login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b></b> System</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">

        <?php if (isset($_SESSION['msg'])) : ?>
            <p class="login-box-msg" style="color: red"><?= $_SESSION['msg'] ?></p>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>


        <form action="<?=Util::url("auth/login") ?>" method="post">
            <?=CSRFGuard::inputTag() ?>
            
            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="Username" name="username"/>
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Password" name="password"/>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">

                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
                <!-- /.col -->
            </div>
        </form>

    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.1.4 -->
<script src="<?= Util::res("vendor/components/jquery/jquery.min.js") ?>" type="text/javascript"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="<?= Util::res("vendor/almasaeed2010/adminlte/bootstrap/js/bootstrap.min.js") ?>" type="text/javascript"></script>

<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>
