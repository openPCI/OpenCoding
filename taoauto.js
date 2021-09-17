var currentRow=0;
var response={}
var codeScript
var warnings=""
function init() {
	if(typeof responses!="undefined") {
      if(typeof data.script!="undefined") var script=data.script
      else {
        var script=''
        for(var i=0; i<Object.keys(responses[0].response).length;i++) {
          script+='\rvar resp'+(i+1)+'=response["'+Object.keys(responses[0].response)[i]+'"];';
        }
        script+='\rreturn {'
        if($(".itemvalue").length>0) {
          script+=$(".itemvalue").map(function(i) {return '\r  '+$(this).data("item_name")+':response["'+Object.keys(responses[0].response)[i]+'"]=="correct"?1:0';
                }).get().join(",")
        } else script+='\r  item1:resp1=="correct"?1:0'
        script+='\r}'
      }
  
       quill.setContents([
        { insert: script+'\n'}
        ]);    
        quill.formatLine(0,quill.getLength(),"code-block",true)
         codeScript=setScript()
      $(".nextautoresponse").click(function() {
        currentRow+=($(this).data("next")==">"?1:-1)
        if(currentRow<0) currentRow=responses.length
        if(currentRow>responses.length) currentRow=0
        codeScript=setScript()
        codeCurrentRow()
        updatestats()
      })
      $("#rescoreThisBtn").click(function() {
        codeScript=setScript()
        codeCurrentRow()
        updatestats()
      })
      $("#rescoreAllBtn").click(function() {
         codeScript=setScript()
         warnings=""
         showWait(true)
         setTimeout(function() {
           for(currentRow=responses.length-1;currentRow>=0;currentRow--) {
             codeCurrentRow();
           }
           showWait(false)
           updatestats()
           if(warnings.length>0) showWarning(warnings,10000)
         },100)
      })
     codeCurrentRow();
	}
}
function setScript() {
  try {
    var newScript=new Function ('response','"use strict";'+quill.getText(0))
    return newScript
  } catch (e) { alert("Error:"+e.message);}
}
function codeCurrentRow() {
  $("#response_id").val(responses[currentRow].response_id)
  response=responses[currentRow].response
  for(task_name in response) {	$('[data-task_name="'+task_name+'"]').html(response[task_name]) }
  responses[currentRow] = Object.assign(responses[currentRow],codeScript(response))
  fillItems() 
}
function fillItems() {
  $(".itemvalue").each(function() {
    $(this).val(responses[currentRow][$(this).data("item_name")])
  })
}

