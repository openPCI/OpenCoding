var splitchar=";"
var filetext=""
var maximgwidth=800
var maximgheight=800

$(function() {
	$("#showloginform").click(function () {get_template("login",{},"loginready")})
	var p=window.location.search.replace(/.*[?&]p=([^&]*)(&|$).*/,"$1")
  	if(p=="") get_template("main",{},"dologin")
	else get_template(p)
//	afterRender({template:(p?p:"main")})
})
function dologin(json) {
	if(typeof json.getlogin!="undefined") get_template("login",{},"loginready")
}
function loginready()  {
	$(".login").click(login);
}
function login() {
	send("login","checklogin",{logintype:$(this).attr("id"),inputUser:$("#inputUser").val(),inputPassword:$("#inputPassword").val(),rememberMe:$("#rememberMe").val(),p:$("#p").val()},"shared");
}
function checklogin(json) {
//  	console.log(json)
	if(!json.warning) {
		showMessage(json.welcome);
		window.location.assign(window.location.search)
	}
}
function send(page,f,d,place) {
	if(typeof(place)=="undefined") var place="frontend";
	if(typeof(d)=="undefined") var d={};
// 	alert(page+f+place)
	d.ajax=1;
	console.log("./"+place+"/"+page+".php");
	$.ajax({
		url: "./"+place+"/"+page+".php",
		data: d,
		type: "POST",
		dataType : "json",
		cache: false,
		success: function( json ) {
			if(json.log) console.log(json.log);
			if(json.warning) {if(json.warning!=""){ showWarning(json.warning,6000); console.log("Warning!"); }}
			if(f) window[f](json); 
		},
		error: function( xhr, status, errorThrown ) {
			alert( "Sorry, there was a problem!" );
			console.log( "Error: " + errorThrown );
			console.log( "Status: " + status );
			console.dir( xhr );
		}
	});
}
function get_template(template,data,f) {
	if(typeof(data)=="undefined") data={}
	data.template=template
    	console.log(template)
	$.ajax({url:"shared/templates.php",data:data,type:"POST",dataType:"json",cache:false,
		success:function(json) {
// 			console.log(json)
			json.contentdiv=data.contentdiv
			insertTemplate(json)
			if(typeof(f)!="undefined")
				window[f](json)
		},
		error: function( xhr, status, errorThrown ) {
			alert( "Sorry, there was a problem!" );
			console.log( "Error: " + errorThrown );
			console.log( "Status: " + status );
			console.dir( xhr );
		}

	});
}
function insertTemplate(json) {
  	console.log("inserting template");
// 	console.log(json)
	$("#"+((typeof json.contentdiv=="undefined")?"contentdiv":json.contentdiv)).html(json.template);
	afterRender(json)
}
function admin() {
		//Nothing here...
}
// // // // // 
// After Rendering functions
function afterRender(json) {
// 	console.log(json)
	if(!json.template) return
		console.log(json.function)
	switch (json.function) {
		case "mytasks":
			codeTask()
			break
		case "training":
			codeTask("training")
			break
		case "codingmanagement":
			codeTask("flaghandling")
			gotcodingmanagement(json)
		break;
		case "projectadmin":
			if(json.links) {
				for(link of json.links) $("#"+link).click(function() {
					var link=$(this).attr("id")
					get_template(link,{},"got"+link)
				})
			}
		break;
	}
}
function codeTask(special="") {
	$(".codeTask").click(function () {get_template("coding",{task_id:$(this).data("task_id"),maxcodedresponse_id:$(this).data("maxcodedresponse_id"),special:special,first_id:$(this).data("first_id")},"gotcoding")});
}
function gotcoding(json) {
	$("#sendcomment").click(sendcomment)
	$("#flag").click(toggleFlag)
	$("#trainingresponse").click(trainingresponse)
	$(".itemvalue").focus(function() {if($(this).val()=="") $(this).val("0")});
	$(".nextresponse").click(function() { getresponse($(this).data("next"))})
	$("#response_id").dblclick(function() {$(this).prop("readonly",false)}).change(function() { getresponse(0)})
	getresponse(typeof (json.first_id)=="undefined"?0:parseInt(json.first_id))
}
function trainingresponse() {
	$("#trainingresponse").toggleClass("text-primary text-muted")
	var used=$("#trainingresponse").hasClass("text-primary")
	var difgiven=false
	while(used && !difgiven) {
		difficulty=window.prompt(_("Please specify the difficulty of coding the response (on a scale from 0 to 255) (used for ordering the responses in training, the ones with lowest difficulty will be presented first)"))
		if(!/^[0-9]+$/.test(difficulty)) {
			alert(_("Please give a number between 0 and 255"))
		} else difgiven=true
	}
	var status=(used?"istrainingresponse":"nottrainingresponse")
	$("#trainingresponse").attr("title",used?$("#trainingresponse").data("used")+difficulty:$("#trainingresponse").data("notused"))
	send("trainingresponse","trainingresponsedone",{status:status,response_id:$("#response_id").val(),difficulty:difficulty},"backend")
}
function trainingresponsedone() {
	
}
function toggleFlag() {
	$("#flag").toggleClass("text-danger text-muted")
	var status=($("#flag").hasClass("text-danger")?"flagged":"resolved")
	if(status=="flagged") {
		$("#flag").attr("title",_("Mark flag resolved."))
		getcommenthistory()
	} else {
		$("#flag").attr("title",_("Flag response."))
	}
	var flaghandling=($("#flaghandling").val()=="true")
	send("flag","flagdone",{actiontype:"flag",status:status,response_id:$("#response_id").val(),flaghandling:flaghandling},"frontend")
}
function sendcomment() {
	var flaghandling=($("#flaghandling").val()=="true")
	send("flag","commentdone",{actiontype:"comment",comment:$("#flagcomment").val(),response_id:$("#response_id").val(),flaghandling:flaghandling},"frontend")
}
function commentdone() {
	$("#flagcomment").val("")
	getcommenthistory()
}
function getcommenthistory() {
	var flaghandling=($("#flaghandling").val()=="true")
	get_template("flagcommentshistory",{contentdiv:"flagcommentshistory",flaghandling:flaghandling},"gotflagcommentshistory")
}
function gotflagcommentshistory(json) {
	$("#flaggedby").html(json.flaggedby)
}
function flagdone() {}
function getresponse(next) {
	console.log(typeof next)
	var codes=[]
	var go=true
	var flagged=$("#flag").hasClass("text-danger")
	if(typeof next=="string") {
		var empty=$('.itemvalue').filter(function(x) {return $(this).val().trim()==""})
		if(empty.length > 0) {
			if (flagged) empty.each(function() {$(this).val(-1);})
			else if(next==">") {
				go=false
				showWarning(_("You need to fill out all codes before proceeding to the next response."))
			} 
		}
		$(".itemvalue").each(function() {
			var val=parseInt($(this).val())
			if(val>parseInt($(this).attr("max")) || val<-1) {
				$(this).addClass("bg-danger").delay("1000").removeClass("bg-danger")
				go=false
				showWarning(_("The value of {0} is out of range",$(this).data("item_name")))
			}
		})
		if(go)
			codes=$(".itemvalue").map(function() {return {item_name:$(this).data("item_name"),code:parseInt($(this).val())}}).get()
	}
	if(go) {
		var flaghandling=($("#flaghandling").val()=="true")
		var training=($("#training").val()=="true")
		send("getresponse","gotresponse",{next:next,task_id:$("#playarea").data("task_id"),response_id:$("#response_id").val(),subtask_ids:$("#playarea").data("subtask_ids"),codes:codes,flagged:flagged,training:training,flaghandling:flaghandling})
	}
}
function gotresponse(json) {
	if(json.dodouble) showMessage("This is double coding");
	if(json.warning || typeof json.returnto!="undefined") {
		if(typeof json.returnto!="undefined") get_template(json.returnto)
	} else {
		$("#response_id").val(json.response_id).prop("readonly",true)
		insertResponse(json)
		$(".itemvalue").val("")
		for(c of json.codes) {
			$('.itemvalue[data-item_name="'+c.item_name+'"]').val(c.code)
		}
		if(typeof json.correctcodes!="undefined") {
			for(i in json.correctcodes) {
				var agree=(json.correctcodes[i].code==json.codes[i].code)
				$('.itemvalue[data-item_name="'+json.correctcodes[i].item_name+'"]').addClass(agree?"bg-success":"bg-warning").removeClass((!agree?"bg-success":"bg-warning"))
				if(!agree) $('.itemvalue[data-item_name="'+json.correctcodes[i].item_name+'"]').data("correctcode",json.correctcodes[i].code).change(function() {
					var agree=$(this).val()==$(this).data("correctcode")
					$(this).addClass(agree?"bg-success":"bg-warning").removeClass((!agree?"bg-success":"bg-warning"))
				})
			}
		} else $('.itemvalue').removeClass("bg-success bg-warning")
		$(".itemvalue").first().focus()
		$(".itemvalue").keydown(function(e)  {if(e.keyCode==13) {getresponse(">"); e.stopPropagation()}})
		if(json.flagstatus=="flagged") {
			getcommenthistory()
			$("#flag").removeClass("text-muted").addClass("text-danger")
			var showhide="show"
		} else {
			$("#flag").removeClass("text-danger").addClass("text-muted")
			var showhide="hide"
		}
		$("#flagcommentsdiv").collapse(showhide)
		if(json.trainingresponse>0 && $("#trainingresponse").hasClass("text-muted") || json.trainingresponse==0 && $("#trainingresponse").hasClass("text-primary")) {
			$("#trainingresponse").toggleClass("text-primary text-muted")
		}
		$("#trainingresponse").attr("title",$("#trainingresponse").hasClass("text-primary")?$("#trainingresponse").data("used")+json.trainingresponse:$("#trainingresponse").data("notused"))
	
	}
}
function gotupload() {
	$("#datafile").change(function() {readCols(colsread,1)})
	$("#doUpload").click(function()  {readCols(doUpload)})
	$(".datetime").click(function() {
		$(this).unbind("click")
		$(this).daterangepicker({autoApply: true,showWeekNumbers:true,timePicker: true,timePicker24Hour: true,timePickerIncrement: 15,locale: {format: 'YYYY/MM/DD HH:mm'},showDropdowns: true,singleDatePicker: true})
	}); 
}
function gottests() {
	$(".edittest").click(function() {alert("Edit of test not implemented")})
	maketasksactive() 
}
function maketasksactive() {
	$('[data-group_id]').each(function() { 
		$('<div class="group_member"><i class="fas fa-level-up-alt fa-rotate-90"></i> '+$(this).children().first().text()+"</div>").appendTo($('[data-task_id='+$(this).data("group_id")+']').children().first()).data("taskContent",$(this)).dblclick(revertgroup)
		$(this).remove()
	})
	$(".picture").change(showImage)
	$("#saveimg").click(function() {
		var imgsrc=$("#modalimg>img").attr("src")
		send("edited","wassaved",{task_id:$(this).data("task_id"),edittype:"task_image",value:imgsrc},"backend")
		$("tr[data-task_id] .uploadedimg>img").attr("src",imgsrc)
	})
	$("#uploadedimg").on("show.bs.modal",function(e) {
		$("#modalimg").html($(e.relatedTarget).html());
		$("#saveimg").data("task_id",$(e.relatedTarget).closest("tr").data("task_id"))
	})
	$(".editable").unbind("keydown").keydown(isEnter)
	$(".editable").unbind("blur").on("blur",edited)
	$(".selectable").unbind("click").click(initselectable)
	$(".additem").unbind("click").click(additem)
	$(".tasktype_variable").change(function() {
// 		console.log({task_id:$(this).closest("tr").data("task_id"),edittype:"tasktype_variables",variable:$(this).data("variablename"),value:$(this).val()})
		send("edited","wassaved",{task_id:$(this).closest("tr").data("task_id"),edittype:"tasktype_variables",variable:$(this).data("variablename"),value:$(this).val()},"backend")
	})
	$(".group_target").draggable({ 
		revert: "invalid",      
		containment: "document",
		helper: "clone",
		cursor: "move",
		handle: "th"
	}).droppable({
		drop: function( event, ui ) {
			$('<div class="group_member"><i class="fas fa-level-up-alt fa-rotate-90"></i> '+ui.draggable.children().first().text()+"</div>").appendTo($(this).children().first()).data("taskContent",ui.draggable).dblclick(revertgroup)
			send("makegroup","groupmade",{parent:$(this).data("task_id"),member:ui.draggable.data("task_id")},"backend")
			disenablegroup()
			ui.draggable.remove()
      }
	}
	)
	disenablegroup()
}
function wassaved() {
	$("#uploadedimg").modal("hide")
}
function disenablegroup() {
	$('.group_target:has(.group_member)').draggable( "disable" );	
	$('.group_target:not(:has(.group_member))').each(function() {
		if(typeof($(this).draggable("instance"))!="undefined" && $(this).draggable( "option", "disabled" )) 
			$(this).draggable( "enable" );
 	})	
}
function groupmade() {}
function additem() {
	var itemname='item'+($(this).index()+1)
	$(this).before('<div><span class="editable" data-edittype="items" data-edittype2="name" data-oldvalue="'+itemname+'" contenteditable>'+itemname+'</span>: 0-<span class="editable" data-edittype="items"  data-edittype2="value" contenteditable>1</span><div>')
	$(this).prev().children(".editable").keydown(isEnter).on("blur",edited)
	var task_id=$(this).closest("tr").data("task_id")
	send("edited","wasedited",{task_id:task_id,edittype:"items",edittype2:"value",oldvalue:itemname,value:1},"backend")
}
function revertgroup() {
	send("revertgroup","groupmade",{member:$(this).data("taskContent").data("task_id")},"backend")
	var t=$(this).data("taskContent")
	$(this).remove()
	$(t).appendTo($("#tasklist"))
	disenablegroup()
	maketasksactive()
}
function isEnter(e) {if((e.keyCode === 13)) $(this).blur();}
function initselectable() {
	var tasktype_id=$(this).data("tasktype_id")
	$(this).html($("#tasktypes").clone().attr("id","tasktypesclone"))
	$("#tasktypesclone").children("[value="+tasktype_id+"]").prop("selected",true)
	$("#tasktypesclone").change(selected)
	$(this).unbind("click")
}
function selected() {
	var task_id=$(this).closest("tr").data("task_id")
	var edittype=$(this).parent().data("edittype")
	var value=$(this).children(":selected").val()
	send("edited","wasedited",{task_id:task_id,edittype:edittype,value:value},"backend")
	$(this).parent().click(initselectable)
	$(this).parent().html($(this).children(":selected").text())
}
function edited() {
	var task_id=$(this).closest("tr").data("task_id")
	var edittype=$(this).data("edittype")
	var edittype2=$(this).data("edittype2")
	var oldvalue=(edittype2=="value"?$(this).prev().data("oldvalue"):$(this).data("oldvalue"))
	var value=$(this).text().trim()
	$(this).data("oldvalue",value)
	send("edited","wasedited",{task_id:task_id,edittype:edittype,edittype2:edittype2,oldvalue:oldvalue,value:value},"backend")
}
function wasedited(json) {
	if(typeof json.variables!="undefined") $("tr[data-task_id="+json.task_id+"] .variables").html(json.variables)
}
function readCols(func,preview=0) {
	var file = $("#datafile").prop("files")[0]
	
	var papa = Papa.parse(file, {
			header: false,
			complete: func,
			preview:preview
          }
        );
	
}
function colsread(results) {
          
	var data = results.data;
	$("#cols").html("")
	$("#username").html("")
	$("#testtime").html("")
	$("#tasks").html("")
	var resp=/RESPONSE/i
	for(var i in data[0]) {
		var colname=data[0][i]
		if(colname!="")
			$("#cols").append('<a href="#" class="badge badge-'+(resp.test(colname)?'primary':'secondary')+' mr-2 column" data-colno="'+i+'">'+data[0][i]+'</a>')
	}
	$(".column").click(movetodatafields)
	$("#datafields").collapse("show")
}
function doUpload(results) {
	var test_name=$("#test_name").val().trim()
	if(test_name=="") alert("Provide a name for the test") 
	else { 
		var cols=$("#usedcols a.column").map(function(x) {return $(this).data("colno")}).get()
		var filtered=results.data.map(function(vals) {
			var a=[]
			for(var c of cols) {
				a.push(vals[c])
			}
			return a
		})
		var before=$("#beforefilter").val()
		if(before.length>0) {
			var beforedate=Date.parse(before)
			filtered=filtered.filter(function(v,i) {
				if(i==0) return true;
				var thisdate=Date.parse(v[1])
				return thisdate<=beforedate
			})
		}
		var after=$("#afterfilter").val()
		if(after.length>0) {
			var afterdate=Date.parse(after)
			filtered=filtered.filter(function(v,i) {
				if(i==0) return true;
				var thisdate=Date.parse(v[1])
				return thisdate>=afterdate
			})
		}
		var testtaker=$("#testtakerfilter").val()
		if(testtaker.length>0) {
			const regex = new RegExp(testtaker);
			filtered=filtered.filter(function(v,i) {
				if(i==0) return true;
				return regex.test(v)
			})
		}
		console.log(filtered[0])
		if(window.confirm(_("You are about to import {0} columns of data from {1} test-takers. Do you want to proceed?",(filtered[0].length-2),filtered.length)))
			send("doUpload","uploaddone",{test_name:test_name,responses:filtered},"backend")
	}
}
function uploaddone(json) {
	if(window.confirm(_("{0} new tests, {1} new tasks, and {2} new responses were registered. Are you done uploading?",json.newtests,json.newtasks,json.newresponses)))
		get_template("projectadmin")
}
function movetodatafields() {
	var moveto=($(this).closest("#usedcols").length>0?"cols":($("#username").is(":empty")?"username":($("#testtime").is(":empty")?"testtime":"tasks")))
	$("#"+moveto).append($(this))
}
// // // // // 
// Dialogs
function showWarning(txt,time) {
	if(typeof(time)=="undefined") var time=3000
	$(".OpenCodingWarning").html(txt)
	$(".OpenCodingWarning").show().delay(time).fadeOut()
}
function showMessage(txt,time) {
	if(typeof(time)=="undefined") var time=3000
	$(".OpenCodingMessage").html(txt)
	$(".OpenCodingMessage").show().delay(time).fadeOut()
}


