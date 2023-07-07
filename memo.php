<?php
// $Id: day.php 2374 2012-08-12 19:11:43Z cimorrison $

require "defaultincludes.inc";
require_once "mincals.inc";
require_once "functions_table.inc";

// Get non-standard form variables
$timetohighlight = get_form_var('timetohighlight', 'int');
$ajax = get_form_var('ajax', 'int');

// Check the user is authorised for this page
checkAuthorised();

$timestamp = mktime(12, 0, 0, $month, $day, $year);

// print the page header
print_header($day, $month, $year, $area, isset($room) ? $room : "");

echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informes</title>
</head>
<body>
    <h1>Informes - Memos</h1>
    <form action="informe">
        <input type="text"> Nombre
        <input type="text"> Nombre
        <input type="text"> Nombre
        <button type="submit"> Generar informe</button>
    </form>
</body>
</html>
'
?>