var TASS = {
    Update: function() {
        $.post(window.location.href, $("#tass").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "Forms", "Update OK");
                    break;
                default:
                    msg = "Update failed: " + resp.ResponseMessage
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    Create: function() {
        $.post(window.location.href, $("#tass").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("tass");
                    Logging.logMessage("Core", "Forms", "Create OK");
                    window.location.replace(location.protocol + "//" + location.hostname + "/TASS/" + resp.DID + '/');
                    break;
                default:
                    msg = "Create failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    ResetMqtt: function() {
        $.post(window.location.href, { "reset_mqtt": 1, "id": $("#did").val() },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        $("#mqttp").text(resp.P)
                        break;
                    default:
                        msg = "Reset failed: " + resp.Message
                        Logging.logMessage("Core", "Forms", msg);
                        break;
                }
            });
    },
};
$(document).ready(function() {

    $("#GeniSysAI").on("click", "#tass_update", function(e) {
        e.preventDefault();
        TASS.Update();
    });

    $("#GeniSysAI").on("click", "#tass_create", function(e) {
        e.preventDefault();
        TASS.Create();
    });

    $("#GeniSysAI").on("click", "#reset_mqtt", function(e) {
        e.preventDefault();
        TASS.ResetMqtt();
    });

});