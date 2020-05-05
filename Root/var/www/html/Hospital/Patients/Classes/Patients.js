var Patients = {
    Create: function() {
        $.post(window.location.href, $("#form").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("form");
                    Logging.logMessage("Core", "Forms", "Create OK");
                    window.location.replace(location.protocol + "//" + location.hostname + "/Hospital/Patients/" + resp.UID + '/');
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
            console.log(resp)
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
        $.post(window.location.href, { "reset_mqtt_patient": 1, "id": $("#id").val() },
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

    $("#GeniSysAI").on("click", "#patient_create", function(e) {
        e.preventDefault();
        Patients.Create();
    });

    $("#GeniSysAI").on("click", "#patient_update", function(e) {
        e.preventDefault();
        Patients.Update();
    });

    $("#GeniSysAI").on("click", "#reset_patients_mqtt", function(e) {
        e.preventDefault();
        Patients.ResetMqtt();
    });

});