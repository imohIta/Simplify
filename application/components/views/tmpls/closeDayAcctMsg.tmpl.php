<?php 
global $registry;
$baseUri =  $registry->get('config')->get('baseUri');

$session = $registry->get('session');


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
                <h1 class="text-center" style="font-size:80px; font-weight:500;">WAIT !</h1>
                <p class="text-center lead">Is Your Shift Over Yet?</p>
                <hr class="">
            </div>
        </div>
        
       
        
        <div class="row">
            <div class="col-lg-6 col-lg-offset-3 text-center ">
                <hr class="">
                <p class="text-gray">Are you handing Over to your reliever...</p>

               


                <!-- had to change this part to make sure that sales are always closed whenever a pos user checks out
                  This is to make sure that stock is always closed
                  previously, this will logout the user without closing the stock
                 -->
                <!-- <a href="<?php echo $registry->get('config')->get('baseUri'); ?>/logout" title="Logout" class="btn btn-default btn-circle" style="margin-left:10px">Not Yet</a> -->
                 <form class="form-horizontal" role="form" method="post" action="<?php echo $registry->get('config')->get('baseUri'); ?>/logout/closeDayAccount">
                  <a href="javascript:void(0);" title="Close Sales" class="btn btn-success btn-circle" data-toggle="modal" data-target="#options">Yes</a>
                  &nbsp;&nbsp;&nbsp;&nbsp;
                      <button type="submit" name="submit" class="btn btn-default btn-circle">Not Yet</button>
                 </form>


                <hr />
                <br />

                     <a href="<?php echo $registry->get('config')->get('baseUri'); ?>/dashboard" title="Logout" class="btn btn-orange btn-circle" style="margin-left:10px"><-- Back</a>
            </div>
        </div>
        
    </div>


    <div class="modal fade" id="options" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Unposted Sale</h4>
              </div>
              <div class="modal-body">
                <p>Do you really want to Close your Sales<p>
                    
                    <form class="form-horizontal" role="form" method="post" action="<?php echo $registry->get('config')->get('baseUri'); ?>/logout/closeDayAccount">

                       
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                              <button type="button"  class="btn btn-danger btn-circle" data-dismiss="modal">Not Really</button>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              <button type="submit" name="submit" class="btn btn-success btn-circle">Yea, Sure</button>
                            </div>
                          </div>
                     </form>


                  
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
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

<?php $session->write('upMsg', null); ?>