function save() {
    var script=quill.getText(0)
	data={script:script}
}
///////////////////////
// MC-functions
function makeMC(resp) {
  // MC is in a format that is simlar to JSON, but we need to convert it a little...
  resp=(resp==""?"[]":resp.replace(/'/g,"\"").replace(/;/g,","))
  try {
    return JSON.parse(resp)
  } catch(e) {
    var error=_("Error in JSON. response_id: <b>{0}</b> resp: <pre style=\"overflow:scroll;max-height:30px\">{1}</pre>. Message: {2}<br>",$("#response_id").val(),resp,e.message)
    warnings+=error
    return {}
  }

}
function scoreMC(resp,correct=[]) {
  if(typeof resp !== 'array') resp=makeMC(resp)
  return (resp.filter(value => correct.includes(value))?1:0)
}
function scoreSC(resp,correct=[]) {
  return (correct.indexOf(resp)>-1?1:0)
}
///////////////////////
// Match-functions
function makeMatch(resp, variablesInColumns=false) {
  // Match is in a format that is simlar to JSON, but we need to convert it a little...
  resp=(resp==""?"{}":resp.replace(/; /g,",").replace(/ /g,":").replace("[","{").replace("]","}").replace(/([0-9a-z_]+)/gi,'"$1"'))
  if(variablesInColumns) {
    resp=resp.replace(/(".*?"):(".*?")/g,"$2:$1")
  }
  try {
    return JSON.parse(resp)
  } catch(e) {
    var error=_("Error in JSON. response_id: <b>{0}</b> resp: <pre style=\"overflow:scroll;max-height:30px\">{1}</pre>. Message: {2}<br>",$("#response_id").val(),resp,e.message)
    warnings+=error
    return {}
  }
}
function scoreMatch(resp,identifier,correct=[],variablesInColumns = false) {
  if(typeof resp !== 'object') resp=makeMatch(resp,variablesInColumns)
  return (correct.indexOf(resp[identifier])>-1?1:0)
}


///////////////////////
// GapMatch-functions
function makeTextGapMatch(resp) {
  // GapMatch is in a format that is simlar to JSON, but we need to convert it a little...
  resp=(resp==""?"{}":resp)//.replace(/'/g,"\"").replace(/;/g,","))
  try {
    return JSON.parse(resp)
  } catch(e) {
    var error=_("Error in JSON. response_id: <b>{0}</b> resp: <pre style=\"overflow:scroll;max-height:30px\">{1}</pre>. Message: {2}<br>",$("#response_id").val(),resp,e.message)
    warnings+=error
    return {}
  }
}

function textInGap(resp,text,gaps,noWhereElse=true) {
  if(typeof resp !== 'object') resp=makeTextGapMatch(resp)
  var isIn=false
  for(gap of gaps) {
    if(typeof resp[gap]!="undefined" && resp[gap].indexOf(text)>-1) {
        isIn=true
        break
    }
  }
  if(isIn && noWhereElse) {
    var gaps=Object.keys(resp).filter(x => !gaps.includes(x));
    for(gap of gaps) {
        if(typeof resp[gap]!="undefined" && resp[gap].indexOf(text)>-1) {
            isIn=false
            break
        }
    }
  }
  return isIn?1:0;
}

///////////////////////
// Gantt
function makeGantt(gantt,names=[],timespan=30,timeFormat="d/M H:mm") { // https://moment.github.io/luxon////parsing?id=table-of-tokens
  //Creaate object
  var ganttobj={gantt:{},names:names,timespan:timespan,timeFormat:timeFormat}
  // Extract intervals from JSON
  ganttarr=(gantt.length>0?JSON.parse(gantt.replace(/'/g,'"')).response:"")
  //Eksplode to list of lists
  ganttarr=ganttarr.split(";")
  //Give rows names and split rows
  for(var i=0;i<ganttarr.length;i++) {
      ganttobj.gantt[names[i]]=ganttarr[i].split(", ").map(function(x) {return x.split(" - ").map(function (t) {return DateTime.fromFormat(t,timeFormat)}).flat()}).flat()
  }
  return ganttobj
}
function isBefore(gantt,a=[],b=[],whichA="all",whichB="all",orEqual=false,strict=true,strictAfter=true) {
    //strictAfter is only used internally to control strict from isAfter
    if(whichA=="last") whichA="all" //Just to help the designer
    if(whichB=="first") whichB="all" //do.
    if(typeof b!="object") b=[b]
    if(typeof a!="object") a=[a]        
    var res=1
    for(var i of a) {
        for(var j of b) {
            if(typeof gantt.gantt[i]!="undefined" && typeof gantt.gantt[j]!="undefined") {
                var aval=(whichA=="all"?gantt.gantt[i][gantt.gantt[i].length-1]:(whichA=="first"?gantt.gantt[i][0]:gantt.gantt[i][whichA])) // All, first or Given number
                var bval=(whichB=="all"?gantt.gantt[j][gantt.gantt[j].length-1]:(whichB=="last"?gantt.gantt[j][0]:gantt.gantt[j][whichB])) // All, last or Given number
                if(!strict & typeof bval=="undefined") bval=Infinity
                if(!strictAfter & typeof aval=="undefined") aval=0
                var thisres=orEqual?aval <= bval:aval < bval
                res=typeof thisres=="undefined"?0:res & thisres
            }
        }
    }
    return(res)
}

function isFirst(gantt,a="",which="all",orEqual=false,strict=true) {
  var res=1
  for(var i of gantt.names) {
    if(a!=i && !isBefore(gantt,a,i,which,"all",orEqual,strict)) {
        res=0
        break
    }
  }
  return(res)
}

function isAfter(gantt,a=[],b=[],whichA="all",whichB="all",orEqual=false,strict=true) {
    return isBefore(gantt,b,a,whichB,whichA,orEqual,true,strict)
}

function isLast(gantt,a=[],which="all",orEqual=false,strict=true) {
    var res=1
    for(i of gantt.names) {
        if(a!=i) res=(res & isAfter(gantt,a,i,which,"all",orEqual,strict))
    }
    return(res)
}


// @param whichA/whichB for isOverlap: any: one or more a/b elements overlap, all: all a/b elements need to overlap, first/first: First a/b needs to overlap, last/last: Last a/b needs to overlap, or use number (or sequence)
// @rdname isBefore
// @export
function isOverlap(gantt,a="",b=[],whichA="any",whichB="any") {
    if(typeof b!="object") b=[b]
    var res=whichA=="all"
    var aval=[]
    switch(whichA) {
        case "all":
        case "any":
            aval=gantt.gantt[a] // Test all elements
            break
        case "first":
            aval=gantt.gantt[a][1] // First
            break
        case "last":
            aval=gantt.gantt[a][gantt.gantt[a].length-1] // Last
            break
        default:
            aval=gantt.gantt[a][whichA] // Given number (or sequence)
    }
    for(i of b) {
        var ival=[]
        switch(whichB) {
            case "all":
            case "any":
                ival=gantt.gantt[i] // Test all elements
                break
            case "first":
                ival=gantt.gantt[i][1] // First
                break
            case "last":
                ival=gantt.gantt[i][gantt.gantt[i].length-1] // Last
                break
            default:
                ival=gantt.gantt[i][whichB] // Given number (or sequence)
        }
        var AinI=aval.filter(x=>ival.includes(x))
        res=(whichA=="any" || whichB=="any")?
                res | AinI.length>0:
                whichA=="all"?
                    res & AinI.length==ival.length:
                    res | AinI.length==ival.length
    }
    return(res?1:0)
}

//noOverlap only if there is actually elements that could have overlapped.
//b: character or vector of characters
// @rdname isBefore
// @export
function noOverlap(gantt,a=[],b=[]) {
    if(typeof b!="object") b=[b]
    var res=1
    for(i of b) {
        res=res & (gantt.gantt[a].length>0 & gantt.gantt[i].length>0?
                    gantt.gantt[a].filter(x=>gantt.gantt[i].includes(x)).length>0:
                    0)
    }
    return (res)    
}

// numSlots
//
// @param gantt a gantt object (created by makeGantt)
// @param a an element
//
// @return Returns number of time slots - also counting non-connected slots
// @export
//
// @examples
// response="{'response':'1/8 10:30 - 1/8 12:30;1/8 13:00 - 1/8 14:00;1/8 11:30 - 1/8 12:30'}"
// gantt=makeGantt(response,names=c("waitress","actor","pianist"),timespan=30,timeFormat="%d/%m %H:%M")
// numSlots(gantt,"actor")
function numSlots(gantt,a=[]) {
  gantt.gantt[a].length
}

// Get start or end time of an element
//
// @param gantt A gantt object (created by makeGantt)
// @param a an element
// @param which start, end
// @param humanReadable If true, time is given in human readable format
//
// @return
// @export
//
// @examples
// response="{'response':'1/8 10:30;1/8 13:00 - 1/8 14:00;1/8 11:30 - 1/8 12:30'}"
// gantt=makeGantt(response,names=c("waitress","actor","pianist"),timespan=30,timeFormat="%d/%m %H:%M")
// getTime(gantt,"waitress",which="end",humanReadable=true)
// getTime(gantt,"actor",which="end",humanReadable=true)
function getTime(gantt,a=[],which="start",humanReadable=false) {
    if(typeof a!="object") a=[a]
    if(typeof gantt.gantt[a]=="undefined") return(NaN)
    var time=(which=="start"?gantt.gantt[a][0].valueOf():gantt.gantt[a][gantt.gantt[a].length-1].valueOf()+gantt.timespan*60)
    return (humanReadable?time.toFormat(gantt.timeFormat):time)
}

// Get the minimum/maximum start/end time among elements
//
// @param gantt A gantt object (created by makeGantt)
// @param a a vector of elements to compare
// @param which Which time to compare ("start" or "end")
// @param strict If true and an element among the compared is not present, NA is returned.
//
// @return Returns minimum/maximum time in seconds of the earliest/latest element starting/ending
// @export
//
// @examples
// response=matrix(c("{'response':'1/8 17:00;;1/8 17:00 - 1/8 18:00;1/8 18:00, 1/8 20:00 - 1/8 21:00;1/8 20:30;1/8 20:00 - 1/8 20:30;1/8 19:30 - 1/8 20:30'}"))
// gantt=makeGantt(response,names=c("waitress","actor","pianist","bartender","cleaning","ticketer","musician"),timespan=30,timeFormat="%d/%m %H:%M")
// getMinTime(gantt,c("pianist","bartender"),which="start")
// getMaxTime(gantt,c("actor","bartender"),which="end",strict=false)
// getMaxTime(gantt,"actor",which="end")
// as.difftime(getMaxTime(gantt,c("pianist","bartender"),which="end")-getMinTime(gantt,c("pianist","bartender"),which="start"),units = "secs")
//
function getMinTime(gantt,a,which,strict=true) {
    if(typeof a!="object") a=[a]
    return a.map(i=>getTime(gantt,i,which)).reduce((x,y)=>Math.min(x,y),Infinity)
}
// a is a vector of names
// @rdname getMinTime
// @export
function getMaxTime(gantt,a,which,strict=true) {
    if(typeof a!="object") a=[a]
    return a.map(i=>getTime(gantt,i,which)).reduce((x,y)=>Math.max(x,y),0)
}

// Get duration
//
// @param gantt A gantt object (created by makeGantt)
// @param a An element
//
// @return Returns duration in seconds
// @export
//
// @examples
// response=matrix(c("{'response':'1/8 17:00;1/8 17:00 - 1/8 17:30;1/8 17:00 - 1/8 18:00;1/8 17:00, 1/8 18:00, 1/8 19:00, 1/8 20:00;1/8 20:30;1/8 20:00 - 1/8 20:30;1/8 19:30 - 1/8 20:30'}"))
// gantt=makeGantt(response2,names=c("waitress","actor","pianist","bartender","cleaning","ticketer","musician"),timespan=30,timeFormat="%d/%m %H:%M")
// getDuration(gantt,"actor")
// getDuration(gantt,"waitress")
// getDuration(gantt,"bartender")
// getDuration(gantt,"cleaning")

function getDuration(gantt,a) {
  return numSlots(gantt,a)*gantt.timespan*60
}

