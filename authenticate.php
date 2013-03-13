date_default_timezone_set("GMT");
ini_set("display_errors","on");
foreach(glob("includes/*.class.php") as $class_filename) {
  require_once($class_filename);
}

SELECT passwd, id_member, id_group, lngfile, is_activated, email_address, additional_groups, member_name, password_salt, openid_uri, passwd_flood FROM members WHERE LOWER(member_name) = LOWER({string:user_name}) LIMIT 1