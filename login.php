<?php

// $Id: admin.php 2142 2011-10-23 18:47:56Z cimorrison $

require_once "defaultincludes.inc";
// Check the user is authorised for this page
checkAuthorised();
header('Location: /reservas/');
?>