<?PHP // $Id$ 
      // auth.php - created with Moodle 1.6 development (2005052400)


$string['alternatelogin'] = 'Pokia� sem vlo��te nejak� URL, bude pou�it� ako prihlasovacia str�nka k tomuto syst�mu. T�to Va�a str�nka by mala obsahova� formul�r s vlastnos�ou \'action\' nastavenou na <strong>\'$a\'</strong>, ktor� vracia pole <strong>username</strong> a <strong>password</strong>.<br />Dbajte na to, aby ste vlo�ili platn� URL! V opa�nom pr�pade by ste mohli komuko�vek vr�tane seba zamedzi� pr�stup k t�mto str�nkam.<br />Ak chcete pou��va� �tandardn� prihlasovaciu str�nku, nechajte toto pole pr�zdne.';
$string['alternateloginurl'] = 'Alternat�vne URL pre prihl�senie';
$string['auth_cas_baseuri'] = 'URI serveru (alebo ni�, pokia� nie je baseUri)<br />Ak je napr. CAS server dostupn� na  host.domena.sk/CAS/ potom nastavte<br />cas_baseuri = CAS/';
$string['auth_cas_create_user'] = 'Pokia� chcete vlo�i� CAS autentifikovan�ch pou��vate�ov do Moodle datab�zy, mus�te zapn�� t�to vo�bu. Pokia� ju nezapnete, bud� sa m�c� prihl�si� len pou��vatelia ktor� u� existuj� v datab�ze Moodle.';
$string['auth_cas_enabled'] = 'Pokia� chcete pou��va� CAS autentifik�ciu, mus�te zapn�� t�to vo�bu';
$string['auth_cas_hostname'] = 'Adresa CAS serveru<br />napr. server.domena.sk';
$string['auth_cas_invalidcaslogin'] = 'Prep��te, nepodarilo sa V�m prihl�si� - nemohli ste by� autorizovan�';
$string['auth_cas_language'] = 'Vybran� jazyk';
$string['auth_cas_logincas'] = 'Zabezpe� spojenie';
$string['auth_cas_port'] = 'Port CAS serveru';
$string['auth_cas_server_settings'] = 'Konfigur�cia CAS serveru';
$string['auth_cas_text'] = 'Zabezpe�en� spojenie';
$string['auth_cas_version'] = 'Verzia CAS';
$string['auth_casdescription'] = 'T�to met�da pou��va CAS server (Central Authentication Service) pre autentifik�ciu u��vate�ov v prostred� jednotn�ho syst�mu prihlasovania (Single Sign On - SSO). Tie� m��ete pou�i� jednoduch� LDAP autentifik�ciu. Pokia� je zadan� meno a heslo platn� oproti CAS, Moodle vytvor� nov�ho u��vate�a v datab�ze, pri�om si potrebn� u��vate�sk� �daje, vezme z datab�zy LDAP. Pri nasleduj�cich prihl�seniach s� u� kontrolovan� len prihlasovacie meno a heslo.';
$string['auth_castitle'] = 'Pou�i� CAS server (SSO)';
$string['auth_common_settings'] = 'Be�n� nastavenia';
$string['auth_data_mapping'] = 'Zobrazenie �dajov';
$string['auth_dbdescription'] = 'T�to met�da vyu��va extern� datab�zov� tabu�ku na kontrolu platnosti dan�ho u��vate�sk�ho mena a hesla. Ak je to nov� konto, m��u by� do prostredia Moodle prenesen� inform�cie aj z in�ch pol��ok.';
$string['auth_dbextrafields'] = 'Tieto pol��ka s� nepovinn�. Je tu mo�nos�, aby niektor� u��vate�sk� pol��ka v prostred� Moodle uv�dzali inform�cie z <b>pol��ok extern�ch datab�z</b>, ktor� tu zad�te.<br />
Ak toto pol��ko nech�te pr�zdne, bude tu uv�dzan� p�vodn� nastavenie.<br />
V obidvoch pr�padoch, bude m�c� u��vate� po prihl�sen� upravova� v�etky tieto pol��ka.';
$string['auth_dbfieldpass'] = 'N�zov pol��ka obsahuj�ceho hesl�';
$string['auth_dbfielduser'] = 'N�zov pol��ka obsahuj�ceho u��vate�sk� men�';
$string['auth_dbhost'] = 'Po��ta� hos�uj�ci datab�zov� server';
$string['auth_dbname'] = 'Vlastn� n�zov datab�zy';
$string['auth_dbpass'] = 'Heslo pre uveden�ho u��vate�a';
$string['auth_dbpasstype'] = '�pecifkujte form�t, ktor� pou��va pol��ko pre heslo. MD5 �ifrovanie je vhodn� pre pripojenie k �al��m be�n�m web aplik�ci�m ako PostNuke';
$string['auth_dbtable'] = 'N�zov tabu�ky v datab�ze';
$string['auth_dbtitle'] = 'Pou�i� extern� datab�zu';
$string['auth_dbtype'] = 'Datab�zov� typ (bli��ie vi� <a href=\"../lib/adodb/readme.htm#drivers\">ADOdb dokument�cia</a>)';
$string['auth_dbuser'] = 'U��vate�sk� meno s pr�stupom do datab�zy len na ��tanie.';
$string['auth_editlock'] = 'Uzamknut� hodnota';
$string['auth_editlock_expl'] = '<p><b>Uzamknut� hodnota:</b>Ak je ak�vna, zabr�ni tomu, aby u��vatelia a administr�tori Moodle priamo upravovali toto pol��ko. T�to vo�bu pou�ite, ak uchov�vate �daje v externom auth syst�me.</p>';
$string['auth_emaildescription'] = 'Emailov� potvrdzovanie je prednastaven� sp�sob overovania. Ke� sa u��vate� prihl�si, vyberie si vlastn� nov� u��vate�sk� meno a heslo a dostane potvrdzuj�ci email na svoju emailov� adresu. Tento email obsahuje bezpe�n� linku na str�nku, kde m��e u��vate� potvrdi� svoje nastavenie. Pri �al��ch prihlasovaniach iba skontroluje u��vate�sk� meno a heslo v porovnan� s �dajmi ulo�en�mi v Moodle datab�ze.';
$string['auth_emailtitle'] = 'Emailov� overovanie';
$string['auth_fccreators'] = 'Zoznam skup�n, ktor�ch �lenovia maj� opr�vnenie na vytv�ranie nov�ch kurzov. Ak ide o viacer� skupiny, odde�te ich \';\'. Men� musia bz� nap�san� presne tak, ako na FirstClass serveri. Syst�m zoh�ad�uje p�sanie mal�ch a ve�k�ch p�smen.';
$string['auth_fcdescription'] = 'T�to met�da pou��va FirstClass server na skontrolovanie spr�vnosti pou��vate�sk�ho mena a hesla.';
$string['auth_fcfppport'] = 'Port servera (3333 je najbe�nej��)';
$string['auth_fchost'] = 'Adresa FirstClass servera. Pou�ite IP adresu alebo meno DNS.';
$string['auth_fcpasswd'] = 'Heslo pre hore uveden� u��vate�sk� ��et';
$string['auth_fctitle'] = 'Pou�i� FirstClass server';
$string['auth_fcuserid'] = 'Odstr�nenie u��vate�sk�ho ��tu z FirstClass servera s nastaven�m privil�gia \'Ved�aj�� administr�tor\'.';
$string['auth_imapdescription'] = 'Na kontrolu spr�vnosti dan�ho u��vate�sk�ho mena a hesla pou��va t�to met�da IMAP server.';
$string['auth_imaphost'] = 'Adresa IMAP serveru. Pou��vajte ��slo IP, nie n�zov DNS.';
$string['auth_imapport'] = '��slo IMAP server portu. Zvy�ajne je to 143 alebo 993.';
$string['auth_imaptitle'] = 'Pou�i� IMAP server';
$string['auth_imaptype'] = 'Typ IMAP serveru.  IMAP servery m��u ma� rozli�n� typy overovania.';
$string['auth_ldap_bind_dn'] = 'Ak chcete pou��va� spoluu��vate�ov, aby ste mohli h�ada� u��vate�ov uve�te to tu. Napr�klad: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_bind_pw'] = 'Heslo pre spoluu��vate�ov.';
$string['auth_ldap_bind_settings'] = 'Spolo�n� nastavenia ';
$string['auth_ldap_contexts'] = 'Zoznam prostred�, kde sa nach�dzaj� u��vatelia. Odde�te rozli�n� prostredia s \';\'. Napr�klad: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Ak umo�n�te vytv�ranie u��vate�ov s emailov�m potvrdzovan�m, �pecifikujte kontext, kde bud� u��vatelia vytvoren�. Tento kontext by mal by� in�, ako pre ostatn�ch u��vate�ov v z�ujme bezpe�nosti. Nepotrebujete prida� tento kontext do premennej ldap-context, Moodle bude vyh�ad�va� u��vate�ov z tohto kontextu automaticky.<br />
<b>Pozor!</b> Mus�te upravi� funkciu auth_user_create() v s�bore auth/ldap/lib.php, aby mohli by� vytvoren� nov� u��vatelia.';
$string['auth_ldap_creators'] = 'Zoznam skup�n, ktor�ch �lenovia maj� dovolen� vytv�ra� nov� kurzy. Jednotliv� skupiny odde�ujte bodko�iarkou. Oby�ajne nie�o ako cn=ucitelia,ou=ostatni,o=univ\'';
$string['auth_ldap_expiration_desc'] = 'Vyberte si \"Nie\", aby sa deaktivovalo kontrolovanie neakt�vneho hesla alebo LDAP na ��tanie passwordexpiration �asu priamo z LDAP';
$string['auth_ldap_expiration_warning_desc'] = 'Po�et dn� pred t�m, ako sa objav� upozornenie o vypr�an� platnosti hesla';
$string['auth_ldap_expireattr_desc'] = 'Nepovinn�: potla�� ldap-vlastnosti, ktor� uchov�vaj�  �as do vypr�ania hesla  asswordAxpirationTime';
$string['auth_ldap_graceattr_desc'] = 'Nepovinn�: Potla�� vlastnos� gracelogin';
$string['auth_ldap_gracelogins_desc'] = 'Umo�ni� podporu LDAP gracelogin. Po tom, ako vypr�� platnos� hesla, u��vate� sa m��e prihl�si�, pok�m nie je hodnota gracelogin 0. Aktiv�ciou tohto nastavenia zobraz�te spr�vu o grace login, ak vypr�� platnos� hesla.';
$string['auth_ldap_host_url'] = '�pecifikujte hostite�a LDAP v podobe URL tj. \'ldap://ldap.myorg.com/\' alebo \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_login_settings'] = 'Nastavenia prihlasovania';
$string['auth_ldap_memberattribute'] = '�pecifikujte �lensk� atrib�t u��vate�a, ke� u��vatelia patria do skup�n; oby�ajne je to \'member\'';
$string['auth_ldap_objectclass'] = 'Nepovinn�: potla�� funkciu objectClass pou��van� na h�adanie u��vate�ov na ldap_user_type. Zvy�ajne t�to vo�bu nepotrebujete meni�.';
$string['auth_ldap_opt_deref'] = 'T�to vo�ba ur�uje, ako sa zaobch�dza s aliasmi pri h�adan�. Vyberte jednu z nasleduj�cich hodn�t: \"Nie\"(LDAP_DEREF_NEVER) alebo \"�no\"(LDAP_DEREF_ALWAYS)';
$string['auth_ldap_passwdexpire_settings'] = 'LDAP nastavenia pri vypr�an� platnosti hesla';
$string['auth_ldap_search_sub'] = 'Uve�te hodnotu <> 0 ak chcete h�ada� u��vate�ov v subkontextoch.';
$string['auth_ldap_server_settings'] = 'LDAP nastavenia servera';
$string['auth_ldap_update_userinfo'] = 'Aktualizova� inform�cie o u��vate�ovi (krstn� meno, priezvisko, adresa...) z LDAP do Moodle. H�ada� v /auth/ldap/attr_mappings.php pre prira�uj�ce inform�cie.';
$string['auth_ldap_user_attribute'] = 'Vlastnos� pou��van� na h�adanie mien u��vate�ov. Zvy�ajne \'cn\'.';
$string['auth_ldap_user_settings'] = 'Nastavenia vzh�adu u��vate�a';
$string['auth_ldap_user_type'] = 'Vyberte si, ako bud� u��vatelia uchov�van� v LDAP. Toto nastavenie tie� �pecifikuje, ako bude fungova� vytv�ranie nov�ch u��vate�ov, grace logins a vypr�anie platnosti hesla.';
$string['auth_ldap_version'] = 'Verzia LDAP protokolu ';
$string['auth_ldapdescription'] = 'T�to met�da poskytuje overovanie s LDAP serverom. 

