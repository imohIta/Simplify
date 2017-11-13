<?php
    #check if user is logged in
    global $registry;
    $session = $registry->get('session');

    #if user not looged in
    if(!$registry->get('session')->read('loggedIn')){
        $registry->get('uri')->redirect();
    }

    #logged in User
    $thisUser = unserialize($registry->get('session')->read('thisUser'));

    $baseUri = $registry->get('config')->get('baseUri');
    $session = $registry->get('session');

    #if invioce not to be shown




?>

<!-- Bootstrap core CSS -->
<link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/css/bootstrap/bootstrap.css" />

<!-- Calendar Styling  -->
<!-- <link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/css/plugins/calendar/calendar.css" /> -->

<!-- Fonts  -->
<link href='http://fonts.googleapis.com/css?family=Raleway:400,500,600,700,300' rel='stylesheet' type='text/css'>

<!-- Base Styling  -->
<link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/css/app/app.v1.css" />

<!-- <meta name="viewport" content="width=device-width, initial-scale=1" /> -->

<title><?php echo $registry->get('config')->get('appTitle'); ?></title>


<div style="width:800px; margin:20px auto">

    <div class="warper container-fluid">



        <div class="page-header">
            <h3 class="no-margn" style="text-align:center">Kelvic Suites <small>& Towers.</small></h3>
            <address style="text-align:center">
                <h4>Departmental Stock Review</h4> <br>
                <h5></h5><?php echo date('F Y', strtotime($session->read('StockReviewYear') . '-' . $session->read
                    ('StockReviewMonth') . '-01')); ?></h5>
            </address>
            <hr>

        </div>





        <div class="row">

            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th colspan="2">Main Bar Water & Drinks</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php

                        ?>

                        <tr>
                            <td></td>
                            <td></td>
                        </tr>

                        </tbody>

                    </table>

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th colspan="2">Pool Bar Water & Drinks</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php

                        ?>

                        <tr>
                            <td></td>
                            <td></td>
                        </tr>

                        </tbody>

                    </table>
                </div>
            </div>


            <div id="print" class="row">
                <div class="col-lg-6"><button class="btn btn-warning" type="button" onclick="printInv('print');">Print</button></div>
                <div class="col-lg-6 text-right"><a href="<?php echo $baseUri; ?>/sales/" class="btn btn-success" title="Back to Make Sales">Back</a></div>
            </div>

        </div>

    </div>

</div>
<!-- Warper Ends Here (working area) -->


<?php
    $session->write('StockReviewYear', null);
    $session->write('StockReviewMonth', null);
?>



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

<script src="<?php echo $baseUri; ?>/assets/js/application/ctrl.js" type="text/javascript"></script>



