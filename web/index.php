<?php
	//Remember you used short_tags in this project. @Blackwood
	include "../config/xo_ubicador.php";
	include CONF."xo_settings.php";
	
	session_start();
	$defined_path = false;
	if ($_ENABLE_CONTROL_PANNEL) {
		if (isset($_POST['filePath'])) {
			$_SESSION['filePath'] = $_POST['filePath'];
			$_SESSION['specialFilePath'] = $_POST['specialFilePath'];
			$defined_path = true;
		}
	} else {
		$_SESSION['specialFilePath'] ="../".INC.$_DEFAULT_XML_FILE;
		$_SESSION['filePath'] = INC.$_DEFAULT_XML_FILE;
		$defined_path = true;
	}

	if($defined_path) {
		header("Location: ./home.php");
	} else {
		header("Location: ./controlpannel.php");
	}
?>