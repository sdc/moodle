<?PHP // $Id$ 
      // auth.php - created with Moodle 1.2 development (2004021500)


$string['auth_dbdescription'] = '�Ըչ���繡����ҹ�����Ź͡㹡�õ�Ǩ�ͺ ��� ����������ʼ�ҹ ��鹶١��ͧ������� ����ҡ account �ѧ������� ���������� �����Ũж١����ѧ��ǹ��ҧ� � Moodle';
$string['auth_dbextrafields'] = '��ͧ�����������������  �س����ö���͡�� ��ҷ���к� �������͹ �ҡ  <b>�ҹ�����Ź͡</b><p>  ����ҡ �������ҧ ������ �к������͡�� ��� default  <p> ��� ����ͧ�ó� ���������ö������䢤�ҵ�ҧ� �� �����ѧ�ҡ ��͡�Թ';
$string['auth_dbfieldpass'] = '��ǹ����բ����Ţͧ  password ';
$string['auth_dbfielduser'] = '��ǹ����բ����Ţͧ usernames';
$string['auth_dbhost'] = '�������������� �纰ҹ������';
$string['auth_dbname'] = '���ͧ͢�ҹ������';
$string['auth_dbpass'] = 'password �ç�Ѻ username';
$string['auth_dbpasstype'] = '�к��ٻẺ������㹪�ͧ��� password  ����� MD5 encrption �ջ���ª��㹡�õԴ��͡Ѻ�������èѴ���������� �� PostNuke';
$string['auth_dbtable'] = '���ͧ͢���ҧ㹰ҹ������';
$string['auth_dbtitle'] = '��ҹ�����Ź͡';
$string['auth_dbtype'] = '�������ͧ�ҹ������(�٢�������������ҡ  <A HREF=../lib/adodb/readme.htm#drivers>ADOdb documentation</A> )';
$string['auth_dbuser'] = 'Username �������ö������ҹ�ҹ��������';
$string['auth_emaildescription'] = '㹡����Ѥ�����Ҫԡ��� �����Ѻ���͹��ѵ� ��ҹ������ ����� default �ͧ�к� ����ͼ������Ѥ� ������͡ ���� ��� ���� ��ҹ���� �к��зӡ������������ѧ ������ͧ������� ����������� �ԧ�� ��Ѻ��ѧ˹����ѡ�ͧ page ��觨��繡���׹�ѹ��� ������ѧ����������ԧ  ��ѧ�ҡ��鹼���� ����ö ��͡�Թ ������� ��� ���� ��ҹ ���';
$string['auth_emailtitle'] = '���Ը�͹��ѵԼ�ҹ������';
$string['auth_imapdescription'] = '���Ըա�� ����������� �� IMAP ���������';
$string['auth_imaphost'] = 'IMAP ����������� �� �Ţ  IP ������Ţ DNS ';
$string['auth_imapport'] = '�����Ţ���� IMAP �»��� ���  143 ���� 993.';
$string['auth_imaptitle'] = '�� IMAP server';
$string['auth_imaptype'] = 'IMAP servers  ����ö�� �Ը� authentication ��� negotiation ���ᵡ��ҧ�';
$string['auth_ldap_bind_dn'] = '����ҡ��ͧ����� bind-user ���ͤ���Ҽ��������� ����ö �кشѧ���仹��  \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = '��������Ѻ bind-user.';
$string['auth_ldap_contexts'] = '��¡�÷������ª��ͧ͢�����㹹��  ����ö �¡ ��Ǣ������ͧ ���� �� �� \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = '�Դ�����������ö���ҧ��ͤ����ͺ�Ѻ�ҧ��������µ��ͧ�� 
�س�����繵�ͧ����ͤ�������� ldap_context-variable, Moodle �Ф�������ѵ��ѵ�
';
$string['auth_ldap_creators'] = '��¡�á�����������͹حҵ�������ö���ҧ��ѡ�ٵ������� ����ö��������¡���� ��������ͧ���� \';\' 
�ѧ������ҧ  \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = '�к� LDAP host ��  \'ldap://ldap.myorg.com/\' ����  \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = '�س���ѵԢͧ��Ҫԡ����ͧ����� ������  \'member\'';
$string['auth_ldap_search_sub'] = '����� &lt;&gt; 0 ����ҡ��ͧ��� ���Ҽ�����ҹ��Ǣ������ ';
$string['auth_ldap_update_userinfo'] = '�Ѿഷ��������Ҫԡ (����,���ʡ��,�������..) �ҡ LDAP �֧  Moodle. ������������  /auth/ldap/attr_mappings.php ';
$string['auth_ldap_user_attribute'] = 'attribute �����㹡�ä��Ҫ��ͼ���� ��ǹ�˭����  \'cn\'.';
$string['auth_ldapdescription'] = '�Ըա��͹��ѵԡ����ҹ��ҹ  external LDAP server ����ҡ ���� ��� ���ʷ������ҹ�鹶١��ͧ Moodle �зӡ�����ҧ ��ª�����Ҫԡ����㹰ҹ������  ����Ŵѧ����� ����ö ��ҹ attribute �ͧ��Ҫԡ�ҡ LDAP  ��� ����ҷ���ͧ���� moodle ��ǧ˹��  ��ѧ�ҡ��� ������͡�Թ ����ա���� �����������ʼ�ҹ��ҹ�� ';
$string['auth_ldapextrafields'] = '��ͧ�����������������  �س����ö���͡�� ��ҷ���к� �������͹ �ҡ  <b>LDAP fileds</b><p>  ����ҡ �������ҧ ������ ������ա�ô֧�����Ũҡ LDAP �к������͡�� ��� default � moodle <p> ��� ����ͧ�ó� ���������ö������䢤�ҵ�ҧ� �� �����ѧ�ҡ ��͡�Թ';
$string['auth_ldaptitle'] = '�� LDAP server';
$string['auth_manualdescription'] = '�Ըա�ù������͹حҵ�����������ö���ҧ�ѭ�ռ������µ��ͧ�� ��蹤�� �����Ũ��繤�ŧ����¹��Ҫԡ���';
$string['auth_manualtitle'] = '�������к���ҹ��';
$string['auth_multiplehosts'] = '����ö�����ʵ����� � ���ŧ� �� host1.com;host2.com;host3.com';
$string['auth_nntpdescription'] = '�Ըչ���� ���� ������ʼ�ҹ��Ҷ١��ͧ������� ���� NNTP server ';
$string['auth_nntphost'] = 'NNTP server �� �Ţ IP  ����� DNS ';
$string['auth_nntpport'] = 'Server port (119 ����ǹ�˭�)';
$string['auth_nntptitle'] = '�� NNTP server';
$string['auth_nonedescription'] = '���������ö ��͡�Թ ������ҧ account ����ѹ�� ������ͧ���Ըա�â�͹��ѵ� �������Ҫԡ�ҡ�ҹ�����Ź͡ ����ͧ�׹�ѹ��ҹ������  ������ѧ㹡�����͡����ո� ��� ���� ��� �к�������ʹ��¹���չ��� ';
$string['auth_nonetitle'] = '����ͧ��͹��ѵ� ͹حҵ�ѹ��';
$string['auth_pop3description'] = '�礪��� ���������Ҷ١��ͧ������� ��ҹ�ҧ  POP3 server ';
$string['auth_pop3host'] = 'POP3 server �� �Ţ IP  ����� DNS ';
$string['auth_pop3port'] = 'Server port (110 �·����)';
$string['auth_pop3title'] = '�� POP3 server';
$string['auth_pop3type'] = '�������ͧ��������� ������������ ��  certificate security ������͡ pop3cert.';
$string['auth_user_create'] = '͹حҵ����������ҧ��';
$string['auth_user_creation'] = '͹حҵ���������������ö���ҧ�ѭ�ռ������еͺ�׹�ѹ�� ���͹حҵ �ô�������任�Ѻ���к� moodule-specific ������͡ user creation ����';
$string['auth_usernameexists'] = '�ռ������͹����к����� ��س����͡��������';
$string['authenticationoptions'] = '�Ըա��͹��ѵԡ������Ҫԡ';
$string['authinstructions'] = '�س����ö�������šѺ����� ����й��Ըա���� ��ҹ��ǹ��� ����������Һ��� username ��� ���ʼ�ҹ �ͧ����ͧ������� ��ͤ������س�к����ǹ���л�ҡ� � ˹�� login  ����ҡ�س�������ҧ��� ��������Ըա�����ҡ�';
$string['changepassword'] = '����¹���� URL';
$string['changepasswordhelp'] = '�س����ö�к��ԧ�� �����������ö������¹ ���� �� ���� ��� ���ʼ�ҹ�� ������ա����� �ԧ��ѧ����ǨйӼ������ѧ˹�� ��͡�Թ ���˹�Ң�������ǹ��� ���ҡ���������� �����ѧ����Ǩ�����ҡ�';
$string['chooseauthmethod'] = '���͡�Ըա��͹��ѵ�';
$string['guestloginbutton'] = '���� login ����Ѻ�ؤ�ŷ����';
$string['instructions'] = '�Ը���';
$string['md5'] = '�������Ẻ MD5  ';
$string['plaintext'] = '���˹ѧ��͸�����';
$string['showguestlogin'] = '�س����ö��͹�����ʴ����� Login ����Ѻ�ؤ�ŷ�����˹�� ��͡�Թ�� ';

?>
