var Logging = {

	'logMessage': function (process, messageType, message) {
		console.log(new Date($.now()) + " | " + process + " | " + messageType + " | " + message);
	},
	'Console': function () {
		var console = {};
		var logger = document.getElementById("logger-console");
		console.log = function (text) {
			var string = '<a class="list-group-item"><i class="fa fa-comment fa-fw"></i> ' + text + '<span class="pull-right text-muted small"><em>4 minutes ago</em></span></a>';
			var element = document.createElement("div");
			var txt = document.createTextNode(string);
			element.prepend(txt);
			logger.prepend(element);
		}
	}
}