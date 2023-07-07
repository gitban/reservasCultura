<?php
require_once "defaultincludes.inc";

	shell_exec("mysqldump.exe -u $db_login -p$db_password $db_database > backup/$db_database.sql"); // 2> backup/error.txt*/

	// Comprimo el archivo en .gz
	$filename = 'backup-'.time().".sql.gz";

	file_put_contents('backup/'.$filename, gzencode( file_get_contents('backup/$db_database.sql')));
	//header('Location:backup/'.$filename);
	// copiado de functions_mail.inc
		require_once 'lib/PHPMailer/PHPMailerAutoload.php';
		$mail = new PHPMailer;

		//$mail->Debugoutput = 'log';

		$mail->Host 		= $smtp_settings['host'];
		$mail->Port 		= $smtp_settings['port'];
		$mail->SMTPAuth 	= $smtp_settings['auth'];

		$mail->SMTPSecure 	= $smtp_settings['secure'];
		$mail->Username 	= $smtp_settings['username'];
		$mail->Password 	= $smtp_settings['password'];

		$mail->Subject     	= "Reservas Dto. Quimica: Backup";
		$mail->From     	= "lacoqui@uns.edu.ar";
		$mail->FromName 	= "Lacoqui";
		$mail->Body 		= "Copia de seguridad del sistema de reservas: Departamento de Quimica";
		$mail->AddAddress("lacoqui@uns.edu.ar","LACOQUI");
		//$mail->AddAttachment('backup/'.$filename);

	if(!$mail->Send()) {
		echo "Ocurrió un error al realizar la copia de seguridad";
	}

?>
