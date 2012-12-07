<?php
	require_once "lib/aws/sdk.class.php";

	error_reporting(-1);
	
	$ec2 = new AmazonEC2();

	// Describe all volumes attached to this instance
	$response = $ec2->describe_volumes();

	if ($response->isOK()) {
		
		print_r($response->body->volumeSet);
		print_r(count($response->body->volumeSet));
	}

?>
