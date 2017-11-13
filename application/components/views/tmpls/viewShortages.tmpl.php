<?php
#check if user is logged in
    global $today;
    if(!$registry->get('session')->read('loggedIn')){
        $registry->get('uri')->redirect();
    }

# This will fetch all Staff Shortage that have not been fully paid for a particular month and year


#logged in User
    $thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | accountant )
    $registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,5,6), true);

    $baseUri = $registry->get('config')->get('baseUri');
    $session = $registry->get('session');


    $year = $session->read('staffShortageYear') ? $session->read('staffShortageYear') : date('Y');
    $month = $session->read('staffShortageMonth') ? $session->read('staffShortageMonth') : date('m');
    $staff = $session->read('staffShortageStaff') ? $session->read('staffShortageStaff') : null;

#fetch all unposted Sales
    if($session->read('staffShortageMonth')){
        $session->write('staffShortageMonth', null);
        $session->write('staffShortageYear', null);
        $session->write('staffShortageStaff', null);
    }

    $shortages = $registry->get('db')->fetchStaffShortages(array(
        'staffId' => $staff,
        'month' => $month,
        'year' => $year
    ));



    #include header
    $registry->get('includer')->render('header', array('css' => array(
        'plugins/typeahead/typeahead.css',
        'plugins/bootstrap-tagsinput/bootstrap-tagsinput.css',
        'plugins/bootstrap-chosen/chosen.css',
        'switch-buttons/switch-buttons.css',
        'plugins/datatables/jquery.dataTables.css',
        'font-awesome.min.css'
    )));

    #include Sidebar
    $registry->get('includer')->render('sidebar', array());


    #include small header
    $registry->get('includer')->renderWidget('smallHeader');


?>



    <!-- Page Body here...Editable region -->

    <div class="warper container-fluid" >

        <div class="page-header">
            <h1>Staff Shortages Log<small style="color:#FF404B">&nbsp;</small></h1>
        </div>

        <hr />

        <div class="row">

            <div class="col-md-8">



                <form method="post" action="<?php echo $baseUri; ?>/credits/viewShortages">

                    <div class="col-sm-4">
                        <select class="form-control chosen-select form-control-circle" data-placeholder="" name="staff">
                            <option value="">All</option>
                            <?php foreach (Staff::fetchAll() as $row) {
                                # code...
                                $s = new Staff($row->id);
                                $selected = ($staff == $s->id) ? 'selected' : '';
                                ?>

                                <option value="<?php echo $s->id; ?>" <?php echo $selected; ?>><?php echo $s->name; ?> ( <?php echo $s->dept; ?> )</option>
                            <?php } ?>
                        </select>
                        <span class="help-block"><small>&nbsp;&nbsp; Staff</small></span>
                    </div>

                    <div class="col-sm-2">
                        <select name="month" data-placeholder="Month" class="chosen-select">
                            <option value=""></option>
                            <?php
                                $months = array('01' => 'Jan', '02' => 'Feb', '03' => 'Mar',
                                                '04' => 'Apr', '05' => 'May', '06' => 'Jun', '07' => 'Jul',
                                                '08' => 'Aug', '09' => 'Sep' , '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
                                );
                                foreach ($months as $key => $value) {

                                    $selected = $month == $key ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>

                                <?php } ?>
                        </select>
                        <span class="help-block"><small>&nbsp;&nbsp; Month</small></span>
                    </div>

                    <div class="col-sm-2">
                        <select name="year" data-placeholder="Month" class="chosen-select">
                            <option value=""></option>
                            <?php

                                for ($i = '2015'; $i <= date('Y'); $i++ ) {
                                    # code...
                                    $selected = ($year == $i) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>

                                <?php } ?>
                        </select>
                        <span class="help-block"><small>&nbsp;&nbsp; Year</small></span>
                    </div>

                    <div class="col-sm-2">
                        <button type="submit" name="search" class="btn btn-warning btn-circle" >Search</button>
                        <span class="help-block"><small>&nbsp;</small></span>
                    </div>
                </form>

                <br style="clear:both" /><br />

                <div id="h" >

                    <!-- Write here -->

                    <div class="panel panel-default">
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body">

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                                <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Date</th>
                                    <th>Staff Name</th>
                                    <th>Staff Department</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $totalShortages = 0;
                                    //if(count($Shortages) > 0){
                                        $count = 1;
                                        foreach ($shortages as $row) {
                                            #fetch total payments for this shortage
                                            $payments = $registry->get('db')->fetchTotalStaffShortagesPayByTransId
                                            ($row->transId);

                                            $bal = $row->amt - $payments;


                                            #check if this user has not completely paid of the shortage
                                            if($bal > 0){
                                                $totalShortages += $bal;

                                                # code...
                                                $class = ( $count % 2 == 0 ) ? 'even' : 'odd';
                                                $staff = new Staff($row->staffId);

                                                ?>
                                                <tr class="<?php echo $class; ?> gradeX">
                                                    <td><?php echo $count; ?></td>
                                                    <td><?php echo dateToString($row->date); ?></td>
                                                    <td><?php echo $staff->name; ?></td>
                                                    <td><?php echo $staff->dept; ?></td>
                                                    <td><?php echo number_format($bal); ?></td>
                                                </tr>
                                                <?php
                                                $count++;
                                           }
                                        }

                                        if($totalShortages != 0){
                                            ?>

                                            <tr class="even gradeX">
                                                <td colspan="4">Total Shortage Amount</td>
                                                <td><?php echo number_format($totalShortages); ?></td>
                                            </tr>


                                        <?php
                                            }
                                            // }
                                         ?>

                                </tbody>
                            </table>


                        </div>
                    </div>
                </div>

            </div>


        </div>

    </div>
    <!-- Warper Ends Here (working area) -->




<?php
    $registry->get('includer')->render('footer', array('js' => array(
        'plugins/nicescroll/jquery.nicescroll.min.js',
        'plugins/typehead/typeahead.bundle.js',
        'plugins/typehead/typeahead.bundle-conf.js',
        'plugins/inputmask/jquery.inputmask.bundle.js',
        'plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js',
        'plugins/bootstrap-chosen/chosen.jquery.js',
        'moment/moment.js',
        'plugins/datatables/jquery.dataTables.js',
        'plugins/datatables/DT_bootstrap.js',
        'plugins/datatables/jquery.dataTables-conf.js',
        'app/custom.js',
        'application/ctrl.js'
    )));

    //'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js'
?>