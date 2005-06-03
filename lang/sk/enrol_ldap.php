<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.6 development (2005053000)


$string['description'] = '<p>Na kontrolu Va�ich z�pisov, m��ete pou�i� LDAP server. Predpokladom je, �e V� LDAP strom obsahuje skupiny, ktor� mapuj� kurzy a ka�d� z t�chto skup�n/kurzov obsahuje z�znamy o u��vate�och, ktor� mapuj� �tudentov.</p>
<p>Predpoklad� sa, �e kurzy s� definovan� ako skupiny v LDAP a ka�d� skupina m� viacero u��vate�sk�ch polo�iek  (<em>member</em> alebo <em>memberUid</em>), ktor� zabezpe�uj� jednozna�n� defin�ciu u��vate�a.</p>
<p>Aby ste mohli pou�i� LDAP zapisovanie, Va�i u��vatelia  <strong>musia</strong> ma� akt�vnu polo�ku idnumber. LDAP skupiny musia ma� idnumber v polo�k�ch pre u��vate�a, aby sa mohli zapisova� do kurzov. Toto bude pravdepodobne fungova� bez probl�mov, ak u� pou��vate LDAP Autentifik�ciu.</p>
<p>Z�pisy sa bud� aktualizova�, ak sa u��vate� prihl�si. Na synchroniz�ciu uchov�vania z�pisov, m��ete pou�i� aj skript. Pozrite sa do  <em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
<p>T�to vo�ba m��e by� nastaven� na automatick� vytv�ranie nov�ch kurzov, ak sa objavia nov� skupiny v LDAP.</p>';
$string['enrol_ldap_autocreate'] = 'Kurzy m��u by� vytv�ran� automaticky, ak existuj� z�pisy do kurzov, ktor� e�te neexistuj� v Moodle.';
$string['enrol_ldap_autocreation_settings'] = 'Nastavenia automatick�ho vytv�rania kurzov';
$string['enrol_ldap_bind_dn'] = 'Ak chcete pou�i� spoluu��vate�a na vyh�ad�vanie u��vate�ov, tu to definujte. Nie�o ako: \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Heslo pre spoluu��vate�a';
$string['enrol_ldap_category'] = 'Kateg�ria pre automaticky vytvoren� kurzy';
$string['enrol_ldap_course_fullname'] = 'Nepovinn�: LDAP polo�ky, z ktor�ch sa m� vybra� cel� meno';
$string['enrol_ldap_course_idnumber'] = 'Pl�n jednozna�n�ho identifik�tora v LDAP, oby�ajne  <em>cn</em> alebo <em>uid</em>. Odpor��a sa \"uzamkn��\" t�to hodnotu, ak pou��vate automatick� vytv�ranie kurzov.';
$string['enrol_ldap_course_settings'] = 'Nastavenia z�pisov do kurzov';
$string['enrol_ldap_course_shortname'] = 'Nepovinn�: LDAP polo�ky, z ktor�ch sa m� vybra� skr�ten� meno';
$string['enrol_ldap_course_summary'] = 'Nepovinn�: LDAP polo�ky, z ktor�ch sa m� vybra� sum�r';
$string['enrol_ldap_editlock'] = 'Uzamkn�� hodnotu';
$string['enrol_ldap_general_options'] = 'Hlavn� nastavenia';
$string['enrol_ldap_host_url'] = '�pecifikujte hos�ovsk� LDAP v URL forme, napr:  \'ldap://ldap.myorg.com/\'
alebo \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = 'Na vyh�ad�vanie kurzov sa pou��va objectClass. Oby�ajne  \'posixGroup\'.';
$string['enrol_ldap_search_sub'] = 'H�ada� ��astn�kov skupiny  v subkontextoch.';
$string['enrol_ldap_server_settings'] = 'Nastavenia LDAP servera';
$string['enrol_ldap_student_contexts'] = 'Zoznam kontextov, kde s� umiestnen� skupiny so z�pismi �tudentov. Rozdielne kontexty odde�te  \';\', napr:  \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Atrib�t u��vate�a, ke� u��vatelia patria (s� zap�san�) do skupiny. Oby�ajne \'member\'
alebo \'memberUid\'.';
$string['enrol_ldap_student_settings'] = 'Nastavenia z�pisov �tudentov';
$string['enrol_ldap_teacher_contexts'] = 'Zoznam kontextov, kde s� umiestnen� skupiny so z�pismi u�ite�ov. Rozdielne kontexty odde�te  \';\', napr:  \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Atrib�t u��vate�a, ke� u��vatelia patria (s� zap�san�) do skupiny. Oby�ajne \'member\'
alebo \'memberUid\'.';
$string['enrol_ldap_teacher_settings'] = 'Nastavenia z�pisov u�ite�ov';
$string['enrol_ldap_template'] = 'Nepovinn�: Pri automaticky vytv�ran�ch kurzoch sa m��u ich nastavenia kop�rova� zo �abl�ny kurzu.';
$string['enrol_ldap_updatelocal'] = 'Aktualizova� miestne �daje';
$string['enrol_ldap_version'] = 'Verzia LDAP protokolu, ktor� pou��va V� server.';
$string['enrolname'] = 'LDAP';

?>
