<?php

function plugin_ldapdog_install() {
   api_plugin_register_hook('ldapdog','top_header_tabs','ldapdog_show_tab','setup.php');
   api_plugin_register_hook('ldapdog','top_graph_header_tabs','ldapdog_show_tab', 'setup.php');
   api_plugin_register_hook('ldapdog','page_head','ldapdog_page_head','setup.php');
   api_plugin_register_hook('ldapdog','page_bottom','ldapdog_page_bottom','setup.php');

   api_plugin_register_hook('ldapdog','create_complete_graph_from_template','ldapdog_create_complete_graph_from_template','setup.php');
   api_plugin_register_hook('ldapdog','graphs_remove','ldapdog_graphs_remove','setup.php');
   api_plugin_register_hook('ldapdog','graphs_sql_where','ldapdog_graphs_sql_where','setup.php');

   api_plugin_register_realm('ldapdog','ldapdog.php','Manage LDAPDog',1);

   ldapdog_setup_database();
}

function plugin_ldapdog_uninstall() {
}

function plugin_ldapdog_check_config() {
   return true;
}

function plugin_ldapdog_version() {
   return ldapdog_version();
}

###################
#   Inner Functin #
###################

function ldapdog_setup_database(){
   
   $data = array();
   $data['columns'][] = array("name" => "ldap_id", "type" => "int(11)", "NULL" => false, "auto_increment" => true);
   $data['columns'][] = array("name" => "ou", "type" => "varchar(255)", "NULL" => true);
   $data['columns'][] = array("name" => "name", "type" => "varchar(255)", "NULL" => true);
   $data['primary'] = "ldap_id";
   $data['type'] = "MyISAM";
   api_plugin_db_table_create("ldapdog","ldapdog_items",$data);

   $data = array();
   $data['columns'][] = array("name" => "ldap_id", "type" => "int(11)", "NULL" => false);
   $data['columns'][] = array("name" => "graph_tree_id", "type" => "int(11)", "NULL" => false);
   $data['type'] = "MyISAM";
   api_plugin_db_table_create("ldapdog","ldapdog_item2tree_perms",$data);
}

function ldapdog_page_head() {
   global $config, $colors;
   if (substr_count($_SERVER["REQUEST_URI"], "ldapdog")) {
      print "\t<script type='text/javascript' src='" . $config['url_path'] . "plugins/ldapdog/js/jquery.min.js'></script>\n";
      print "\t<script type='text/javascript' src='" . $config['url_path'] . "plugins/ldapdog/js/bootstrap.min.js'></script>\n";
      print "\t<script type='text/javascript' src='" . $config['url_path'] . "plugins/ldapdog/js/ldapdog.js'></script>\n";
      print "\t<link href='" . $config['url_path'] . "plugins/ldapdog/css/bootstrap.min.css' rel='stylesheet'>\n";
   }
}

function ldapdog_page_bottom() {

}

function ldapdog_version() {
   return array(	
      'name'		=>	'ldapdog',
      'version'	=>	'0.1',
      'longname'	=>	'LDAP Authentication and Authorization for HiNet CHT',
      'author'	=>	'zonghua',
      'homepage'      => 	'http://docs.cacti.net/plugin:ldapdog',
      'email'		=>	'radiohead0401@gmail.com',
      'url'		=>	'http://docs.cacti.net/plugin:ldapdog');
}

function ldapdog_show_tab() {
   global $config;
   if (!api_user_realm_auth('ldapdog.php')) {return;}
   $tab_img="tab_ldapdog.png";
   if (basename($_SERVER['PHP_SELF']) == 'ldapdog.php'){$tab_img="tab_ldapdog_down.png";}
   print '<a href="'.$config['url_path'].'plugins/ldapdog/ldapdog.php"><img src="' . $config['url_path'] . 'plugins/ldapdog/images/'.$tab_img.'" alt="thold" align="absmiddle" border="0"/></a>';
}


#########################
#   Auditing Function   #
#########################


function ldapdog_create_complete_graph_from_template($save){

   $logdate = date("Y-m-d His");
   $fdate   = date("Ymd");
   $filename = "/tmp/changeLog.".$fdate;
   $fp = fopen($filename,"a+");
   fwrite($fp,$logdate.",CREATE_GRAPH,graph_id:".$save["id"]."&snmp:".$save["snmp_query_id"]."/".$save["snmp_index"]."\n");
   fclose($fp);
}

function ldapdog_graphs_remove($graphs){

   $logdate = date("Y-m-d His");
   $fdate   = date("Ymd");
   $filename = "/tmp/changeLog.".$fdate;
   $fp = fopen($filename,"a+");
   fwrite($fp,$logdate.",REMOVE_GRAPH,graph_id:".$graphs[0]["local_graph_id"]."\n");
   fclose($fp);
}

function ldapdog_graphs_sql_where($sql_where){
   if(!isset($_SESSION['sess_user_id'])){return "";}
$user =  db_fetch_assoc("SELECT * FROM user_auth WHERE id = " . $_SESSION["sess_user_id"]);
if(!isset($user)){ return "";}
if($user[0]["realm"] != 1){return "";} //It's not LDAP user
$ldap_sql = "AND graph_local.host_id IN "
   ."( SELECT host_id FROM graph_tree_items, user_auth_perms "
   ."WHERE user_auth_perms.item_id = graph_tree_items.host_id  AND user_auth_perms.user_id = ".$_SESSION['sess_user_id'].")";
return $ldap_sql;
}

?>
