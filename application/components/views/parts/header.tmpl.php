<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from freakpixels.com/portfolio/brio/ by HTTrack Website Copier/3.x [XR&CO'2010], Fri, 06 Feb 2015 13:57:34 GMT -->
<head>
  
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?php echo $registry->get('config')->get('appTitle'); ?></title>

  <!-- Prevent Indexing frm serach engines -->
  <meta name="robots" content="noindex">

	<meta name="description" content="">
	<meta name="author" content="Akshay Kumar">

  <!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/css/bootstrap/bootstrap.css" /> 

  
	<!-- Calendar Styling  -->
  <!-- <link rel="stylesheet" href="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/css/plugins/calendar/calendar.css" /> -->
   
  <!-- Include Minified Css files --> 
  <?php echo $css; ?>

  <!-- Base Styling  -->
  <link rel="stylesheet" href="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/css/app/app.v1.css" />
  

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	
	<link rel="stylesheet" type"text/css" href="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/css/print.css" media="print">

</head>
<body data-ng-app >

<div id="uriHolder" style="display:none"><?php echo $registry->get('config')->get('baseUri'); ?></div>
	
    <!-- Preloader -->
    <div class="loading-container">
      <div class="loading">
        <div class="l1">
          <div></div>
        </div>
        <div class="l2">
          <div></div>
        </div>
        <div class="l3">
          <div></div>
        </div>
        <div class="l4">
          <div></div>
        </div>
      </div>
    </div>
    <!-- Preloader -->