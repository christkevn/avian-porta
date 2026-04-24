SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    DATA_TYPE,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_KEY
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'nama_schema_kamu'
ORDER BY TABLE_NAME, ORDINAL_POSITION;

----------------

# TABLE_NAME, COLUMN_NAME, DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY
'activity_logs', 'id', 'int', 'int(11)', 'NO', 'PRI'
'activity_logs', 'user_id', 'int', 'int(11)', 'YES', ''
'activity_logs', 'program_id', 'int', 'int(11)', 'YES', ''
'activity_logs', 'action', 'varchar', 'varchar(100)', 'YES', ''
'activity_logs', 'created_at', 'datetime', 'datetime', 'YES', ''
'activity_logs', 'ip_address', 'varchar', 'varchar(45)', 'YES', ''
'login_attempts', 'id', 'int', 'int(11)', 'NO', 'PRI'
'login_attempts', 'username', 'varchar', 'varchar(50)', 'YES', ''
'login_attempts', 'ip_address', 'varchar', 'varchar(45)', 'YES', ''
'login_attempts', 'attempt_count', 'int', 'int(11)', 'YES', ''
'login_attempts', 'last_attempt_at', 'datetime', 'datetime', 'YES', ''
'menus', 'id', 'int', 'int(11)', 'NO', 'PRI'
'menus', 'program_id', 'int', 'int(11)', 'NO', ''
'menus', 'name', 'varchar', 'varchar(100)', 'NO', ''
'programs', 'id', 'int', 'int(11)', 'NO', 'PRI'
'programs', 'name', 'varchar', 'varchar(100)', 'YES', 'UNI'
'programs', 'created_at', 'datetime', 'datetime', 'YES', ''
'sessions', 'id', 'varchar', 'varchar(255)', 'NO', 'PRI'
'sessions', 'user_id', 'bigint', 'bigint(20) unsigned', 'YES', 'MUL'
'sessions', 'ip_address', 'varchar', 'varchar(45)', 'YES', ''
'sessions', 'user_agent', 'text', 'text', 'YES', ''
'sessions', 'payload', 'longtext', 'longtext', 'NO', ''
'sessions', 'last_activity', 'int', 'int(11)', 'NO', 'MUL'
'settings', 'id', 'int', 'int(11)', 'NO', 'PRI'
'settings', 'name', 'varchar', 'varchar(15)', 'NO', ''
'settings', 'text', 'text', 'text', 'NO', ''
'settings', 'created_at', 'datetime', 'datetime', 'NO', ''
'settings', 'created_by', 'varchar', 'varchar(10)', 'NO', ''
'settings', 'updated_at', 'datetime', 'datetime', 'NO', ''
'settings', 'updated_by', 'varchar', 'varchar(10)', 'NO', ''
'users', 'id', 'int', 'int(10) unsigned', 'NO', 'PRI'
'users', 'username', 'varchar', 'varchar(20)', 'NO', ''
'users', 'password', 'text', 'text', 'NO', ''
'users', 'tipe', 'varchar', 'varchar(10)', 'NO', ''
'users', 'nama', 'varchar', 'varchar(50)', 'NO', ''
'users', 'email', 'varchar', 'varchar(50)', 'NO', ''
'users', 'level', 'varchar', 'varchar(20)', 'NO', ''
'users', 'cabang', 'varchar', 'varchar(10)', 'YES', ''
'users', 'aktif', 'int', 'int(1)', 'NO', ''
'users', 'created_at', 'datetime', 'datetime', 'NO', ''
'users', 'created_by', 'varchar', 'varchar(20)', 'NO', ''
'users', 'updated_at', 'datetime', 'datetime', 'NO', ''
'users', 'updated_by', 'varchar', 'varchar(20)', 'NO', ''
'users', 'password_expiry_at', 'datetime', 'datetime', 'YES', ''
'user_menu_permissions', 'id', 'int', 'int(11)', 'NO', 'PRI'
'user_menu_permissions', 'user_id', 'int', 'int(11)', 'NO', 'MUL'
'user_menu_permissions', 'menu_id', 'int', 'int(11)', 'NO', ''
'user_menu_permissions', 'can_view', 'tinyint', 'tinyint(1)', 'YES', ''
'user_menu_permissions', 'can_insert', 'tinyint', 'tinyint(1)', 'YES', ''
'user_menu_permissions', 'can_update', 'tinyint', 'tinyint(1)', 'YES', ''
'user_menu_permissions', 'can_delete', 'tinyint', 'tinyint(1)', 'YES', ''
'user_programs', 'id', 'int', 'int(11)', 'NO', 'PRI'
'user_programs', 'user_id', 'int', 'int(11)', 'NO', 'MUL'
'user_programs', 'program_id', 'int', 'int(11)', 'NO', ''

-------------------------------------
#todo
-fix pas login 419
-buatin semua menu dan controller dan viewnya unttuk masing masing table di atas sudah ada jadi tinggal bikin controllernya dan viewnya saja, untup insert + delete jadikan 1 blade saja


-untuk contohh blade pakai  
portal\resources\views\data_noo\index.blade.php
portal\resources\views\data_noo\form.blade.php

=kemungkinan menu yang dibutuhkan:
- Dashboard
- User Management
- Program Management
- Menu Management
- User Menu Permissions
- User Program Permissions
saja

#todo 
kalau selain user AD = 
login → cek expired
kalau expired → redirect ganti password
gagal 3 kali-> expired = now -> harus ganti password
----------------------


#todo 
selain user level
="SUPER"
->hide menu semua
-> sisa dashboard saja

sesuai user login 
-> orang ini bisa buka program mana ->
di buat buble shortcut ngaraah ke program itu nanti ngarah ke url yang ada di model program,

kalau user level super 
-> semua menu ada
dashboard sama sesuai user login 
-> orang ini bisa buka program mana ->
di buat buble shortcut ngaraah ke program itu nanti ngarah ke url yang ada di model program,