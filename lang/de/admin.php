<?PHP // $Id$ 
      // admin.php - created with Moodle 1.6 development (2005060201)


$string['adminseesallevents'] = 'Adiministrator/innen sehen alle Termine';
$string['adminseesownevents'] = 'Administrator/innen sehen nur Termine eigener Kurse';
$string['blockinstances'] = 'Instnazen';
$string['blockmultiple'] = 'Mehrfach';
$string['cachetext'] = 'Dauer der G�ltigkeit f�r Cache';
$string['calendarsettings'] = 'Kalender';
$string['change'] = '�ndern';
$string['configallowcoursethemes'] = 'Mit der Aktivierung erlauben Sie die Auswahl kursspezifischer Themes. Kursthemes �berschreiben die Einstellungen f�r die gesamte Installation und von Nutzer/innen.';
$string['configallowemailaddresses'] = 'Wenn Sie die Nutzung bestimmter E-Mail-Adressen verbindlich vorgeben wollen, k�nnen Sie diese auf bestimmte Domains begrenzen. Tragen Sie dazu die zul�ssigen Domains ein, z.B. <strong>unserefirma.de</strong>.';
$string['configallowunenroll'] = 'Wenn Sie \'Ja\' w�hlen, haben Teilnehmer/innen die M�glichkeit, sich jederzeit selbst aus ihren Kursen auszutragen; andernfalls liegt das allein in der Hand der Kursleiter/innen oder Administrator/innen.';
$string['configallowuserblockhiding'] = 'Wollen Sie zulassen, dass Nutzer/innen selber Bl�cke ein-/und ausblenden k�nnen. Das Feature verwendet Javaskript und Cookies, um den Status zu speichern. Die Einstellung bezieht sich nur auf die Ansicht der Nutzer/innnen.';
$string['configallowuserthemes'] = 'Die Einstellung erlaubt Nutzer/innen, ein Theme auszuw�hlen. Damit wird das Theme der Installation �berschrieben, nicht aber kursspezifische Themeeinstellungen.';
$string['configallusersaresitestudents'] = 'Hier legen Sie den Zugriff zu den Lernaktivit�ten auf der Startseite der moodle-Installation fest. Wenn Sie \'Ja\' eintragen, kann jede/r best�tigte Teilnehmer/in, die Lernaktivit�ten auf der Startseite durchf�hren. Wenn Sie \'Nein\' eintragen, k�nnen nur Teilmnehmer/innen, die derzeit in mindestens einem Kurs eingetragen sind, die Lernaktivit�ten auf der Startseite ausf�hren. Nur die Administrator/innen und spezielle zugelassene Trainer/innen k�nnen die Aktivit�ten auf der Startseite einrichten und bearbeiten.';
$string['configautologinguests'] = 'Sollen G�ste automatisch eingeloggt werden, wenn der Kurs den Zugang f�r G�ste erlaubt?';
$string['configcachetext'] = 'Diese Einstellung kann gr��ere Sites (oder bei Verwendung von Textfiltern) erheblich beschleunigen. Textkopien werden in ihrer verarbeiteten Fassung f�r die festgelegte Zeit vorgehalten. Eine zu niedrige Einstellung kann sogar zu einer leichten Verlangsamung f�hren, bei einer zu hohen Einstellung kann die Aktualisierung der Texte (z.B. mit neuen Links) allerdings zu lange brauchen.';
$string['configclamactlikevirus'] = 'Behandle die Dateien wir virenhaltige Dateien';
$string['configclamdonothing'] = 'Behandle die Dateien als ok';
$string['configclamfailureonupload'] = 'Legen Sie fest was passieren soll, wenn Sie hochgeladene Dateien mit Clam-AV auf Viren �berpr�fen, dabei aber ein Fehler auftritt.
Wenn Sie ausw�hlen \'Behandle Dateien wie virenhaltige Dateien\', werden Sie in das Quarant�ne-Verzeichnis verschoben oder gel�scht. Wenn Sie w�hlen \'Behandle die Dateien als ok\' werden sie normal ohne Pr�fung hochgeladen. In jedem Fall werden Admins benachrichtigt, dass ein Problem aufgetreten ist. Seien Sie mit dieser Einstellung sehr vorsichtig.';
$string['configcountry'] = 'Wenn Sie hier ein Land einstellen, wird dieses Land als Vorgabe f�r neue Zug�nge gew�hlt.  Wenn die Nutzer/innen ein Land aktiv w�hlen sollen, lassen Sie das Feld einfach leer.';
$string['configdbsessions'] = 'Diese Einstellung verwendet die Datenbank auch dazu Informationen �ber aktuelle Sitzungen abzuspeichern. Das ist sinnvol bei sehr gro�en Anwendungen oder Anwendungen, die �ber mehrere Clustern von Servern verteilt arbeiten. Meist kann die Einstellung deaktiviert bleiben. Bei einer �nderung der Einstellung werden alle aktuellen Nutzer/innen ausgeloggt. Das gilt auch f�r Admins.';
$string['configdebug'] = 'Wenn Sie dies einschalten, werden die Fehlermeldungen von PHP erweitert, so dass mehr Warnungen ausgegeben werden. Dies ist nur n�tzlich f�r Entwickler.';
$string['configdeleteunconfirmed'] = 'Wenn Sie die Authentifikation per E-Mail verwenden, geben Sie hier den Zeitraum an, innerhalb dessen die Nutzer ihre Registrierung best�tigen m�ssen. Unbest�tigte Zug�nge verfallen danach und werden gel�scht.';
$string['configdenyemailaddresses'] = 'Definieren Sie hier Domains von denen keine E-Mail-Adressen akzeptiert werden. z.B. <strong>hotmail.com yahoo.de</strong>.';
$string['configdigestmailtime'] = 'Personen, die E-Mails als Digest (Zusammenfassung) eingerichtet haben, erhalten diese Zusammenfassung einmal t�glich. Mit dieser Einstellung legen Sie fest zu welchem Zeitpunkt. Beim darauf folgenden Cron-Aufruf werden die Zusammenfassungen mit versandt.';
$string['configdisplayloginfailures'] = 'Anzeige von Informationen �ber fr�here gescheiterte Logins der ausgew�hlten Nutzer.';
$string['configenablerssfeeds'] = 'Diese Einstellung aktiviert RSS-Feeds f�r die gesamte Seite. Es ist zus�tzlich erforderlich, RSS-Feeds in den einzelnen Modulen zu aktivieren. Gehen Sie dazu zu den Modul-Einstellungen in der Administration.';
$string['configenablerssfeedsdisabled'] = 'Diese Option ist nicht verf�gbar, weil die RSS-Ffeds f�r alle Seiten deaktiviert sind.  Um diese zu aktivieren, gehen sie zu den Variablen in der Administration';
$string['configerrorlevel'] = 'W�hlen Sie die Menge der PHP Warnungen, die Sie angezeigt bekommen m�chten. \'Normal\' ist meist eine gute Wahl.';
$string['configextendedusernamechars'] = 'Aktivieren Sie diese Einstellung, damit jedes Zeichen im Nutzernamen zul�ssig ist (Dies beeinflusst bereits vorhandene Namen nicht). In der Grundeinstellung sind nur alphanumerische Zeichen zul�ssig.';
$string['configfilterall'] = 'Filter �ber alle Begriffe, inkl. �berschriften, Titel, Navigationselemente, etc.) Dies kann im Zusammenhang mit dem Multilang-Filter n�tzlich sein. Es belastet den Server jedoch stark und kann zu einer Reduzierung der Arbeitsgeschwindigkeit f�hren.';
$string['configfilteruploadedfiles'] = 'Beim Aktivieren dieser Option werden alle hochgeladenen HTML- und Textdateien �ber den Filter bearbeitet bevor sie angezeigt werden.';
$string['configforcelogin'] = 'Normalerweise k�nnen die Startseite und die Kurs�bersicht (nicht jedoch die Kurse) eingesehen werden ohne sich einzuloggen. Aktivieren Sie diese Option, wenn ein Login zwingend sein soll, um IRGENDETWAS auf dieser Site ausf�hren zu k�nnen.';
$string['configforceloginforprofiles'] = 'Wenn Sie diese Einstellung aktivieren, m�ssen Teilnehmer/inenn sich erst anmelden, um die Profile der Trainer/innen einsehen zu k�nnen. In der Grundeinstellung (\'Nein\') k�nnen die Teilnehmer/innen sich vor der AnmeldungzumKurs �ber die Trainer/inenn informieren. Zugleich k�nnen auch Suchmaschinen auf diese Profile zugreifen.';
$string['configframename'] = 'Sofern Sie Moodle innerhalb eines Frames einbinden, tragen Sie hier den Namen des Frames ein. Anderenfalls sollte dieser Wert auf \'_top\' stehen.';
$string['configfullnamedisplay'] = 'Hier k�nnen Sie festlegen, wie Namen in Ihrer Vollform angezeigt werden. In den meisten F�llen wird die Grundeinstellung \"Vorname + Nachname\" geeignet sein. Sie k�nnen jedoch auch die Nachnamen ausblenden, ganz nach Ihren Konventionen.';
$string['configgdversion'] = 'Zeigt Ihnen die installierte Version von GD an. Die angezeigte Version wurde automatisch ermittelt. �ndern Sie diese nicht, es sei denn Sie wissen wirklich, was Sie tun. ';
$string['confightmleditor'] = 'W�hlen Sie aus, ob Sie die Verwendung des Text-Editors prinzipiell zulassen m�chten. Der Editor ist allerdings auf kompatible Browser angewiesen; sonst bleibt er unsichtbar. Die Nutzer k�nnen die Verwendung auch individuell ablehnen.';
$string['configidnumber'] = 'Diese Option legt fest, ob (a) Nutzer nicht nach einer ID Nummer gefragt werden, (b) Nutzer zwar nach einer ID Nummer gefragt werden, das Feld aber leer lassen k�nnen oder (c) Nutzer nach einer ID Nummer gefragt werden und dieses Feld nicht leer lassen k�nnen. Die ID Nummer des Nutzers wird in seinem Profil angezeigt.';
$string['configintro'] = 'Auf dieser Seite k�nnen Sie eine Anzahl von Konfigurations-Variablen spezifizieren, die Ihnen helfen, dass Moodle auf Ihrem Server zuverl�ssig arbeitet. Sorgen Sie sich nicht grossartig - die Standard-Einstellungen funktionieren normalerweise sehr gut.S ie k�nnen jederzeit auf diese Seite zur�ckkommen und Einstellungen �ndern.';
$string['configintroadmin'] = 'Auf dieser Seite sollten Sie den/die Hauptadminstrator/in einrichten, der/die die vollst�ndige Kontrolle �ber die Site hat. Achten Sie darauf, hier einen sicheren Benutzernamen samt Passwort sowie eine g�ltige E-Mail-Adresse anzugeben. Weitere Administrator/innen k�nnen Sie sp�ter festlegen.';
$string['configintrosite'] = 'Diese Seite erlaubt es Ihnen, die Startseite und den Namen der neuen Site einzurichten. Sie k�nnen sp�ter �ber die Startseite (Konfiguration > Seiteneinstellungen) hierher zur�ckkehren und die Einstellungen jederzeit bearbeiten.';
$string['configintrotimezones'] = 'Diese Seite sucht nach neuen Enstellungen f�r Zeitzonen, inkl. neue Regelungen f�r die Sommerzeit, und aktualisiert die Datenbank. Dabei werden die Orte gepr�ft: $a. Der Vorgang ist normalerweise sehr sicher und beeintr�chtigt Ihre Installation nicht. Wollen Sie die Zeitzonen nun aktualisieren?';
$string['configlang'] = 'W�hlen Sie die Standard-Sprache f�r die gesamte Seite. Die Benutzer k�nnen diese sp�ter �berschreiben.';
$string['configlangcache'] = 'Speichern der Sprachmenus. Spart eine Menge Speicherund Prozessleistung. Mit der Aktivierung dauert es ein paar Minuten zur Aktualisierung wenn Sprachen gel�scht oder hinzugef�gt wurden.';
$string['configlangdir'] = 'In den meisten Sprachen schreibt man von links nach rechts, aber in einigen anderen, wie Arabisch oder Hebr�isch, schreibt man von rechts nach links.';
$string['configlanglist'] = 'Lassen Sie dieses Feld leer, um allen Nutzern zu erlauben, aus jeder installierten Sprache auszuw�hlen. Sie k�nnen ebenso das Sprachmen� verk�rzen, indem Sie eine durch Kommas getrennte Liste der Spachcodes angeben, die Sie f�r die Auswahl m�chten. Zum Beispiel en, es_es, fr, it.';
$string['configlangmenu'] = 'W�hlen Sie aus, ob Sie generell das Sprach-Auswahlmen� auf Ihrer Startseite angezeigt haben m�chten, auf der Anmeldungsseite etc. Dies betrifft nicht die M�glichkeit des Nutzers, seine bevorzugte Sprache in seinem Profil einzustellen.';
$string['configlocale'] = 'W�hlen Sie eine f�r die gesamte Seite g�ltige Region (Zeitzone) - diese wird die Anzeige jedes Datums beeinflussen. Sie m�ssen die Daten dieser Region auf Ihrem Betriebssystem installiert haben. Sofern Sie nicht wissen, was Sie ausw�hlen sollen, lassen Sie dieses Feld leer.';
$string['configloginhttps'] = 'Wenn Sie diese Einstellung aktivieren wird eine sichere https Verbindung f�r den login-Prozessgenutzt. Danach wird eine normale http Verbindung genutzt.
ACHTUNG: Die Einstellung erfordert eine gesonderte Aktivierung von https auf dem Server. Wenn diese NICHT besteht, k�nnen Sie sich selber vom Zugriff zur Seite ausschlie�en!!!';
$string['configloglifetime'] = 'Dies definiert die Zeitdauer, f�r die die Statistiken der Nutzer-Aktivit�ten gespeichert werden. �ltere Statistiken werden automatisch gel�scht. Speichern Sie diese Daten nur so lange, wie sie unbedingt ben�tigt werden. Wenn Sie einen ausgelasteten Server haben und Geschwindigkeitseinbr�che feststellen, sollten Sie den Statistik-Zeitraum reduzieren.';
$string['configlongtimenosee'] = 'Wenn sich Teilnehmer/innen nach einer sehr langen Zeit nicht mehr angemeldet haben, werden Sie automatisch nach dieser Zeit aus dem Kurs ausgetragen.';
$string['configmaxbytes'] = 'Dieser Wert legt f�r die gesamte Site die maximale Gr��e f�r das Hochladen von Dateien fest. Der Eintrag wird begrenzt durch die PHP-Einstellung \'upload_max_filesize\' und die Apache-Einstellung \'LimitRequestBody\'. Diese Rahmeneinstellung begrenzt also auch die maximal w�hlbare Gr��e auf Kurs- oder Modulebene.';
$string['configmaxeditingtime'] = 'Hier bestimmen Sie die Zeitspanne, in der die Teilnehmer/innen die Foren-Beitr�ge, Journal-Antworten usw. erneut bearbeiten d�rfen. Normalerweise sind 30 Minuten ein guter Wert. ';
$string['configmessaging'] = 'Soll das Messaging-System ziwschen Nutzer/innen aktiviert werden?';
$string['confignoreplyaddress'] = 'Tragen Sie hier die E-Mail-Adresse ein, die als Absender beim Versand von Nachrichten (z.B. aus foren) genutzt werden soll, wenn die E-Mailadresse des Trainers nicht f�r R�ckantworten genutzt werden kann.';
$string['confignotifyloginfailures'] = 'E-Mail Benachrichtigungen k�nnen versandt werden, wenn Login-Fehler aufgezeichnet wurden. Wer sollte die Nachrichten sehen?';
$string['confignotifyloginthreshold'] = 'Nach wie vielen gescheiterten Anmeldeversuchen soll eine Benachrichtigung erfolgen (nur wenn diese auch aufgezeichnet werden)?';
$string['configopentogoogle'] = 'Wenn Sie diese Option aktivieren, wird Google erlaubt, Ihre Seite als Gast zu besuchen. Au�erdem werden Besucher, die �ber einen Link von Google kommen, automatisch als \'G�ste\' eingeloggt. Dies gilt nat�rlich nur f�r Kurse, die G�ste (ohne Schl�ssel) zulassen.';
$string['configpathtoclam'] = 'Pfad f�r Clam-AV. Zumeist /usr/bon/clamscan oder user/bin/clamdscan. Die Einstellung ist erforderlich, damit Clam-AV gefunden wird.';
$string['configproxyhost'] = 'Wenn dieser <B>Server</B> einen Proxy braucht (beispielsweise eine Firewall), um Internetzugriff zu bekommen, dann tragen Sie hier den Namen und den Port des Proxys ein. Anderenfalls lassen sie das Feld leer.';
$string['configquarantinedir'] = 'Wenn Clam-AV infizierte Dateien in ein Quarant�ne-Verzeichnis verschieben soll, definieren Sie das Verzeichnis hier. Wenn Sie den Eintrag leer lassen, das Verzeichnis ung�ltig ist oder nicht beschrieben werden kann, werden infizierte Dateien gel�scht. Tragen Sie keinen Slash am Ende ein.';
$string['configrunclamonupload'] = 'Clam-AV f�r hochgeladene Dateien nutzen? Sie m�ssen zus�tzlich einen Pfad zu Clam-AV in pathtoclam eintragen. Clam-Av ist ein freier Virenscanner (http.//www.clamav.net).';
$string['configsectioninterface'] = 'Gestaltung';
$string['configsectionmail'] = 'E-Mail';
$string['configsectionmaintenance'] = 'Wartung';
$string['configsectionmisc'] = 'Verschiedenes';
$string['configsectionoperatingsystem'] = 'Arbeitsweise';
$string['configsectionpermissions'] = 'Rechte';
$string['configsectionsecurity'] = 'Sicherheit';
$string['configsectionuser'] = 'Nutzer/innen';
$string['configsecureforms'] = 'Moodle kann einen zus�tzlichen Grad an Sicherheit verwenden, wenn es Daten von Web-Formularen erh�lt. Sofern dies eingeschaltet ist, dann wird die Variable HTTP_REFERER gegen
die Adresse des aktuellen Formulars gepr�ft.
In einigen wenigen F�llen kann das Probleme verursachen, wenn der Nutzer eine Firewall benutzt (z.B. Zonealarm), die so konfiguriert ist, das der HTTP_REFERER nicht mitgesendet wird.
Das Ergebnis ist, dass Sie bei einem Formular nicht weiterkommen.
Sofern Nutzer z.B. Probleme mit der Zugangsseite haben, sollten Sie diese Einstellung deaktivieren, so ist Ihre Seite allerdings offener f�r Brute-Force-Attacken. Im Zweifelsfall, belassen Sie es auf \'ja\'.';
$string['configsessioncookie'] = 'Diese Einstellung legt den Namen des Cookies, der f�r Moodle Zugriffe benutzt wird fest. Dieser Eintrag ist optional und nur sinnvoll, um zu verhindern das mehrere Cookies sich �berlagern. Dies kann der Fall sein, wenn mehrere Moodle-Systeme auf der gleichen Webseite installiert sind. ';
$string['configsessiontimeout'] = 'Wenn eingeloggte Benutzer l�nger keine Aktionen ausf�hren (Seiten laden), werden sie automatisch ausgeloggt. Diese Variable legt die betreffende Zeitspanne fest.';
$string['configshowblocksonmodpages'] = 'Enige Lernaktivit�ten erlauben, die Nutzung von Bl�cken innerhalb der Aktivit�t. Mit diesr Einstellung erm�glichen Sie den Trainer/inenn auf der Kursseite diese Bl�cke in die Lernaktivit�t einzuf�gen. Andernfalls steht diese Option nicht zur Verf�gung.';
$string['configshowsiteparticipantslist'] = 'Alle auf dieser Seite angezeigten Teilnehmer/innen und Trainer/innen werden in der Teilnehmer/innen-Liste aufgef�hrt. Wer soll die Teilnehmerliste einsehen d�rfen?';
$string['configsitepolicy'] = 'Wenn Sie eine Zustimmungserkl�rung verwenden, die alle Nutzer/innen bei der Nutzung akzeptieren m�ssen, k�nnen Sie hier die URL f�r diese Seite festlegen. Dies kann z.B. im Verzeichnis der Startseite sein. z.B. http://IhreDomain.de/file.php/1/zustimmung.html';
$string['configslasharguments'] = 'Dateien (Bilder, Dokumente usw.) k�nnen �ber ein Skript, das \'Slash-Argumente\' benutzt (zweite Option) einfacher in Internet-Browsern, Proxy-Servern usw. zwischengespeichert werden.
Leider erlauben nicht alle PHP-Server diese Methode, so dass Sie, sofern Sie Probleme bei der Anzeige von Dateien oder Bildern (beispielsweise den Benutzer-Fotos) haben, diese Variable auf die erste Option stellen m�ssen. ';
$string['configsmtphosts'] = 'Geben Sie hier den vollen Namen von einem oder mehreren lokalen SMTP-Servern an, die Moodle f�r den E-Mail-Versand benutzen soll (beispielsweise \'E-Mail.a.de\' oder \'E-Mail.a.de;E-Mail.b.de\'). Wenn Sie dieses frei lassen, wird Moodle die Standard-Methode von PHP zum Senden von E-Mails verwenden.';
$string['configsmtpuser'] = 'Sofern Sie einen SMTP-Server angegeben haben und der Server Zugangsdaten erfordert, dann geben Sie hier Benutzernamen und Passwort an.';
$string['configteacherassignteachers'] = 'Sollen Trainer/innen ihren Kursen selbst weitere Kolleg/innen zuordnen k�nnen=? Falls \'Nein\', k�nnen nur der Kursersteller und Administrator/innen den Kursen Trainer/innen zuordnen.';
$string['configthemelist'] = 'Wenn das Feld leer bleibt kann jedes Theme ausgew�hlt werden. Wenn ads Ausawhlmenus f�r Themes verk�rzt werden soll, k�nnen Sie hier die ausw�hlbaren Themes eintragen, Trennen Sie die Namen der Themes mit Kommas; z.B.: standard,orangewhite. Achten Sie darauf den Wortzwischenraum nicht zu benutzen';
$string['configtimezone'] = 'Stellen Sie hier die bevorzugte Zeitzone ein. Sie steuert die Zeitanzeige in Ihren Kursen. Jeder Teilnehmer kann selbst in seinem Profil eine eigene Zeitzone einstellen und damit Ihre Voreinstellung f�r sich aufheben. Die Einstellung \"Serverzeit\"  verwendet hier die Zeiteinstellung Ihres Internetservers. Die Einstellung \"Serverzeit\" im Nutzerprofil hingegen greift auf die Einstellung Ihres Moodleprogramms an dieser Stelle zur�ck.';
$string['configunzip'] = 'Geben Sie hier den Pfad zum Programm unzip an (Nur Unix). Dieser wird f�r das Entpacken von ZIP-Archiven auf dem Server ben�tigt.';
$string['configvariables'] = 'Variablen konfigurieren';
$string['configwarning'] = 'Vorsicht bei der Ver�nderung dieser Einstellungen, ungeeignete Werte k�nnen zu Problemen f�hren.';
$string['configzip'] = 'Geben Sie hier den Pfad zum Programm zip an (nur Unix). Dieser wird f�r die Erstellung ZIP-Archiven auf dem Server ben�tigt.';
$string['confirmation'] = 'Best�tigung';
$string['cronwarning'] = 'Das <a href=\"cron.php\">Cron-Skript</ a> wurde in den letzten 24 Stunden nicht ausgef�hrt. <br />Die  <a href=\"../doc/?frame=install.html&sub=cron\">Installations Dokumentation</a> erl�utert wie Sie diesen Vorgang automatisieren k�nnen.';
$string['edithelpdocs'] = 'Hilfedateien bearbeiten';
$string['editstrings'] = 'Menutexte bearbeiten';
$string['filterall'] = 'Alle Begriffe filtern';
$string['filteruploadedfiles'] = 'Filter f�r hochgeladene Dateien';
$string['helpadminseesall'] = 'Sollen Admins alle  Kalendereintr�ge sehen oder nur die, die sie betreffen?';
$string['helpcalendarsettings'] = 'Konfiguration verschiedener Kalender und datums- und zeitbezogener Einstellungen';
$string['helpforcetimezone'] = 'Sie k�nenn Nutzer/innen erlauben eine eigene Zeitzone einzustellen oder die verwendete Zeitzone fest definieren.';
$string['helpsitemaintenance'] = 'F�r Upgrades und andere Arbeiten am System';
$string['helpstartofweek'] = 'An welchem Tag soll die Woche im Kalender beginnen?';
$string['helpupcominglookahead'] = 'Wie viele Tage im voraus sollen k�nftige Termine gesucht werden?';
$string['helpupcomingmaxevents'] = 'Wie viele Termine sollen maximal als k�nftige Termine angezeigt werden?';
$string['helpweekenddays'] = 'Welche Tage der Woche sollen als Wochenende farbig hervorgehoben werden?';
$string['importtimezones'] = 'Update der Zeitzonenliste';
$string['importtimezonescount'] = '$a->count Eintr�ge importiert von $a->source';
$string['importtimezonesfailed'] = 'Keine Daten gefunden (schlechte Nachricht)';
$string['incompatibleblocks'] = 'Inkompatible Bl�cke';
$string['optionalmaintenancemessage'] = 'Optionale Wartungsinformation';
$string['pleaseregister'] = 'Registrieren Sie Ihre Seite, um diesen Button zu entfernen';
$string['sitemaintenance'] = 'Die Seite wird zur Zeit �berarbeitet und steht f�r kurze Zeit nicht zur Verf�gung.';
$string['sitemaintenancemode'] = 'Wartungsmodus';
$string['sitemaintenanceoff'] = 'Der Wartungsmodus wurde wieder abgeschaltet. Die Seite steht wieder zur Verf�gung.';
$string['sitemaintenanceon'] = 'Die Seite befindet sich zur Zeit im Wartungsmodus. Nur Admins k�nnen die Seite nutzen.';
$string['sitemaintenancewarning'] = 'Die Seite befindet sich zur Zeitim Wartungsmodus. Nur Admins k�nnen sie nutzen und sich einloggen. Um zum Normalmodus zur�ck zu kehren, klicken Sie auf <a href=\"maintenance.php\">Wartungsmodus abschalten</a>.';
$string['tabselectedtofront'] = 'Tabellen mit Tabulatoren: soll die Reihe mit dem aktiven Tabulator im Vordergrund plaziert werden?';
$string['therewereerrors'] = 'Es geht Fehler in Ihren Daten';
$string['timezoneforced'] = 'Dies wurde durch die Administration festgelegt.';
$string['timezoneisforcedto'] = 'F�r alle Nutzer/innen festlegen.';
$string['timezonenotforced'] = 'Nutzer/innen k�nnen eine eigene Zeitzone ausw�hlen.';
$string['upgradeforumread'] = 'In der Version 1.5 k�nnen Sie Forenbeitr�ge als gelesen/ungelesen markieren.<br />F�r dieseFunkton m�ssen die Datenbanktabellen aktualisiert werden. <a href=\"$a\">Tabellen jetzt aktualisieren</a>.';
$string['upgradeforumreadinfo'] = 'Mit einer neuen Funktion in moodle 1.5 k�nnen Forenbeitr�ge als gelesen/ungelesen markiert werden. Um diese Funktion zu verwenden, m�ssen die Datenbanktabellen aktualisiert werden. Je nach Gr��e der Datenbank kann dieser Vorgang l�ngere Zeit (Stunden) erfordern. F�hren Sie diesen Vorgang am besten in Zeiten mit wenigen Zugriffen aus. Die Seite funktioniert w�hrend der Umstellung weiter. Die Nutzer/innen bemerken davon nichts. Wenn Sie den Vorgang einmal gestartet haben, darf er nicht unterbrochen werden. Lassen Sie das Browserfenster dabei offen. Solten Sie das Browser-Fenster versehentlich schlie�en, k�nnen Sie den Prozess neu starten. <br />Wollen Sie nun starten?';
$string['upgradelogs'] = 'F�r die vollst�ndige Funktionsf�higkeit m�ssen die alten Log-Daten aktualisiert werden. <a href=\"$a\">More information</a>';
$string['upgradelogsinfo'] = 'Die Art und Weise in der Log-Daten gespeichert werden wurde ver�ndert. Damit Sie Ihre alten Log-Daten mit den Einzelaktivit�ten einsehen k�nnen, m�ssen die alten Log-Daten aktualisiert werden. Je nachdem wie viele Daten auf Ihrer Seite gespeichert sind, kann dieser Vorgang eine l�ngere Zeit beanspruchen (u.U. mehrere Stunden). Der Vorgang beansprucht die Datenbank bei umfangreichen Seiten stark. Wenn Sie den Vorgang einmal gestartet haben, m�ssen Sie ihn ohne Unterbrechung abschlie�en lassen. Das Browserfenster darf nicht geschlossen und die Internetverbindung nicht unterbrochen werden in dieser Zeit. Der Zugriff auf Ihre Seite durch andere Anwender ist dadurch nicht beeintr�chtigt. <br /><br>Wollen Sie nun Ihre Log-Daten aktualisieren?';
$string['upgradesure'] = 'Moodledateien wurden ver�ndert. Ihre Installation von moodle wird auf die Version $a aktualisiert.
<p>Wenn Sie dies tun, k�nnen Sie nicht zu einer fr�heren Version zur�ckkehren.</p>
<p>Sind Sie sicher, dass Sie das Update ausf�hren wollen?</p>';
$string['upgradingdata'] = 'Daten aktualisieren';
$string['upgradinglogs'] = 'Log-Daten aktualisieren';

?>
