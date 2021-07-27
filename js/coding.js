
window.addEventListener('message', function(event){
var type = event.data.type;
if(messageListeners[type])
while(messageListeners[type].length > 0){
	var handler = messageListeners[type][messageListeners[type].length-1];
	handler(event);
	messageListeners[type].pop();
}
});

var messageListeners = {};
function onceMessage(type, cb){  
	if(!messageListeners[type]) 
	messageListeners[type] = [];  
	messageListeners[type].push(cb);
}
function sendMessage(type, value){
if(window.parent)
	$("#playarea iframe")[0].contentWindow.postMessage({
	type: type,
	value: value
	},'*');
}
