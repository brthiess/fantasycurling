<?php

//Table listing
if(ACTION == ''){
	
	include("includes/widgets/inquirieschart.php");
	
	include("includes/widgets/searchform.php");
	
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Inquiries 
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content nopadding'>";
			echo "<table cellpadding='0' cellspacing='0' border='0' class='tablesorter'>";
		
			echo "<thead>";
			echo "<th>Email</th>";
			echo "<th>Inquiry</th>";
			echo "<th>Request Date</th>";
			echo "<th class='{sorter:false}'>&nbsp;</th>";
			echo "</thead>";
			
			echo "<tbody>";
			foreach($inquiries as $row){
				echo "<tr>";
					echo "<td>" .$row['email']. "</td>";
					echo "<td>" .$row['inquiry']. "</td>";
					echo "<td>" .date("F d, Y",strtotime($row['timestamp'])). "</td>";
					echo "<td class='right'><a href='" .PAGE_URL. "?action=edit&item_id=" .$row['inquiry_id']. "' class='button-sm'><i class='fa fa-eye'></i>View</a></td>";
				echo "</tr>";	
			}
			echo "</tbody>";
			echo "</table>";
			
			//Pager
			$CMSBuilder->tablesorter_pager();
		
		echo "</div>";	
	echo "</div>";

}else{
	
	if(ACTION == 'edit'){
		$data = $inquiries[ITEM_ID];
		$row = $data;
	}

	echo "<form action='' method='post' enctype='multipart/form-data'>";
	
	//Inquiry details
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Inquiry Details
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content clearfix'>";
			echo "<div class='form-field'>";
			
				echo "<label><strong>Inquiry Type:</strong></label>";
				echo "<p>".$row['inquiry']."</p>";
				
				echo "<label><strong>Sent On:</strong></label>";
				echo "<p>".date("F d, Y",strtotime($row['timestamp']))."</p>";
				
				if($row['inquiry'] == "General Inquiry" && $row['name'] != ""){
					echo "<label><strong>From:</strong></label>";
					echo "<p>".$row['name']."</p>";
				}
				
				echo "<label><strong>Email:</strong></label>";
				echo "<p>".$row['email']."</p>";
				
				if($row['inquiry'] == "General Inquiry"){
					
					if($row['phone'] != ""){
						echo "<label><strong>Phone:</strong></label>";
						echo "<p>".$row['phone']."</p>";
					}
					
					if($row['company'] != ""){
						echo "<label><strong>Company:</strong></label>";
						echo "<p>".$row['company']."</p>";
					}
					
					if($row['message'] != ""){
						echo "<label><strong>Message:</strong></label>";
						echo "<p>".$row['message']."</p>";
					}
				}
				
			echo "</div>";
		echo "</div>";
	echo "</div>"; //inquiry details

	//Sticky footer
	echo "<footer id='cms-footer' class='resize'>";		
		echo "<a href='" .PAGE_URL. "' class='cancel'>Cancel</a>";
	echo "</footer>";
	
	echo "<input type='hidden' name='xssid' value='" .$_COOKIE['xssid'] ."' />";
	echo "</form>";
	
}

?>