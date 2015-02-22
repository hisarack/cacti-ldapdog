<?php
chdir('../../');
include_once('./include/global.php');

function createNewLDAPId(){
	
	if($_GET["ldap_name"] == null)return;
	if($_GET["ldap_ou"]   == null)return;

	$ldap_name = $_GET["ldap_name"];
	$ldap_ou   = $_GET["ldap_ou"];

	$sql  = 'SELECT * FROM ldapdog_items WHERE ou = "'.$ldap_ou.'"';
        $result = db_fetch_assoc($sql);
        if(count($result) != 0){return;}
	db_execute('INSERT INTO ldapdog_items(name,ou) values("'.$ldap_name.'","'.$ldap_ou.'")');

}


function removeLDAPId(){
	
	if($_GET["ldap_id"] == null)return;

	$ldap_id = $_GET["ldap_id"];

        $sql  = 'SELECT * FROM ldapdog_items WHERE ldap_id = '.$ldap_id;
        $result = db_fetch_assoc($sql);
        if(count($result) == 0){return;}
	db_execute('DELETE FROM ldapdog_items WHERE ldap_id = '.$ldap_id);
	db_execute('DELETE FROM ldapdog_item2tree_perms WHERE ldap_id = '.$ldap_id);
}


function addObjectUnit(){

	if($_GET["ldap_id"] == null)return;
	if($_GET["graph_tree_id"]   == null)return;

	$ldap_id = $_GET["ldap_id"];
	$graph_tree_id = $_GET["graph_tree_id"];

	$sql  = 'SELECT * FROM ldapdog_items WHERE ldap_id = '.$ldap_id;
        $result = db_fetch_assoc($sql);
        if(count($result) == 0){return;}
	$sql  = 'SELECT * FROM ldapdog_item2tree_perms WHERE graph_tree_id = '.$graph_tree_id. ' AND ldap_id = '.$ldap_id;
        $result = db_fetch_assoc($sql);
        if(count($result) != 0){return;}
	db_execute('INSERT INTO ldapdog_item2tree_perms(ldap_id,graph_tree_id) values('.$ldap_id.','.$graph_tree_id.')');
	showObjectUnits($ldap_id);
}

function delObjectUnit(){

	if($_GET["ldap_id"] == null)return;
	if($_GET["graph_tree_id"]   == null)return;

	$ldap_id = $_GET["ldap_id"];
	$graph_tree_id = $_GET["graph_tree_id"];

	$sql  = 'SELECT * FROM ldapdog_items WHERE ldap_id = '.$ldap_id;
        $result = db_fetch_assoc($sql);
        if(count($result) == 0){return;}
	db_execute('DELETE FROM ldapdog_item2tree_perms WHERE ldap_id = '.$ldap_id.' AND graph_tree_id = '.$graph_tree_id);
	showObjectUnits($ldap_id);
}

function chgObjectUnit(){

	if($_GET["ldap_id"] == null)return;
	if($_GET["new_ou"]  == null)return;

	$ldap_id = $_GET["ldap_id"];
	$new_ou  = $_GET["new_ou"];

	$sql  = 'SELECT * FROM ldapdog_items WHERE ldap_id = '.$ldap_id;
        $result = db_fetch_assoc($sql);
        if(count($result) == 0){return;}
	db_execute('UPDATE ldapdog_items SET ou = "'.$new_ou.'" WHERE ldap_id = '.$ldap_id);
}


function showObjectUnits($ldap_id){
	$ou_ary = array();
	$sql  = 'select ldap_id,graph_tree_id,name from ldapdog_item2tree_perms hl2t, graph_tree gt where hl2t.ldap_id = '.$ldap_id.' and hl2t.graph_tree_id = gt.id order by name';
        $result = db_fetch_assoc($sql);
	foreach($result as $row){array_push($ou_ary,$row);}
	echo json_encode($ou_ary);
}


$action = $_GET["action"];

if(strcmp($action,"CREATE_LDAP") == 0){ createNewLDAPId(); }
if(strcmp($action,"REMOVE_LDAP") == 0){ removeLDAPId(); }
if(strcmp($action,"ADD_LDAP_OU") == 0){ addObjectUnit(); }
if(strcmp($action,"DEL_LDAP_OU") == 0){ delObjectUnit(); }
if(strcmp($action,"CHG_LDAP_OU") == 0){ chgObjectUnit(); }
?>
