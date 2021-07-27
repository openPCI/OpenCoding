-- phpMyAdmin SQL Dump
-- version 4.9.7deb1
-- https://www.phpmyadmin.net/
--
-- Vært: localhost:3306
-- Genereringstid: 27. 07 2021 kl. 18:41:52
-- Serverversion: 8.0.26
-- PHP-version: 7.4.18

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
-- Struktur-dump for tabellen `assign_task`
--

CREATE TABLE `assign_task` (
  `task_id` int UNSIGNED NOT NULL,
  `coder_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `assign_test`
--

CREATE TABLE `assign_test` (
  `test_id` int UNSIGNED NOT NULL,
  `coder_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `coded`
--

CREATE TABLE `coded` (
  `response_id` int UNSIGNED NOT NULL,
  `codes` json NOT NULL,
  `coder_id` int NOT NULL,
  `coding_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isdoublecode` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `flags`
--

CREATE TABLE `flags` (
  `flag_id` int UNSIGNED NOT NULL,
  `response_id` int UNSIGNED NOT NULL,
  `flagstatus` enum('flagged','resolved') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `coder_id` int UNSIGNED NOT NULL,
  `comments` json NOT NULL,
  `manager_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `projects`
--

CREATE TABLE `projects` (
  `project_id` int UNSIGNED NOT NULL,
  `project_name` varchar(256) NOT NULL,
  `unit_id` int UNSIGNED NOT NULL,
  `doublecodingpct` tinyint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Data dump for tabellen `projects`
--

INSERT INTO `projects` (`project_id`, `project_name`, `unit_id`, `doublecodingpct`) VALUES
(1, 'GBL21', 1, 5);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `responses`
--

CREATE TABLE `responses` (
  `response_id` int UNSIGNED NOT NULL,
  `task_id` int UNSIGNED NOT NULL,
  `testtaker` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `response` mediumtext NOT NULL,
  `response_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int UNSIGNED NOT NULL,
  `task_name` varchar(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `task_description` varchar(256) NOT NULL,
  `task_image` mediumblob NOT NULL,
  `tasktype_id` mediumint UNSIGNED NOT NULL,
  `tasktype_variables` json NOT NULL,
  `items` json NOT NULL,
  `scoring_rubrics` text NOT NULL,
  `task_data` json NOT NULL,
  `group_id` int UNSIGNED NOT NULL DEFAULT '0',
  `test_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `tasktypes`
--

CREATE TABLE `tasktypes` (
  `tasktype_id` int UNSIGNED NOT NULL,
  `manualauto` enum('manual','auto') NOT NULL DEFAULT 'manual',
  `tasktype_name` varchar(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `tasktype_description` varchar(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `playareatemplate` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `responseareatemplate` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `codeareatemplate` text NOT NULL,
  `tasktype_instructions` text NOT NULL,
  `insert_script` text NOT NULL,
  `variables` json NOT NULL,
  `styles` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Data dump for tabellen `tasktypes`
--

INSERT INTO `tasktypes` (`tasktype_id`, `manualauto`, `tasktype_name`, `tasktype_description`, `playareatemplate`, `responseareatemplate`, `codeareatemplate`, `tasktype_instructions`, `insert_script`, `variables`, `styles`) VALUES
(1, 'manual', 'Shorttext', 'Short text answers', '<img src=\"{{task_image}}\">', '{% for subtask_name in subtasks %}    <div class=\"alert alert-secondary\"><span><em>{{subtask_name}}:</em></span> <span data-task_name=\"{{subtask_name}}\"></span></div>{% endfor %}\r\n<div class=\"alert alert-primary\" data-task_name=\"{{task_name}}\"></div>', '', '', 'for(r of json.responses) {	$(\'[data-task_name=\"\'+r.task_name+\'\"]\').html(r.response) }', '{}', ''),
(2, 'manual', 'Brainstorm', 'Brainstorm', '<div id=\"brainstorm\"></div>', '', '', '', '	$(\"#brainstorm\").html(\"\");\r\n	json.responses[0].response.split(/[|\\n]/g).forEach(function(str) {\r\n		var row = str.split(\';\');\r\n		if(row.length !== 3) return;\r\n		var div = $(\'<div class=\"\'+(row[1] != \"{{testtakername}}\"?\"text-muted\":\"\")+\'\"><strong class=\"brainstorm-name\">\'+row[1]+\'</strong><span>\'+row[2]+\'</span></div>\');\r\n		$(\"#brainstorm\").append(div);   \r\n	})', '{\"testtakername\": \"Dig\"}', '#brainstorm {     height:600px;    overflow:scroll;}'),
(3, 'manual', 'Voxelcraft', 'Building 3D figures with square blocks', '<iframe width=\"800\" height=\"600\" src=\"{{gameurl}}\"></iframe>', '', '', '', 'var scene=(json.responses[0].response?JSON.parse(json.responses[0].response).data:[]);if(typeof firstdone==\"undefined\") {	onceMessage(\'ready\', function(){		sendMessage(\'setScene\',scene);		firstdone=true;	}) } else sendMessage(\'setScene\',scene);', '{\"gameurl\": \"../openPCIs/voxelcraft/game/\"}', ''),
(4, 'manual', '3Droom', 'Interior design of 3D rooms', '<iframe width=\"800\" height=\"600\" src=\"{{gameurl}}\">\n</iframe>\n\n', '', '', '', 'var d=(json.responses[0].response?JSON.parse(json.responses[0].response):[]);if(typeof firstdone==\"undefined\") {	onceMessage(\'ready\', function(){		sendMessage(\'loadExercise\',d);		firstdone=true;	}) } else sendMessage(\'loadExercise\',d);', '{\"gameurl\": \"../openPCIs/theroom/game/\"}', ''),
(5, 'auto', 'Clean Responses', 'Clean responses', '<h2 class=\"header\">Categories</h2>\n 	<div>\n 		<button data-sorttype=\"num\" data-sortdirection=\"-1\" class=\"sortcat btn btn-bw\">Sort by frequency</button>\n 		<button data-sorttype=\"alpha\" data-sortdirection=\"1\" class=\"sortcat btn btn-bw\">Sort alphabetically</button>\n 	</div>\n <div id=\"categorylist\" class=\"scroller\">\n 	<dl id=\"categories\"></dl>\n </div> \n\n\n', '\n', '<div class=\"header\" >\n <h2>Words</h2>\n</div>\n<div id=\"wordlist\">\n 	<div >\n 		<button data-sorttype=\"num\" data-sortdirection=\"1\" class=\"sort btn btn-bw\">Sort by frequency</button>\n 		<button data-sorttype=\"alpha\" data-sortdirection=\"1\" class=\"sort btn btn-bw\">Sort alphabetically</button>\n 	</div>\n 	<div class=\"scroller\">\n 		<dl id=\"words\"></dl>\n 	</div>\n</div>\n\n\n', '<h3>Coding</h3> 					<dl> 						<dt>Double click</dt> 						<dd>Double clicking on a word creates a new category named after that word.</dd> 						<dt>Edit spelling</dt> 						<dd>The categories can be edited by clicking on them.</dd> 						<dt>Drag and drop</dt> 						<dd>Drag words to the category they belong to.</dd> 						<dd>Or drag words to New category to create a new category (same as double clicking).</dd> 						<dt>Sort words</dt> 						<dd>Click on the sort buttons to sort the words numerically or alphabetically.</dd> 						<dt>Save</dt> 						<dd>The current state of coding is saved when you click finish, so you can save and return later.</dd> 					</dl>', '\r\nvar words\r\nvar leavingCat=false\r\nfunction init() {\r\n	$(\".sort\").click(buttonsortwords)\r\n	$(\".sortcat\").click(sortcats)\r\n	\r\n	if(sessionStorage.getItem(\"responses\")!=null) {\r\n		data=JSON.parse(sessionStorage.getItem(\"data\"))\r\n		responses=JSON.parse(sessionStorage.getItem(\"responses\"))\r\n		words=data.words\r\n		buildList()\r\n		sortwords(\"num\",-1)\r\n	}\r\n}\r\nfunction save() {\r\n	var data={words:words}\r\n	console.log(data)\r\n	sessionStorage.setItem(\"data\", JSON.stringify(data)); \r\n	var items=JSON.parse(sessionStorage.getItem(\"items\")) //Names of the items\r\n	console.log(items)\r\n	var item=Object.keys(items)[0] //We only use the first item, no matter how many have been created.\r\n	responses=responses.map(function(response) {\r\n		response[item]=((response.response!=\"\" && typeof(words[response.response])!=\"undefined\" && words[response.response].category!=\"\")?words[response.response].category:\"\")\r\n		return response\r\n	})\r\n	sessionStorage.setItem(\"responses\", JSON.stringify(responses)); \r\n}\r\nfunction updatewords() {\r\n	$(\".word\").each(function() { \r\n		var word=$(this).find(\".name\").text()\r\n		var cat=$(this).prevAll(\".category\")\r\n		var category=\"\"\r\n		if(cat.length>0) category=cat.first().find(\".name\").text()\r\n		words[word].category=category\r\n	})\r\n}\r\nfunction sortcats() {\r\n	var sorttype=$(this).data(\"sorttype\")\r\n	var sortdirection=$(this).data(\"sortdirection\")\r\n	$(this).data(\"sortdirection\",sortdirection==1?-1:1)\r\n	var newcat=$(\".newcategory,#lastph\").remove()\r\n	var elemobj=$(\"#categories .category\").map(function() {return $(this).add($(this).nextUntil(\"dt\"))})\r\n	var neworder=elemobj.sort(function(a,b) {\r\n		switch(sorttype) {\r\n			case \"alpha\": \r\n			console.log(sortdirection*$($(a)[0]).children(\".name\").text().localeCompare($($(b)[0]).children(\".name\").text()))\r\n				return sortdirection*$($(a)[0]).children(\".name\").text().localeCompare($($(b)[0]).children(\".name\").text())\r\n				break\r\n			case \"num\":\r\n				var an=Number($($(a)[0]).children(\".num\").text())\r\n				var bn=Number($($(b)[0]).children(\".num\").text())\r\n				console.log(sortdirection*(an>bn?1:an<bn?-1:0))\r\n				return sortdirection*(an>bn?1:an<bn?-1:0)\r\n		}\r\n	})\r\n	$(\"#categories\").html(\"\")\r\n	for(var obj of neworder) {\r\n		for(var obj1 of obj) $(\"#categories\").append(obj1)\r\n	}\r\n	$(\"#categories\").append(newcat)\r\n 	$(\"#categories .word\").on(\"dblclick\",addword)\r\n\r\n}\r\nfunction buttonsortwords() {\r\n	var sorttype=$(this).data(\"sorttype\")\r\n	var sortdirection=$(this).data(\"sortdirection\")\r\n	$(this).data(\"sortdirection\",sortdirection==1?-1:1)\r\n	sortwords(sorttype,sortdirection)\r\n}\r\nfunction sortwords(sorttype,sortdirection) {\r\n	var neworder=$(\"#words .word\").sort(function(a,b) {\r\n		switch(sorttype) {\r\n			case \"alpha\": \r\n				return sortdirection*$(a).find(\".name\").text().localeCompare($(b).find(\".name\").text())\r\n				break\r\n			case \"num\":\r\n				var an=Number($(a).find(\".num\").text())\r\n				var bn=Number($(b).find(\".num\").text())\r\n				return sortdirection*(an>bn?1:an<bn?-1:0)\r\n		}\r\n	})\r\n	$(\"#words\").html(neworder)\r\n	$(\"#words .word\").on(\"dblclick\",addword)\r\n\r\n}\r\nfunction buildList() {\r\n	// List all unique answers \r\n	if(typeof words==\"undefined\") {\r\n		words={}\r\n	} else {\r\n		for(const [w,v] of Object.entries(words)) {\r\n			words[w].instances=0\r\n		}\r\n	}\r\n	for(var r=0;r<responses.length;r++) {\r\n		if(typeof(responses[r].response)!=\"undefined\" && responses[r].response.trim()!=\"\") {\r\n			var word=responses[r].response.trim()\r\n			if(typeof(words[word])==\"undefined\") words[word]={instances:1,category:\"\"}\r\n			words[word].instances+=1\r\n		}\r\n	}\r\n//	sessionStorage.setItem(\"words\", JSON.stringify(words)); \r\n	showWords()\r\n}\r\nfunction showWords() {\r\n	// Reset categories\r\n	$(\"#categories\").html(\'<dt class=\"category newcategory\">New Category</dt><dd id=\"lastph\">+</dd>\')\r\n	var list=\"\"\r\n	for(var w in words) {\r\n		var word=\'<dd class=\"word\"><span class=\"name\">\'+w+\'</span> (<span class=\"num\">\'+words[w].instances+\'</span>)\'+\'</dd>\'\r\n		var wordelem=$(word)\r\n		var cat=words[w].category.normalize()\r\n		if(cat!=\"\") {\r\n			var dt=$(\"dt\").filter(function(){ return $(this).find(\".name\").text().normalize() === cat})\r\n			if(dt.length==0) {\r\n				var dt=$(\".newcategory\").clone()\r\n				dt.removeClass(\"newcategory\")\r\n				dt.html(wordelem.html())\r\n				dt.find(\".name\").text(cat)\r\n				contenteditablename(dt.find(\".name\"))\r\n				dt.prependTo(\"#categories\")\r\n			}\r\n			dt.after(wordelem)\r\n			updatesum(wordelem)\r\n		} else list+=word\r\n	}\r\n	$(\"#words\").html(list)\r\n	// This takes a long time when words has been saved in sessionStorage - but not when it is loaded from csv. Doesn\'t make sense!\r\n	$(\"#words\").sortable({\r\n		connectWith:\"#categories\",\r\n		placeholder: \"ui-state-highlight\",\r\n		stop: dropped\r\n	}).disableSelection();\r\n	$(\"#categories\").sortable({\r\n		connectWith:\"#categories,#words\",\r\n		placeholder: \"ui-state-highlight\",\r\n		items: \"> dd\",\r\n		stop: dropped,\r\n		start: started\r\n	}).disableSelection();\r\n	$(\".word\").on(\"dblclick\",addword)\r\n}\r\nfunction contenteditablename(elem) {\r\n	elem.attr(\"contenteditable\",true)\r\n	elem.keydown(function(e) {\r\n		if(e.keyCode==13) $(this).blur()\r\n	})\r\n	elem.on(\"blur\",updatewords)\r\n}\r\nfunction addword() {\r\n	var word=$(this)\r\n	var dt=$(\".newcategory\").clone()\r\n	dt.removeClass(\"newcategory\")\r\n	dt.html(word.html())\r\n	contenteditablename(dt.find(\".name\"))\r\n	dt.appendTo(\"#categories\")\r\n	dt.after(word)\r\n	updatesum(dt)\r\n	$(\".newcategory\").appendTo(\"#categories\")\r\n	$(\"#lastph\").appendTo(\"#categories\")\r\n	updatewords()\r\n}\r\nfunction started(event,ui) {\r\n	var item=$(ui.item)\r\n	leavingCat=item.prev(\".category\")\r\n	console.log(leavingCat)\r\n}\r\nfunction dropped(event,ui) {\r\n	var word=$(ui.item)\r\n	if(word.prev().length==0 || word.prev().text()==\"New Category\" || word.prev().attr(\"id\")==\"lastph\") {\r\n		var newcat=$(\".newcategory\").clone()\r\n		newcat.removeClass(\"newcategory\")\r\n		word.before(newcat)\r\n		newcat.html(word.html())\r\n		contenteditablename(newcat.find(\".name\"))\r\n		$(\".newcategory\").appendTo(\"#categories\")\r\n		$(\"#lastph\").appendTo(\"#categories\")\r\n	} else { // update number of members\r\n		updatesum(word)\r\n	}\r\n	if(leavingCat && !leavingCat.next().hasClass(\"word\")) {\r\n		leavingCat.remove()\r\n	} else updatesum(word)\r\n	leavingCat=false\r\n	updatewords()\r\n}\r\nfunction updatesum(word) {\r\n	var cat=word.prevAll(\".category\").index() // First previous category\r\n	var last=word.nextAll(\".category\").index() // Last previous category\r\n	var members=0\r\n	for(var i=cat+1;i<last;i++) members+=Number($($(\"#categories\").children().get(i)).find(\".num\").text())\r\n	$($(\"#categories\").children().get(cat)).find(\".num\").text(members)\r\n\r\n}\r\n', '{}', '.placeholder {\n	width:100px;\n	height:20px;\n	background-color:#f8f9fa;\n	border-style:solid;\n	border-width:1px;\n	border-color:#CCC;\n}\ndd {\n	padding-left:15px;\n}\n.scroller {\n	max-height:700px;\n	overflow:scroll;\n	resize:vertical;\n}\n.header {\n	height:40px;\n	padding: 0px 15px 4px 15px;\n	margin: 20px 0px 20px 0px ;\n	background-color:#f8f9fa;\n	border-style:solid;\n	border-width:1px;\n	border-color:#CCC;\n}\n\n'),
(6, 'auto', '3DRoom Auto', 'Use a script to auto-code 3D room responses.', '<iframe width=\"100%\" height=\"600\" src=\"{{gameurl}}\"></iframe>\n', '<div><button id=\"rescoreThisBtn\" class=\"btn btn-success\">Code this response</button><button id=\"rescoreAllBtn\" class=\"btn btn-success float-right\">Code all responses</button></div>\n<div class=\"quill\" id=\"codingscript\" style=\"width:100%; max-height:400px;\" placeholder=\"Write a coding script here\"></div>\n\n', '', '', 'var currentRow=0;\nfunction init() {\n	if(sessionStorage.getItem(\"responses\")!=null) {\n		data=JSON.parse(sessionStorage.getItem(\"data\"))\n		responses=JSON.parse(sessionStorage.getItem(\"responses\"))\n        quill.setContents([\n        { insert: (typeof data.script!=\"undefined\"?data.script:\'\')+\'&slashn;\'},//, attributes: {\'code-block\':true}}, //Did not format html ...\n        ]);    \n        quill.formatLine(0,quill.getLength(),\"code-block\",true)\n        \n        onceMessage(\'ready\', function(){\n            loadCurrentRow()\n          });\n      $(\".nextautoresponse\").click(function() {\n        currentRow+=($(this).data(\"next\")==\">\"?1:-1)\n        if(currentRow<0) currentRow=responses.length\n        if(currentRow>responses.length) currentRow=0\n        loadCurrentRow()\n      })\n      $(\"#rescoreThisBtn\").click(function() {\n        loadCurrentRow()\n      })\n      $(\"#rescoreAllBtn\").click(function() {\n            currentRow=-1;\n            next();\n      })\n	}\n}\n\nfunction next(){\n      currentRow++;\n//   console.log(\"rowno \"+rowno)\n      if(currentRow >= responses.length) {\n        currentRow = 0;\n        loadCurrentRow();\n        showMessage(\"Autocoding completed\")\n      } else {\n          recalculateRoom(currentRow,next);\n      }\n}\n\nfunction loadCurrentRow() {\n  sendMessage(\'setScoringFunction\',quill.getText(0));\n  $(\"#response_id\").val(responses[currentRow].response_id)\n  recalculateRoom(currentRow,fillItems);\n}\nfunction fillItems() {\n  $(\".itemvalue\").each(function() {\n    var val=responses[currentRow][$(this).attr(\"name\")]\n    $(this).val(val?val:0)\n  })\n}\nfunction recalculateRoom(rowNo, callback){\n//     \n    var response=responses[rowNo].response\n    if(!response || response.length==0) {\n        $(\"#waiticon\").hide()\n        callback();\n    } else {\n        onceMessage(\'rescore\', function(event){\n            responses[rowNo] = Object.assign(responses[rowNo],JSON.parse(event.data.value).score);\n            $(\"#waiticon\").hide()\n            setTimeout(callback, 10);\n        });\n        try {\n            var thisdata = JSON.parse(response);\n            $(\"#waiticon\").show()\n            sendMessage(\'rescore\', thisdata);\n        } catch(e){\n            console.log(\'JSON PARSE ERROR\', e);\n            // Remove the listener created in onceMessage\n            $(\"#waiticon\").hide()\n            messageListeners[\'rescore\'].pop()\n            callback();\n        }\n    }\n}\n\n\nfunction save() {\n    var script=quill.getText(0)\n	var data={script:script}\n	console.log(data)\n	sessionStorage.setItem(\"data\", JSON.stringify(data)); \n	sessionStorage.setItem(\"responses\", JSON.stringify(responses)); \n}\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n', '{\"gameurl\": \"../openPCIs/theroom/game/\", \"definition\": \"definition.json\"}', ''),
(7, 'auto', 'Voxelcraft Auto', 'Use script to auto-code Voxelcraft responses.', '<iframe width=\"100%\" height=\"600\" src=\"{{gameurl}}\"></iframe>\n\n', '<div><button id=\"rescoreThisBtn\" class=\"btn btn-success\">Code this response</button><button id=\"rescoreAllBtn\" class=\"btn btn-success float-right\">Code all responses</button></div>\n<div class=\"quill\" id=\"codingscript\" style=\"width:100%; max-height:400px;\" placeholder=\"Write a coding script here\"></div>\n\n\n\n\n\n\n', '', '', 'var currentRow=0;\nfunction init() {\n	if(sessionStorage.getItem(\"responses\")!=null) {\n		data=JSON.parse(sessionStorage.getItem(\"data\"))\n		responses=JSON.parse(sessionStorage.getItem(\"responses\"))\n        quill.setContents([\n        { insert: (typeof data.script!=\"undefined\"?data.script:\'\')+\'&slashn;\'},//, attributes: {\'code-block\':true}}, //Did not format html ...\n        ]);    \n        quill.formatLine(0,quill.getLength(),\"code-block\",true)\n        \n        onceMessage(\'ready\', function(){\n            loadCurrentRow()\n          });\n      $(\".nextautoresponse\").click(function() {\n        currentRow+=($(this).data(\"next\")==\">\"?1:-1)\n        if(currentRow<0) currentRow=responses.length\n        if(currentRow>responses.length) currentRow=0\n        loadCurrentRow()\n      })\n      $(\"#rescoreThisBtn\").click(function() {\n        loadCurrentRow()\n      })\n      $(\"#rescoreAllBtn\").click(function() {\n            currentRow=-1;\n            next();\n      })\n	}\n}\n\nfunction next(){\n      currentRow++;\n//   console.log(\"rowno \"+rowno)\n      if(currentRow >= responses.length) {\n        currentRow = 0;\n        loadCurrentRow();\n        showMessage(\"Autocoding completed\")\n      } else {\n          recalculateVoxelcraft(currentRow,next);\n      }\n}\n\nfunction loadCurrentRow() {\n  sendMessage(\'setScoringFunction\',quill.getText(0));\n  $(\"#response_id\").val(responses[currentRow].response_id)\n  recalculateVoxelcraft(currentRow,fillItems);\n}\nfunction fillItems() {\n  $(\".itemvalue\").each(function() {\n    $(this).val(responses[currentRow][$(this).attr(\"name\")])\n  })\n}\nfunction recalculateVoxelcraft(rowNo, callback){\n//     \n    var response=responses[rowNo].response\n    if(!response || response.length==0) {\n      response=\'{\"data\":[]}\'\n    }\n        onceMessage(\'rescore\', function(event){\n            //console.log(\'rescore\', event.data.value);\n            responses[rowNo] = Object.assign(responses[rowNo],event.data.value.score);\n            //console.log(responses[rowNo])\n            //console.log(\"hiding\")\n            $(\"#waiticon\").hide()\n            setTimeout(callback, 10);\n        });\n        try {\n            var thisdata = JSON.parse(response);\n            //console.log(\"showing\")\n            $(\"#waiticon\").show()\n            sendMessage(\'rescore\', thisdata);\n        } catch(e){\n            console.log(\'JSON PARSE ERROR\', e);\n            // Remove the listener created in onceMessage\n            //console.log(\"hiding\")\n            $(\"#waiticon\").hide()\n            messageListeners[\'rescore\'].pop()\n            callback();\n        }\n}\n\n\nfunction save() {\n    var script=quill.getText(0)\n	var data={script:script}\n	console.log(data)\n	sessionStorage.setItem(\"data\", JSON.stringify(data)); \n	sessionStorage.setItem(\"responses\", JSON.stringify(responses)); \n}\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n', '{\"gameurl\": \"../openPCIs/voxelcraft/game/\"}', '');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `tests`
--

CREATE TABLE `tests` (
  `test_id` int UNSIGNED NOT NULL,
  `test_name` varchar(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `project_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `trainingresponses`
--

CREATE TABLE `trainingresponses` (
  `response_id` int UNSIGNED NOT NULL,
  `difficulty` tinyint UNSIGNED NOT NULL,
  `manager_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `users`
--

CREATE TABLE `users` (
  `user_id` int UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(256) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Data dump for tabellen `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`) VALUES
(1, 'admin', 'admin@edu', '')

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_permissions`
--

CREATE TABLE `user_permissions` (
  `user_id` int UNSIGNED NOT NULL,
  `unittype` enum('opencodingadmin','projectadmin','codingadmin','test','task') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `unit_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Data dump for tabellen `user_permissions`
--

INSERT INTO `user_permissions` (`user_id`, `unittype`, `unit_id`) VALUES
(1, 'opencodingadmin', 0),
(1, 'projectadmin', 1),
(1, 'codingadmin', 1)

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `assign_task`
--
ALTER TABLE `assign_task`
  ADD UNIQUE KEY `test_id` (`task_id`,`coder_id`),
  ADD KEY `coder_id` (`coder_id`);

--
-- Indeks for tabel `assign_test`
--
ALTER TABLE `assign_test`
  ADD UNIQUE KEY `test_id` (`test_id`,`coder_id`),
  ADD KEY `coder_id` (`coder_id`);

--
-- Indeks for tabel `coded`
--
ALTER TABLE `coded`
  ADD UNIQUE KEY `response_id_2` (`response_id`,`coder_id`);

--
-- Indeks for tabel `flags`
--
ALTER TABLE `flags`
  ADD PRIMARY KEY (`flag_id`),
  ADD UNIQUE KEY `response_id` (`response_id`,`coder_id`) USING BTREE;

--
-- Indeks for tabel `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`);

--
-- Indeks for tabel `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`response_id`),
  ADD UNIQUE KEY `task_id` (`task_id`,`testtaker`,`response_time`) USING BTREE;

--
-- Indeks for tabel `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `test_id` (`test_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indeks for tabel `tasktypes`
--
ALTER TABLE `tasktypes`
  ADD PRIMARY KEY (`tasktype_id`);

--
-- Indeks for tabel `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`test_id`),
  ADD UNIQUE KEY `test_name` (`test_name`,`project_id`) USING BTREE,
  ADD KEY `project_id` (`project_id`);

--
-- Indeks for tabel `trainingresponses`
--
ALTER TABLE `trainingresponses`
  ADD UNIQUE KEY `response` (`response_id`);

--
-- Indeks for tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks for tabel `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD UNIQUE KEY `unique` (`user_id`,`unittype`,`unit_id`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `flags`
--
ALTER TABLE `flags`
  MODIFY `flag_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tilføj AUTO_INCREMENT i tabel `responses`
--
ALTER TABLE `responses`
  MODIFY `response_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `tasktypes`
--
ALTER TABLE `tasktypes`
  MODIFY `tasktype_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tilføj AUTO_INCREMENT i tabel `tests`
--
ALTER TABLE `tests`
  MODIFY `test_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Begrænsninger for tabel `assign_task`
--
ALTER TABLE `assign_task`
  ADD CONSTRAINT `assign_task_ibfk_1` FOREIGN KEY (`coder_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assign_task_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrænsninger for tabel `assign_test`
--
ALTER TABLE `assign_test`
  ADD CONSTRAINT `assign_test_ibfk_1` FOREIGN KEY (`coder_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `assign_test_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `tests` (`test_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrænsninger for tabel `coded`
--
ALTER TABLE `coded`
  ADD CONSTRAINT `coded_ibfk_1` FOREIGN KEY (`response_id`) REFERENCES `responses` (`response_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrænsninger for tabel `flags`
--
ALTER TABLE `flags`
  ADD CONSTRAINT `flags_ibfk_1` FOREIGN KEY (`response_id`) REFERENCES `responses` (`response_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrænsninger for tabel `responses`
--
ALTER TABLE `responses`
  ADD CONSTRAINT `responses_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrænsninger for tabel `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `tests` (`test_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrænsninger for tabel `tests`
--
ALTER TABLE `tests`
  ADD CONSTRAINT `tests_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrænsninger for tabel `trainingresponses`
--
ALTER TABLE `trainingresponses`
  ADD CONSTRAINT `trainingresponses_ibfk_1` FOREIGN KEY (`response_id`) REFERENCES `responses` (`response_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
