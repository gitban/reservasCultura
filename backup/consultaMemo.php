<?php
require 'defaultincludes.inc';
require 'word.inc';

require_once dirname(__FILE__) . '/helper/word/PHPWord-master/src/PhpWord/Autoloader.php';
\PhpOffice\PhpWord\Autoloader::register();

header('Content-Type: text/html; charset=UTF-8');

// Tomo los datos desde el request
$json = file_get_contents('php://input');

// Convierto a un objeto PHP
$data = json_decode($json);

$tipo_consulta = $data->tipo_consulta;
$fechaDesde = date_create_from_format('d/m/Y', $data->fecha_desde);
$fechaHasta = date_create_from_format('d/m/Y', $data->fecha_hasta);
$desde = strtotime(date_format($fechaDesde, 'm/d/Y 00:00'));
$hasta = strtotime(date_format($fechaHasta, 'm/d/Y 23:59'));
//var_dump($hasta);exit;
$edificio = $data->edificio;
$dias = array(
    'Sunday' => 'Domingo',
    'Monday' => 'Lunes',
    'Tuesday' => 'Martes',
    'Wednesday' => 'Miércoles',
    'Thursday' => 'Jueves',
    'Friday' => 'Viernes',
    'Saturday' => 'Sábado',
);
// obtengo las salas de la base de datos del sistema y las almaceno en un array
$sql_sala = 'SELECT * FROM reservas_room ';
$sala_nombre = @mysql_query($sql_sala);
if (!$sala_nombre) {
    echo 'Error de BD, no se pudo consultar la base de datos\n';
    echo 'Error MySQL: ' . @mysql_error();
    exit;
}
$array_salas[] = '';
while ($salas = mysql_fetch_assoc($sala_nombre)) {
    $array_salas[$salas['id']] = $salas['room_name'];
}

