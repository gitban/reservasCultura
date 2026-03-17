<?php
$auth["type"]="db";

// $Id: config.inc.php 2211 2011-12-24 09:27:00Z cimorrison $

/**************************************************************************
 *   MRBS Configuration File
 *   Configure this file for your site.
 *   You shouldn't have to modify anything outside this file
 *   (except for the lang.* files, eg lang.en for English, if
 *   you want to change text strings such as "Meeting Room
 *   Booking System", "room" and "area").
 **************************************************************************/

/**********
 * Timezone
 **********/
 
// The timezone your meeting rooms run in. It is especially important
// to set this if you're using PHP 5 on Linux. In this configuration
// if you don't, meetings in a different DST than you are currently
// in are offset by the DST offset incorrectly.
//
// Note that timezones can be set on a per-area basis, so strictly speaking this
// setting should be in areadefaults.inc.php, but as it is so important to set
// the right timezone it is included here.
//
// When upgrading an existing installation, this should be set to the
// timezone the web server runs in.  See the INSTALL document for more information.
//
// A list of valid timezones can be found at http://php.net/manual/timezones.php
// The following line must be uncommented by removing the '//' at the beginning
//$timezone = "Europe/London";
$timezone = "America/Argentina/Buenos_Aires";

/*******************
 * Database settings
 ******************/
// Which database system: "pgsql"=PostgreSQL, "mysql"=MySQL,
// "mysqli"=MySQL via the mysqli PHP extension
$dbsys = "mysql";
// Hostname of database server. For pgsql, can use "" instead of localhost
// to use Unix Domain Sockets instead of TCP/IP.
$db_host = "localhost";
// Database name:
$db_database = "reservas";
// Database login user name:
$db_login = "cye";
// Database login password:
$db_password = 'claEXTana2486';
// Prefix for table names.  This will allow multiple installations where only
// one database is available
$db_tbl_prefix = "reservas_";
// Uncomment this to NOT use PHP persistent (pooled) database connections:
// $db_nopersist = 1;


/* Add lines from systemdefaults.inc.php and areadefaults.inc.php below here
   to change the default configuration. Do _NOT_ modify systemdefaults.inc.php
   or areadefaults.inc.php.  */
$simple_trailer = TRUE;

/*$typel["C"] = "Cedida";
$typel["A"] = "Alquilada";
*/
$booking_types = array();
$booking_types[] = "A";
$booking_types[] = "C";
$vocab["type.A"] = "Alquilada";
$vocab["type.C"] = "Cedida";

// Default type for new bookings
$default_type = "C";


// from systemdefaults.inc.php
$mrbs_admin = "Jerónimo Spadaccioli / Esteban A. Perez";
$mrbs_admin_email = "lacoqui@uns.edu.ar";
$mrbs_company_logo = "images/logo_chico.png"; 
$mrbs_company = 'Secretaría General de Cultura y Extensión Universitaria';   // This line must always be uncommented ($mrbs_company is used in various places)

/*$select_options['entry.responsable'] = array(	'1' => 'Coffee', 
												'2' => 'Sandwiches',
												'3' => 'Hot Lunch',
												'4' => 'asdf as');*/

$select_options['entry.event'] = array(            'as' => 'Asamblea', 
                                                'ch' => 'Charla', 
                                                'ci' => 'Cine', 
                                                'cl' => 'Colación', 
                                                'co' => 'Congreso', 
                                                'cu' => 'Curso', 
                                                'jo' => 'Jornada', 
                                                'ju' => 'Juicio', 
                                                'mu' => 'Muestra', 
                                                'ms' => 'Musicales', 
                                                'pl' => 'Presentación Libro', 
                                                'te' => 'Teatro', 
                                                'vc' => 'Videoconferencia',
                                                'ot' => '-- Otro --'
                                                );


$select_options['entry.organiza'] = array(      '' => '', 
                                                'U' => 'UNS', 
                                                'S' => 'Secretaría', 
                                                'O' => 'Otro'
					);

$vocab["entry.organiza"] = "Organiza";

$vocab["entry.event"] = "Evento";
$vocab["entry.mayordomia"] = "Requerimientos Mayordomía";
$vocab["entry.audiovisuales"] = "Audiovisuales";
$vocab["entry.contact"] = "Contacto";
$vocab["entry.contact_phone"] = "Teléfono";
$vocab["entry.contact_mail"] = "Correo electrónico";
$vocab["entry.contact_entity"] = "Entidad";

$mail_settings['admin_lang'] = 'es';
$disable_automatic_language_changing = 1;
$default_language_tokens = "es";
$override_locale = "esp"; 
// ------------------------------ //
$mail_settings['debug'] = TRUE;
// Set this to TRUE if you do not want any email sent, whatever the rest of the settings.
// This is a global setting that will override anything else.   Useful when testing MRBS.
$mail_settings['disabled'] = FALSE;
$mail_settings['admin_on_bookings']  = TRUE;
$mail_settings['booker'] = FALSE;
$mail_settings['details'] = TRUE;
$mail_settings['html']  = TRUE;

$mail_settings['admin_backend'] = 'smtp';
$mail_settings['admin_lang'] = 'es';
$smtp_settings['host'] = 'ssl://smtps.uns.edu.ar';  // SMTP server (smtps.uns.edu.ar)
$smtp_settings['port'] = 465;           // SMTP port number
$smtp_settings['auth'] = TRUE;        // Whether to use SMTP authentication
$smtp_settings['username'] = 'lacoqui';       // Username (if using authentication)
$smtp_settings['password'] = 'atomo22';       // Password (if using authentication)
$mail_settings['from'] = 'lacoqui@uns.edu.ar';
//$mail_settings['recipients'] = 'mpereyra@criba.edu.ar, pdmd2012@gmail.com';
$mail_settings['recipients'] = 'lacoqui@uns.edu.ar;reservasalones@uns.edu.ar';
$mail_settings['debug'] = FALSE;
?>
