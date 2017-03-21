<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html lang='en' class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php if(isset($pageTitle))echo $pageTitle; ?></title>
        <meta name="description" content="">
        <!--<meta name="viewport" content="width=device-width">-->
        <meta name="viewport" content="width=device-width,user-scalable=no">
        <link rel="stylesheet" href="<?php echo base_url();?>assets/css/bootstrap.min.css">
        <?php if(ENVIRONMENT == 'development') : ?>
            <link rel="stylesheet" href="<?php echo base_url();?>assets/css/bootswatch.cerulean.min.css">
        <?php else : ?>
            <link rel="stylesheet" href="<?php echo base_url();?>assets/css/bootswatch.united.min.css">
        <?php endif ; ?>
        <!--used to override bootstrap/bootswatch's css elements so touching bootstrap files isn't necessary-->
        <link rel="stylesheet" href="<?php echo base_url();?>assets/css/bs.override.css">
        <!--<link rel="stylesheet" href="<?php echo base_url();?>assets/css/bootstrapValidator.min.css">-->
        <link rel="stylesheet" href="//cdn.jsdelivr.net/jquery.bootstrapvalidator/0.5.3/css/bootstrapValidator.min.css"/>
        <!--my custom css addtions not in bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url();?>assets/css/custom.css"> 
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
        <script src="<?php echo base_url();?>assets/js/jquery.ui.touch-punch.min.js"></script>
        <link type="text/css" href="<?php echo base_url();?>assets/css/jquery.signature.css" rel="stylesheet"> 
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.signature.js"></script>
        <script src="//cdn.jsdelivr.net/jquery.bootstrapvalidator/0.5.3/js/bootstrapValidator.min.js"></script>
        <script src="<?php echo base_url();?>assets/js/random-stuff.js"></script>
        <script src="<?php echo base_url();?>assets/js/bootstrap.min.js"></script>
    </head>
    <body>
