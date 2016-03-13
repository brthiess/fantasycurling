<?php if(isset($_POST['save'])){ ?>
    
<script type="text/javascript">
$(document).ready(function(){
<?php for($i=0; $i<count($cropimages); $i++){ ?>
	
	$('#cropmulti<?php echo ($i+1); ?>').Jcrop({
		setSelect:   [ 0, 0, <?php echo $cropimages[$i]['width']; ?>, <?php echo $cropimages[$i]['height']; ?> ],
		aspectRatio: <?php echo $cropimages[$i]['width']; ?>/<?php echo $cropimages[$i]['height']; ?>,
		boxWidth: <?php echo ($cropimages[$i]['width'] > 600 ? 600 : 300); ?>,
		boxHeight: <?php echo ($cropimages[$i]['width'] > 600 ? 600 : 300); ?>,
		bgColor: 'white',
		onSelect: function(coords){
			updateCoordsMulti(coords, <?php echo ($i+1); ?>);		
		}
	});
	
<?php } ?>
});
function checkCoordsMulti(){
	var success = true;
	$('.imgheight').each(function(){
		if(!parseInt($(this).val())) success = false;
	});
	if(!success){
		alert('Please select a crop region for all images then press submit.');
		return false;
	}
};
function updateCoordsMulti(c, id){
	$('#x'+id).val(c.x);
	$('#y'+id).val(c.y);
	$('#w'+id).val(c.w);
	$('#h'+id).val(c.h);
};
</script>

<?php
	
	//Crop form
	echo "<form action='' method='post' onSubmit=\"return checkCoordsMulti();\">";
	
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Crop Images
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content clearfix'>";
		
			for($i=0; $i<count($cropimages); $i++){
		
				echo "<p><label>" .$cropimages[$i]['label']. " <small>(" .$cropimages[$i]['width']. " x " .$cropimages[$i]['height']. ")</small></label>";
				echo "<img src='" .$path.$cropimages[$i]['dir'].$cropimages[$i]['src']. "?' id='cropmulti" .($i+1). "' class='cropbox' /></p>";
				
				echo "<input type='hidden' id='x" .($i+1). "' name='x[]' value='0' class='xcoord' />";
				echo "<input type='hidden' id='y" .($i+1). "' name='y[]' value='0' class='ycoord' />";
				echo "<input type='hidden' id='w" .($i+1). "' name='w[]' value='" .$cropimages[$i]['width']. "' class='imgwidth' />";
				echo "<input type='hidden' id='h" .($i+1). "' name='h[]' value='" .$cropimages[$i]['height']. "' class='imgheight' />";
				
				echo "<input type='hidden' name='imgdir[]' value='" .$cropimages[$i]['dir'] ."'>";
				echo "<input type='hidden' name='imgsrc[]' value='" .$cropimages[$i]['src'] ."'>";
				echo "<input type='hidden' name='imgwidth[]' value='" .$cropimages[$i]['width'] ."'>";
				echo "<input type='hidden' name='imgheight[]' value='" .$cropimages[$i]['height'] ."'>";
				echo "<input type='hidden' name='imglabel[]' value='" .$cropimages[$i]['label'] ."'>";
					
			}
		
		echo "</div>";
	echo "</div>";

	echo "<footer id='cms-footer' class='resize sticky'>";
		echo "<a href='" .PAGE_URL. "' class='cancel'>Cancel</a>";
		echo "<button type='submit' class='button f_right' name='crop'><i class='fa fa-crop'></i>Crop Images</button>";
	echo "</footer>";
	
	echo "<input type='hidden' name='xssid' value='" .$_COOKIE['xssid'] ."' />";
	echo "</form>";


//Do cropping
}else if(isset($_POST['crop'])){
	
	for($i=0; $i<count($_POST['imgsrc']); $i++){
		
		$imgsrc = $_POST['imgsrc'][$i];
		if($imgsrc != ''){
			$targ_w 		= $_POST['imgwidth'][$i];
			$targ_h 		= $_POST['imgheight'][$i];
			$img_quality 	= 90;
			$src 			= $_POST['imgdir'][$i].$imgsrc;
			$imgtype		= exif_imagetype($src);
			
			if($imgtype == IMAGETYPE_JPEG){
			
				$img_r 			= imagecreatefromjpeg($src);
				$dst_r 			= imagecreatetruecolor($targ_w, $targ_h);
				imagecopyresampled($dst_r, $img_r, 0, 0, $_POST['x'][$i], $_POST['y'][$i], $targ_w, $targ_h, $_POST['w'][$i], $_POST['h'][$i]);
				imagejpeg($dst_r,$src,$img_quality);
				
			}else if($imgtype == IMAGETYPE_GIF){
			
				$img_r 			= imagecreatefromgif($src);
				$dst_r 			= imagecreatetruecolor($targ_w, $targ_h);
				imagecopyresampled($dst_r, $img_r, 0, 0, $_POST['x'][$i], $_POST['y'][$i], $targ_w, $targ_h, $_POST['w'][$i], $_POST['h'][$i]);
				imagegif($dst_r,$src,$img_quality);
			
			}else if($imgtype == IMAGETYPE_PNG){
				
				$img_r = imagecreatefrompng($src);
				imagealphablending($img_r, true);
	
				$dst_r = imagecreatetruecolor($targ_w, $targ_h);
				imagesavealpha($dst_r, true);
				imagealphablending($dst_r, false);
				$transparent = imagecolorallocatealpha($dst_r, 0, 0, 0, 127);
				imagefill($dst_r, 0, 0, $transparent);
	
				imagecopyresampled($dst_r, $img_r, 0, 0, $_POST['x'][$i], $_POST['y'][$i], $targ_w, $targ_h, $_POST['w'][$i], $_POST['h'][$i]);
				imagepng($dst_r, $src);
				
			}
			
			imagedestroy($img_r);
			imagedestroy($dst_r);
			
		}
	}
}

?>