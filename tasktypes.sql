-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Vært: localhost:3306
-- Genereringstid: 26. 10 2021 kl. 10:10:01
-- Serverversion: 8.0.26
-- PHP-version: 7.2.34-24+ubuntu20.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `opencoding`
--

-- --------------------------------------------------------


--
-- Data dump for tabellen `tasktypes`
--

INSERT INTO `tasktypes` (`tasktype_id`, `manualauto`, `tasktype_name`, `tasktype_description`, `playareatemplate`, `responseareatemplate`, `codeareatemplate`, `tasktype_instructions`, `insert_script`, `variables`, `styles`) VALUES
(1, 'manual', 'Shorttext', 'Short text answers', '{% for subtask_name in subtasks %}\n  <div class=\"alert alert-secondary\">\n     <span><em>{{subtask_name}}:</em></span>\n     <span data-task_name=\"{{subtask_name}}\"></span>\n  </div>\n{% endfor %}\n<div class=\"alert alert-primary\" data-task_name=\"{{task_name}}\"></div>\n\n\n', '<img src=\"{{task_image}}\">\n', '0', '', 'for(r of json.responses) {	$(\'[data-task_name=\"\'+r.task_name+\'\"]\').html(r.response) }', '{}', ''),
(2, 'manual', 'Brainstorm', 'Brainstorm', '<div id=\"brainstorm\"></div>', '<img src=\"{{task_image}}\">\n\n\n', '0', '', '	$(\"#brainstorm\").html(\"\");\r\n	json.responses[0].response.split(/[|\\n]/g).forEach(function(str) {\r\n		var row = str.split(\';\');\r\n		if(row.length !== 3) return;\r\n		var div = $(\'<div class=\"\'+(row[1] != \"{{testtakername}}\"?\"text-muted\":\"\")+\'\"><strong class=\"brainstorm-name\">\'+row[1]+\'</strong><span>\'+row[2]+\'</span></div>\');\r\n		$(\"#brainstorm\").append(div);   \r\n	})', '{\"testtakername\": \"You\"}', '#brainstorm {     height:600px;    overflow:scroll;}'),
(3, 'manual', 'Voxelcraft', 'Building 3D figures with square blocks', '<iframe width=\"800\" height=\"600\" src=\"{{gameurl}}\"></iframe>\n<div>\n  <button class=\"btn btn-secondary\" id=\"reloadscene\" title=\"Reload scene\"><i class=\"fas fa-redo-alt\"></i></button>\n</div>\n\n', '', '0', '', 'var scene=(json.responses[0].response?JSON.parse(json.responses[0].response).data:[]);\nif(typeof firstdone==\"undefined\") {    \n    onceMessage(\'ready\', function()\n    {        \n        sendMessage(\'setScene\',scene);        \n        firstdone=true;    })\n    }\nelse\n    sendMessage(\'setScene\',scene);\n$(\"#reloadscene\").click(function() {\n    sendMessage(\'setScene\',scene)\n    }\n)\n', '{\"gameurl\": \"../openPCIs/voxelcraft/game/\"}', ''),
(4, 'manual', '3Droom', 'Interior design of 3D rooms', '<iframe width=\"800\" height=\"600\" src=\"{{gameurl}}\"></iframe>\n<div>\n  <button class=\"btn btn-secondary\" id=\"reloadscene\" title=\"Reload scene\"><i class=\"fas fa-redo-alt\"></i></button>\n</div>\n\n', '', '0', '', 'var d=(json.responses[0].response?JSON.parse(json.responses[0].response):[]);\nif(typeof firstdone==\"undefined\") {    \n    onceMessage(\'ready\', function(){\n        sendMessage(\'loadExercise\',d);\n        firstdone=true;    })\n    } else sendMessage(\'loadExercise\',d);\n$(\"#reloadscene\").click(function() {\n    sendMessage(\'loadExercise\',d)\n    }\n)\n\n\n', '{\"gameurl\": \"../openPCIs/theroom/game/\"}', ''),
(5, 'auto', 'Clean Responses', 'Clean responses', '<h2 class=\"header\">Categories\n        <button class=\"btn btn-sm btn-secondary float-right mt-1\" id=\"export\">Export categories</button>\n        <input type=\"file\" id=\"importfile\" multiple accept=\"text/json\" style=\"display:none\">\n        <button class=\"btn btn-sm btn-secondary float-right mr-1 mt-1\" id=\"import\">Import categories</button>\n\n</h2>\n 	<div>\n 		<button data-sorttype=\"num\" data-sortdirection=\"-1\" class=\"sortcat btn btn-bw\">Sort by frequency</button>\n 		<button data-sorttype=\"alpha\" data-sortdirection=\"1\" class=\"sortcat btn btn-bw\">Sort alphabetically</button>\n 	</div>\n <div id=\"categorylist\" class=\"scroller\">\n 	<dl id=\"categories\"></dl>\n </div> \n\n\n\n\n\n\n\n', '\n', '<div class=\"header\" >\n <h2>Words</h2>\n</div>\n<div id=\"wordlist\">\n 	<div >\n 		<button data-sorttype=\"num\" data-sortdirection=\"1\" class=\"sort btn btn-bw\">Sort by frequency</button>\n 		<button data-sorttype=\"alpha\" data-sortdirection=\"1\" class=\"sort btn btn-bw\">Sort alphabetically</button>\n 	</div>\n 	<div class=\"scroller\">\n 		<dl id=\"words\"></dl>\n 	</div>\n</div>\n\n\n', '<h3>Coding</h3> 					<dl> 						<dt>Double click</dt> 						<dd>Double clicking on a word creates a new category named after that word.</dd> 						<dt>Edit spelling</dt> 						<dd>The categories can be edited by clicking on them.</dd> 						<dt>Drag and drop</dt> 						<dd>Drag words to the category they belong to.</dd> 						<dd>Or drag words to New category to create a new category (same as double clicking).</dd> 						<dt>Sort words</dt> 						<dd>Click on the sort buttons to sort the words numerically or alphabetically.</dd> 						<dt>Save</dt> 						<dd>The current state of coding is saved when you click finish, so you can save and return later.</dd> 					</dl>', 'var words\nvar leavingCat=false\nfunction init() {\n	$(\".sort\").click(buttonsortwords)\n	$(\".sortcat\").click(sortcats)\n	$(\"#export\").click(exportdata)\n    $(\"#import\").click(function() {$(\"#importfile\").click()})\n    $(\"#importfile\").change(importdata)\n	if(responses!=null) {\n        prepareCategories(data)\n	}\n}\nfunction prepareCategories(data) {\n      var resparr=[]\n      for(var i=0;i<responses.length;i++) {\n        for(const p in responses[i].response) {\n          resparr.push(responses[i].response[p])\n        }\n      }\n      //Only include words that has responses (when importing words)\n		if(typeof data.words==\"undefined\") data.words={}\n		words=Object.keys(data.words)\n              .filter(key => resparr.includes(key))\n                .reduce((obj, key) => {\n                  obj[key] = data.words[key];\n                  return obj;\n                }, {});\n		buildList()\n		sortwords(\"num\",-1)\n}\nfunction exportdata() {\n	var data={words:words}\n    var hiddenElement = document.createElement(\'a\');\n    hiddenElement.href = \'data:text/json;charset=utf-8,\' + JSON.stringify(data);\n    hiddenElement.target = \'_blank\';\n    hiddenElement.download = \'categories.json\';\n    hiddenElement.click();\n}\nfunction importdata() {\n    var importfile=this.files[0];\n    const reader = new FileReader();\n    reader.onload = (function() { prepareCategories(JSON.parse(reader.result))});\n    reader.readAsText(importfile);\n    \n}\nfunction save() {\n	data={words:words}\n	//console.log(items)\n    \n	responses=responses.map(function(r) {\n      var response=r.response\n      var len=Math.min(items.length,Object.keys(response).length)\n      for(var i=0;i<len;i++) { \n      	var item=items[i] //We use items in the order they are created/ordered\n        var task=Object.keys(response)[i] //This might create a mess - object order is not guaranteed...\n  		r[item]=((response[task]!=\"\" && typeof(words[response[task].trim()])!=\"undefined\" && words[response[task].trim()].category!=\"\")?words[response[task].trim()].category:\"\")\n//console.log(i+\": \"+item+\" og \"+task+\" er \"+response[task]+\" blev \"+words[response[task]].category)\n      }\n//console.log(r)\n      return r\n	})\nconsole.log(responses)\n}\nfunction updatewords() {\n	$(\".word\").each(function() { \n		var word=$(this).find(\".name\").text()\n		var cat=$(this).prevAll(\".category\")\n		var category=\"\"\n		if(cat.length>0) category=cat.first().find(\".name\").text()\n		words[word].category=category\n	})\n}\nfunction sortcats() {\n	var sorttype=$(this).data(\"sorttype\")\n	var sortdirection=$(this).data(\"sortdirection\")\n	$(this).data(\"sortdirection\",sortdirection==1?-1:1)\n	var newcat=$(\".newcategory,#lastph\").remove()\n	var elemobj=$(\"#categories .category\").map(function() {return $(this).add($(this).nextUntil(\"dt\"))})\n	var neworder=elemobj.sort(function(a,b) {\n		switch(sorttype) {\n			case \"alpha\": \n			console.log(sortdirection*$($(a)[0]).children(\".name\").text().localeCompare($($(b)[0]).children(\".name\").text()))\n				return sortdirection*$($(a)[0]).children(\".name\").text().localeCompare($($(b)[0]).children(\".name\").text())\n				break\n			case \"num\":\n				var an=Number($($(a)[0]).children(\".num\").text())\n				var bn=Number($($(b)[0]).children(\".num\").text())\n				console.log(sortdirection*(an>bn?1:an<bn?-1:0))\n				return sortdirection*(an>bn?1:an<bn?-1:0)\n		}\n	})\n	$(\"#categories\").html(\"\")\n	for(var obj of neworder) {\n		for(var obj1 of obj) $(\"#categories\").append(obj1)\n	}\n	$(\"#categories\").append(newcat)\n 	$(\"#categories .word\").on(\"dblclick\",addword)\n\n}\nfunction buttonsortwords() {\n	var sorttype=$(this).data(\"sorttype\")\n	var sortdirection=$(this).data(\"sortdirection\")\n	$(this).data(\"sortdirection\",sortdirection==1?-1:1)\n	sortwords(sorttype,sortdirection)\n}\nfunction sortwords(sorttype,sortdirection) {\n	var neworder=$(\"#words .word\").sort(function(a,b) {\n		switch(sorttype) {\n			case \"alpha\": \n				return sortdirection*$(a).find(\".name\").text().localeCompare($(b).find(\".name\").text())\n				break\n			case \"num\":\n				var an=Number($(a).find(\".num\").text())\n				var bn=Number($(b).find(\".num\").text())\n				return sortdirection*(an>bn?1:an<bn?-1:0)\n		}\n	})\n	$(\"#words\").html(neworder)\n	$(\"#words .word\").on(\"dblclick\",addword)\n\n}\nfunction buildList() {\n	// List all unique answers \n	if(typeof words==\"undefined\") {\n		words={}\n	} else {\n		for(const [w,v] of Object.entries(words)) {\n			words[w].instances=0\n		}\n	}\n	for(var r=0;r<responses.length;r++) {\n        for(const p in responses[r].response) {\n    		if(typeof(responses[r].response[p])!=\"undefined\" && responses[r].response[p].trim()!=\"\") {\n    			var word=responses[r].response[p].trim()\n    			if(typeof(words[word])==\"undefined\") words[word]={instances:1,category:\"\"}\n    			words[word].instances+=1\n    		}\n        }\n  	}\n//	sessionStorage.setItem(\"words\", JSON.stringify(words)); \n	showWords()\n}\nfunction showWords() {\n	// Reset categories\n	$(\"#categories\").html(\'<dt class=\"category newcategory\">New Category</dt><dd id=\"lastph\">+</dd>\')\n	var list=\"\"\n	for(var w in words) {\n		var word=\'<dd class=\"word\"><span class=\"name\">\'+w+\'</span> (<span class=\"num\">\'+words[w].instances+\'</span>)\'+\'</dd>\'\n		var wordelem=$(word)\n		var cat=words[w].category.normalize()\n		if(cat!=\"\") {\n			var dt=$(\"dt\").filter(function(){ return $(this).find(\".name\").text().normalize() === cat})\n			if(dt.length==0) {\n				var dt=$(\".newcategory\").clone()\n				dt.removeClass(\"newcategory\")\n				dt.html(wordelem.html())\n				dt.find(\".name\").text(cat)\n				contenteditablename(dt.find(\".name\"))\n				dt.prependTo(\"#categories\")\n			}\n			dt.after(wordelem)\n			updatesum(wordelem)\n		} else list+=word\n	}\n	$(\"#words\").html(list)\n	$(\"#words\").sortable({\n		connectWith:\"#categories\",\n		placeholder: \"ui-state-highlight\",\n		stop: dropped\n	}).disableSelection();\n	$(\"#categories\").sortable({\n		connectWith:\"#categories,#words\",\n		placeholder: \"ui-state-highlight\",\n		items: \"> dd\",\n		stop: dropped,\n		start: started\n	}).disableSelection();\n	$(\".word\").on(\"dblclick\",addword)\n}\nfunction contenteditablename(elem) {\n	elem.attr(\"contenteditable\",true)\n	elem.keydown(function(e) {\n		if(e.keyCode==13) $(this).blur()\n	})\n	elem.on(\"blur\",updatewords)\n}\nfunction addword() {\n	var word=$(this)\n	var dt=$(\".newcategory\").clone()\n	dt.removeClass(\"newcategory\")\n	dt.html(word.html())\n	\ncontenteditablename(dt.find(\".name\"))\n	dt.appendTo(\"#categories\")\n	dt.after(word)\n	updatesum(dt)\n	$(\".newcategory\").appendTo(\"#categories\")\n	$(\"#lastph\").appendTo(\"#categories\")\n	updatewords()\n}\nfunction started(event,ui) {\n	var item=$(ui.item)\n	leavingCat=item.prev(\".category\")\n	console.log(leavingCat)\n}\nfunction dropped(event,ui) {\n	var word=$(ui.item)\n	if(word.prev().length==0 || word.prev().text()==\"New Category\" || word.prev().attr(\"id\")==\"lastph\") {\n		var newcat=$(\".newcategory\").clone()\n		newcat.removeClass(\"newcategory\")\n		word.before(newcat)\n		newcat.html(word.html())\n		contenteditablename(newcat.find(\".name\"))\n		$(\".newcategory\").appendTo(\"#categories\")\n		$(\"#lastph\").appendTo(\"#categories\")\n	} else { // update number of members\n		updatesum(word)\n	}\n	if(leavingCat && !leavingCat.next().hasClass(\"word\")) {\n		leavingCat.remove()\n	} else updatesum(word)\n	leavingCat=false\n	updatewords()\n}\nfunction updatesum(word) {\n	var cat=word.prevAll(\".category\").index() // First previous category\n	var last=word.nextAll(\".category\").index() // Last previous category\n	var members=0\n	for(var i=cat+1;i<last;i++) members+=Number($($(\"#categories\").children().get(i)).find(\".num\").text())\n	$($(\"#categories\").children().get(cat)).find(\".num\").text(members)\n\n}', '{}', '.placeholder {\n	width:100px;\n	height:20px;\n	background-color:#f8f9fa;\n	border-style:solid;\n	border-width:1px;\n	border-color:#CCC;\n}\ndd {\n	padding-left:15px;\n}\n.scroller {\n	max-height:700px;\n	overflow:scroll;\n	resize:vertical;\n}\n.header {\n	height:40px;\n	padding: 0px 15px 4px 15px;\n	margin: 20px 0px 20px 0px ;\n	background-color:#f8f9fa;\n	border-style:solid;\n	border-width:1px;\n	border-color:#CCC;\n}\n\n'),
(6, 'auto', '3DRoom Auto', 'Use a script to auto-code 3D room responses.', '<iframe width=\"100%\" height=\"600\" src=\"{{gameurl}}\"></iframe>\n', '<div><button id=\"rescoreThisBtn\" class=\"btn btn-success\">Code this response</button><button id=\"rescoreAllBtn\" class=\"btn btn-success float-right\">Code all responses</button></div>\n<div class=\"quill\" id=\"codingscript\" style=\"width:100%; max-height:400px;\" placeholder=\"Write a coding script here\"></div>\n\n', '', '', 'var currentRow=0;\nfunction init() {\n	if(typeof responses!=\"undefined\") {\n        quill.setContents([\n        { insert: (typeof data.script!=\"undefined\"?data.script:\'function score(){\\r  return {}\\r}\')+\'&slashn;\'},//, attributes: {\'code-block\':true}}, //Did not format html ...\n        ]);    \n        quill.formatLine(0,quill.getLength(),\"code-block\",true)\n        \n        onceMessage(\'ready\', function(){\n            loadCurrentRow()\n          });\n      $(\".nextautoresponse\").click(function() {\n        currentRow+=($(this).data(\"next\")==\">\"?1:-1)\n        if(currentRow<0) currentRow=responses.length\n        if(currentRow>responses.length) currentRow=0\n        loadCurrentRow()\n      })\n      $(\"#rescoreThisBtn\").click(function() {\n        loadCurrentRow()\n      })\n      $(\"#rescoreAllBtn\").click(function() {\n            currentRow=-1;\n            next();\n      })\n	}\n}\n\nfunction next(){\n      currentRow++;\n//   console.log(\"rowno \"+rowno)\n      if(currentRow >= responses.length) {\n        currentRow = 0;\n        loadCurrentRow();\n        showMessage(\"Autocoding completed\")\n      } else {\n          recalculateRoom(currentRow,next);\n      }\n}\n\nfunction loadCurrentRow() {\n  sendMessage(\'setScoringFunction\',quill.getText(0));\n  $(\"#response_id\").val(responses[currentRow].response_id)\n  recalculateRoom(currentRow,fillItems);\n}\nfunction fillItems() {\n  $(\".itemvalue\").each(function() {\n    var val=responses[currentRow][$(this).data(\"item_name\")]\n    $(this).val(val?val:0)\n  })\n}\nfunction recalculateRoom(rowNo, callback){\n//     \n    var response=responses[rowNo].response\n    if(!response || response.length==0) {\n        $(\"#waiticon\").hide()\n        callback();\n    } else {\n        onceMessage(\'rescore\', function(event){\n            responses[rowNo] = Object.assign(responses[rowNo],JSON.parse(event.data.value).score);\n            $(\"#waiticon\").hide()\n            setTimeout(callback, 10);\n        });\n        try {\n            var resp=response[Object.keys(response)[0]]\n            var thisdata = JSON.parse(resp.length>0?resp:\"{\\\"data\\\":[]}\");\n            $(\"#waiticon\").show()\n            sendMessage(\'rescore\', thisdata);\n        } catch(e){\n            console.log(\'JSON PARSE ERROR\', e);\n            // Remove the listener created in onceMessage\n            $(\"#waiticon\").hide()\n            messageListeners[\'rescore\'].pop()\n            callback();\n        }\n    }\n}\n\n\nfunction save() {\n    var script=quill.getText(0)\n	data={script:script}\n}\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n', '{\"gameurl\": \"../openPCIs/theroom/game/\", \"definition\": \"definition.json\"}', ''),
(7, 'auto', 'Voxelcraft Auto', 'Use script to auto-code Voxelcraft responses.', '<iframe width=\"100%\" height=\"600\" src=\"{{gameurl}}\"></iframe>\n\n', '<div><button id=\"rescoreThisBtn\" class=\"btn btn-success\">Code this response</button><button id=\"rescoreAllBtn\" class=\"btn btn-success float-right\">Code all responses</button></div>\n<div class=\"quill\" id=\"codingscript\" style=\"width:100%; max-height:400px;\" placeholder=\"Write a coding script here\"></div>\n\n\n\n\n\n\n', '', '', 'var currentRow=0;\nfunction init() {\n	if(typeof responses!=\"undefined\") {\n        quill.setContents([\n        { insert: (typeof data.script!=\"undefined\"?data.script:\'function score(){\\r  return {}\\r}\')+\'&slashn;\'},//, attributes: {\'code-block\':true}}, //Did not format html ...\n        ]);    \n        quill.formatLine(0,quill.getLength(),\"code-block\",true)\n        \n        onceMessage(\'ready\', function(){\n            loadCurrentRow()\n          });\n      $(\".nextautoresponse\").click(function() {\n        currentRow+=($(this).data(\"next\")==\">\"?1:-1)\n        if(currentRow<0) currentRow=responses.length\n        if(currentRow>responses.length) currentRow=0\n        loadCurrentRow()\n      })\n      $(\"#rescoreThisBtn\").click(function() {\n        loadCurrentRow()\n      })\n      $(\"#rescoreAllBtn\").click(function() {\n            currentRow=-1;\n            next();\n      })\n	}\n}\n\nfunction next(){\n      currentRow++;\n//   console.log(\"rowno \"+rowno)\n      if(currentRow >= responses.length) {\n        currentRow = 0;\n        loadCurrentRow();\n        showMessage(\"Autocoding completed\")\n      } else {\n          recalculateVoxelcraft(currentRow,next);\n      }\n}\n\nfunction loadCurrentRow() {\n  sendMessage(\'setScoringFunction\',quill.getText(0));\n  $(\"#response_id\").val(responses[currentRow].response_id)\n  recalculateVoxelcraft(currentRow,fillItems);\n}\nfunction fillItems() {\n  $(\".itemvalue\").each(function() {\n    $(this).val(responses[currentRow][$(this).data(\"item_name\")])\n  })\n}\nfunction recalculateVoxelcraft(rowNo, callback){\n//     \n    var response=responses[rowNo].response\n    if(!response || response.length==0) {\n      response=\'{\"data\":[]}\'\n    }\n        onceMessage(\'rescore\', function(event){\n            //console.log(\'rescore\', event.data.value);\n            responses[rowNo] = Object.assign(responses[rowNo],event.data.value.score);\n            //console.log(responses[rowNo])\n            //console.log(\"hiding\")\n            $(\"#waiticon\").hide()\n            setTimeout(callback, 10);\n        });\n        try {\n            var resp=response[Object.keys(response)[0]]\n            var thisdata = JSON.parse(resp.length>0?resp:\"{\\\"data\\\":[]}\");\n//            console.log(thisdata)\n            $(\"#waiticon\").show()\n            sendMessage(\'rescore\', thisdata);\n        } catch(e){\n            console.log(\'JSON PARSE ERROR\', e);\n            // Remove the listener created in onceMessage\n            //console.log(\"hiding\")\n            $(\"#waiticon\").hide()\n            messageListeners[\'rescore\'].pop()\n            callback();\n        }\n}\n\n\nfunction save() {\n    var script=quill.getText(0)\n	data={script:script}\n}\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n', '{\"gameurl\": \"../openPCIs/voxelcraft/game/\"}', ''),
(8, 'auto', 'MC Auto', 'Code correct response in TAO MC interaction', '', '', '', '<h2>Response-variables</h2><p>Responses are in the response-object. Access responses from a task using</p><pre class=\"ql-syntax\" spellcheck=\"false\">var resp=response[taskname] \n</pre><h3>Multiple Choice-functions</h3><pre class=\"ql-syntax\" spellcheck=\"false\">makeMC(resp) \n</pre><p>Creates an object from TAO\'s Multiple Choice-interaction. You don\'t have to run the responses through this function. scoreMC does it for you.</p><pre class=\"ql-syntax\" spellcheck=\"false\">scoreMC(resp,correct=[])\n</pre><p>Scores a Multiple Choice from TAO. Give the correct choices as an array. </p><pre class=\"ql-syntax\" spellcheck=\"false\">scoreSC(resp,correct=[])\n</pre><p>Scores a Single Choice from TAO. Give the correct choices as an array.</p>', '///////////////////////\n// MC-functions\nfunction makeMC(resp) {\n  // MC is in a format that is simlar to JSON, but we need to convert it a little...\n  resp=(resp==\"\"?\"[]\":resp.replace(/\'/g,\"\\\"\").replace(/;/g,\",\"))\n  try {\n    return JSON.parse(resp)\n  } catch(e) {\n    var error=_(\"Error in JSON. response_id: <b>{0}</b> resp: <pre style=\\\"overflow:scroll;max-height:30px\\\">{1}</pre>. Message: {2}<br>\",$(\"#response_id\").val(),resp,e.message)\n    warnings+=error\n    return {}\n  }\n\n}\nfunction scoreMC(resp,correct=[]) {\n  if(typeof resp !== \'array\') resp=makeMC(resp)\n  return (resp.filter(value => correct.includes(value))?1:0)\n}\nfunction scoreSC(resp,correct=[]) {\n  return (correct.indexOf(resp)>-1?1:0)\n}\n\n', '{}', ''),
(9, 'auto', 'Match Auto', '', '', '', '', '<h2>Response-variables</h2><p>Responses are in the response-object. Access responses from a task using</p><pre class=\"ql-syntax\" spellcheck=\"false\">var resp=response[taskname] \n</pre><h3>Match-functions</h3><pre class=\"ql-syntax\" spellcheck=\"false\">makeMatch(resp, variablesInColumns=false) \n</pre><p>Creates an object from TAO\'s Match-interaction. You don\'t have to run the responses through this function. scoreMatch does it for you.</p><pre class=\"ql-syntax\" spellcheck=\"false\">scoreMatch(resp,identifier,correct=[],variablesInColumns = false)\n</pre><p>Scores a Match from TAO. Give the identifier which should be matched with the correct choices. Choices is given as an array. If you want to transpose the object, use variablesInColumns.</p>', '///////////////////////\n// Match-functions\nfunction makeMatch(resp, variablesInColumns=false) {\n // Match is in a format that is simlar to JSON, but we need to convert it a little...\n resp=(resp==\"\"?\"{}\":resp.replace(/; /g,\",\").replace(/ /g,\":\").replace(\"[\",\"{\").replace(\"]\",\"}\").replace(/([0-9a-z_]+)/gi,\'\"$1\"\'))\nif(variablesInColumns) {\n  resp=resp.replace(/(\".*?\"):(\".*?\")/g,\"$2:$1\")\n}\n try {\nreturn JSON.parse(resp)\n} catch(e) {\nvar error=_(\"Error in JSON. response_id: <b>{0}</b> resp: <pre style=\\\"overflow:scroll;max-height:30px\\\">{1}</pre>. Message: {2}<br>\",$(\"#response_id\").val(),resp,e.message)\nwarnings+=error\nreturn {}\n}\n}\nfunction scoreMatch(resp,identifier,correct=[],variablesInColumns = false) {\nif(typeof resp !== \'object\') resp=makeMatch(resp,variablesInColumns)\n return (correct.indexOf(resp[identifier])>-1?1:0)\n}\n\n', '{}', ''),
(10, 'auto', 'Gap Match Auto', '', '', '', '', '<h2>Response-variables</h2><p>Responses are in the response-object. Access responses from a task using</p><pre class=\"ql-syntax\" spellcheck=\"false\">var resp=response[taskname] \n</pre><h2>Gap Match Functions</h2><pre class=\"ql-syntax\" spellcheck=\"false\">makeTextGapMatch(resp)\n</pre><p>Creates a GapMatch-object. You don\'t have to call makeTextGapMatch - textInGap does it for you.</p><pre class=\"ql-syntax\" spellcheck=\"false\">textInGap(resp,text,gaps,noWhereElse=true)\n</pre><p>Test if a text is put in one of the slots given as a string or an array of strings. NowWhereElse=true only awards points if the text is not in another slot as well.</p>', '\n\n///////////////////////\n// GapMatch-functions\nfunction makeTextGapMatch(resp) {\n // GapMatch is in a format that is simlar to JSON, but we need to convert it a little...\n resp=(resp==\"\"?\"{}\":resp)//.replace(/\'/g,\"\\\"\").replace(/;/g,\",\"))\n try {\nreturn JSON.parse(resp)\n} catch(e) {\nvar error=_(\"Error in JSON. response_id: <b>{0}</b> resp: <pre style=\\\"overflow:scroll;max-height:30px\\\">{1}</pre>. Message: {2}<br>\",$(\"#response_id\").val(),resp,e.message)\nwarnings+=error\nreturn {}\n}\n}\n\nfunction textInGap(resp,text,gaps,noWhereElse=true) {\n if(typeof resp !== \'object\') resp=makeTextGapMatch(resp)\n var isIn=false\n for(gap of gaps) {\n   if(typeof resp[gap]!=\"undefined\" && resp[gap].indexOf(text)>-1) {\n      isIn=true\n      break\n   }\n }\n if(isIn && noWhereElse) {\n   var gaps=Object.keys(resp).filter(x => !gaps.includes(x));\n   for(gap of gaps) {\n      if(typeof resp[gap]!=\"undefined\" && resp[gap].indexOf(text)>-1) {\n         isIn=false\n         break\n      }\n   }\n }\n return isIn?1:0;\n}\n\n\n', '{}', ''),
(11, 'auto', 'Gantt Auto', '', '', '', '', '<h2>Response-variables</h2><p>Responses are in the response-object. Access responses from a task using</p><pre class=\"ql-syntax\" spellcheck=\"false\">var resp=response[taskname] \n</pre><h2>Gantt Functions</h2><pre class=\"ql-syntax\" spellcheck=\"false\">makeGantt(resp,names=[],timespan=30,timeFormat=\"d/M H:mm\")\n</pre><p>Makes a Gantt-object to be manipulated with these functions:</p><pre class=\"ql-syntax\" spellcheck=\"false\">isBefore(gantt,a=[],b=[],whichA=\"all\",whichB=\"all\",orEqual=false,strict=true,strictAfter=true)\n\nisFirst(gantt,a=\"\",which=\"all\",orEqual=false,strict=true) \n\nisAfter(gantt,a=[],b=[],whichA=\"all\",whichB=\"all\",orEqual=false,strict=true)\n\nisLast(gantt,a=[],which=\"all\",orEqual=false,strict=true) \n\nisOverlap(gantt,a=\"\",b=[],whichA=\"any\",whichB=\"any\") \n\nnoOverlap(gantt,a=[],b=[]) \n\nnumSlots(gantt,a=\"\") \n\ngetTime(gantt,a=[],which=\"start\",humanReadable=false) \n\ngetMinTime(gantt,a,which,strict=true)\n\ngetMaxTime(gantt,a,which,strict=true) \n\ngetDuration(gantt,a) \n</pre>', '///////////////////////\n// Gantt\nfunction makeGantt(gantt,names=[],timespan=30,timeFormat=\"d/M H:mm\") { // https://moment.github.io/luxon////parsing?id=table-of-tokens\n //Creaate object\n var ganttobj={gantt:{},names:names,timespan:timespan,timeFormat:timeFormat}\n // Extract intervals from JSON\n ganttarr=(gantt.length>0?JSON.parse(gantt.replace(/\'/g,\'\"\')).response:\"\")\n //Eksplode to list of lists\n ganttarr=ganttarr.split(\";\")\n //Give rows names and split rows\n for(var i=0;i<ganttarr.length;i++) {\n    ganttobj.gantt[names[i]]=ganttarr[i].split(\", \").map(function(x) {return x.split(\" - \").map(function (t) {return DateTime.fromFormat(t,timeFormat)}).flat()}).flat()\n }\n return ganttobj\n}\nfunction isBefore(gantt,a=[],b=[],whichA=\"all\",whichB=\"all\",orEqual=false,strict=true,strictAfter=true) {\n   //strictAfter is only used internally to control strict from isAfter\n   if(whichA==\"last\") whichA=\"all\" //Just to help the designer\n   if(whichB==\"first\") whichB=\"all\" //do.\n   if(typeof b!=\"object\") b=[b]\n   if(typeof a!=\"object\") a=[a]      \n   var res=1\n   for(var i of a) {\n      for(var j of b) {\n         if(typeof gantt.gantt[i]!=\"undefined\" && typeof gantt.gantt[j]!=\"undefined\") {\n            var aval=(whichA==\"all\"?gantt.gantt[i][gantt.gantt[i].length-1]:(whichA==\"first\"?gantt.gantt[i][0]:gantt.gantt[i][whichA])) // All, first or Given number\n            var bval=(whichB==\"all\"?gantt.gantt[j][gantt.gantt[j].length-1]:(whichB==\"last\"?gantt.gantt[j][0]:gantt.gantt[j][whichB])) // All, last or Given number\n            if(!strict & typeof bval==\"undefined\") bval=Infinity\n            if(!strictAfter & typeof aval==\"undefined\") aval=0\n            var thisres=orEqual?aval <= bval:aval < bval\n            res=typeof thisres==\"undefined\"?0:res & thisres\n         }\n      }\n   }\n   return(res)\n}\n\nfunction isFirst(gantt,a=\"\",which=\"all\",orEqual=false,strict=true) {\n var res=1\n for(var i of gantt.names) {\n   if(a!=i && !isBefore(gantt,a,i,which,\"all\",orEqual,strict)) {\n      res=0\n      break\n   }\n }\n return(res)\n}\n\nfunction isAfter(gantt,a=[],b=[],whichA=\"all\",whichB=\"all\",orEqual=false,strict=true) {\n   return isBefore(gantt,b,a,whichB,whichA,orEqual,true,strict)\n}\n\nfunction isLast(gantt,a=[],which=\"all\",orEqual=false,strict=true) {\n   var res=1\n   for(i of gantt.names) {\n       if(a!=i) res=(res & isAfter(gantt,a,i,which,\"all\",orEqual,strict))\n   }\n   return(res)\n}\n\n\n// @param whichA/whichB for isOverlap: any: one or more a/b elements overlap, all: all a/b elements need to overlap, first/first: First a/b needs to overlap, last/last: Last a/b needs to overlap, or use number (or sequence)\n// @rdname isBefore\n// @export\nfunction isOverlap(gantt,a=\"\",b=[],whichA=\"any\",whichB=\"any\") {\n   if(typeof gantt.gantt[a] == \"undefined\") return 0\n   if(typeof b!=\"object\") b=[b]\n   var res=whichA==\"all\"\n   var aval\n   switch(whichA) {\n      case \"all\": \n      case \"any\":\n         aval=gantt.gantt[a] // Test all elements\n         break\n      case \"first\":\n         aval=gantt.gantt[a][1] // First\n         break\n      case \"last\":\n         aval=gantt.gantt[a][gantt.gantt[a].length-1] // Last \n         break\n      default:\n         aval=gantt.gantt[a][whichA] // Given number (or sequence)\n   }\n   if(typeof aval!=\"undefined\") {\n      for(i of b) {\n         var ival\n         switch(whichB) {\n            case \"all\": \n            case \"any\":\n               ival=gantt.gantt[i] // Test all elements\n               break\n            case \"first\":\n               ival=gantt.gantt[i][1] // First\n               break\n            case \"last\":\n               ival=gantt.gantt[i][gantt.gantt[i].length-1] // Last \n               break\n            default:\n               ival=gantt.gantt[i][whichB] // Given number (or sequence)\n         }\n         var AinI=aval.filter(x=>ival.includes(x))\n         res=(whichA==\"any\" || whichB==\"any\")?\n               res | AinI.length>0:\n               whichA==\"all\"?\n                  res & AinI.length==ival.length:\n                  res | AinI.length==ival.length \n      }\n   }\n   return(res?1:0)\n}\n\n//noOverlap only if there is actually elements that could have overlapped.\n//b: character or vector of characters\n// @rdname isBefore\n// @export\nfunction noOverlap(gantt,a=[],b=[]) {\n   if(typeof gantt.gantt[a] == \"undefined\") return 0\n   if(typeof b!=\"object\") b=[b]\n   var res=1\n   for(i of b) {\n      res=res & (gantt.gantt[a].length>0 & gantt.gantt[i].length>0?\n               gantt.gantt[a].filter(x=>gantt.gantt[i].includes(x)).length>0:\n               0)\n   }\n   return (res)   \n}\n\n// numSlots\n//\n// @param gantt a gantt object (created by makeGantt)\n// @param a an element\n//\n// @return Returns number of time slots - also counting non-connected slots\n// @export\n//\n// @examples \n// response=\"{\'response\':\'1/8 10:30 - 1/8 12:30;1/8 13:00 - 1/8 14:00;1/8 11:30 - 1/8 12:30\'}\"\n// gantt=makeGantt(response,names=c(\"waitress\",\"actor\",\"pianist\"),timespan=30,timeFormat=\"%d/%m %H:%M\")\n// numSlots(gantt,\"actor\")\nfunction numSlots(gantt,a=\"\") {\n return (typeof gantt.gantt[a]==\"undefined\"?0:gantt.gantt[a].length)\n}\n\n// Get start or end time of an element\n//\n// @param gantt A gantt object (created by makeGantt)\n// @param a an element\n// @param which start, end\n// @param humanReadable If true, time is given in human readable format\n//\n// @return\n// @export\n//\n// @examples\n// response=\"{\'response\':\'1/8 10:30;1/8 13:00 - 1/8 14:00;1/8 11:30 - 1/8 12:30\'}\"\n// gantt=makeGantt(response,names=c(\"waitress\",\"actor\",\"pianist\"),timespan=30,timeFormat=\"%d/%m %H:%M\")\n// getTime(gantt,\"waitress\",which=\"end\",humanReadable=true)\n// getTime(gantt,\"actor\",which=\"end\",humanReadable=true)\nfunction getTime(gantt,a=[],which=\"start\",humanReadable=false) {\n   if(typeof a!=\"object\") a=[a]\n   if(typeof gantt.gantt[a]==\"undefined\") return(NaN)\n   var time=(which==\"start\"?gantt.gantt[a][0].valueOf():gantt.gantt[a][gantt.gantt[a].length-1].valueOf()+gantt.timespan*60)\n   return (humanReadable?time.toFormat(gantt.timeFormat):time)\n}\n\n// Get the minimum/maximum start/end time among elements\n//\n// @param gantt A gantt object (created by makeGantt)\n// @param a a vector of elements to compare\n// @param which Which time to compare (\"start\" or \"end\")\n// @param strict If true and an element among the compared is not present, NA is returned. \n//\n// @return Returns minimum/maximum time in seconds of the earliest/latest element starting/ending\n// @export\n//\n// @examples\n// response=matrix(c(\"{\'response\':\'1/8 17:00;;1/8 17:00 - 1/8 18:00;1/8 18:00, 1/8 20:00 - 1/8 21:00;1/8 20:30;1/8 20:00 - 1/8 20:30;1/8 19:30 - 1/8 20:30\'}\"))\n// gantt=makeGantt(response,names=c(\"waitress\",\"actor\",\"pianist\",\"bartender\",\"cleaning\",\"ticketer\",\"musician\"),timespan=30,timeFormat=\"%d/%m %H:%M\")\n// getMinTime(gantt,c(\"pianist\",\"bartender\"),which=\"start\")\n// getMaxTime(gantt,c(\"actor\",\"bartender\"),which=\"end\",strict=false)\n// getMaxTime(gantt,\"actor\",which=\"end\")\n// as.difftime(getMaxTime(gantt,c(\"pianist\",\"bartender\"),which=\"end\")-getMinTime(gantt,c(\"pianist\",\"bartender\"),which=\"start\"),units = \"secs\")\n// \nfunction getMinTime(gantt,a,which,strict=true) {\n   if(typeof a!=\"object\") a=[a]\n   return a.map(i=>getTime(gantt,i,which)).reduce((x,y)=>Math.min(x,y),Infinity)\n}\n// a is a vector of names\n// @rdname getMinTime\n// @export\nfunction getMaxTime(gantt,a,which,strict=true) {\n   if(typeof a!=\"object\") a=[a]\n   return a.map(i=>getTime(gantt,i,which)).reduce((x,y)=>Math.max(x,y),0)\n}\n\n// Get duration\n//\n// @param gantt A gantt object (created by makeGantt)\n// @param a An element\n//\n// @return Returns duration in seconds\n// @export\n//\n// @examples\n// response=matrix(c(\"{\'response\':\'1/8 17:00;1/8 17:00 - 1/8 17:30;1/8 17:00 - 1/8 18:00;1/8 17:00, 1/8 18:00, 1/8 19:00, 1/8 20:00;1/8 20:30;1/8 20:00 - 1/8 20:30;1/8 19:30 - 1/8 20:30\'}\"))\n// gantt=makeGantt(response2,names=c(\"waitress\",\"actor\",\"pianist\",\"bartender\",\"cleaning\",\"ticketer\",\"musician\"),timespan=30,timeFormat=\"%d/%m %H:%M\")\n// getDuration(gantt,\"actor\")\n// getDuration(gantt,\"waitress\")\n// getDuration(gantt,\"bartender\")\n// getDuration(gantt,\"cleaning\")\n\nfunction getDuration(gantt,a) {\n return numSlots(gantt,a)*gantt.timespan*60\n}', '{}', '');

