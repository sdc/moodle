<?PHP // $Id$ 
      // install.php - created with Moodle 1.4 alpha (2004081500)


$string['admindirerror'] = 'El directorio especificado para admin es incorrecto';
$string['admindirname'] = 'Directorio Admin';
$string['admindirsetting'] = '
<p>Muy pocos servidores web usan /admin como URL especial para permitirle acceder a un panel de control o similar. Desgraciadamente, esto entra en conflicto con la ubicaci�n est�ndar de las p�ginas de administraci�n de Moodle Usted puede corregir esto renombrando el directorio admin en su instalaci�n, y poniendo aqu� ese nuevo nombre. Por ejemplo: <blockquote> moodleadmin</blockquote>.
As� se corregir�n los enlaces admin en Moodle.</p>';
$string['chooselanguage'] = 'Seleccionar idioma';
$string['configfilenotwritten'] = 'El script instalador no ha podido crear autom�ticamente un archivo config.php con las especificaciones elegidas. Por favor, copie el siguiente c�digo en un archivo llamado config.php y coloque ese archivo en el directorio ra�z de Moodle.';
$string['configfilewritten'] = 'config.php se ha creado con �xito';
$string['configurationcomplete'] = 'Configuraci�n completa';
$string['database'] = 'Base de datos';
$string['databasesettings'] = ' <p>Ahora necesita configurar la base de datos en la que se almacenar� la mayor parte de datos de Moodle. Esta base de datos debe haber sido ya creada, y disponer de un nombre de usuario y de una contrase�a de acceso.</p>
<p>Tipo: mysql o postgres7<br />
Servidor: e.g., localhost or db.isp.com<br />
Nombre: Nombre de la base de datos, e.g., moodle<br />
Usuario: nombre de usuario de la base de datos<br />
Contrase�a: contrase�a de la base de datos<br />
Prefijo de tablas: prefijo a utilizar en todos los nombres de tabla</p>';
$string['dataroot'] = 'Datos';
$string['datarooterror'] = 'El ajuste \'Data\' es incorrecto';
$string['dbconnectionerror'] = 'Error de conexi�n con la base de datos. Por favor, compruebe los ajustes de la base de datos';
$string['dbcreationerror'] = 'Error al crear la base de datos. No se ha podido crear la base de datos con el nombre y ajustes suministrados';
$string['dbhost'] = 'Servidor';
$string['dbpass'] = 'Contrase�a';
$string['dbprefix'] = 'Prefijo de tablas';
$string['dbtype'] = 'Tipo';
$string['directorysettings'] = ' <p><b>WWW:</b>
Necesita decir a Moodle d�nde est� localizado. Especifique la direcci�n web completa en la que se ha instalado Moodle. Si su sitio web es accesible a trav�s de varias URLs, seleccione la que resulte de acceso m�s natural a sus estudiantes. No incluya la �ltima barra</p>
<p><b>Directorio:</b>
Especifique la ruta OS completa a esta misma ubicaci�n
Aseg�rese de que escribe correctamente may�sculas y min�sculas</p>
<p><b>Datos:</b>
Usted necesita un lugar en el que Moodle pueda guardar los archivos subidos. Este directorio debe ser legible Y ESCRIBIBLE por el usuario del servidor web (normalmente \'nobody\' o \'apache\'), pero no deber�a ser directamente accesible desde la web.</p>';
$string['dirroot'] = 'Directorio';
$string['dirrooterror'] = 'El ajuste de \'Directorio\' es incorrecto. Int�ntelo con el siguiente';
$string['wwwroot'] = 'WWW';
$string['wwwrooterror'] = 'El ajuste \'WWW\' es incorrecto';

?>
