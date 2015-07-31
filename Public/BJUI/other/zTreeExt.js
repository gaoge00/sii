/**
 * 
 */
function makeTree(p_getjosnurl,p_param,p_treeid,p_optionid,p_optionname,p_treeobj)
{
	 var settingKeys = {
				async : {
					enable : true,//启用异步加载
					url :p_getjosnurl, //"__URL__/AjaxGetAllKeys", //异步请求地址
					type : "get",
					otherParam : p_param,
//					{
//						p_param
//						//"id" : '{$id}'
//					}
					dataFilter : null
				//autoParam:["id", "str1"], //需要传递的参数,为你在ztree中定义的参数名称
				//
				},
				view : {
					selectedMulti : false  //是否多选
				//,addDiyDom: M_AddDiyDom
				},
				check : {
					autoCheckTrigger : false, //自动验证
					chkboxType: { "Y" : "", "N" : "" },  //不级联父节点选择  
					chkStyle : "checkbox",
					enable : true,
					nocheckInherit : false
				},
				data : {
					key : {
						checked : "checked",
						children : "children",
						name : "name",
						title : "name"
					},
					simpleData : {
						enable : true,
						idKey : "id",
						pIdKey : "pid",
						rootPId : 0
					},
					keep : {
						parent : true,
						leaf : true
					},
				},
				callback : {
					onAsyncSuccess : zTreeOnAsyncSuccessKeys,
					onAsyncError : zTreeOnAsyncError,
					onCheck : getSelectedNodesKeys
				}
			};  
	 
	 
	 function zTreeOnAsyncError(event, treeId, treeNode) {
			alert("异步加载失败!");
		}

		  function zTreeOnAsyncSuccessKeys(event, treeId, treeNode, msg) {
			//var treeObj = $.fn.zTree.getZTreeObj("j_select_tree_keys");
			var treeObj = $.fn.zTree.getZTreeObj(p_treeid);
			treeObj.expandAll(true);
			getSelectedNodesKeys();
			//getRules();
		}  
		
		

		//所选节点  测试一下
		  function getSelectedNodesKeys() {
			  
				var selectedNode = $.fn.zTree.getZTreeObj(p_treeid)
						.getCheckedNodes();
				//var checkedValueID = $('#keysid').val();
				//var checkedValueName = $('#keysname').val();
				var checkedValueID = $(p_optionid).val();
				checkedValueID = "";
				var checkedValueName = $(p_optionname).val();
				checkedValueName = "";
				//alert("123123123123");
				var tempCheckedValueID = "";
				var tempCheckedValueName = "";
				for (var i = 0; i < selectedNode.length; i++) {
					//alert(selectedNode[i].MenuID);
					tempCheckedValueID += "," + selectedNode[i].id;
					tempCheckedValueName += "," + selectedNode[i].name;
					//$('#CheckedRule').val(checkedValue + "," + selectedNode[i].MenuID);
				}
		
				if (tempCheckedValueID != '') {
					tempCheckedValueID = tempCheckedValueID.substr(1);
				}
				if (tempCheckedValueName != '') {
					tempCheckedValueName = tempCheckedValueName.substr(1);
				}
				$(p_optionid).val(tempCheckedValueID);
				$(p_optionname).val(tempCheckedValueName);
		}  
		 
		
		$.fn.zTree.init(p_treeobj, settingKeys);
}

/*屏蔽Backspace，输入内容不屏蔽*/
$(document).keydown(function(e){
    var target = e.target ;
    var tag = e.target.tagName.toUpperCase();
    if(e.keyCode == 8){
     if((tag == 'INPUT' && !$(target).attr("readonly"))||(tag == 'TEXTAREA' && !$(target).attr("readonly"))){
      if((target.type.toUpperCase() == "RADIO") || (target.type.toUpperCase() == "CHECKBOX")){
       return false ;
      }else{
       return true ;
      }
     }else{
      return false ;
     }
    }
}); 