// la consulta 2 es cuando el usuario selecciona la opcion 'por sala'
if ($tipo_consulta == 2) {
    $edificio = 'por_sala';
    $sala = $data->sala;
    $sql = "SELECT * FROM reservas_entry WHERE start_time >= ${desde} AND end_time <= ${hasta} AND room_id = ${sala}";
    $sql_sala = "SELECT * FROM reservas_room WHERE id = ${sala}";

    $resultado = @mysql_query($sql);

    if (!$resultado) {
        echo 'Error de BD, no se pudo consultar la base de datos\n';
        echo 'Error MySQL: ' . @mysql_error();
        exit;
    }

    $reporteArray[] = '';
    // arreglo para almacenar los registros y poder enviar la informacion luego a la funcion que genera el word
    echo '<h1>Reporte de reservas de la sala: ' . $array_salas[$sala] . '</h1>';
    echo '<h2>Entre los días: ' . date_format($fechaDesde, 'd/m/Y') . ' y ' . date_format($fechaHasta, 'd/m/Y') . '</h2>';
    echo '<br>';
    echo "<table style='border:solid 1px blue; width:90em; margin:5em'>";
    echo "<th style='border:solid 1px darkgray; padding:2px;'>Inicio</th>
    <th style='border:solid 1px darkgray; padding:2px;'>Sala</th>
    <th style='border:solid 1px darkgray; padding:2px;'>Nombre</th>
    <th style='border:solid 1px darkgray; padding:2px;'>Descripcion</th>
    <th style='border:solid 1px darkgray; padding:2px;'>Contacto</th>
    <th style='border:solid 1px darkgray; padding:2px;'>Entidad</th>
    <th style='border:solid 1px darkgray; padding:2px;'>AUDIOVISUALES</th>
    <th style='border:solid 1px darkgray; padding:2px;'>MAYORDOMÍA</th>";
    $i = 0;
    $datoAnterior[] = '';
    while ($datos = @mysql_fetch_assoc($resultado)) {
        array_push($reporteArray, $datos);
        if ($i % 2 == 0) {
            echo "<tr style='background-color:#bccbc5'>";
        } else {
            echo "<tr style='background-color:#c9fdc9'>";
        }
        ;
        if ($datoAnterior['name'] != $datos['name'] || $datoAnterior['start_time'] != $datos['start_time']) {
            $i++;
            $sala_nombre_corto = str_replace(strtoupper($edificio . ' -'), '', strtoupper($array_salas[$datos['room_id']]));
            $sala_nombre_corto = str_replace('-', '', $sala_nombre_corto);
            $dia = $dias[date('l', $datos['start_time'])];
            echo "<td style='border:solid 1px darkgray; padding:2px;'><b>" . $dia . '</b><br> ' . date('d/m/Y \. H:i', $datos['start_time']) . ' a ' . date('H:i', $datos['end_time']) . '</td>';
            echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $sala_nombre_corto . '</td>';
            echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['name'] . '</td>';
            echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['description'] . '</td>';
            echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['contact'] . '</td>';
            echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['contact_entity'] . '</td>';
            echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['audiovisuales'] . '</td>';
            echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['mayordomia'] . '</td>';
            echo '</tr>';
        }
        $datoAnterior = $datos;
    }
    echo '</table>';
    mysql_free_result($resultado);
    var_dump($edificio);
    var_dump($array_salas[$sala]);
  //  exit;
    $wd = word($edificio, $array_salas[$sala], $reporteArray, $fechaDesde, $fechaHasta);

    $nombreReporte = 'memo-' . utf8_decode($array_salas[$sala]) . '-' . date('d-m-Y') . '.docx';
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($wd, 'Word2007');
    $objWriter->save('memos/' . $nombreReporte);

    echo"<a href='memos/memo-" . $array_salas[$sala] . "-" . date('d-m-Y') . ".docx' type='button' class='btn btn-info'>Descargar WORD</a>";
    echo '<br><br><br>';
} else {
    // inicio de la consulta por edificio
    switch ($edificio) {
        case 'colon':
            $salas = array(11, 12, 13);
            break;
        case 'casa_cultura':
            $salas = array(15, 37, 49);
            break;
        case 'rondeau':
            $salas = array(39, 40, 48);
            break;
        case 'alem':
            $salas = array(16, 41);
            break;
    }
    // caso particular edificio Rondeau, el informe se genera por días y no por sala
    if ($edificio == 'rondeau' || $edificio == 'casa_cultura') {
        ($edificio == 'casa_cultura') ?
            $sql = "SELECT * FROM reservas_entry WHERE start_time >= ${desde} AND end_time <= ${hasta} AND ( room_id LIKE '15' OR room_id LIKE '49')" :
            $sql = "SELECT * FROM reservas_entry WHERE start_time >= ${desde} AND end_time <= ${hasta} AND ( room_id LIKE '36' OR room_id LIKE '51' OR room_id LIKE '39' OR room_id LIKE '40' OR room_id LIKE '48')";

        $resultado = @mysql_query($sql);

        if (!$resultado) {
            echo 'Error de BD, no se pudo consultar la base de datos\n';
            echo 'Error MySQL: ' . @mysql_error();
            exit;
        }
        $reporteArray[] = '';
        echo '<h1>Reporte de reservas de las salas de ' . strtoupper($edificio) . ' </h1>';
        echo '<h2>Entre los días: ' . date_format($fechaDesde, 'd/m/Y') . ' y ' . date_format($fechaHasta, 'd/m/Y') . '</h2>';
        echo "<table style='border:solid 1px blue; width:90em; margin:5em'>";
        echo "<th style='border:solid 1px darkgray; padding:2px;'>Inicio</th>
            <th style='border:solid 1px darkgray; padding:2px;'>Sala</th>
            <th style='border:solid 1px darkgray; padding:2px;'>Nombre</th>
            <th style='border:solid 1px darkgray; padding:2px;'>Descripcion</th>
        <th style='border:solid 1px darkgray; padding:2px;'>Contacto</th>
        <th style='border:solid 1px darkgray; padding:2px;'>Entidad</th>
        <th style='border:solid 1px darkgray; padding:2px;'>AUDIOVISUALES</th>
        <th style='border:solid 1px darkgray; padding:2px;'>MAYORDOMÍA</th>";
        $i = 0;
        $datoAnterior[] = '';

        while ($datos = @mysql_fetch_assoc($resultado)) {
            if ($i % 2 == 0) {
                echo "<tr style='background-color:#bccbc5'>";
            } else {
                echo "<tr style='background-color:#c9fdc9'>";
            }
            ;
            $dia = $dias[date('l', $datos['start_time'])];
            if ($datoAnterior['name'] != $datos['name'] || $datoAnterior['start_time'] != $datos['start_time']) {
                $i++;
                //var_dump($edificio);exit;
                $sala_nombre_corto = str_replace(strtoupper($edificio), '', strtoupper($array_salas[$datos['room_id']]));
                $sala_nombre_corto = str_replace('-', '', $sala_nombre_corto);
                array_push($reporteArray, $datos);
                echo "<td style='border:solid 1px darkgray; padding:2px;'><b>" . $dia . '</b><br> ' . date('d/m/Y \. H:i', $datos['start_time']) . ' a ' . date('H:i', $datos['end_time']) . '</td>';
                echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $sala_nombre_corto . '</td>';
                echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['name'] . '</td>';
                echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['description'] . '</td>';
                echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['contact'] . '</td>';
                echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['contact_entity'] . '</td>';
                echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['audiovisuales'] . '</td>';
                echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['mayordomia'] . '</td>';
                echo '</tr>';

            }
            $datoAnterior = $datos;
        }
        echo '</tbody></table>';
        mysql_free_result($resultado);

        $wd = word($edificio, $array_salas, $reporteArray, $fechaDesde, $fechaHasta);
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($wd, 'Word2007');
        $objWriter->save('memos/memo-' . $edificio . '-' . date('d-m-Y') . '.docx');

        echo "<a href='memos/memo-" . $edificio . "-" . date('d-m-Y') . ".docx' type='button' class='btn btn-info'>Descargar WORD</a>";
        echo '<br><br><br>';

    } else {
        foreach ($salas as $sala) {
            $reporteArray[] = '';
            $sql = "SELECT * FROM reservas_entry WHERE start_time >= ${desde} AND end_time <= ${hasta} AND room_id = ${sala}";
            $sql_sala = "SELECT * FROM reservas_room WHERE id = ${sala}";

            $resultado = @mysql_query($sql);

            if (!$resultado) {
                echo 'Error de BD, no se pudo consultar la base de datos\n';
                echo 'Error MySQL: ' . @mysql_error();
                exit;
            }
            $sala_nombre = @mysql_query($sql_sala);
            if (!$sala_nombre) {
                echo 'Error de BD, no se pudo consultar la base de datos\n';
                echo 'Error MySQL: ' . @mysql_error();
                exit;
            }
            $sala_nombre = @mysql_fetch_assoc($sala_nombre);
            echo '<h1>Reporte de reservas de la sala: ' . $sala_nombre['room_name'] . '</h1>';
            echo '<h2>Entre los días: ' . date_format($fechaDesde, 'd/m/Y') . ' y ' . date_format($fechaHasta, 'd/m/Y') . '</h2>';
            echo "<table style='border:solid 1px blue; width:90em; margin:5em'>";
            echo "<th style='border:solid 1px darkgray; padding:2px;'>Inicio</th>
            <th style='border:solid 1px darkgray; padding:2px;'>Sala</th>
            <th style='border:solid 1px darkgray; padding:2px;'>Nombre</th>
            <th style='border:solid 1px darkgray; padding:2px;'>Descripcion</th>
            <th style='border:solid 1px darkgray; padding:2px;'>Contacto</th>
            <th style='border:solid 1px darkgray; padding:2px;'>Entidad</th>
            <th style='border:solid 1px darkgray; padding:2px;'>AUDIOVISUALES</th>
            <th style='border:solid 1px darkgray; padding:2px;'>MAYORDOMÍA</th>";
            $i = 0;
            $datoAnterior[] = '';
            while ($datos = @mysql_fetch_assoc($resultado)) {
                if ($i % 2 == 0) {
                    echo "<tr style='background-color:#bccbc5'>";
                } else {
                    echo "<tr style='background-color:#c9fdc9'>";
                }
                ;
                $dia = $dias[date('l', $datos['start_time'])];
                $sala_nombre_corto = str_replace(strtoupper($edificio), '', strtoupper($array_salas[$datos['room_id']]));
                $sala_nombre_corto = str_replace('-', '', $sala_nombre_corto);
                if ($datoAnterior['name'] != $datos['name'] || $datoAnterior['start_time'] != $datos['start_time']) {
                    $i++;
                    array_push($reporteArray, $datos);
                    echo "<td style='border:solid 1px darkgray; padding:2px;'><b>" . $dia . '</b><br> ' . date('d/m/Y \. H:i', $datos['start_time']) . ' a ' . date('H:i', $datos['end_time']) . '</td>';
                    echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $sala_nombre_corto . '</td>';
                    echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['name'] . '</td>';
                    echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['description'] . '</td>';
                    echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['contact'] . '</td>';
                    echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['contact_entity'] . '</td>';
                    echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['audiovisuales'] . '</td>';
                    echo "<td style='border:solid 1px darkgray; padding:2px;'>" . $datos['mayordomia'] . '</td>';
                    echo '</tr>';
                }
                $datoAnterior = $datos;
            }
            echo '</tbody></table>';
            mysql_free_result($resultado);
            $wd = word($edificio, $array_salas, $reporteArray, $fechaDesde, $fechaHasta);
            $nombreReporte = utf8_decode($sala_nombre['room_name']);
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($wd, 'Word2007');
            $objWriter->save('memos/memo-' . $nombreReporte . '-' . date('d-m-Y') . '.docx');

            echo "<a href='memos/memo-" . $sala_nombre['room_name'] . '-' . date('d-m-Y') . ".docx' type='button' class='btn btn-info'>Descargar WORD</a>";
            echo '<br><br><br>';
            unset($reporteArray);
        }
    }
}

?>
<br>
<br>