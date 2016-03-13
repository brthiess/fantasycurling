<?php

//example for including the Organization markup for ContactPoints
//html microdata markup included in footer

//contact points snippet
$phone_info = array();
if(trim($global['contact_phone']) != ""){
	$phone_info[] = array("@type" => "ContactPoint", "telephone" => formatIntlNumber($global['contact_phone']), "contactType" => "customer support");
}
if(trim($global['contact_toll_free']) != ""){
	$phone_info[] = array("@type" => "ContactPoint", "telephone" => formatIntlNumber($global['contact_toll_free']), "contactType" => "customer support", "contactOption" => "TollFree");
}

//global numbers
foreach($global['global_numbers'] as $number){
	if($number['phone'] != ""){
	
		print_r($number);
	
		$contact_option = array();
		if($number['tollfree']){
			$contact_option[] = "TollFree";
		}
		if($number['hearingimpaired']){
			$contact_option[] = "HearingImpairedSupported";
		}
		
		$phone_info[] = array("@type" => "ContactPoint", "telephone" => formatIntlNumber($number['phone']), "contactType" => $number['type'], "contactOption" => $contact_option);
	}		
}

?>
	
<script type="application/ld+json">
{ "@context" : "http://schema.org",
  "@type" : "Organization",
  "url" : "http://<?php echo $_SERVER['HTTP_HOST']; ?>",
  "logo" : "http://<?php echo $_SERVER['HTTP_HOST']; ?>/images/logo.png",
  "contactPoint" :  <?php echo json_encode($phone_info); ?>
}
</script>