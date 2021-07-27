
	
// 	function insertResponse(json) {
		
var words
function initDone(){
	$(".sort").click(sortwords)
	$("#dosave").click(dosave)
	if(sessionStorage.getItem("data")!=null) {
		data=JSON.parse(localStorage.getItem("data"))
		responses=JSON.parse(localStorage.getItem("responses"))
		words=data.words
		cols=data.cols
		showCols(responses,cols)
		showWords()
	}
}
function dosave() {
		updatewords()

}
function updatewords() {
	$(".word").each(function() { 
		var word=$(this).find(".name").text()
		var cat=$(this).prevAll(".category")
		var category=""
		if(cat.length>0) category=cat.first().find(".name").text()
		words[word].category=category
	})
//	sessionStorage.setItem("words", JSON.stringify(words)); 
}
function sortwords() {
	var sorttype=$(this).data("sorttype")
	var sortdirection=$(this).data("sortdirection")
	$(this).data("sortdirection",sortdirection==1?-1:1)
	var neworder=$("#words .word").sort(function(a,b) {
		switch(sorttype) {
			case "alpha": 
				return sortdirection*$(a).find(".name").text().localeCompare($(b).find(".name").text())
				break
			case "num":
				var an=Number($(a).find(".num").text())
				var bn=Number($(b).find(".num").text())
				return sortdirection*(an>bn?1:an<bn?-1:0)
		}
	})
	$("#words").html(neworder)
}
function showCols(responses,selectedcols=[]) {
	// List all unique answers ordered alphabetically
	var list=""
	for(var i=0;i<responses[0].length;i++) {
		list+='<span class="colname'+(selectedcols.indexOf(i)>-1?' selectedcol':'')+'">'+responses[0][i]+'</span> '
	}
	$("#collist").html(list)
	$(".colname").click(function() {
		$(this).toggleClass("selectedcol")
		cols=$(".selectedcol").map(function() {return $(this).index()}).get()
		sessionStorage.setItem("cols", JSON.stringify(cols)); 

		buildList()
	})
}
function buildList() {
	// List all unique answers ordered alphabetically
	words={}
	for(var r=1;r<responses.length;r++) {
		for(var c of cols) {
			if(typeof(responses[r][c])!="undefined" && responses[r][c].trim()!="") {
				var word=responses[r][c].trim()
				if(typeof(words[word])=="undefined") words[word]={instances:1,category:""}
				words[word].instances+=1
			}
		}
	}
//	sessionStorage.setItem("words", JSON.stringify(words)); 
	showWords()
}
function showWords() {
	// Reset categories
	$("#categories").html('<dt class="category newcategory">New Category</dt><dd id="lastph">+</dd>')
	var list=""
	for(var w in words) {
		var word='<dd class="word"><span class="name">'+w+'</span> (<span class="num">'+words[w].instances+'</span>)'+'</dd>'
		var wordelem=$(word)
		var cat=words[w].category
		if(cat!="") {
			var dt=$("dt").filter(function(){ return $(this).find(".name").text() === cat})
			if(dt.length==0) {
				var dt=$(".newcategory").clone()
				dt.removeClass("newcategory")
				dt.html(wordelem.html())
				dt.find(".name").attr("contenteditable",true)
				dt.prependTo("#categories")
			}
			dt.after(wordelem)
			updatesum(wordelem)
		} else list+=word
	}
	$("#words").html(list)
	// This takes a long time when words has been saved in sessionStorage - but not when it is loaded from csv. Doesn't make sense!
	$("#words").sortable({
		connectWith:"#categories",
		placeholder: "ui-state-highlight",
		stop: dropped
	}).disableSelection();
	$("#categories").sortable({
		connectWith:"#categories,#words",
		placeholder: "ui-state-highlight",
		items: "> dd",
		stop: dropped,
		start: started
	}).disableSelection();
	$(".word").on("dblclick",addword)
}
function addword() {
	var word=$(this)
	var dt=$(".newcategory").clone()
	dt.removeClass("newcategory")
	dt.html(word.html())
	dt.find(".name").attr("contenteditable",true)
	dt.appendTo("#categories")
	dt.after(word)
	updatesum(word)
	$(".newcategory").appendTo("#categories")
	$("#lastph").appendTo("#categories")
}
function started(event,ui) {
	var item=$(ui.item)
	leavingCat=item.prevAll(".category").index() // First previous category
}
function dropped(event,ui) {
	var word=$(ui.item)
	if(word.prev().length==0 || word.prev().text()=="New Category" || word.prev().attr("id")=="lastph") {
		var newcat=$(".newcategory").clone()
		newcat.removeClass("newcategory")
		word.before(newcat)
		newcat.html(word.html())
		newcat.find(".name").attr("contenteditable",true)
		$(".newcategory").appendTo("#categories")
		$("#lastph").appendTo("#categories")
	} else { // update number of members
		updatesum(word)
	}
	if(leavingCat>-1) {
		var word=$($("#categories").children().get(leavingCat)).next(".word")
		if(word.length==0) { // No children. Remove.
			$($("#categories").children().get(leavingCat)).remove()
		} else updatesum(word)
		leavingCat=-1
	}
	updatewords()
}
function updatesum(word) {
	var cat=word.prevAll(".category").index() // First previous category
	var last=word.nextAll(".category").index() // Last previous category
	var members=0
	for(var i=cat+1;i<last;i++) members+=Number($($("#categories").children().get(i)).find(".num").text())
	$($("#categories").children().get(cat)).find(".num").text(members)

}
// 		$(function) {
// 			initDone()
// 		}
// 	}
