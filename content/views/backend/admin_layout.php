<?php 
$base_href = getBaseHref(true);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>RI Admin</title>
<base href="<?php echo $base_href;?>">

<link rel="stylesheet" type="text/css" href="<?php echo $base_href;?>includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="<?php echo $base_href;?>includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="<?php echo $base_href;?>includes/menu.js"></script>
<script language="javascript" src="<?php echo $base_href;?>includes/general.js"></script>



</head>

<body onload="init()">
	

	<!-- body //-->

	<div id='wrapper'>
		<?php echo $riview->getHelper('php::holder')->get('main')?>
		
	</div>
	<!-- body_eof //-->
	

	 
</body>
</html>