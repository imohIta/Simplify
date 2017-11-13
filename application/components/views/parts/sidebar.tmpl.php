<?php
$thisUser = unserialize($registry->get('session')->read('thisUser'));
$baseUri = $registry->get('config')->get('baseUri');
$names = explode($thisUser->name, ' ');
$name = $names[0]; 
?>
<aside class="left-panel">
    		 
            <div class="user text-center">
                  <img src="<?php echo $baseUri; ?>/assets/images/avtar/user.png" class="img-circle" alt="...">
                  <h4 class="user-name"><?php echo $name; ?></h4>
                  
                  <div class="dropdown user-login">
                  <button class="btn btn-xs dropdown-toggle btn-rounded" type="button" data-toggle="dropdown" aria-expanded="true" >
                     <?php echo $thisUser->role; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-size:16px">+</span>
                  </button>
                  <?php if($registry->get('authenticator')->checkPrivilege($thisUser->privilege, array(1,4,10)) ){ ?>
                  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                    <?php if($thisUser->privilege == 1){ ?>
                        <li role="presentation"><a role="menuitem" href="<?php echo $baseUri; ?>/account/changePrivilege?id=1"> Admin</a></li>
                        <li role="presentation"><a role="menuitem" href="<?php echo $baseUri; ?>/account/changePrivilege?id=2"> Manager</a></li>
                        <li role="presentation"><a role="menuitem" href="<?php echo $baseUri; ?>/account/changePrivilege?id=3"> Auditor</a></li>
                    <?php } ?>

                    <?php if($thisUser->privilege == 1 || $thisUser->privilege == 4){ ?>
                        <li role="presentation"><a role="menuitem" href="<?php echo $baseUri; ?>/account/changePrivilege?id=4"> Duty Manager</a></li>
                    <?php } ?>

                    <?php if($thisUser->privilege == 1){ ?>
                        <li role="presentation"><a role="menuitem" href="<?php echo $baseUri; ?>/account/changePrivilege?id=5"> Accountant</a></li>
                        <li role="presentation"><a role="menuitem" href="<?php echo $baseUri; ?>/account/changePrivilege?id=6"> Cashier</a></li>
                        <li role="presentation"><a role="menuitem" href="<?php echo $baseUri; ?>/account/changePrivilege?id=7"> Reception</a></li>
                        <li role="presentation"><a role="menuitem" href="<?php echo $baseUri; ?>/account/changePrivilege?id=8"> Pool bar</a></li>
                        <li role="presentation"><a role="menuitem" href="<?php echo $baseUri; ?>/account/changePrivilege?id=9"> Main Bar</a></li>

                    <?php } ?>

                    <?php if($thisUser->privilege == 1 || $thisUser->privilege == 10){ ?>
                        <li role="presentation"><a role="menuitem" href="<?php echo $baseUri; ?>/account/changePrivilege?id=10"> Resturant</a></li>
                        <li role="presentation"><a role="menuitem" href="<?php echo $baseUri; ?>/account/changePrivilege?id=11"> Rest. Drinks</a></li>
                    <?php } ?>
                   
                    <?php if($thisUser->privilege == 1 || $thisUser->privilege == 4){ ?>
                        <li role="presentation"><a role="menuitem" href="<?php echo $baseUri; ?>/account/changePrivilege?id=12"> Kitchen</a></li>
                    <?php } ?>

                    <?php if($thisUser->privilege == 1){ ?>
                        <li role="presentation"><a role="menuitem" href="<?php echo $baseUri; ?>/account/changePrivilege?id=13"> Store</a></li>
                         <li role="presentation"><a role="menuitem" href="<?php echo $baseUri; ?>/account/changePrivilege?id=14"> Purchaser</a></li>
                    <?php } ?>

                     <?php if($thisUser->privilege == 1 || $thisUser->privilege == 4){ ?>
                         <li role="presentation"><a role="menuitem" href="<?php echo $baseUri; ?>/account/changePrivilege?id=15"> House Kping</a></li>
                    <?php } ?>

                    
                  </ul>
                  <?php } ?>
                  </div>	 
            </div>
        
            
          
            <nav class="navigation">
            	<ul class="list-unstyled">

                <li class="active"><a href="<?php echo $baseUri; ?>/dashboard" title="DashBoard"><i class="fa fa-home"></i><span class="nav-label">Dashboard</span></a></li>
            
                <?php
                switch ($thisUser->get('activeAcct')) {
                    case 7:
                        # Reception
                ?>

                	
                    <li class="has-submenu"><a href="#"><i class="fa fa-gear"></i> <span class="nav-label">Operations</span></a>
                    	<ul class="list-unstyled">
                        	<li><a href="<?php echo $baseUri; ?>/guest/checkInOptions">CheckIn</a></li>
                            <li><a href="<?php echo $baseUri; ?>/guest/checkOut">CheckOut</a></li>
                        </ul>
                    </li>
                    <li class="has-submenu"><a href="#"><i class="fa fa-group"></i> <span class="nav-label">Guest</span></a>
                    	<ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/guest/">View All</a></li>
                        	<li><a href="<?php echo $baseUri; ?>/guest/transactions">Transactions</a></li>
                            <li><a href="<?php echo $baseUri; ?>/guest/changeRoom">ChangeRoom</a></li>
                            <li><a href="<?php echo $baseUri; ?>/guest/manage">Manage</a></li>
                            <li><a href="<?php echo $baseUri; ?>/previousGuest/">Previous Guest</a></li>
                            <li><a href="<?php echo $baseUri; ?>/guest/special">Special Guest</a></li>
                            
                        </ul>
                    </li>
                    <li class="has-submenu"><a href="#"><i class="fa fa-folder"></i> <span class="nav-label">Reservations</span></a>
                    	<ul class="list-unstyled">
                        	<li><a href="<?php echo $baseUri; ?>/reservation/">New</a></li>
                            <li><a href="<?php echo $baseUri; ?>/reservation/viewOptions">View All</a></li>
                        </ul>
                    </li>
                    <li class="has-submenu"><a href="#"><i class="fa fa-fax"></i> <span class="nav-label">Transactions</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/transaction/log">Log</a></li>
                            <li><a href="<?php echo $baseUri; ?>/transaction/reverseAppli">Transaction Reversal</a></li>
                        </ul>
                    </li>
                    <li class="has-submenu"><a href="#"><i class="fa fa-file-text-o"></i> <span class="nav-label">Reports</span></a>
                    	<ul class="list-unstyled">
                        	
                            <li><a href="<?php echo $baseUri; ?>/report/roomStatus">Room Status</a></li>
                            <li><a href="<?php echo $baseUri; ?>/report/police">Police Report</a></li>
                        </ul>
                    </li>

                    <li class="has-submenu"><a href="#"><i class="fa fa-laptop"></i> <span class="nav-label">Others</span></a>
                        <ul class="list-unstyled">
                            
                            <!-- <li><a href="<?php echo $baseUri; ?>/reception/chairmanExpensesOptions">Chairman Expenses</a></li> -->
                            <li><a href="<?php echo $baseUri; ?>/reception/manageBadRooms">Manage Bad Rooms</a></li>
                        </ul>
                    </li>
                    <li><a href="<?php echo $baseUri; ?>/logout" title="Logout"><i class="fa fa-power-off"></i><span class="nav-label">Logout</span></a></li>
                    
                
                <?php
                break;

                case 8: case 9: case 10: case 11:
                ?>
                <li class="has-submenu"><a href="#"><i class="fa fa-gear"></i> <span class="nav-label">Sales</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/sales/">New</a></li>
                            <li><a href="<?php echo $baseUri; ?>/sales/unposted">Incomplete</a></li>
                        </ul>
                    </li>
                <li class="has-submenu"><a href="#"><i class="fa fa-folder"></i> <span class="nav-label">Credits</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/credits/">Credits Log</a></li>
                            <li><a href="<?php echo $baseUri; ?>/credits/paymentsLog">Credit Payments Log</a></li>
                            <li><a href="<?php echo $baseUri; ?>/credits/makePayment">Make Payment</a></li>
                        </ul>
                    </li>
                <li class="has-submenu"><a href="#"><i class="fa fa-exchange"></i> <span class="nav-label">Stock</span></a>
                        <ul class="list-unstyled">
                            <?php if($thisUser->get('activeAcct') == 10 || $thisUser->get('activeAcct') == 11){ ?>
                                <li><a href="<?php echo $baseUri; ?>/stock/resturantOptions">View</a></li>
                            <?php }else{ ?>
                                <li><a href="<?php echo $baseUri; ?>/stock/">View</a></li>
                            <?php } ?>
                            <li><a href="<?php echo $baseUri; ?>/stock/removeItem">Remove Item</a></li>
                            <li><a href="<?php echo $baseUri; ?>/stock/posOpeningStock">View Opening Stock</a></li>
                        </ul>
                    </li>

                <li class="has-submenu"><a href="#"><i class="fa fa-fax"></i> <span class="nav-label">Transactions</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/transaction/log">Log</a></li>
                            <li><a href="<?php echo $baseUri; ?>/transaction/reverseAppli">Transaction Reversal</a></li>
                        </ul>
                </li>

                <li><a href="<?php echo $baseUri; ?>/requisition/apply" ><i class="fa fa-plug"></i><span class="nav-label">Requisition</span></a></li>

                <li><a href="<?php echo $baseUri; ?>/stock/deploymentOpeningStk" ><i class="fa fa-archive"></i><span class="nav-label">Enter Opening Stock</span></a></li>

                <li><a href="<?php echo $baseUri; ?>/logout/posOptions" title="Logout"><i class="fa fa-power-off"></i><span class="nav-label">Logout</span></a></li>
                <?php
                break;

                case 13: # Store

                ?>
                <li class="has-submenu"><a href="#"><i class="fa fa-exchange"></i> <span class="nav-label">Stock</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/stock/">View</a></li>
                            <li><a href="<?php echo $baseUri; ?>/stock/removeItem">Remove Item</a></li>
                            <li><a href="<?php echo $baseUri; ?>/stock/reducedItems">Reduced Items</a></li>
                        </ul>
                </li>

                <li class="has-submenu"><a href="#"><i class="fa fa-plug"></i> <span class="nav-label">Requisitions</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/requisition/issued">Issued</a></li>
                            <li><a href="<?php echo $baseUri; ?>/requisition/unissued">Un-Issued</a></li>
                        </ul>
                </li>
				<li><a href="<?php echo $baseUri; ?>/stock/deploymentOpeningStk" ><i class="fa fa-archive"></i><span class="nav-label">Enter Opening Stock</span></a></li>

                <li><a href="<?php echo $baseUri; ?>/logout" title="Logout"><i class="fa fa-power-off"></i><span class="nav-label">Logout</span></a></li>
                    


                <?php
                break;

                case 5: # Acountant
                ?>
                <li><a href="<?php echo $baseUri; ?>/transaction/mgtOptions" title="Transaction"><i class="fa fa-bug"></i><span class="nav-label">Transactions</span></a></li>
                <li class="has-submenu"><a href="#"><i class="fa fa-exchange"></i> <span class="nav-label">Impress</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/impress/payIn">Pay In</a></li>
                            <li><a href="<?php echo $baseUri; ?>/impress/addExpenses">Add Expenses</a></li>
                            <li><a href="<?php echo $baseUri; ?>/impress/">Expenses Log</a></li>
                            <li><a href="<?php echo $baseUri; ?>/impress/addNewCategory">Add New Category</a></li>
                            <li><a href="<?php echo $baseUri; ?>/auditor/impressAcctMgt">Impress Account Mgt.</a></li>
                        </ul>
                </li>
                <li><a href="<?php echo $baseUri; ?>/accountant/ledger" title="Ledger"><i class="fa fa-adjust"></i><span class="nav-label">Legder</span></a></li>
                <li class="has-submenu"><a href="#"><i class="fa fa-cube"></i> <span class="nav-label">Guest</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/guest/">Guest Log</a></li>
                            <li><a href="<?php echo $baseUri; ?>/previousGuest/">Previous Guest</a></li>
                            <li><a href="<?php echo $baseUri; ?>/guest/transactions">Guest Transactions</a></li>
                            <li><a href="<?php echo $baseUri; ?>/cashier/guestChart">Guest Chart</a></li>
                            <li><a href="<?php echo $baseUri; ?>/guest/guestBalances">Guest Balances</a></li>
                        </ul>
                </li>
                <li class="has-submenu"><a href="#"><i class="fa fa-exchange"></i> <span class="nav-label">Staff</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/credits/staffCreditsLog">Credits Log</a></li>
                            <li><a href="<?php echo $baseUri; ?>/credits/viewShortages">Shortages</a></li>
                            <li><a href="<?php echo $baseUri; ?>/credits/subchargesOption">Subcharges</a></li>
                        </ul>
                </li>
				<li class="has-submenu"><a href="#"><i class="fa fa-cab"></i> <span class="nav-label">Stock</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/stock/mgtOptions">Stock List</a></li>
                            <li><a href="<?php echo $baseUri; ?>/stock/removals">Removals</a></li>
                            <li><a href="<?php echo $baseUri; ?>/stock/posDailyClosingStock">POS Daily Closing Stock</a></li>
							<li><a href="<?php echo $baseUri; ?>/stock/addItemToDept">Add New Item to Dept Stock</a></li>
                        </ul>
                </li>
				
				<li><a href="<?php echo $baseUri; ?>/requisition/issued"><i class="fa fa-bus"></i><span class="nav-label">Requisitions</span></a></li>

               
                
				
				<li class="has-submenu"><a href="#"><i class="fa fa-calculator"></i> <span class="nav-label">Menu / Items</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/item/addNewOptions">Add New</a></li>
                            <li><a href="<?php echo $baseUri; ?>/item/editOptions">Edit</a></li>
							<li><a href="<?php echo $baseUri; ?>/stock/addItemToDept">Add New Item to Dept Stock</a></li>
                        </ul>
                </li>
				 
				 
                <li class="has-submenu"><a href="#"><i class="fa fa-cab"></i> <span class="nav-label">Others</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/reception/chairmanExpensesOptions">Chairman Expenses</a></li>
                            <li><a href="<?php echo $baseUri; ?>/cashier/viewBankDeposits">Bank Deposits</a></li>
                            <li><a href="<?php echo $baseUri; ?>/report/roomStatus">Room Status</a></li>
                            <li><a href="<?php echo $baseUri; ?>/accountant/stockReview">Stock Review</a></li>

                        </ul>
                </li>
                <li><a href="<?php echo $baseUri; ?>/logout" title="Logout"><i class="fa fa-power-off"></i><span class="nav-label">Logout</span></a></li>


                <?php

                break;

                case 4: # Duty manager
                ?>

                <li class="has-submenu"><a href="#"><i class="fa fa-bug"></i> <span class="nav-label">Transactions</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/transaction/mgtOptions">Log</a></li>
                            <li><a href="<?php echo $baseUri; ?>/transaction/reversals">Reversals</a></li>
                        </ul>
                </li>

                <li class="has-submenu"><a href="#"><i class="fa fa-cab"></i> <span class="nav-label">Stock</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/stock/mgtOptions">Stock List</a></li>
                            <li><a href="<?php echo $baseUri; ?>/stock/posDailyClosingStock">POS Daily Closing Stock</a></li>
                            <li><a href="<?php echo $baseUri; ?>/stock/addItemToDept">Add New Item to Dept Stock</a></li>
                            <!-- <li><a href="<?php echo $baseUri; ?>/stock/removeItemFromToDept">Remove Item From Dept Stock</a></li> -->
                        </ul>
                </li>

                <li class="has-submenu"><a href="#"><i class="fa fa-bomb"></i> <span class="nav-label">Approvals</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/stock/approvePurchase">Stock Purchase</a></li>
                        </ul>
                </li>

                <li class="has-submenu"><a href="#"><i class="fa fa-cogs"></i> <span class="nav-label">Rooms</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/report/roomList">Print Room List</a></li>
                            <li><a href="<?php echo $baseUri; ?>/report/roomStatus">Room Status</a></li>
                        </ul>
                </li>

                <li class="has-submenu"><a href="#"><i class="fa fa-calculator"></i> <span class="nav-label">Menu / Items</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/item/addNewOptions">Add New</a></li>
                            <li><a href="<?php echo $baseUri; ?>/item/editOptions">Edit</a></li>
                        </ul>
                </li>

                <li><a href="<?php echo $baseUri; ?>/logout" title="Logout"><i class="fa fa-power-off"></i><span class="nav-label">Logout</span></a></li>

                <?php 
                break;

                case 12: case 15: # kitchen Housekeeping
                ?>
                <li class="has-submenu"><a href="#"><i class="fa fa-exchange"></i> <span class="nav-label">Stock</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/stock/">View</a></li>
                            <?php if($thisUser->get('activeAcct') == 12) { ?>
                                <li><a href="<?php echo $baseUri; ?>/stock/addKitchenItem">Add Item ( Soup )</a></li>
                                <li><a href="<?php echo $baseUri; ?>/stock/issueKitchenItem">Issue Item</a></li>
                            <?php } ?>
                            <li><a href="<?php echo $baseUri; ?>/stock/removeItem">Remove Item</a></li>
                        </ul>
                </li>
                <li><a href="<?php echo $baseUri; ?>/requisition/apply" title="Logout"><i class="fa fa-plug"></i><span class="nav-label">Requisition</span></a></li>

                    <li><a href="<?php echo $baseUri; ?>/stock/deploymentOpeningStk" ><i class="fa fa-archive"></i><span class="nav-label">Enter Opening Stock</span></a></li>
                
                <li><a href="<?php echo $baseUri; ?>/logout" title="Logout"><i class="fa fa-power-off"></i><span class="nav-label">Logout</span></a></li>
                 


                <?php
                break;


                case 14: # Purchaser
                ?>
                <li class="has-submenu"><a href="#"><i class="fa fa-exchange"></i> <span class="nav-label">Purchases</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/stock/addToStore">Add to Store</a></li>
                            <li><a href="<?php echo $baseUri; ?>/stock/recentStoreAdditions">Recent Store Additions</a></li>
                            <li><a href="<?php echo $baseUri; ?>/stock/rejectedStoreAdditions">Rejected Store Additions</a></li>
                </ul>
                </li>
                
                <li><a href="<?php echo $baseUri; ?>/stock/conversionRates" title="Conversion Rates"><i class="fa fa-plug"></i><span class="nav-label">Conversion Rates</span></a></li>
                <li><a href="<?php echo $baseUri; ?>/logout" title="Logout"><i class="fa fa-power-off"></i><span class="nav-label">Logout</span></a></li>
                 

                <?php
                break;

                    case 6 : # cashier

                ?>
                <li><a href="<?php echo $baseUri; ?>/cashier/cashBook" title="Cash Book"><i class="fa fa-credit-card"></i><span class="nav-label">Cash Book</span></a></li>
                <li class="has-submenu"><a href="#"><i class="fa fa-code"></i> <span class="nav-label">Cash Returns</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/cashier/collectReturns">Collect</a></li>
                            <li><a href="<?php echo $baseUri; ?>/cashier/asAtOptions">As At</a></li>
                        </ul>
                </li>
                <li class="has-submenu"><a href="#"><i class="fa fa-bug"></i> <span class="nav-label">Transactions</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/transaction/mgtOptions">Log</a></li>
                            <li><a href="<?php echo $baseUri; ?>/transaction/reverseAppli">Reversals</a></li>
                        </ul>
                </li>
				
				

                <li class="has-submenu"><a href="#"><i class="fa fa-cogs"></i> <span class="nav-label">Analysis Charts</span></a>
                        <ul class="list-unstyled">
                            
                            <li><a href="<?php echo $baseUri; ?>/report/roomStatus">Room Status</a></li>
                        </ul>
                </li>
				<li class="has-submenu"><a href="#"><i class="fa fa-cube"></i> <span class="nav-label">Guest</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/guest/">Guest Log</a></li>
							<li><a href="<?php echo $baseUri; ?>/cashier/guestChart">Guest Chart</a></li>
							<li><a href="<?php echo $baseUri; ?>/guest/transactions">Guest Transactions</a></li>
                            <li><a href="<?php echo $baseUri; ?>/previousGuest/">Previous Guest</a></li>
                        </ul>
                </li>
				
                <li class="has-submenu"><a href="#"><i class="fa fa-beer"></i> <span class="nav-label">Bank Deposits</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/cashier/newBankDeposit">New</a></li>
                            <li><a href="<?php echo $baseUri; ?>/cashier/viewBankDeposits">View All</a></li>
                        </ul>
                </li>

                <li class="has-submenu"><a href="#"><i class="fa fa-cube"></i> <span class="nav-label">Others</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/stock/posDailyClosingStock">POS Daily Closing Stock</a></li>
                            <li><a href="<?php echo $baseUri; ?>/previousGuest/">Previous Guest</a></li>
                        </ul>
                </li>

                <li><a href="<?php echo $baseUri; ?>/logout" title="Logout"><i class="fa fa-power-off"></i><span class="nav-label">Logout</span></a></li>



                <?php
                break;

                case 2 : # Manager
                ?>
                <li class="has-submenu"><a href="#"><i class="fa fa-bug"></i> <span class="nav-label">Transactions</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/transaction/mgtOptions">Log</a></li>
                            <li><a href="<?php echo $baseUri; ?>/transaction/reversals">Reversals</a></li>
                        </ul>
                </li>

                <li class="has-submenu"><a href="#"><i class="fa fa-cab"></i> <span class="nav-label">Stock</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/stock/mgtOptions">Stock List</a></li>
                            <li><a href="<?php echo $baseUri; ?>/stock/removals">Removals</a></li>
                            <li><a href="<?php echo $baseUri; ?>/stock/posDailyClosingStock">POS Daily Closing Stock</a></li>
							<li><a href="<?php echo $baseUri; ?>/stock/addItemToDept">Add New Item to Dept Stock</a></li>
                        </ul>
                </li>
				
				<li><a href="<?php echo $baseUri; ?>/requisition/issued"><i class="fa fa-bus"></i><span class="nav-label">Requisitions</span></a></li>

                <li class="has-submenu"><a href="#"><i class="fa fa-cab"></i> <span class="nav-label">Others</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/cashier/viewBankDeposits">Bank Deposits</a></li>
                            <li><a href="<?php echo $baseUri; ?>/report/roomStatus">Room Status</a></li>
                            <li><a href="<?php echo $baseUri; ?>/reception/chairmanExpensesLog">Chairman Expenses</a></li>
                            <!-- <li><a href="<?php echo $baseUri; ?>/">SMS Message</a></li> -->
                        </ul>
                </li>

                <li class="has-submenu"><a href="#"><i class="fa fa-group"></i> <span class="nav-label">Guest</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/guest/">View All</a></li>
                            <li><a href="<?php echo $baseUri; ?>/guest/transactions">Transactions</a></li>
                            <li><a href="<?php echo $baseUri; ?>/previousGuest/">Previous Guest</a></li>
                            <li><a href="<?php echo $baseUri; ?>/guest/autobillExemptions">AutoBill Exemptions</a></li>
                        </ul>
                </li>

                <li><a href="<?php echo $baseUri; ?>/impress/"><i class="fa fa-compass"></i><span class="nav-label">Impress</span></a></li>

                

                <li class="has-submenu"><a href="#"><i class="fa fa-calculator"></i> <span class="nav-label">Menu / Items</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/item/addNewOptions">Add New</a></li>
                            <li><a href="<?php echo $baseUri; ?>/item/editOptions">Edit</a></li>
							<li><a href="<?php echo $baseUri; ?>/stock/addItemToDept">Add New Item to Dept Stock</a></li>
                        </ul>
                </li>

                 <li class="has-submenu"><a href="#"><i class="fa fa-cube"></i> <span class="nav-label">Staff</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/account/addNew">Add New</a></li>
                            <li><a href="<?php echo $baseUri; ?>/account/viewAll">View All</a></li>
                        </ul>
                </li>


                <li><a href="<?php echo $baseUri; ?>/logout" title="Logout"><i class="fa fa-power-off"></i><span class="nav-label">Logout</span></a></li>

                <?php
                break;

                case 3: # Auditor
                ?>

                <li class="has-submenu"><a href="#"><i class="fa fa-bug"></i> <span class="nav-label">Transactions</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/transaction/mgtOptions">Log</a></li>
                            <li><a href="<?php echo $baseUri; ?>/transaction/reversals">Reversals</a></li>
                        </ul>
                </li>

                <li class="has-submenu"><a href="#"><i class="fa fa-cab"></i> <span class="nav-label">Stock</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/stock/mgtOptions">Stock List</a></li>
                            <li><a href="<?php echo $baseUri; ?>/stock/removals">Removals</a></li>
                            <li><a href="<?php echo $baseUri; ?>/stock/posDailyClosingStock">POS Daily Closing Stock</a></li>
                        </ul>
                </li>

                <li class="has-submenu"><a href="#"><i class="fa fa-group"></i> <span class="nav-label">Guest</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/guest/">View All</a></li>
                            <li><a href="<?php echo $baseUri; ?>/guest/transactions">Transactions</a></li>
                            <li><a href="<?php echo $baseUri; ?>/previousGuest/">Previous Guest</a></li>
                            <li><a href="<?php echo $baseUri; ?>/guest/autobillExemptions">AutoBill Exemptions</a></li>
                        </ul>
                </li>

                <li><a href="<?php echo $baseUri; ?>/impress/"><i class="fa fa-compass"></i><span class="nav-label">Impress</span></a></li>

                <li><a href="<?php echo $baseUri; ?>/requisition/issued"><i class="fa fa-bus"></i><span class="nav-label">Requisitions</span></a></li>

                <li class="has-submenu"><a href="#"><i class="fa fa-lock"></i> <span class="nav-label">Reports</span></a>
                        <ul class="list-unstyled">
                            
                            <li><a href="<?php echo $baseUri; ?>/auditor/financialReport">Financial Report</a></li>
                            <li><a href="<?php echo $baseUri; ?>/auditor/impressAcctMgt">Impress Account Mgt.</a></li>
                            <li><a href="<?php echo $baseUri; ?>/auditor/financialPosition">Financial Position</a></li>
                        </ul>
                </li>

                <li class="has-submenu"><a href="#"><i class="fa fa-cloud"></i> <span class="nav-label">Others</span></a>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo $baseUri; ?>/cashier/viewBankDeposits">Bank Deposits</a></li>
                            <li><a href="<?php echo $baseUri; ?>/report/roomStatus">Room Status</a></li>
                            <li><a href="<?php echo $baseUri; ?>/reception/chairmanExpensesLog">Chairman Expenses</a></li>
                        </ul>
                </li>


                <li><a href="<?php echo $baseUri; ?>/logout" title="Logout"><i class="fa fa-power-off"></i><span class="nav-label">Logout</span></a></li>


                <?php
                break;

                    case 1: # Admin
                ?>
                <li><a href="<?php echo $baseUri; ?>/admin/resetApp"><i class="fa fa-bus"></i><span class="nav-label">Reset App</span></a></li>

                <li><a href="<?php echo $baseUri; ?>/admin/flushTable"><i class="fa fa-compass"></i><span class="nav-label">Flush Table</span></a></li>

                <li><a href="<?php echo $baseUri; ?>/logout" title="Logout"><i class="fa fa-power-off"></i><span class="nav-label">Logout</span></a></li>
                <?php
                break;
                    
                default:
                ?>
                    <li><a href="<?php echo $baseUri; ?>/logout" title="Logout"><i class="fa fa-power-off"></i><span class="nav-label">Logout</span></a></li>
                <?php   

                    break;
                }
                ?>

                

                </ul>
            </nav>
            
    </aside>