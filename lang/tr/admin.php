<?PHP // $Id$ 
      // admin.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2005033100)


$string['adminseesallevents'] = 'Y�neticiler b�t�n olaylar� g�r�r';
$string['configallowunenroll'] = 'Bu se�enek \'Evet\' ise ��renciler istedikleri zaman kendi kendilerine kurstan kay�tlar�n� sildirebilirler. Di�er durumda buna izin verilmez ve sadece y�neticiler ve e�itimciler bu i�i yapmal�d�r.';
$string['configcountry'] = 'Buradan bir �lke se�erseniz, yeni kullan�c�lar i�in bu �lke varsay�lan olarak se�ili olacakt�r. �lke se�meyi zorunlu tutmak istiyorsan�z, bu se�ene�i ayarlamay�n.';
$string['configdebug'] = 'Bu se�ene�i a��k tutarsan�z PHP\'deki error_reporting metodu daha fazla uyar� mesaj� g�sterecektir. Bu, geli�tiriciler i�in kullan��l�d�r.';
$string['configdeleteunconfirmed'] = 'Bu, email yetkilendirmesi kullan�yorsan�z, kullan�c�n�n ne kadar s�rede bu emali onaylamas� gerekti�ini belirtir. Bu s�reden sonra, onyalanma�� eski hesaplar silinecektir.';
$string['configdisplayloginfailures'] = 'Bu, se�ilen kullan�c�n�n �nceden yapm�� oldu�u giri� hatalar� hakk�nda ekranda bilgi g�sterir.';
$string['configextendedusernamechars'] = '��rencilerin kullan�c� adlar�nda iste�i herhangi bir karakteri se�ebilmesini istiyorsan�z bu ayar� etkinle�tirin. (Not: Ad� ve soyad�n� etkilemez, giri� i�in kullan�lan kullan�c� ad�n� etkiler) Bu ayar \'hay�r\' ise sadece ingilizceki alfan�merik karakterler kullan�labilecektir.';
$string['configgdversion'] = 'Kurulu olan GD s�r�m�n� se�iniz. Varsay�lan olarak se�ilen otomatik olarak alg�lanm��t�r. Ne yapt���n�z� bilmiyorsan�z buray� de�i�tirmeyiniz.';
$string['configlang'] = 'Sitenin tamam�nda ge�erli olan varsay�lan bir dil se�in. Kullan�c�lar daha sonra istedikleri dili se�ebilirler.';
$string['configlanglist'] = 'Kurulumla birlikte gelen dillerin herhangi birinin se�ilebilmesi i�in buray� bo� b�rak�n. Ancak dil men�s�n� k�s�tlamak istiyorsan�z buraya dil listesini virg�lle ay�rarak girin. �rnek: tr,fr,de,en_us';
$string['configlangmenu'] = 'Ana sayfa, giri� sayfas� vb. yerlerde dil men�s�n�n g�r�n�p g�r�nmeyece�ini belirtin. Bu, kullan�c�n�n kendi profilinde d�zenleyebilece�i dil tercihini etkilemeyecektir.';
$string['configlocale'] = 'Sitenin tamam�nda ge�erli olan yerelle�tirme kodunu girin. Bu, g�n bi�imini ve dilini etkileyecektir. ��letim sisteminde bu yerelle�tirmenin var olmas� gerekmektedir. E�er neyi se�ene�inizi bilmiyorsan�z bo� b�rak�n�z.
<br /> �rnekler: Linux i�in: de_DE, en_US, tr_TR; Windows i�in: turkish, german, spanish';
$string['configopentogoogle'] = 'Bu ayar etkinle�tirilirse, Google, siteye konuk kullan�c� olarak giri� yapabilecektir. Ek olarak, sitenize Google arac�l���yla gelen kullan�c�lar da konuk kullan�c� olarak giri� yapabileceklerdir. Not: Bu, zaten ziyaret�i giri�ine a��k olan kurslara eri�imi Google a��s�ndan �effafla�t�r�r.';
$string['configsectioninterface'] = 'Aray�z';
$string['configsectionmail'] = 'Mail';
$string['configsectionmaintenance'] = 'Bak�m';
$string['configsectionmisc'] = '�e�itli';
$string['configsectionoperatingsystem'] = '��letim Sistemi';
$string['configsectionpermissions'] = '�zinler';
$string['configsectionsecurity'] = 'G�venlik';
$string['configsectionuser'] = 'Kullan�c�';
$string['configsessioncookie'] = 'Bu se�enek Moodle oturumlar� i�in kullan�lan �erezlerin ad�n� ayarlar. Bu se�enek iste�e ba�l�d�r, ancak ayn� anda ayn� web sitesi birden �ok moodle kopyas� ile �al���yorsa bu se�enek olu�an kar���kl��� ortadan kald�r�r.';
$string['configsessiontimeout'] = 'Bu siteye giri� yapan kullan�c�lar uzun s�re i�lem yapmazlarsa (sayfalar� gezinmezse) ne kadar s�re i�inde oturum sona erecek?';
$string['configsmtphosts'] = 'Moodle\'nin email g�ndermesi i�in bir veya birden fazla SMTP sunucu girebilirsiniz (�r: \'mail.a.com\' veya \'mail.a.com;mail.b.com\'). Bu se�ene�i bo� b�rak�rsan�z PHP\'nin email g�nderirken kulland��� varsay�lan metot kullan�lacakt�r.';
$string['configsmtpuser'] = 'Yukar�da bir SMTP sunucu belirttiyseniz ve bu sunucu yetki istiyorsa buraya sunucu i�in kullan�c� ad� ve �ifreyi giriniz.';
$string['configunzip'] = 'Unzip program�n�n yerini belirtin (Sadece Unix i�in, iste�e ba�l�d�r). Belirtilirse, sunucuda zip ar�ivini a�mak i�in bu kullan�lacakt�r. Bo� b�rak�rsan�z, zip ar�ivini a�mak i�in dahili i�lemler kullan�lacakt�r.';
$string['configvariables'] = 'De�i�kenler';
$string['configwarning'] = 'Bu ayarlar� de�i�tirirken dikkatli olun. Bilmedi�iniz de�erleri girmeniz sorunlara sebep olabilir.';
$string['configzip'] = 'Zip program�n�n yerini belirtin (Sadece Unix i�in, iste�e ba�l�d�r). Belirtilirse, sunucuda zip ar�ivi olu�turmak i�in bu kullan�lacakt�r. Bo� b�rak�rsan�z, zip ar�ivi olu�turmak i�in dahili i�lemler kullan�lacakt�r.';
$string['confirmation'] = 'Onay';
$string['edithelpdocs'] = 'Yard�m belgelerini d�zenle';
$string['editstrings'] = '�fadeleri d�zenle';

?>
