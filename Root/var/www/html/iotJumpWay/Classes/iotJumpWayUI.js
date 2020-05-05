var iotJumpwayUI = {
    Update: function() {
        $.post(window.location.href, $("#form").serialize(), function(resp) {
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
    CreateZone: function() {
        $.post(window.location.href, $("#form").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    window.location.replace(location.protocol + "//" + location.hostname + "/iotJumpWay/" + resp.LID + "/Zones/" + resp.ZID + '/');
                    Logging.logMessage("Core", "Forms", "Create OK");
                    break;
                default:
                    msg = "Update failed: " + resp.ResponseMessage
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    UpdateZone: function() {
        $.post(window.location.href, $("#form").serialize(), function(resp) {
            console.log(resp)
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
    CreateDevice: function() {
        $.post(window.location.href, $("#form").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                case "OK":
                    window.location.replace(location.protocol + "//" + location.hostname + "/iotJumpWay/" + resp.LID + "/Zones/" + resp.ZID + "/Devices/" + resp.DID + '/');
                    Logging.logMessage("Core", "Forms", "Create OK");
                    break;
                default:
                    msg = "Update failed: " + resp.ResponseMessage
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    UpdateDevice: function() {
        $.post(window.location.href, $("#form").serialize(), function(resp) {
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
    CreateApplication: function() {
        $.post(window.location.href, $("#form").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    window.location.replace(location.protocol + "//" + location.hostname + "/iotJumpWay/" + resp.LID + "/Applications/" + resp.AID + '/');
                    Logging.logMessage("Core", "Forms", "Create OK");
                    break;
                default:
                    msg = "Update failed: " + resp.ResponseMessage
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    UpdateApplication: function() {
        $.post(window.location.href, $("#form").serialize(), function(resp) {
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
    ResetAppMqtt: function() {
        $.post(window.location.href, { "reset_mqtt_app": 1, "id": $("#id").val() },
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
    CreateSensor: function() {
        $.post(window.location.href, $("#form").serialize(), function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    window.location.replace(location.protocol + "//" + location.hostname + "/iotJumpWay/Sensors/" + resp.SID + '/');
                    Logging.logMessage("Core", "Forms", "Create OK");
                    break;
                default:
                    msg = "Update failed: " + resp.ResponseMessage
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    UpdateSensor: function() {
        $.post(window.location.href, $("#form").serialize(), function(resp) {
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
};
$(document).ready(function() {

    $("#GeniSysAI").on("click", "#location_update", function(e) {
        e.preventDefault();
        iotJumpwayUI.Update();
    });

    $("#GeniSysAI").on("click", "#zone_create", function(e) {
        e.preventDefault();
        iotJumpwayUI.CreateZone();
    });

    $("#GeniSysAI").on("click", "#zone_update", function(e) {
        e.preventDefault();
        iotJumpwayUI.UpdateZone();
    });

    $("#GeniSysAI").on("click", "#device_create", function(e) {
        e.preventDefault();
        iotJumpwayUI.CreateDevice();
    });

    $("#GeniSysAI").on("click", "#device_update", function(e) {
        e.preventDefault();
        iotJumpwayUI.UpdateDevice();
    });

    $("#GeniSysAI").on("click", "#sensor_create", function(e) {
        e.preventDefault();
        iotJumpwayUI.CreateSensor();
    });

    $("#GeniSysAI").on("click", "#sensor_update", function(e) {
        e.preventDefault();
        iotJumpwayUI.UpdateSensor();
    });

    $("#GeniSysAI").on("click", "#application_create", function(e) {
        e.preventDefault();
        iotJumpwayUI.CreateApplication();
    });

    $("#GeniSysAI").on("click", "#application_update", function(e) {
        e.preventDefault();
        iotJumpwayUI.UpdateApplication();
    });

    $("#GeniSysAI").on("click", "#reset_app_mqtt", function(e) {
        e.preventDefault();
        iotJumpwayUI.ResetAppMqtt();
    });

});