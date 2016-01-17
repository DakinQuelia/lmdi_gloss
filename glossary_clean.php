<?php
// glossary_clean.php
// (c) 2016 - LMID - Pierre Duhem
// PHP script deleting all entries created by the extension in the database
// To be used if the normal desactivation/data deletion doesn't work. 
// Don't forget to empty the cache afterwards. 

define('IN_PHPBB', true);
$phpbb_root_path = '../../../';
// $phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$sql1 = "DELETE FROM ${table_prefix}acl_roles WHERE role_name LIKE 'ROLE_GLOSS%'";
$sql2 = "DELETE FROM ${table_prefix}acl_options WHERE auth_option LIKE '%lmdi_glossary'";
$sql3 = "DELETE FROM ${table_prefix}config WHERE config_name LIKE 'lmdi_glossary%'";
$sql4 = "ALTER TABLE ${table_prefix}users DROP COLUMN lmdi_gloss";
$sql5 = "DELETE FROM ${table_prefix}ext WHERE ext_name = 'lmdi/gloss'";
$sql6 = "DROP TABLE ${table_prefix}glossary";

echo ("Request 1 = $sql1.<br>\n");
echo ("Request 2 = $sql2.<br>\n");
echo ("Request 3 = $sql3.<br>\n");
echo ("Request 4 = $sql4.<br>\n");
echo ("Request 5 = $sql5.<br>\n");
echo ("Request 6 = $sql6.<br>\n");

$db->sql_query($sql1);
$db->sql_query($sql2);
$db->sql_query($sql3);
$db->sql_query($sql4);
$db->sql_query($sql5);
$db->sql_query($sql6);
$cache->purge ();

?>