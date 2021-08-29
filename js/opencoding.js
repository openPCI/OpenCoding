var splitchar=";"
var filetext=""
var maximgwidth=800
var maximgheight=800

$(function() {
	$("#showloginform").click(function () {get_template("login",{},"loginready")})
	var p=window.location.search.replace(/.*[?&]p=([^&]*)(&|$).*/,"$1")
	$("#userinfo").click(function() {get_template("myUser",{},"gotmyuser")})
	$(".newLang").click(function() {var search=window.location.search; window.location.replace((search?search+"&":"?")+"setlang="+$(this).attr("src").replace(".png","").split("/")[2])});

  	if(p=="") get_template("main",{},"dologin")
	else get_template(p)
//	afterRender({template:(p?p:"main")})
})
function gotmyuser() {
	setTimeout(function() {
		$(".userinput").change(function() { send("useredit","useredited",{type:$(this).attr("id"),value:$(this).val()},"backend")})
	},100) // To give the browser time to autofill password...
}
function useredited(json) {
	if(json.message) showMessage(json.message)
}
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
		window.location.assign(json.chooseproject?"?p=chooseproject":window.location.search)
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
			if(typeof(json.relogin)!="undefined") {
				get_template("login",{},"loginready")
			} else {
				json.contentdiv=data.contentdiv
				insertTemplate(json)
				if(typeof(f)=="undefined" && typeof(window["got"+template.replace(/^get/,"")])!="undefined") f="got"+template.replace(/^get/,"")
				if(typeof(f)!="undefined")
					window[f](json)
			}
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
		case "chooseproject":
			chooseproject()
			break
		case "mytasks":
			codeTask()
			break
		case "training":
			codeTask("training")
			break
		case "management":
		case "projectadmin":
		case "opencodingadmin":
			if(json.links) {
				for(link of json.links) $("#"+link).click(function() {
					var link=$(this).attr("id")
					get_template(link,{},"got"+link)
				})
			}
		break;
	}
}
function chooseproject(json) {
	$(".selectproject").click(function(){ send("selectproject","projectselected",{project_id:$(this).data("project_id")},"frontend")})
}
function projectselected() {
	window.location.assign("?p=main")
}
function gottasktypes(json) {
	$("#scripting").on("shown.bs.modal",function(e) {
		var tasktype_id=$(e.relatedTarget).closest("tr").data("tasktype_id")
		var edittype=$(e.relatedTarget).data("edittype")
		$("#savecode").data("tasktype_id",tasktype_id)
		$("#savecode").data("edittype",edittype)
		$("#editor").attr("class","language-"+$(e.relatedTarget).data("language"))
		$(".OpenCodingMessage").html(_("Rendering the code can take very long time. Please be patient."))
		$(".OpenCodingMessage").show()
		quill.setContents([
			{ insert: '\n' }
		]);	
		send("tasktypecontent","gottasktypecontent",{tasktype_id:tasktype_id,edittype:edittype,getsave:"get"},"backend")
	})
	$("#savecode").click(function() {
		var tasktype_id=$(this).data("tasktype_id")
		var edittype=$(this).data("edittype")
		var content=quill.getText(0).replace(/\\n/,"&slashn;")//$("#editor").find("pre").text()
// 		console.log(quill.getText(0))
		send("tasktypecontent","savedtasktypecontent",{tasktype_id:tasktype_id,content:content,edittype:edittype,getsave:"save"},"backend")
		var row=$("[data-tasktype_id="+tasktype_id+"]")
		row.children("[data-edittype="+edittype+"]").addClass("text-info").removeClass("text-muted")
	})
	hljs.configure({   // optionally configure hljs
		languages: ['javascript', 'html', 'css','handlebars']
	});

	quill = new Quill('#editor', {
	modules: {
		syntax: true,              // Include syntax module
		toolbar: false //[['code-block']]  // Include button in toolbar
	},
	theme: 'snow'
	});
	$(".htmleditable").unbind("click").click(edithtml)
	$(".editable").unbind("keydown").keydown(isEnter)
	$(".editable").unbind("blur").on("blur",tasktypeedited)
	maketasktypesactive()
}
function tasktypeedited() {
	var tasktype_id=$(this).closest("tr").data("tasktype_id")
	var edittype=$(this).data("edittype")
	var edittype2=$(this).data("edittype2")
	var oldvalue=(edittype2=="value"?$(this).prev().data("oldvalue"):$(this).data("oldvalue"))
	var value=$(this).text().trim()
	$(this).data("oldvalue",value)
	send("edited","wasedited",{task_id:task_id,edittype:edittype,edittype2:edittype2,oldvalue:oldvalue,value:value,edittable:"tasktypes"},"backend")
}
function addvariable() {
	var variablename='variable'+($(this).index()+1)
	$(this).before('<div><span class="editable first" data-edittype="variables" data-edittype2="name" data-oldvalue="'+variablename+'" contenteditable>'+variablename+'</span>: <span class="editable" data-edittype="variables"  data-edittype2="value" contenteditable>'+_('Default value')+'</span><span class="deletevariable float-right"><i class="fa fa-trash-alt"></i></span><div>')
	$(this).prev().children(".editable").keydown(isEnter).on("blur",edited)
	var tasktype_id=$(this).closest("tr").data("tasktype_id")
	send("edited","wasedited",{tasktype_id:tasktype_id,edittype:"variables",edittype2:"value",oldvalue:variablename,value:"",edittable:"tasktypes"},"backend")
}
function deletevariable() {
	var oldvalue=$(this).siblings(".first").data("oldvalue")
	var tasktype_id=$(this).closest("tr").data("tasktype_id")
	send("edited","wasedited",{tasktype_id:tasktype_id,edittype:"variables",edittype2:"delete",oldvalue:oldvalue,edittable:"tasktypes"},"backend")
	$(this).parent().remove()
}

