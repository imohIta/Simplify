<?php

$thisUser = unserialize($registry->get('session')->read('thisUser'));


#check privilege
$registry->get('authenticator')->checkPrivilege($thisUser->privilege, array(1,4,10), true);

$baseUri = $registry->get('config')->get('baseUri');


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
        	<h3 class="text-center">Sign in as</h3>
            <hr>
            <div style="text-align:center">
            <?php
                if($registry->get('authenticator')->checkPrivilege($thisUser->privilege, array(1))){
            ?>
            <ul class="list-unstyled list-inline showcase-btn">
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=1" class="login-option" data-id="1"><button type="button" class="btn btn-default btn-circle">Admin</button></a></li>
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=2" class="login-option" data-id="2"><button type="button" class="btn btn-gry btn-circle">Manager</button></a></li>
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=3" class="login-option" data-id="3"><button type="button" class="btn btn-success btn-circle">Auditor</button></a></li>
            </ul>

            <ul class="list-unstyled list-inline showcase-btn">
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=4" class="login-option" data-id="4"><button type="button" class="btn btn-warning btn-circle">Duty Manager</button></a></li>
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=5" class="login-option" data-id="5"><button type="button" class="btn btn-danger btn-circle">Accountant</button></a></li>
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=6" class="login-option" data-id="6"><button type="button" class="btn btn-info btn-circle">Cashier</button></a></li>
            </ul>
            
            <ul class="list-unstyled list-inline showcase-btn">
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=7" class="login-option" data-id="7"><button type="button" class="btn btn-blue btn-circle">Reception</button></a></li>
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=8" class="login-option" data-id="8"><button type="button" class="btn btn-orange btn-circle">Pool Bar</button></a></li>
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=9" class="login-option" data-id="9"><button type="button" class="btn btn-purple btn-circle">Main Bar</button></a></li>
            </ul>

            <ul class="list-unstyled list-inline showcase-btn">
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=10" class="login-option" data-id="10"><button type="button" class="btn btn-wine btn-circle">Resturant</button></a></li>
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=11" class="login-option" data-id="11"><button type="button" class="btn btn-yellow btn-circle">Resturant Drinks</button></a></li>
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=12" class="login-option" data-id="12"><button type="button" class="btn btn-test btn-circle">Kitchen</button></a></li>
            </ul>

            <ul class="list-unstyled list-inline showcase-btn">
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=13" class="login-option" data-id="13"><button type="button" class="btn btn-purple btn-circle">Store</button></a></li>
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=14" class="login-option" data-id="14"><button type="button" class="btn btn-success btn-circle">Purchaser</button></a></li>
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=15" class="login-option" data-id="15"><button type="button" class="btn btn-orange btn-circle">House keeping</button></a></li>
            </ul>

            <?php }elseif($registry->get('authenticator')->checkPrivilege($thisUser->privilege, array(10))){ ?>

            <ul class="list-unstyled list-inline showcase-btn">
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=10" class="login-option" data-id="1"><button type="button" class="btn btn-default btn-circle">Resturant</button></a></li>
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=11" class="login-option" data-id="2"><button type="button" class="btn btn-gry btn-circle">Resturant Drinks</button></a></li>
                
            </ul>

            <?php }else{ ?>
            <ul class="list-unstyled list-inline showcase-btn">
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=4" class="login-option" data-id="1"><button type="button" class="btn btn-default btn-circle">Duty Manager</button></a></li>
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=12" class="login-option" data-id="2"><button type="button" class="btn btn-gry btn-circle">Kitchen</button></a></li>
                <li><a href="<?php echo $baseUri; ?>/account/changePrivilege?id=15" class="login-option" data-id="3"><button type="button" class="btn btn-orange btn-circle">HouseKeeping</button></a></li>
            </ul>

            <?php } ?>

            </div>

            <hr>

            <a href="<?php echo $registry->get('config')->get('baseUri'); ?>/logout" title="Back to Login Page"><p class="text-center text-gray">Back</p></a>
            
            
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
