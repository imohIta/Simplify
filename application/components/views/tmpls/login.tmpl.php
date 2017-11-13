<?php
if($registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/dashboard');
}
?>
<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from freakpixels.com/portfolio/brio/signin.html by HTTrack Website Copier/3.x [XR&CO'2010], Fri, 06 Feb 2015 14:02:50 GMT -->
<head>
  
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?php echo $registry->get('config')->get('appTitle'); ?> </title>

	<meta name="description" content="">
	<meta name="author" content="Akshay Kumar">

	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/css/bootstrap/bootstrap.css" />

    <!-- Fonts  -->
   
    
    <!-- Base Styling  -->
    <link rel="stylesheet" href="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/css/app/app.v1.css" />

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>	
    
    <br /><br />
	
    <div class="container">
    	<div class="row">
        <?php 
           if($registry->get('session')->read('formMsg')){
            echo $registry->get('session')->read('formMsg');
            $registry->get('session')->write('formMsg', NULL);
           }
        ?>

    	<div class="col-lg-4 col-lg-offset-4">
        	<h3 class="text-center">Simplify <sup><small>for hotels</small></sup></h3>
            <p class="text-center">Enter username & password to login</p>
            <hr class="clean">
        	<form role="form" method="post" action="<?php echo $registry->get('config')->get('baseUri'); ?>/login/authenticate" >
              <div class="form-group input-group">
              	<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                <input type="text" name="username" class="form-control"  placeholder="Username">
              </div>
              <div class="form-group input-group">
              	<span class="input-group-addon"><i class="fa fa-key"></i></span>
                <input type="password" name="password" class="form-control"  placeholder="Password">
              </div>
              <div class="form-group">
                <label class="cr-styled">
                    <input type="checkbox" ng-model="todo.done">
                    <i class="fa"></i> 
                </label>
                Remember me
              </div>
        	  <button type="submit" class="btn btn-purple btn-block" name="submit">Log In</button>
            </form>

            <hr>
            
            <p class="text-center text-gray">...Developed by <img src="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/images/oxygyn-logo-s.png">.</p>
            <!-- <hr>
            
            <p class="text-center text-gray">Dont have account yet!</p>
            <button type="submit" class="btn btn-default btn-block">Create Account</button> -->
        </div>
        </div>
    </div>
    
    
    
    <!-- JQuery v1.9.1 -->
	<script src="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/js/jquery/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/js/plugins/underscore/underscore-min.js"></script>
    <!-- Bootstrap -->
    <script src="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/js/bootstrap/bootstrap.min.js"></script>
    
    <!-- Globalize -->
    <script src="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/js/globalize/globalize.min.js"></script>
    
    <!-- NanoScroll -->
    <script src="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/js/plugins/nicescroll/jquery.nicescroll.min.js"></script>
    
	
    
    
    <!-- Custom JQuery -->
	<script src="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/js/app/custom.js" type="text/javascript"></script>
    

    
	<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','../../../www.google-analytics.com/analytics.js','ga');
    
    ga('create', 'UA-56821827-1', 'auto');
    ga('send', 'pageview');
    
    </script>
</body>

<!-- Mirrored from freakpixels.com/portfolio/brio/signin.html by HTTrack Website Copier/3.x [XR&CO'2010], Fri, 06 Feb 2015 14:02:50 GMT -->
</html>
