<?php 
#check if user is logged in


if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}

$thisUser = unserialize($registry->get('session')->read('thisUser'));


#include header
$registry->get('includer')->render('header', array('css' => array(
																'font-awesome.min.css',
																)));

#include Sidebar
$registry->get('includer')->render('sidebar', array());


#include small header
$registry->get('includer')->renderWidget('smallHeader');

    //try to update shiftTimes
    setShiftTimes();


 
?>
    	
<script type="text/javascript">

function sivamtime() {
  now=new Date();
  hour=now.getHours();
  min=now.getMinutes();
  sec=now.getSeconds();

if (min<=9) { min="0"+min; }
if (sec<=9) { sec="0"+sec; }
if (hour>12) { hour=hour-12; add="PM"; }
else { hour=hour; add="AM"; }
if (hour==12) { add="PM"; }

time = ((hour<=9) ? "0"+hour : hour) + ":" + min + ":" + sec + " " + add;

if (document.getElementById) { document.getElementById('theTime').innerHTML = time; }
else if (document.layers) {
 document.layers.theTime.document.write(time);
 document.layers.theTime.document.close(); }

setTimeout("sivamtime()", 1000);
}
window.onload = sivamtime;


</script>
    
	
    
    <!-- Page Body here...Editable region -->
        
        <div class="warper container-fluid">
        	
            <div class="page-header"><h1>Dashboard <small style="color:#FF404B">( <?php echo $thisUser->role; ?> )</small></h1></div>

            <hr />
            
            <div class="row" >
                <br /><br /><br />
                 <p class="text-center lead"><?php echo dateToString(today()); ?></p>
            	<h1 class="text-center" id="theTime" style="color:#ddd; font-size:60px"></h1>
            
            </div>
            
            
        </div>
        <!-- Warper Ends Here (working area) -->
        

        <?php  
        	$registry->get('includer')->render('footer', array('js' => array(
        													'plugins/nicescroll/jquery.nicescroll.min.js',
        													'app/custom.js'
        													)));

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>