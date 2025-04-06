<?php
session_start();
session_destroy();
header("Location: newform.php");
exit();
?>