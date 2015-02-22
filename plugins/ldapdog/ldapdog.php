<?php
chdir('../../');
include_once('./include/auth.php');
include_once('./include/global.php');
include_once($config['include_path'] . '/top_header.php');
?>

<div class="panel panel-primary">
	<div class="panel-heading">Create New User Group</div>
	<div class="panel-body">
		<span class="label label-default">Group Name</span>
		<input type="text" id="new-group-name"/>
		<span class="label label-default">Specific LDAP DN</span>
		<input type="text" id="new-group-ou"/>
		<button type="button" class="btn btn-xs" onclick="createNewLDAP()">Create</button>
	</div>
</div>

<?php

class LDAPDogGroup{
	public $ldap_name;
	public $ldap_ou;
	public $graph_tree_ids;
}

function getGroupPermission(){

	$hinet_groups = array();
        $tmp_hinet_group = null;
        $sql  = 'SELECT * FROM ldapdog_items ORDER BY name ';
        $result = db_fetch_assoc($sql);
        foreach($result as $row){
		$tmp_hinet_group = new LDAPDogGroup;
		$tmp_hinet_group->ldap_name = $row["name"];
		$tmp_hinet_group->ldap_ou   = $row["ou"];
		$tmp_hinet_group->graph_tree_ids = array();
		$hinet_groups[$row["ldap_id"]] = $tmp_hinet_group;
	} 

        $sql  = 'SELECT * FROM ldapdog_item2tree_perms';
        $result = db_fetch_assoc($sql);
        foreach($result as $row){
		$tmp_hinet_group = $hinet_groups[$row["ldap_id"]];
		array_push($tmp_hinet_group->graph_tree_ids,$row["graph_tree_id"]);
	}
	return $hinet_groups;
}

function getGraphTree(){

	$trees = array();
        $sql  = 'SELECT * FROM graph_tree';
        $result = db_fetch_assoc($sql);
        foreach($result as $row){
		$trees[$row["id"]] = $row["name"];
	} 
	return $trees;
}

$trees = getGraphTree();
$ldapdog_groups = getGroupPermission();
?>

<?php foreach($ldapdog_groups as $ldapdog_ldap_id => $ldapdog_group): ?>
        <ul class="list-group" id="ldapdog_group_<?php echo $ldapdog_ldap_id ?>">

                <li class="list-group-item list-group-item-info">
                        <h4>
                           <span class="label label-default">Group Name</span>
                           <?php echo $ldapdog_group->ldap_name?>
                        </h4>
                        <div>
                           <span class="label label-default">Specific LDAP DN</span>
                           <span class="ldapdog_group_ou"><?php echo $ldapdog_group->ldap_ou?></span>
                        </div>
                </li>

		<li class="list-group-item">
			<input type="text" class="new_ou"/>
			<button class="btn btn-xs btn-primary" onclick="changeObjectUnit(this)">Modify LDAP DN</button>
			<select class="graph_trees">
			<?php foreach($trees as $tree_id => $tree_name): ?>
				<option value="<?php echo $tree_id?>"><?php echo $tree_name?></option>
			<?php endforeach; ?>
			</select>
			<button type="button" class="btn btn-xs btn-primary" onclick="addObjectUnit(this)">Add</button>
			<button type="button" class="btn btn-xs btn-primary"  onclick="removeLDAP(this)">Delete</button>
			<input type="hidden" class="ldapdog_group_id" value="<?php echo $ldapdog_ldap_id ?>"/>
                </li>

		<?php foreach($ldapdog_group->graph_tree_ids as $graph_tree_id): ?>
                <li class="list-group-item ldapdog_dn">
                           <?php echo $trees[$graph_tree_id]?>
                           <button type="button" class="btn btn-xs btn-info pull-right" onclick="delObjectUnit(this)">Delete</button>
                           <input type="hidden" class="graph_tree_id" value="<?php echo $graph_tree_id ?>"/>
                           <input type="hidden" class="ldapdog_group_id" value="<?php echo $ldapdog_ldap_id ?>"/>
                </li>
		<?php endforeach; ?>
	</ul>
<?php endforeach; ?>

<?php
include_once($config['include_path'] . '/bottom_footer.php');
?>
