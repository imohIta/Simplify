<?php 
global $registry;
$baseUri =  $registry->get('config')->get('baseUri');
?>
<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from freakpixels.com/portfolio/brio/404.html by HTTrack Website Copier/3.x [XR&CO'2010], Fri, 06 Feb 2015 14:01:35 GMT -->
<head>
  
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?php echo $registry->get('config')->get('appTitle'); ?> </title>

	<meta name="description" content="">
	<meta name="author" content="Akshay Kumar">

	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/css/bootstrap/bootstrap.css" /> 

    <!-- Fonts  -->
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,500,600,700,300' rel='stylesheet' type='text/css'>
    
    <!-- Base Styling  -->
    <link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/css/app/app.v1.css" />

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>	
    
	
    <div class="container">

        <br /><br />
    
    	<div class="row">
            <div class="col-lg-6 col-lg-offset-3">
                <h1 class="text-center" style="font-size:80px; font-weight:500;">ERROR !</h1>
                <p class="text-center lead"><?php echo $msg; ?></p>
                <hr class="">
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-lg-offset-4">
            
            <form>
            <div class="input-group">
              <input type="search" class="form-control" placeholder="Search Here...">
              <span class="input-group-btn">
                <button type="submit" class="btn btn-purple">Search</button>
              </span>
            </div>
            </form>
              
            
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-6 col-lg-offset-3 text-center ">
            	<hr class="">
                <p class="text-gray">Do not Fret...We are here for You...Just make sure you are doing the right thing & if Error stills shows...Contact Admin</p>
                <a href="<?php echo $registry->get('config')->get('baseUri'); ?>/dashboard" title="Back To DashBoard" class="btn btn-default">Go Home</a>
            </div>
        </div>
        
    </div>
    
    
    
    <!-- JQuery v1.9.1 -->
	<script src="<?php echo $baseUri; ?>/assets/js/jquery/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="<?php echo $baseUri; ?>/assets/js/plugins/underscore/underscore-min.js"></script>
    <!-- Bootstrap -->
    <script src="<?php echo $baseUri; ?>/assets/js/bootstrap/bootstrap.min.js"></script>
    
    <!-- Globalize -->
    <script src="<?php echo $baseUri; ?>/assets/js/globalize/globalize.min.js"></script>
    
    <!-- NanoScroll -->
    <script src="<?php echo $baseUri; ?>/assets/js/plugins/nicescroll/jquery.nicescroll.min.js"></script>
    
	
    
    
    <!-- Custom JQuery -->
	<script src="<?php echo $baseUri; ?>/assets/js/app/custom.js" type="text/javascript"></script>
 
</body>

<!-- Mirrored from freakpixels.com/portfolio/brio/404.html by HTTrack Website Copier/3.x [XR&CO'2010], Fri, 06 Feb 2015 14:01:35 GMT -->
</html>