Ak je u��vate�sk� meno a heslo spr�vne, Moodle vytvor� nov�ho u��vate�a v svojej datab�ze. 	  Tento modul dok�e ��ta� u��vate�sk� vlastnosti z LDAP a vyplni� �elan� pol��ka v Moodle. 

Pre nasleduj�ce prihlasovania sa kontroluj� iba u��vate�sk� meno a heslo.';
$string['auth_ldapextrafields'] = 'Tieto pol��ka s� nepovinn�. Je tak� mo�nos�, �e Moodle u��vate�sk� pol��ka bud� uv�dza� inform�cie z <b>LDAP pol��ok</b> ,ktor� tu ud�te.<br />
<p>Ak tu ni� neuvediete, inform�cie z LDAP nebud� preveden�, a namiesto toho bude uv�dzan� Moodle nastavenie.</p>
<p>V obidvoch pr�padoch bude m�c� u��vate� po prihl�sen� korigova� v�etky tieto pol��ka.</p>';
$string['auth_ldaptitle'] = 'Pou�i� LDAP server';
$string['auth_manualdescription'] = 'T�to met�da neumo��uje u��vate�om vytv�ra� vlastn� kont�. V�etky kont� mus� manu�lne vytvori� administr�tor.';
$string['auth_manualtitle'] = 'Len manu�lne kont�';
$string['auth_multiplehosts'] = 'Tu m��u by� �pecifikovan� viacer� host OR adresy (napr. host1.com;host2.com;host3.com)alebo (napr.xxx.xxx.xxx.xxx;xxx.xxx.xxx.xxx)';
$string['auth_nntpdescription'] = 'Tento postup pou��va na kontrolu spr�vnosti u��vate�sk�ho mena a hesla NNTP server.';
$string['auth_nntphost'] = 'Adresa NNTP servera. Pou�ite ��slo IP, nie n�zov DNS.';
$string['auth_nntpport'] = 'Server port (119 je najbe�nej��)';
$string['auth_nntptitle'] = 'Pou�i� NNTP server';
$string['auth_nonedescription'] = 'U��vatelia sa m��u prihl�si� a vytvori� kont� bez overovania s extern�m serverom a bez potvrdzovania prostredn�ctvom emailu. Bu�te opatrn� pri tejto vo�be - myslite na bezpe�nos� a probl�my pri administr�cii, ktor� t�m m��u vznikn��.';
$string['auth_nonetitle'] = '�iadne overenie';
$string['auth_pamdescription'] = 'T�to met�da pou��va PAM na pr�stup do u��vate�sk�ch mien na tomto serveri. Mus�te si nain�talova� <a href=\"http://www.math.ohio-state.edu/~ccunning/pam_auth/\">PHP4 PAM Authentication</a>, aby ste mohli pou��va� tento modul.';
$string['auth_pamtitle'] = 'PAM (Pluggable Authentication Modules)';
$string['auth_passwordisexpired'] = 'Platnos� V�ho hesla vypr�ala. Chcete si zmeni� Va�e heslo teraz?';
$string['auth_passwordwillexpire'] = 'Platnos� V�ho hesla vypr�� o $a dn�. Chcete si zmeni� Va�e heslo teraz?';
$string['auth_pop3description'] = 'Tento postup pou��va  na kontrolu spr�vnosti u��vate�sk�ho mena a hesla POP3 server.';
$string['auth_pop3host'] = 'Adresa POP3 servera. Pou�ite ��slo IP , nie n�zov DNS.';
$string['auth_pop3mailbox'] = 'Meno po�tovej schr�nky, s ktorou by mohol by� nadviazan� kontakt (v��inou prie�inok doru�enej po�ty)';
$string['auth_pop3port'] = 'Server port (110 je najbe�nej��)';
$string['auth_pop3title'] = 'Pou��va� POP3 server';
$string['auth_pop3type'] = 'Typ servera. Ak v� server pou��va certifikovan� zabezpe�enie, vyberte si pop3cert.';
$string['auth_shib_convert_data'] = 'API pre �pravu �dajov';
$string['auth_shib_convert_data_description'] = 'Toto API (aplika�n� rozhranie) V�m umo��uje �alej upravova� �daje, ktor� m�te k dispoz�cii zo syst�mu Shibboleth. Viac infom�ci� <a href=\"../auth/shibboleth/README.txt\" target=\"_blank\">n�jdete tu</a>.';
$string['auth_shib_convert_data_warning'] = 'S�bor neexistuje alebo nie je �itateln� procesom web serveru!';
$string['auth_shib_instructions'] = 'Pou�ite <a href=\"$a\">prihl�senie cez Shibboleth</a>, pokia� Va�a in�tit�cia tento syst�m podporuje.<br />V opa�nom pr�pade pou�ite norm�lny formul�r pre prihl�senie.';
$string['auth_shib_instructions_help'] = 'Tu m��ete vlo�i� vlastn� inform�cie o Va�om syst�me Shibboleth. Bud� se zobrazova� na prihlasovacej str�nke. Vlo�en� inform�cie by maly obsahova� odkaz na zdroj chr�nen� syst�mom Shibboleth, ktor� presmeruje pou��vate�ov na \"<b>$a</b>\", tak�e sa pou��vatelia syst�mu Shibboleth bud� m�c� prihl�si� do Moodle. Ak ponech�te toto pole pr�zdne, bud� se na prihlasovacej str�nke zobrazova� v�eobecn� pokyny.';
$string['auth_shib_only'] = 'Len pre Shibboleth';
$string['auth_shib_only_description'] = 'Za�krtnite t�to vo�bu, pokia� si chcete vyn�ti� prihl�senie za pomoci syst�mu Shibboleth';
$string['auth_shib_username_description'] = 'N�zov premennej prostredia webserveru Shibboleth, ktor� m� by� pou�it� ako u��vate�sk� meno Moodle ';
$string['auth_shibboleth_login'] = 'Prihl�senie cez Shibboleth';
$string['auth_shibboleth_manual_login'] = 'Ru�n� prihl�senie';
$string['auth_shibbolethdescription'] = 'T�to met�da umo��uje vytv�ra� a overova� pou��vatelov pomocou syst�mu <a href=\"http://shibboleth.internet2.edu/\" target=\"_blank\">Shibboleth</a>.<br />
Uistite sa, �e ste si pre��tali s�bor <a href=\"../auth/shibboleth/README.txt\" target=\"_blank\">README</a> obsahuj�ci inform�cie o tom, ako nastavi� v� Moodle pre podporu syst�mu Shibboleth.';
$string['auth_shibbolethtitle'] = 'Shibboleth';
$string['auth_updatelocal'] = 'Aktualizova� miestne �daje';
$string['auth_updatelocal_expl'] = '<p><b>Aktualizova� miestne �daje:</b> Ak je t�to vo�ba akt�vna, pol��ko bude aktualizovan� (z externej autentifik�cie) zaka�d�m, ke� sa u��vate� prihl�si, alebo je tu synchroniz�cia u��vate�a. Pol��ka, ktor� by sa mali miestne aktualizova�, by mali by� uzamknut�.</p>';
$string['auth_updateremote'] = 'Aktualizova� extern� �daje';
$string['auth_updateremote_expl'] = '<p><b>Aktualizova� extern� �daje:</b> Ak je t�to vo�ba akt�vna, extern� autentifik�cia bude aktualizovan�, ke� sa aktualizuje z�znam o u��vate�ovi. Pol��ka by nemali by� uzamknut�, aby sa mohli upravova�.</p>';
$string['auth_updateremote_ldap'] = '<p><b>Pozn�mka:</b> Aktualiz�cia extern�ch LDAP �dajov si vy�aduje nastavenie binddn a bindpw spoluu��vate�om s pr�vom �pravy v�etk�ch z�znamov o u��vate�och. Moment�lne sa tu neuchov�vaj� vlastnosti viacer�ch hodn�t a pri aktualiz�cii sa odstr�nia nadbyto�n� hodnoty.</p>';
$string['auth_user_create'] = 'Umo�ni� vytv�ranie u��vate�ov';
$string['auth_user_creation'] = 'Nov� (anonymn�) u��vatelia m��u vytv�ra� u��vate�sk� kont� v externom prostred� a overova� ich cez email. Ak to umo�n�te, nezabudnite tie� konfigurova� �pecifick� vo�by pre jednotliv� moduly.';
$string['auth_usernameexists'] = 'Vybran� u��vate�sk� meno u� existuje. Pros�m, vyberte si in�.';
$string['authenticationoptions'] = 'Mo�nosti overovania';
$string['authinstructions'] = 'Tu m��ete uvies� pokyny pre u��vate�ov, aby vedeli, ak� u��vate�sk� meno a heslo maj� pou��va�. Text, ktor� tu vlo��te sa objav� na prihlasovacej str�nke. Ak to tu neuvediete, nebud� zobrazen� �iadne pokyny.';
$string['changepassword'] = 'Zmeni� heslo URL';
$string['changepasswordhelp'] = 'Tu m��ete uvies� miesto, na ktorom si Va�i u��vatelia m��u obnovi� alebo zmeni� u��vate�sk� meno/heslo, ak ho zabudli. Pre u��vate�ov to bude zobrazen� ako tla�idlo na prihlasovacej str�nke ich u��vate�skej str�nky. Ak to tu neuvediete, tla�idlo sa nezobraz�.';
$string['chooseauthmethod'] = 'Vyberte si postup overovania: ';
$string['forcechangepassword'] = 'Vy�adova� zmenu hesla';
$string['forcechangepassword_help'] = 'Vy�adova� od u��vate�ov zmenu hesla pri ich �al�om prihl�sen� do Moodle';
$string['forcechangepasswordfirst_help'] = 'Vy�adova� od u��vate�ov zmenu hesla pri ich prvom prihl�sen� do Moodle';
$string['guestloginbutton'] = 'Prihlasovacie tla�idlo pre hos�a';
$string['instructions'] = 'In�trukcie';
$string['md5'] = 'MD5 �ifrovanie';
$string['plaintext'] = '�ist� text';
$string['showguestlogin'] = 'M��ete skry�, alebo zobrazi� prihlasovacie tla�idlo pre hos�a na prihlasovacej str�nke.';
$string['stdchangepassword'] = 'Pou�i� �tandardn� str�nku pre zmenu hesla';
$string['stdchangepassword_expl'] = 'Ak extern� autentifika�n� syst�m povo�uje zmeny hesla v prostred� Moodle, prepnite t�to vo�bu na \"�no\". Toto nastavenie potla�� funkciu \"Zmeni� heslo URL\".';
$string['stdchangepassword_explldap'] = 'Pozn�mka: Odpor��a sa pou��vanie LDAP cez SSL �ifrovac� tunel (ldaps://), ak je LDAP server vzdialen�.';

?>
