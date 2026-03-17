<?php
require_once dirname(__FILE__) . '/helper/word/PHPWord-master/src/PhpWord/Autoloader.php';
\PhpOffice\PhpWord\Autoloader::register();
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Font;

function word($edificio, $salas, $datos, $inicio, $fin)
{
	// array utilizado para obtener los días de la semana en español
	$dias = array ( 
    'Sunday' => 'Domingo',
    'Monday' => 'Lunes',
    'Tuesday' => 'Martes',
    'Wednesday' => 'Miércoles',
    'Thursday' => 'Jueves',
    'Friday' => 'Viernes',
    'Saturday' => 'Sábado',
    );
	
	// array utilizado para obtener los meses en español
	$meses = array ( 
    'January'=>'enero',
	'February'=>'febrero',
	'March'=>'marzo',
	'April'=>'abril',
	'May'=>'mayo',
	'June'=>'uunio',
	'July'=>'julio',
	'August'=>'agosto',
	'September'=>'septiembre',
	'Octuber'=>'octubre',
	'November'=>'noviembre',
	'December'=>'diciembre'
    );
	
	//Instancia phpWord.
	$documento = new PhpWord();

	// Nueva seccion
	$seccion = $documento->addSection(
		array(
			'marginLeft' => 1000,
			'marginRight' => 500,
			'marginTop' => 1500,
			'marginBottom' => 500,
			'orientation' => 'landscape'
		)
	);
	// Add header for all other pages
	$subsequent = $seccion->addHeader();
	$subsequent->addImage(dirname(__FILE__) . '/images/logocyeuns.png', array('width' => 1000));//, 'height' => 80));
if($edificio == 'rondeau'){
	$ubicacion = $edificio;
}else{
	$ubicacion = $salas[$datos[1]["room_id"]];
}
	$intervalo = date_format($inicio, 'd')." de ".$meses[date_format($inicio, 'F')]." de ".date_format($inicio, 'Y')." al "
	.date_format($fin, 'd')." de " .$meses[date_format($fin, 'F')]." de ".date_format($fin, 'Y');
	$seccion->addText("MEMORANDUM ".strtoupper($ubicacion),array('name' => 'Arial', 'size' => '14', 'bold' => 'true','underline' => 'single'),array('align' => 'center'));
	$seccion->addText("Del ".date_format($inicio, 'd')." de ".$meses[date_format($inicio, 'F')]." de ".date_format($inicio, 'Y')." al "
	.date_format($fin, 'd')." de " .$meses[date_format($fin, 'F')]." de ".date_format($fin, 'Y'),
	array('name' => 'Arial', 'size' => '12', 'bold' => 'true','underline' => 'single'),array('align' => 'center'));
	
	$seccion->addTextBreak(); 		
	$seccion->addText("	De: Secretaría General de Cultura y Extensión Universitaria",array('name' => 'Arial', 'size' => '11', 'bold' => 'true'));
	$seccion->addText("A: Dirección de Intendencia, Dirección de Medios Audiovisuales",array('name' => 'Arial', 'size' => '11', 'bold' => 'true'));
	
	$seccion->addText("-OBJETO: Comunicar las siguientes actividades:",array('name' => 'Arial', 'size' => '11', 'bold' => 'true'));
	$seccion->addTextBreak(); 		
	
	
	// Tabla personalizada
	$estilo_tabla = array(
		'width' => '15000',
		'borderColor' => '08897d',
		'borderSize' => '5',
		'cellMargin' => '20',
		'bgColor' => 'a5dda7',
	);
	$primera_fila = array('bgColor' => 'F2F2F2');
	$documento->addTableStyle('mitabla', $estilo_tabla, $primera_fila);
	$styleCell = array('valign' => 'center');
	$tabla = $seccion->addTable('mitabla');
	$tabla->addRow();
	$tabla->addCell(1300, $styleCell)->addText(htmlspecialchars('Día/hora'));
	$tabla->addCell(1300, $styleCell)->addText(htmlspecialchars('Sala'));
	$tabla->addCell(4000, $styleCell)->addText(htmlspecialchars('Evento'));
	$tabla->addCell(3000, $styleCell)->addText(htmlspecialchars('Descripción'));
	$tabla->addCell(4000, $styleCell)->addText(htmlspecialchars('Contacto'));
	$tabla->addCell(4000, $styleCell)->addText(htmlspecialchars('Entidad'));
	$tabla->addCell(3500, $styleCell)->addText(htmlspecialchars('Audiovisuales'));
	$tabla->addCell(4000, $styleCell)->addText(htmlspecialchars('Mayordomía'));

	foreach ($datos as $row) {
		if($row){
		if($row["audiovisuales"]!=""){
			$styleCell = array('valign' => 'center','bgColor' => '63ef68');
			}else{
			$styleCell = array('valign' => 'center','bgColor' => 'fffff2');
		}
		$tabla->addRow();
		$dia = $dias[date('l', $row["start_time"])];
		$tabla->addCell(1300, $styleCell)->addText(htmlspecialchars($dia.' ' .date("d/m/Y H:i", $row["start_time"]). ' a ' .date("H:i", $row["end_time"])));
		$tabla->addCell(3000, $styleCell)->addText(htmlspecialchars($salas[$row["room_id"]]));
		$tabla->addCell(3000, $styleCell)->addText(htmlspecialchars($row["name"]));
		$tabla->addCell(4000, $styleCell)->addText(htmlspecialchars($row["description"]));
		$tabla->addCell(4000, $styleCell)->addText(htmlspecialchars($row["contact"].'-Tel: '.$row["contact_phone"]));
		$tabla->addCell(4000, $styleCell)->addText(htmlspecialchars($row["contact_entity"]));
		$tabla->addCell(3500, $styleCell)->addText(htmlspecialchars($row["audiovisuales"]));
		$tabla->addCell(4000, $styleCell)->addText(htmlspecialchars($row["mayordomia"]));
		}
	}
	$footer = $seccion->addFooter();
	$footer->addPreserveText('Página {PAGE} de {NUMPAGES} - '.$intervalo);
	return $documento;
}
 function downloadWord($nombreReporte){
	header("Content-Disposition: attachment; filename='".$nombreReporte."'");
    return file_get_contents(dirname(__FILE__) ."/memos/".$nombreReporte);

 }