<?php

	//Wants this parameter:
	//	$waveform_filename_source
	//	$waveform_filename_target


  //header('Content-type: image/png');
  $old_image = imagecreatefrompng($waveform_filename_source);
  $old_width = imagesx($old_image);
  $old_height = imagesy($old_image);
  $new_image = imagecreate(256, 64);
  $img_black = ImageColorAllocate ($new_image, 0, 0, 0);
  $img_transparent =  imagecolorallocatealpha ( $new_image , 80 , 80 , 80 , 120 );
  imagefill ( $new_image , 10 , 10 , $img_transparent );
  
  $y_pos_percent = 0;
  $max_y = 0;
  $min_y = $old_height/2;
  $y_pos_array = array();
  for ($i = 0; $i < 256; $i++) {
	$this_x_pos = round($i / 256 * $old_width);
	//echo("$this_x_pos\n<br/>");
	$flip_y_pos = 0;
	$last_color = imagecolorat ( $old_image , 1 , 1 );
	$last_y_pos_percent = $y_pos_percent;
	for ($j = 0; $j < $old_height/2; $j++) {
		 $this_color = imagecolorat ( $old_image , $this_x_pos , $j );
		 if ($this_color != $last_color) { $flip_y_pos = $j; }
		 $last_color = $this_color;
	}
	if ($flip_y_pos > $max_y) { $max_y = $flip_y_pos; }
	if ($flip_y_pos < $min_y) { $min_y = $flip_y_pos; }
	$y_pos_array[$i] = $flip_y_pos;
  }
  //echo ("<table>");
  $min_y = $min_y*0.9;
  $max_y = $max_y*1.1;
  for ($i = 1; $i < 256; $i++) {
	  $start_y = $y_pos_array[$i-1]-$min_y;
	  $start_y = $start_y/($max_y-$min_y);
	  
	  
	  
	  $end_y = $y_pos_array[$i]-$min_y;
	  $end_y = $end_y/($max_y-$min_y);
	  	  
	  //echo ("<tr><td>".($min_y/$max_y)."</td><td>".($i-1)."</td><td>".$start_y."</td><td>".$i."</td><td>".$end_y."</td></tr>\n");
	  
	  imageline($new_image,$i-1,round($start_y * 64),$i,round($end_y * 64),$img_black);
  }
  //echo ("</table>");
  imagefilter ( $new_image , IMG_FILTER_SMOOTH, 100);
  imagepng($new_image,$waveform_filename_target);

?>
