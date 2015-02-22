
var createNewLDAP = function(){
	var ldap_name=$("#new-group-name").val();
	var ldap_ou=$("#new-group-ou").val();
	$.ajax({
		url:'./ldapdog_functions.php',
		type:'get',
		data:{"action":"CREATE_LDAP","ldap_name":ldap_name,"ldap_ou":ldap_ou},
		error: function(xhr){
			console.log("ajax error :"+xhr);
		},
		success: function(response){
                        location.reload();
		}
	});
}


var removeLDAP = function(e){
	var group_unit = $(e).parent();
	var ldap_id = $(group_unit).find(".ldapdog_group_id").val();
	$.ajax({
		url:'./ldapdog_functions.php',
		type:'get',
		data:{"action":"REMOVE_LDAP","ldap_id":ldap_id},
		error: function(xhr){
			console.log("ajax error :"+xhr);
		},
		success: function(response){
			$("ul#ldapdog_group_"+ldap_id).empty();
		}
	});
}


var changeObjectUnit = function(e){
	var group_unit = $(e).parent();
	var new_ou = $(group_unit).find(".new_ou").val();
	var ldap_id = $(group_unit).find(".ldapdog_group_id").val();
	$.ajax({
		url:'./ldapdog_functions.php',
		type:'get',
		data:{"action":"CHG_LDAP_OU","ldap_id":ldap_id,"new_ou":new_ou},
		error: function(xhr){
			console.log("ajax error :"+xhr.responseText);
		},
		success: function(response){
			$("ul#ldapdog_group_"+ldap_id+" span.ldapdog_group_ou").html(new_ou);
			showMessage(ldap_id,"Change LDAP DN Successfully","alert-success");
		}
	});
}


   
var addObjectUnit = function(e){
	var group_unit = $(e).parent();
	var graph_tree_id = $(group_unit).find(".graph_trees").val();
	var ldap_id = $(group_unit).find(".ldapdog_group_id").val();
	$.ajax({
		url:'./ldapdog_functions.php',
		type:'get',
		data:{"action":"ADD_LDAP_OU","ldap_id":ldap_id,"graph_tree_id":graph_tree_id},
		dataType:'json',
		error: function(xhr){
			console.log("ajax error :"+xhr.responseText);
		},
		success: function(response){
			rebuildObjectUnits(ldap_id,response);
			showMessage(ldap_id,"Add Group Successfully","alert-success");
		}
	});
}


var delObjectUnit = function(e){
	var tree_unit = $(e).parent();
	var graph_tree_id = $(tree_unit).find(".graph_tree_id").val();
	var ldap_id = $(tree_unit).find(".ldapdog_group_id").val();
	$.ajax({
		url:'./ldapdog_functions.php',
		type:'get',
		data:{"action":"DEL_LDAP_OU","ldap_id":ldap_id,"graph_tree_id":graph_tree_id},
		dataType:'json',
		error: function(xhr){
			console.log("ajax error :"+xhr.responseText);
		},
		success: function(response){
			rebuildObjectUnits(ldap_id,response);
			showMessage(ldap_id,"Delete Group Successfully","alert-success");
		}
	});
}
  
var rebuildObjectUnits = function(ldap_id,tree_list){
	$("ul#ldapdog_group_"+ldap_id+" .ldapdog_dn").remove();
	for(var tree_item_id in tree_list){
		var tree_item = tree_list[tree_item_id];
		var li = $('<li/>')
			.addClass('list-group-item')
			.addClass('ldapdog_dn')
			.append(tree_item["name"])
			.append('<button type="button" class="btn btn-xs btn-info pull-right" onclick="delObjectUnit(this)">Delete</button>')
			.append('<input type="hidden" class="graph_tree_id" value="'+tree_item["graph_tree_id"]+'"/>')
			.append('<input type="hidden" class="ldapdog_group_id" value="'+tree_item["ldap_id"]+'"/>');
		$("ul#ldapdog_group_"+ldap_id).append(li);
	}
}

var showMessage = function(ldap_id,msg,alert_type){
       var messageBox = $('<div/>')
                        .addClass('alert')
                        .addClass(alert_type)
                        .attr('role','alert')
                        .html(msg);
       console.log(messageBox);
       $("ul#ldapdog_group_"+ldap_id).after(messageBox);
       messageBox.fadeOut(3000);
}