function maketasktypesactive() {
	$(".editable").unbind("keydown").keydown(isEnter)
	$(".editable").unbind("blur").on("blur",edited)
	$(".manualautotoggle").unbind("click").click(togglemanualauto)
	$(".addvariable").unbind("click").click(addvariable)
	$(".deletevariable").unbind("click").click(deletevariable)
	$("#newtasktype").click("click",function() {
		send("newtasktype","newtasktypecreated",{},"backend")
	}
	)
}
function newtasktypecreated() {
	get_template("tasktypes",{},"gottasktypes")
}
function togglemanualauto() {
	var manualauto=($(this).data("manualauto")=="auto"?"manual":"auto")
	$(this).data("manualauto",manualauto).text(manualauto)
	var tasktype_id=$(this).closest("tr").data("tasktype_id")
	send("edited","wasedited",{tasktype_id:tasktype_id,edittype:"manualauto",value:manualauto,edittable:"tasktypes"},"backend")
}
function savedtasktypecontent() {
	$("#scripting").modal("hide")
}
function gottasktypecontent(json) {
	quill.setContents([
	{ insert: json.content.replace("&slashn;","\\n")},//, attributes: {'code-block':true}}, //Did not format html ...
	{ insert: '\n' }
	]);	
	quill.formatLine(0,quill.getLength(),"code-block",true)
 	$(".OpenCodingMessage").hide()

}
function codeTask(special="") {
	$(".docode").click(function () {
		var codetype=$(this).data("codetype")
		if(codetype=="revise") special="revise"
		var auto=(codetype=="autocode"?"auto":"")
		get_template(auto+"coding",{task_id:$(this).closest("tr").data("task_id"),special:special,codetype:codetype,flagstatus:$(this).data("flagstatus")},"got"+auto+"coding")
	});
}
function gotautocoding(json) {
// 	$(".nextresponse").click(function() { getautoresponse($(this).data("next"))})
// 	$("#response_id").dblclick(function() {$(this).prop("readonly",false)}).change(function() { getautoresponse(0)})
	if($(".quill").length>0) {
		hljs.configure({   // optionally configure hljs
			languages: ['javascript', 'html', 'css','handlebars']
		});

		quill = new Quill('.quill', {
		modules: {
			syntax: true,              // Include syntax module
			toolbar: false //[['code-block']]  // Include button in toolbar
		},
		theme: 'snow'
		});
		quill.formatLine(0,quill.getLength(),"code-block",true)

	}
	$("#updatestats").click(function() {
		$("#statrow").empty()
		$(".itemvalue").each(function() {
			var itemname=$(this).data("item_name")
			$("#statrow").append('<div class="form-group col-3"><h6>'+itemname+'</h6><p class="itemstat" data-item_name="'+itemname+'"></p></div>')
		})
		$(".itemstat").each(function() {
			var item_name=$(this).data("item_name")
			// Hack? Should the script save the responses to sessionStorage, and then we take it from there...
			var val=responses.reduce(function(a,c){return a+(Number.isNaN(Number(c[item_name]))?0:Number(c[item_name]))},0)
			$(this).text(val)
		})
	})
	send("initauto","initauto",{task_id:$("#playarea").data("task_id"),subtask_ids:$("#playarea").data("subtask_ids")},"frontend")
}
function initauto(json) {
	console.log(json)
	$(".edititem_name").keydown(isEnter).blur(autoedit)
	$("#additem").unbind("click").click(additemauto)

	// openCoding gets the data (as JSON) saved earlier by the tasktype, and the responses.
	// Everything is put in sessionStorage for the tasktype script to fetch. 
	// initDone should be defined by the tasktype custom script
	sessionStorage.setItem("data", JSON.stringify(json.data)); 
	sessionStorage.setItem("items", JSON.stringify(json.items));
	sessionStorage.setItem("responses", JSON.stringify(json.responses));
	$(".autosave").click(autosave)
	if(typeof init!="function" || typeof save!="function" ) {
		alert("Please provide init() and save() functions in your tasktype-script.")
	} else init()
}
function autoedit() {
	send("edited","autoedited",{task_id:$("#playarea").data("task_id"),edittype:"items",edittype2:"name",oldvalue:$(this).data("item_name"),value:$(this).text(),edittable:"tasks"},"backend")
	$(this).data("item_name",$(this).text())
	$(this).next().data("item_name",$(this).text())
}
function autoedited(json) {
}
function additemauto() {
	var itemname=_('item')+($(".itemvalue").length+1)
	var add=$('<div class="form-group col-3"><label for="item'+itemname+'" data-item_name="'+itemname+'" contenteditable class="edititem_name">'+itemname+'</label><input type="number" data-item_name="'+itemname+'" id="item'+itemname+'" class="form-control itemvalue" disabled></div>')
	add.find(".edititem_name").keydown(isEnter).on("blur",autoedit)
	$("#coderow").append(add)
	send("edited","wasedited",{task_id:$("#playarea").data("task_id"),edittype:"items",edittype2:"value",oldvalue:itemname,value:1},"backend")
}

