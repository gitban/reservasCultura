<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/memo.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>

<?php
require 'defaultincludes.inc';

// Get non-standard form variables
$timetohighlight = get_form_var('timetohighlight', 'int');
$ajax = get_form_var('ajax', 'int');

// Check the user is authorised for this page
checkAuthorised();

$timestamp = mktime(12, 0, 0, $month, $day, $year);
$roommatch = get_form_var('roommatch', 'string');

// print the page header
print_header($day, $month, $year, $area, isset($room) ? $room : '');

?>

</html>

<body>
    <h1 id="titulo_informe"> INFORME DE RESERVAS :</h1>
    <br>
    <form name="informes" id="informes">
        <div class="formulario">
            <div id="div_report_end">
                <div id="tipo_informe">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radio_tipo_informe" id="radio_tipo_informe"
                        value="1" checked>
                        <label class="form-check-label" for="flexRadioDefault2">
                            INFORME POR EDIFICIO
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radio_tipo_informe" id="radio_tipo_informe"
                            value="2">
                        <label class="form-check-label" for="flexRadioDefault1">
                            INFORME POR SALA
                        </label>
                    </div>
                </div>
                <div class="opciones">
                    <?php
                    // Get non-standard form variables
                    $default_report_days = 10;
                    $to_date = getdate(mktime(0, 0, 0, $month, $day + $default_report_days, $year));
                    $from_day = get_form_var('from_day', 'int', $day);
                    $from_month = get_form_var('from_month', 'int', $month);
                    $from_year = get_form_var('from_year', 'int', $year);
                    $to_day = get_form_var('to_day', 'int', $to_date['mday']);
                    $to_month = get_form_var('to_month', 'int', $to_date['mon']);
                    $to_year = get_form_var('to_year', 'int', $to_date['year']);

                    echo '<div class="item_opcion" id="calendario"><h2 id="titulo_opcion">Seleccione período</h2>';
                    echo '<div id="desde"> Desde:</label><br>';
                    genDateSelector('desde_', $from_day, $from_month, $from_year);
                    echo '</div>';

                    echo '<div id="hasta"><label> Hasta:</label><br>';
                    genDateSelector('hasta_', $to_day, $to_month, $to_year);
                    echo '</div>';
                    ?>
                </div>
                <div class="item_opcion" id="por_edificio">
                    <h2 id="titulo_opcion">Seleccion Edificio para Memo</h2>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radio_edificio" id="alem" checked>
                        <label class="form-check-label" for="flexRadioDefault1">
                            Alen 1253
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radio_edificio" id="colon">
                        <label class="form-check-label" for="flexRadioDefault2">
                            Colon 80
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radio_edificio" id="casa_cultura">
                        <label class="form-check-label" for="flexRadioDefault3">
                            Casa de la Cultura
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radio_edificio" id="rondeau">
                        <label class="form-check-label" for="flexRadioDefault4">
                            Rondeau
                        </label>
                    </div>
                </div>

                <?php
                echo "<div class='item_opcion' id='por_sala'><h2 id='titulo_opcion'>Seleccione Sala</h2>";
                //"Selecciono la lista de Edificios disponibles desde la base de datos"; 
                $sql_area = "SELECT area_name,id FROM reservas_area";
                $areas = @mysql_query($sql_area);
                echo "<select name=Edificio class='form-select form-select-sm' id=edificio disabled='true' value=''>id Nombre</option>"; // comando list box desplegable
                while ($row = @mysql_fetch_assoc($areas)) { //Array de registros almacenados en $row
                    echo "<option value=$row[id]>$row[area_name]</option>";
                    /* los valores de las opciones son agregadas en el loop */
                }
                echo "</select> "; // cierro el desplegable
                

                //"Selecciono la lista de salas disponibles desde la base de datos"; 
                
                $sql_room = "SELECT room_name,id FROM reservas_room";
                $salas = @mysql_query($sql_room);
                echo "<select name=salas class='form-select form-select-sm' id=sala disabled='true' value=''>id Nombre</option>"; // comando list box desplegable
                while ($row = @mysql_fetch_assoc($salas)) { //Array de registros almacenados en $row
                    echo "<option value=$row[id]>$row[room_name]</option>";
                    /* los valores de las opciones son agregadas en el loop */
                }
                echo "</select></div>"; // cierro el desplegable
                ?>
            </div>
        </div>
        <button type="submit" class="btn btn-danger" id="btn_enviar"> Generar informe</button>

        </div>
        <div id="reporte"></div>
    </form>
    <div id="descarga">
    </div>
    <script src="memo.js"></script>
   
    </div>
</body>

</html>