function showImage() {
	var file=$(this).get(0)
	if (file.files && file.files[0]) {
        var imgdiv = $(this).siblings('.showImg');  
// 		var img=$('<img class="logoimg">')
		var FR= new FileReader();
		FR.addEventListener("load", function(e) {
			var img = new Image();
// 			img.className="uploadedimg"
			img.src = e.target.result;

			img.onload = function () {
			var rel=Math.max(img.width/maximgwidth,img.height/maximgheight)
			if(rel>1) { 
				var canvas = document.createElement("canvas");
				
				img.width = (img.width / rel) 
				img.height = (img.height / rel)

				var ctx = canvas.getContext("2d");
				ctx.clearRect(0, 0, canvas.width, canvas.height);
				canvas.width = img.width;
				canvas.height = img.height;
				ctx.drawImage(img, 0, 0, img.width, img.height);
				img.src=canvas.toDataURL("image/png")
			}
			imgdiv.html(img)
		}
		}); 
		FR.readAsDataURL( file.files[0] );
	}
}
function gotusers() {
	$("#createpass").click(function() {
		var s = ''
		var a = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!-/()?=<>'
		var z = a.length-1
		for (var i = 0; i<16; i++) s+= a.charAt(Math.floor(Math.random() * (z))) 
		$("#password").val(s).attr("type","text").focus().select()
		document.execCommand("copy");
		$('#password').attr("title",_("Password copied to clipboard")).tooltip('show')
		setTimeout(function() { $("#password").tooltip('hide')}, 2000);
	})
	$("#newuser").on("show-bs-modal",function() {$(".userinput").val("")})
	$("#savenewuser").click(function() {
		var userinfo=flattenObj($(".userinput").map(function() {return {[$(this).attr("id")]:$(this).val()}}).get())
		send("savenewuser","newusersaved",{userinfo:userinfo},"backend")
	})
	$(".changePermissions").click(changePermissionsselect)
}
function changePermissionsselect() {
	console.log($(this))
	$(this).unbind("click")
	var permissions=($(this).text().trim().length>0?$(this).text().split(", "):[])//.map(function(x) {return x.trim()})
	console.log(permissions)
	var select=$("#permissiontypes").clone()
	select.attr("id","")
	$(this).html(select)
	for(permission of permissions) {
		console.log(permission)
		 $("."+permission).prop("checked",true)
	}
	$(this).find("input").change(changePermissions)
}
function changePermissions() {
	console.log($(this).prop("checked"))
	send("changePermissions","permissionschanged",{user_id:$(this).closest("tr").data("user_id"),unittype:$(this).val(),given:$(this).prop("checked")},"backend")
	var td=$(this).closest(".changePermissions")
	td.click(changePermissionsselect)
	var permissions=td.find("input").map(function() {if($(this).prop("checked")) return $(this).val()}).get().join(", ")
	console.log(permissions)
	td.html(permissions)
}
function permissionschanged() {}
function flattenObj(arr) {
	const flatObject = {};
	for(obj of arr){
      for(const property in obj){
         flatObject[`${property}`] = obj[property];
      }
   };
   return flatObject;
}
function newusersaved() {
	$("#newuser").modal("hide")
	get_template("users",{},"gotusers")
}
function gotcodingmanagement() {
	$("#addcodermodal").on("shown.bs.modal",function(e) {
		$("#addcoders").data("unittype",$(e.relatedTarget).closest("tr").data("unittype"))
		$("#addcoders").data("unit_id",$(e.relatedTarget).closest("tr").data("unit_id"))
		$("#newcoder").focus()
	})
	$(".addcoder").click(addcoder)
	$(".deletecoder").click(deletecoder)
	$("#addcoders").click(addcoders)
	$("#newcoder").keydown(getcoder)
}
function getcoder() {
	var codername=$(this).val()
	if(codername.length>1) send("getcoder","gotcoder",{codername:codername},"backend")
}
function gotcoder(json) {
	if(typeof json.userfound!="undefined") {
		var newcoder=$('<a href="#" class="badge badge-primary mr-2 column addcoder" data-user_id="'+json.userfound.user_id+'">'+json.userfound.username+' ('+json.userfound.email+')'+'</a>')
		$("#newcoderdiv").html(newcoder)
		newcoder.click(addcoder)
	} else $("#codername").val("")
}
function addcoder() {
	$("#newcoder").val("")
	$(this).appendTo($(($(this).closest("#newcoders").length==0?"#newcoders":"#knowncoders")))
}
function addcoders() {
	console.log($(this))
	send("addcoders","codersadded",{unittype:$(this).data("unittype"),unit_id:$(this).data("unit_id"),user_ids:$("#newcoders .addcoder").map(function() {return $(this).data("user_id")}).get()},"backend")
}
function deletecoder() {
	send("deletecoder","coderdeleted",{unittype:$(this).closest("tr").data("unittype"),unit_id:$(this).closest("tr").data("unit_id"),user_id:$(this).data("user_id")},"backend")
}

function codersadded() {
	$("#addcodermodal").modal("hide")
	get_template("codingmanagement",{},"gotcodingmanagement")
}
function coderdeleted() {
	get_template("codingmanagement",{},"gotcodingmanagement")
}