function autosave() {
	save()
	send("autosave",$(this).data("type"),{responses:JSON.parse(sessionStorage.getItem("responses")),data:JSON.parse(sessionStorage.getItem("data")),task_id:$("#playarea").data("task_id")},"frontend")
}
function saved() {
	showMessage(_("Coding saved"))
}
function finish() { 
	get_template("mytasks")
	
}
function gotcoding(json) {
	$("#sendcomment").click(sendcomment)
	$("#flag").click(toggleFlag)
	$("#trainingresponse").click(trainingresponse)
	$(".itemvalue").focus(function() {if($(this).val()=="") $(this).val("0")});
	$(".nextresponse").click(function() { getresponse($(this).data("next"))})
	$("#response_id").dblclick(function() {$(this).prop("readonly",false)}).change(function() { getresponse($(this).val())})
	getresponse()
}
function trainingresponse() {
	$("#trainingresponse").toggleClass("text-primary text-muted")
	var used=$("#trainingresponse").hasClass("text-primary")
	var difgiven=false
	var cancelling=false
	while(used && !difgiven) {
		difficulty=window.prompt(_("Please specify the difficulty of coding the response (on a scale from 0 to 255) (used for ordering the responses in training, the ones with lowest difficulty will be presented first)"))
		if(difficulty===null) { cancelling=true; break;}
		if(!/^[0-9]+$/.test(difficulty)) {
			alert(_("Please give a number between 0 and 255"))
		} else difgiven=true
	}
	if(!cancelling) {
		var status=(used?"istrainingresponse":"nottrainingresponse")
		$("#trainingresponse").attr("title",used?$("#trainingresponse").data("used")+difficulty:$("#trainingresponse").data("notused"))
		send("trainingresponse","trainingresponsedone",{status:status,response_id:$("#response_id").val(),difficulty:difficulty},"backend")
	}
}
function trainingresponsedone() {
	
}
function toggleFlag() {
	$("#flag").toggleClass("text-danger text-muted")
	var status=($("#flag").hasClass("text-danger")?"flagged":"resolved")
	var flaghandling=($("#flaghandling").val()=="true")
	if(!flaghandling)
		$("#flagcommentsdiv").collapse(($("#flag").hasClass("text-danger")?"show":"hide"))
	if(status=="flagged") {
		$("#flag").attr("title",_("Mark flag resolved."))
		getcommenthistory()
		$("#flagcomment").prop("disabled",false)
	} else {
		$("#flag").attr("title",_("Flag response."))
	}
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
	get_template("flagcommentshistory",{contentdiv:"flagcommentshistory",response_id:$("#response_id").val(),flaghandling:flaghandling},"gotflagcommentshistory")
}
function gotflagcommentshistory(json) {
	$("#flaggedby").html(json.flaggedby)
}
function flagdone() {}
function getresponse(next) {
	console.log(typeof next)
	var codes=[]
	var go=true
	var nocodes=false
	var flagged=$("#flag").hasClass("text-danger")
	if(typeof next=="string") {
		if(next=="code0") {
			$('.itemvalue').val(0)
			next=">"
		}
		var empty=$('.itemvalue').filter(function(x) {return $(this).val().trim()==""})
		if(empty.length > 0) {
			if (flagged) empty.each(function() {$(this).val(-1);})
			else if(next==">") {
				go=false
				showWarning(_("You need to fill out all codes before proceeding to the next response."))
			} else nocodes=true
		}
		$(".itemvalue").each(function() {
			var val=parseInt($(this).val())
			if(val>parseInt($(this).attr("max")) || val<-1) {
				$(this).addClass("bg-danger").delay("1000").removeClass("bg-danger")
				go=false
				showWarning(_("The value of {0} is out of range",$(this).data("item_name")))
			}
		})
		if(go && !nocodes)
			codes=$(".itemvalue").map(function() {return {item_name:$(this).data("item_name"),code:parseInt($(this).val())}}).get()
	}
	if(go) {
		var flaghandling=($("#flaghandling").val()=="true")
		var flagstatus=($("#flagstatus").val())
		var training=($("#training").val()=="true")
		var revise=($("#revise").val()=="true")
		send("getresponse","gotresponse",{next:next,task_id:$("#playarea").data("task_id"),response_id:$("#response_id").val(),subtask_ids:$("#playarea").data("subtask_ids"),codes:codes,flagged:flagged,training:training,revise:revise,flaghandling:flaghandling,flagstatus:flagstatus})
	}
}
function gotresponse(json) {
// 	if(json.dodouble) showMessage("This is double coding");
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
		var showhide="hide"
		if(json.flagstatus=="flagged") {
			getcommenthistory()
			$("#flag").removeClass("text-muted").addClass("text-danger")
			var showhide="show"
		} else {
			if($("#flaghandling").val()=="true") {
				getcommenthistory()
				showhide="show"
				$("#flagcomment").prop("disabled",true)
			} 
			$("#flag").removeClass("text-danger").addClass("text-muted")
		}
		$("#flagcommentsdiv").collapse(showhide)
		if(json.trainingresponse>0 && $("#trainingresponse").hasClass("text-muted") || json.trainingresponse==0 && $("#trainingresponse").hasClass("text-primary")) {
			$("#trainingresponse").toggleClass("text-primary text-muted")
		}
		console.log(json.trainingresponse)
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
function gotdownload() {
	$(".testcheck").click(function() {
		$(this).siblings("ul").find('input[type=checkbox]').prop("checked",$(this).prop("checked"))//function(i,p) {console.log(p); return (!p)})
	})
	$(".taskcheck").click(function() {
		var checked=$(this).closest("ul").find('input[type=checkbox]').map(function() {return $(this).prop("checked")}).get()
		var all=checked.indexOf(false)==-1
		var some=checked.indexOf(true)>-1 && !all
		var cb=$(this).closest(".testli").find("ul").siblings("input[type=checkbox]")
		cb.prop("checked",all)
		cb.prop("indeterminate",some)
	})
	$("#doDownload").click(doDownload)
}
function doDownload() {
var tasks=$(".taskcheck").map(function() { return $(this).data("task_id") }).get()

// 	initprogress("scoresheet")
	var formData=new FormData();
	formData.append("tasks", JSON.stringify(tasks));
  let xhr = new XMLHttpRequest();
  xhr.responseType = 'arraybuffer';
  xhr.open('POST', 'backend/doDownload.php');
  xhr.send(formData); 

  xhr.onload = function(e) {
      if (this.status == 200) {
          var blob = new Blob([this.response], {type: 'text/csv'});
          let a = document.createElement("a");
          a.style = "display: none";
          document.body.appendChild(a);
          let url = window.URL.createObjectURL(blob);
          a.href = url;
          a.download = _('opencoding')+'.csv';
          a.click();
          window.URL.revokeObjectURL(url);
// 		  $("#progressmodal").modal("hide")
// 		  clearTimeout(progresstimeout)
      }else{
          //deal with your error state here
      }
  };
}
function initprogress(progresstype) {
	$("#progressbar").val("0")
	$("#progressbar").html("0 %")
	$("#progressmodal").modal("show")
	send("resetprogress","doNothing",{progresstype:progresstype})
	progress(progresstype)
}
var progresstimeout;
function progress(progresstype){
	$.ajax({
		url: "./frontend/progress.php",
		data: {progresstype:progresstype},
		type: "POST",
		dataType : "text",
		cache: false,
		success: function( progressval ) {
			progressval=parseInt(progressval,10)
			$("#progressbar").val(progressval)
			$("#progressbar").html(progressval+" %")
			if(progressval<100) {
				progresstimeout=window.setTimeout(progress,100,progresstype)
			} else $("#progressmodal").modal("hide")
		}
	})	
}

function gotprojects() {
	$("#newproject").click(function() {
		var project_name=window.prompt(_("Name of the new project?"))
		send("newproject","projectedited",{project_name:project_name},"backend")
	})
	$(".deleteproject").click(function() {
		if(window.confirm(_("Are you REALLY sure you want to delete this project? YOU WILL LOOSE ALL DATA AND CODING RELATED TO THIS PROJECT!"))) {
			var project_id=$(this).closest("tr").data("project_id")
			send("deleteproject","projectedited",{project_id:project_id},"backend")
		}
	})
	$(".changeproject").click(function() {
		var project_id=$(this).closest("tr").data("project_id")
		changeproject(project_id,"projectadmin")
	})
}
function changeproject(project_id,page) {
	send("changeproject","projectchanged",{project_id: project_id,page},"shared")
}
function projectchanged(json) {
	let stateObj = { }
	history.pushState(stateObj, _("Open Coding"), "?p="+json.page)
	location.reload();
}
function projectedited() {
	get_template("projects",{},"gotprojects");
}
function gottests() {
	$("#newtest").click(function() {
		var test_name=window.prompt(_("Name of the new test?"))
		send("newtest","testedited",{test_name:test_name},"backend")
	})
	$(".uploadresponses").click(function() {get_template("upload",{test_id:$(this).siblings(".test_name").data("test_id"),test_name:$(this).siblings(".test_name").data("test_name"),},"gotupload")})
	$(".edittest").click(function() {
		var div=$(this).siblings(".test_name")
		var newtestname=window.prompt(_("Enter new name of test"),div.text().trim())	
		if(newtestname) send("edited","testedited",{edittype:"test_name",edittable:"tests",value:newtestname,test_id:div.data("test_id")},"backend")
	})
	maketasksactive() 
}
function maketasksactive() {
	
	$('[data-group_id]').each(function() { 
		$('<div class="group_member"><i class="fas fa-level-up-alt fa-rotate-90"></i> '+$(this).children().first().text()+"</div>").appendTo($('[data-task_id='+$(this).data("group_id")+']').children().first()).data("taskContent",$(this)).dblclick(revertgroup)
		$(this).remove()
	})
	$(".picture").unbind("change").change(showImage)
	$("#saveimg").unbind("click").click(function() {
		var imgsrc=$("#modalimg>img").attr("src")
		$("#uploadedimg").modal("hide")
		send("edited","testedited",{task_id:$(this).data("task_id"),edittype:"task_image",value:imgsrc},"backend")
		
	})
	$("#uploadedimg").unbind("show.bs.modal").on("show.bs.modal",function(e) {
		$("#modalimg").html($(e.relatedTarget).html());
		$("#saveimg").data("task_id",$(e.relatedTarget).closest("tr").data("task_id"))
	})
	$(".itemsort").unbind("click").click(function() {
		var active=$( ".itemsdiv" ).sortable( "option", "disabled" )
		$(".itemsdiv").sortable(active?"enable":"disable")
		$(".itemsort").toggleClass("text-success",active)
	})
	$(".itemsdiv").sortable({
		placeholder: "ui-state-highlight",
		handle: ".editable",
		stop: function(event,ui) {
			send("itemsorted","itemsortdone",{order:$(event.target).find(".name").map(function() {return $(this).text()}).get(),task_id:$(this).closest("tr").data("task_id")},"backend")
		}
	}).sortable("disable")
	$(".editable").unbind("keydown").keydown(isEnter)
	$(".editable").unbind("blur").on("blur",edited)
	$(".htmleditable").unbind("click").click(edithtml)
	$(".selectable").unbind("click").click(initselectable)
	$(".additem").unbind("click").click(additem)
	$(".deleteitem").unbind("click").click(deleteitem)
	$(".tasktype_variable").unbind("change").change(function() {
// 		console.log({task_id:$(this).closest("tr").data("task_id"),edittype:"tasktype_variables",variable:$(this).data("variablename"),value:$(this).val()})
		send("edited","wassaved",{task_id:$(this).closest("tr").data("task_id"),edittype:"tasktype_variables",variable:$(this).data("variablename"),value:$(this).val()},"backend")
	})
	$(".deletetask").click(function() {
		if(window.confirm(_("Are you REALLY sure you want to delete this task? YOU WILL LOOSE ALL DATA AND CODING RELATED TO THIS TASK!"))) {
			var task_id=$(this).closest("tr").data("task_id")
			send("deletetask","testedited",{task_id:task_id},"backend")
		}
	})
	$(".movetask").click(function() {
			$(this).before($("#tests"))
			$("#tests").unbind("change").change(function() {
				var task_id=$(this).closest("tr").data("task_id")
				send("movetask","testedited",{task_id:task_id,test_id:$(this).children(":selected").val()},"backend")
			})
	})
	$(".deletetest").click(function() {
		if(window.confirm(_("Are you REALLY sure you want to delete this test? YOU WILL LOOSE ALL DATA AND CODING RELATED TO THIS TEST!"))) {
			var test_id=$(this).closest("tr").data("test_id")
			send("deletetest","testedited",{test_id:test_id},"backend")
		}
	})

	makegroupsdraggable()
	disenablegroup()
}
function testedited() {
	get_template("tests",{},"gottests")
}
function makegroupsdraggable() {
	$(".group_target").draggable({ 
		revert: "invalid",      
		containment: "document",
		helper: "clone",
		cursor: "move",
		handle: "th"
	}).droppable({
		accept: ".group_target",
		drop: function( event, ui ) {
			$('<div class="group_member"><i class="fas fa-level-up-alt fa-rotate-90"></i> '+ui.draggable.children().first().text()+"</div>").appendTo($(this).children().first()).data("taskContent",ui.draggable).dblclick(revertgroup)
			send("makegroup","groupmade",{parent:$(this).data("task_id"),member:ui.draggable.data("task_id")},"backend")
			disenablegroup()
			ui.draggable.remove()
      }
	}
	)
}
function itemsortdone() {
}
function edithtml() {
	var id=$(this).find(".htmleditablediv").attr("id")
	var content=$(this).find(".htmleditablediv").html();
	$(".htmleditable").unbind("click")
	$(this).append('<button class="btn btn-success" onclick="savehtml(event)">'+_("Save")+'</button>')
	quill = new Quill('#'+id, {
		modules: {
		},
		theme: 'snow'
		});
	$(this).find(".ql-editor").html(content)

}
function savehtml(e) {
	var th=$(event.currentTarget)
	var div=th.siblings(".htmleditablediv")
	var content=div.find(".ql-editor").html()
	var task_id=th.closest("tr").data("task_id")
	var tasktype_id=th.closest("tr").data("tasktype_id")
	var edittype=div.data("edittype")
	send("edited","waseditedhtml",{task_id:task_id,tasktype_id:tasktype_id,edittype:edittype,value:content,edittable:$("#edittable").data("edittable")},"backend")
}
function waseditedhtml(json) {
	console.log(json)
	$(".htmleditable").click(edithtml)
	var div=$(".ql-editor").closest(".htmleditablediv")
	div.html(json.value)
	div.attr("class","htmleditablediv")
	div.parent().html(div)
	
}
function wassaved() {
	
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
	var add=$(this).parent()
	var itemname=_('item')+(add.index()+1)
	var div=add.parent()
	add.before('<div><span class="editable first" data-edittype="items" data-edittype2="name" data-oldvalue="'+itemname+'" contenteditable>'+itemname+'</span>: 0-<span class="editable" data-edittype="items"  data-edittype2="value" contenteditable>1</span><span class="deleteitem float-right"><i class="fa fa-trash-alt"></i></span><div>')
	add.prev().children(".editable").keydown(isEnter).on("blur",edited)
	var task_id=add.closest("tr").data("task_id")
	send("edited","wasedited",{task_id:task_id,edittype:"items",edittype2:"value",oldvalue:itemname,value:1},"backend")
}
function deleteitem() {
	var oldvalue=$(this).siblings(".first").data("oldvalue")
	var task_id=$(this).closest("tr").data("task_id")
	send("edited","wasedited",{task_id:task_id,edittype:"items",edittype2:"delete",oldvalue:oldvalue},"backend")
	$(this).parent().remove()
}
function revertgroup() {
	var taskContent=$(this).data("taskContent")[0].outerHTML
	var member=$(this).data("taskContent").data("task_id")
	var test_id=$(this).closest(".testdiv").data("test_id")
	$(this).remove()
	send("revertgroup","groupreverted",{member:member,taskContent:taskContent,test_id:test_id},"backend")
}
function groupreverted(json) {
	var elem=$(json.taskContent).removeClass("ui-draggable-disabled").data("group_id",null)
	$("#tasklist"+json.test_id).append(elem)
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
	var tasktype_id=$(this).closest("tr").data("tasktype_id")
	var edittype=$(this).data("edittype")
	var edittype2=$(this).data("edittype2")
	var oldvalue=(edittype2=="value"?$(this).prev().data("oldvalue"):$(this).data("oldvalue"))
	var value=$(this).text().trim().replace(/[\n\r]/,"")
	$(this).data("oldvalue",value)
	send("edited","wasedited",{task_id:task_id,tasktype_id:tasktype_id,edittype:edittype,edittype2:edittype2,oldvalue:oldvalue,value:value,edittable:$("#edittable").data("edittable")},"backend")
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
	var test_id=$("#test_id").val()
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
		send("doUpload","uploaddone",{test_id:test_id,responses:JSON.stringify(filtered)},"backend")

}
function uploaddone(json) {
	if(window.confirm(_("{0} new tasks, and {1} new responses were registered. Are you done uploading?",json.newtasks,json.newresponses)))
		get_template("tests")
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
	$(".deleteuser").click(function() {
		if(window.confirm(_("Are you sure you want to delete this user?"))) {
			var user_id=$(this).closest("tr").data("user_id")
			send("deleteuser","editusersaved",{user_id:user_id},"backend")
		}
	})
	$("#edituser").on("shown.bs.modal",function(e) {
		var user_id=$(e.relatedTarget).closest("tr").data("user_id")
		if(typeof user_id!="undefined") {
			var row=$(e.relatedTarget).closest("tr")
			$("#user_id").val(user_id)
			$("#username").val(row.find('[data-type="username"]').text())
			$("#email").val(row.find('[data-type="email"]').text())
			$("#password").attr("placeholder",_("Unchanged"))
		} else
		$(".userinput").val("")
		
	})
	$("#saveuser").click(function() {
		var userinfo=flattenObj($(".userinput").map(function() {return {[$(this).attr("id")]:$(this).val()}}).get())
		send("saveuser","editusersaved",{userinfo:userinfo},"backend")
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
	console.log({user_id:$(this).closest("tr").data("user_id"),unittype:$(this).val(),given:$(this).prop("checked")})
	send("changePermissions","permissionschanged",{user_id:$(this).closest("tr").data("user_id"),unittype:$(this).val(),unit_id:$(this).closest(".changePermissions").data("unit_id"),given:$(this).prop("checked")},"backend")
// 	var td=$(this).closest(".changePermissions")
// 	td.click(changePermissionsselect)
// 	var permissions=td.find("input").map(function() {if($(this).prop("checked")) return $(this).val()}).get().join(", ")
// 	console.log(permissions)
// 	td.html(permissions)
}
function permissionschanged() {
	get_template("users",{},"gotusers")
}
function flattenObj(arr) {
	const flatObject = {};
	for(obj of arr){
      for(const property in obj){
         flatObject[`${property}`] = obj[property];
      }
   };
   return flatObject;
}
function editusersaved() {
	$("#edituser").modal("hide")
	get_template("users",{},"gotusers")
}
function gotcodingmanagement() {
	codeTask("flaghandling")
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
function gotcodermanagement() {
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
	var user_ids=$("#newcoders .addcoder").map(function() {return $(this).data("user_id")}).get()
	if(user_ids.length>0) send("addcoders","codersadded",{unittype:$(this).data("unittype"),unit_id:$(this).data("unit_id"),user_ids:user_ids},"backend")
	else showWarning(_("You haven't selected coders to add. Click on the name to put it to the group of coders to add."))
}
function deletecoder() {
	send("deletecoder","coderdeleted",{unittype:$(this).closest("tr").data("unittype"),unit_id:$(this).closest("tr").data("unit_id"),user_id:$(this).data("user_id")},"backend")
}

function codersadded() {
	$("#addcodermodal").modal("hide")
	get_template("codingmanagement",{})
}
function coderdeleted() {
	get_template("codingmanagement",{})
}

