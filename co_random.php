<?php
//echo(".r2");




	$random_includition = rand(0,255);
	
	if ($random_includition < 25) {
		include('rd_checknew.php');
	} elseif ($random_includition < 50) {
		include('rd_checkflattr.php');		
	} elseif ($random_includition < 75) {
		include('rd_removespam.php');		
	}
	//$content['foot'] .= " $random_includition ";
?>
