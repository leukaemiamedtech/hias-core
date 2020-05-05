var Staff = {
    Create: function() {
        $.post(window.location.href, $("#form").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("form");
                    Logging.logMessage("Core", "Forms", "Create OK");
                    window.location.replace(location.protocol + "//" + location.hostname + "/Hospital/Staff/" + resp.UID + '/');
                    break;
                default:
                    msg = "Create failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    Update: function() {
        $.post(window.location.href, $("#form").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("form");
                    Logging.logMessage("Core", "Forms", "Update OK");
                    break;
                default:
                    msg = "Update failed: " + resp.ResponseMessage
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    ResetMqtt: function() {
        $.post(window.location.href, { "reset_mqtt_staff": 1, "id": $("#aid").val() },
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
    ResetPass: function() {
        $.post(window.location.href, { "reset_pass": 1, "id": $("#id").val(), "user": $("#username").val() },
            function(resp) {
                console.log(resp)
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        var modal = $(this)
                        modal.find('.modal-title').text('New Password')
                        modal.find('.modal-body input').val(resp.pw)
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

    $("#GeniSysAI").on("click", "#staff_update", function(e) {
        e.preventDefault();
        Staff.Update();
    });

    $("#GeniSysAI").on("click", "#staff_create", function(e) {
        e.preventDefault();
        Staff.Create();
    });

    $("#GeniSysAI").on("click", "#reset_staff_mqtt", function(e) {
        e.preventDefault();
        Staff.ResetMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_pass", function(e) {
        e.preventDefault();
        Staff.ResetPass();
    });

});