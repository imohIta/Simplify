<?php
  global $registry;
  $baseUri = $registry->get('config')->get('baseUri');
  ?>
    <section class="content">

        <header class="top-head container-fluid">
            <button type="button" class="navbar-toggle pull-left">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <form role="search" class="navbar-left app-search pull-left hidden-xs">
              <input type="text" placeholder="Enter keywords..." class="form-control form-control-circle">
         	</form>

            <nav class=" navbar-default hidden-xs" role="navigation">
                <ul class="nav navbar-nav">
                <li class="dropdown">
                  <a data-toggle="dropdown" class="dropdown-toggle" href="#">Quick Actions <span class="caret"></span></a>
                  <ul role="menu" class="dropdown-menu">
                    <li><a href="#">Manage Account</a></li>
                    <li class="divider"></li>
                    <li><a href="<?php echo $registry->get('config')->get('baseUri'); ?>/logout">Logout</a></li>

                  </ul>
                </li>
              </ul>
            </nav>

            <ul class="nav-toolbar">

                <li class="dropdown"><a href="#" title="Notifications" data-toggle="dropdown"><i class="fa fa-bell-o"></i><span class="badge">3</span></a>
                	<div class="dropdown-menu arrow pull-right md panel panel-default arrow-top-right notifications">
                        <div class="panel-heading">
                      	Notifications
                        </div>

                        <div class="list-group">

                            <!-- <a href="#" class="list-group-item">
                            <div class="media">
                              <div class="user-status busy pull-left">
                              <img class="media-object img-circle pull-left" src="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/images/avtar/user2.png" alt="user#1" width="40">
                              </div>
                              <div class="media-body">
                                <h5 class="media-heading">Lorem ipsum dolor sit consect....</h5>
                                <small class="text-muted">23 Sec ago</small>
                              </div>
                            </div>
                            </a>
                            <a href="#" class="list-group-item">
                            <div class="media">
                              <div class="user-status offline pull-left">
                              <img class="media-object img-circle pull-left" src="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/images/avtar/user3.png" alt="user#1" width="40">
                              </div>
                              <div class="media-body">
                                <h5 class="media-heading">Nunc elementum, enim vitae</h5>
                                <small class="text-muted">23 Sec ago</small>
                              </div>
                            </div>
                            </a>
                            <a href="#" class="list-group-item">
                            <div class="media">
                              <div class="user-status invisibled pull-left">
                              <img class="media-object img-circle pull-left" src="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/images/avtar/user4.png" alt="user#1" width="40">
                              </div>
                              <div class="media-body">
                                <h5 class="media-heading">Praesent lacinia, arcu eget</h5>
                                <small class="text-muted">23 Sec ago</small>
                              </div>
                            </div>
                            </a>
                            <a href="#" class="list-group-item">
                            <div class="media">
                              <div class="user-status online pull-left">
                              <img class="media-object img-circle pull-left" src="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/images/avtar/user5.png" alt="user#1" width="40">
                              </div>
                              <div class="media-body">
                                <h5 class="media-heading">In mollis blandit tempor.</h5>
                                <small class="text-muted">23 Sec ago</small>
                              </div>
                            </div>
                            </a> -->

                            <?php
                            $thisUser = unserialize($registry->get('session')->read('thisUser'));
                            $nots = $thisUser->fetchNotifications(3);
                            if(is_null($nots) || count($nots) < 1){ ?>

                                <p style="padding:10px">No Notification Found</p>

                            <?php }else{  ?>

                          <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" >
                            <thead>
                                <tr>

                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Details</th>

                                </tr>
                            </thead>
                            <tbody>
                              <?php
                              $count = 1;
                              foreach ($nots as $row) {
                                $staff = new Staff($row->staffId);
                              ?>
                                 <tr>

                                    <td><?php echo dateToString($row->date); ?></td>
                                    <td><?php echo timeToString($row->time); ?></td>
                                    <td><?php echo $staff->name . ' ' . $row->details; ?></td>


                                </tr>
                                <?php } ?>
                              </tbody>
                            </table>


                            <?php
                                }
                            ?>

                            <a href="<?php echo $baseUri; ?>/notifications/" class="btn btn-info btn-flat btn-block">View All Notifications</a>

                        </div>

                    </div>
                </li>

            </ul>
        </header>
        <!-- Header Ends -->
