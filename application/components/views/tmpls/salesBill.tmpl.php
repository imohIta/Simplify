<?php
    #check if user is logged in
    global $today;

    #if user not looged in
    if(!$registry->get('session')->read('loggedIn')){
        $registry->get('uri')->redirect();
    }

    #logged in User
    $thisUser = unserialize($registry->get('session')->read('thisUser'));

    $baseUri = $registry->get('config')->get('baseUri');
    $session = $registry->get('session');

    #if invioce not to be shown
    if(!$session->read('showSalesBill')){
        $registry->get('uri')->redirect($baseUri . '/guest/checkInOptions');
    }

    $data = $session->read('saleDetails' . $thisUser->id . $thisUser->get('activeAcct'));
    //var_dump($data); die;

    $url = $baseUri . '/sales/';
    if($session->read('incompleteSaleTransId_' . $thisUser->id . '_' . $thisUser->get('activeAcct'))){
        $url = $baseUri . '/sales/unposted';
        $session->write('incompleteSaleTransId_' . $thisUser->id . '_' . $thisUser->get('activeAcct'), null);
    }

?>

<style>

    #wrapper{
        width:360px;
        font-family: "Calibri";
        font-size:16pt;
        border:1px solid #fff;
        margin:4px;
        text-align:center;
    }

</style>

<div id="wrapper" >

    <div>
        <h3 style="font-size:20pt"><strong>Kelvic Suites </strong><small>& Towers.</small></h3>
        <p style="font-size:18pt;line-height: 0.2em; text-align: center;"><strong>Plot 107, Area E, New Owerri,</strong><p>
        <p style="font-size:18pt;line-height: 0.2em; text-align: center; padding-bottom:10px; border-bottom:1px solid #000;"><strong>Owerri, Imo State</strong></p>

        <table style="width:100%" style="font-size:18pt">
            <tr>
                <td style="width:100%; font-size:16pt;"><strong>Invioce No :</strong> <?php echo generateTransId(); ?></td>

            </tr>
            <tr>
                <td style="width:100%; font-size:16pt;"><strong>Date : </strong> <?php echo dateToString(today()); ?></td>
            </tr>
            <tr>

                <td style="width:100%; font-size:16pt;"><strong>Time : </strong> <?php echo timeToString(time()); ?></td>
            </tr>
            <?php
                if($data['guestType'] == 1){
                    $room = new Room($data['roomId']);
                    ?>
                    <tr>

                        <td style="width:100%; font-size:16pt;"><strong>Room No : <?php echo $room->no; ?></strong></td>
                    </tr>
                <?php } ?>
        </table>
        <h3 style="text-align:center; font-size:18pt"><strong><?php echo strtoupper($thisUser->role); ?>
                BILL</strong></h3>


        <table style="width:100%; font-size:16pt;">
            <thead>
            <tr>
                <td style="font-size:16pt;"><strong>DESC</strong></td>
                <td style="font-size:16pt;"><strong>RATE</strong></td>
                <td style="font-size:16pt;"><strong>AMT</strong></td>
            </tr>
            </thead>

            <tbody>
            <?php
                $count = 1;
                $total = 0;
                foreach ($data['items'] as $row) {
                    $total += $row['amt'];
                    ?>

                    <tr>
                        <td style="font-size:16pt;"><?php echo '( ' . $row['qty'] . ' ) ' . $row['itemName']; ?></td>
                        <td style="font-size:16pt;"><?php echo number_format($row['price']); ?></td>
                        <td style="font-size:16pt;"><?php echo number_format((int)$row['amt']); ?></td>
                    </tr>

                    <?php
                    $count++;
                }
            ?>

            <tr>
                <td colspan="2" style="font-size:16pt"><strong>Total Amount</strong></td>
                <td  style="font-size:16pt"><strong><?php echo number_format($total); ?></strong></td>
            </tr>

            </tbody>

        </table>

        <hr style="color:#666; padding5px 0" />

        <div id="print" class="row" style="margin-top:10px">
            <div style="float:left; width:50%;text-align:left"><a href="javascript:void(0)" style="cursor:pointer"
                                                                  onclick="printInv
                ('print');
                ">Print
                    Invoice</a></div>
            <div style="float:left; width:50%;text-align:right" class="col-lg-6 text-right"><a href="<?php echo $url; ?>" style="cursor:pointer" title="Back to
                 Make
                Sales">Back</a></div>
        </div>

        <br style="clear:both" />





    </div>

</div>


<script src="<?php echo $baseUri; ?>/assets/js/application/ctrl.js" type="text/javascript"></script>