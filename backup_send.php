<?php

require "defaultincludes.inc";
    // Genero el dump de la BD
    	// $db_database = "reservas";
		//$db_login = "cye";
		//$db_password = 'claEXTana2486';
	exec("\"C:\Program Files\MySQL\MySQL Server 5.5\bin\mysqldump.exe\" -u $db_login -p$db_password $db_database > backup/reservas.sql"); // 2> backup/error.txt*/
	
	// Comprimo el archivo en .gz
	$filename = 'backup-'.time().".sql.gz";
	file_put_contents('backup/'.$filename, gzencode( file_get_contents('backup/reservas.sql')));
	//header('Location:backup/'.$filename);

	// copiado de functions_mail.inc
		require_once 'lib/PHPMailer/PHPMailerAutoload.php';
		$mail = new PHPMailer;

		//$mail->Debugoutput = 'log';
	    $mail->SMTPDebug = SMTP::DEBUG_CONNECTION; 

		$mail->Host 		= $smtp_settings['host'];
		$mail->Port 		= $smtp_settings['port'];
		$mail->SMTPAuth 	= $smtp_settings['auth'];
		$mail->SMTPSecure 	= $smtp_settings['secure'];
		$mail->Username 	= $smtp_settings['username'];
		$mail->Password 	= $smtp_settings['password'];

		$mail->Subject     	= "Reservas Sec Cult y Ext: Backup (Te saluda Jeronimo)";
		$mail->From     	= "lacoqui@uns.edu.ar";
		$mail->FromName 	= "CyE Backup Systems";
		$mail->Body 		= "Copia de seguridad del sistema de reservas: Secretaria de Cultura y Extension";
		$mail->AddAddress("esteban.perez@uns.edu.ar","CyE Backup Systems");
		$mail->AddAttachment('backup/'.$filename);

	if(!$mail->Send()) {
		echo "Ocurrió un error al realizar la copia de seguridad";
	} 

?>