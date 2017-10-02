<?php 
	if(isset($_SESSION['ERR_MSG']) && !empty($_SESSION['ERR_MSG'])) {
		echo('<p class="erro_msg">'.$_SESSION['ERR_MSG'].'</p>'); 
	}

	if(isset($_SESSION['SUCSS_MSG']) && !empty($_SESSION['SUCSS_MSG'])) {
		echo('<p class="sucss_msg">'.$_SESSION['SUCSS_MSG'].'</p>'); 
	}

	if(isset($_SESSION['SUCSS_MSG']) && !empty($_SESSION['SUCSS_MSG'])) {
		unset($_SESSION['SUCSS_MSG']);
	}
	if(isset($_SESSION['ERR_MSG']) && !empty($_SESSION['ERR_MSG'])) {
		unset($_SESSION['ERR_MSG']);
	}
?>	

<style>
	.sucss_msg { background-color: rgb(223,240,216); padding: 10px; color:rgb(60,118,61); text-align: center; }
	.erro_msg { background-color: rgb(242,222,222); padding: 10px; color:rgb(169,68,66); text-align: center;}
</style>