var EMAR = {
    location: 0,
    zone: 0,
    device: 0,
    controller: 0,
    Update: function() {
        $.post(window.location.href, $("#emar").serialize(), function(resp) {
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
    Create: function() {
        $.post(window.location.href, $("#emar").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("emar");
                    Logging.logMessage("Core", "Forms", "Create OK");
                    window.location.replace(location.protocol + "//" + location.hostname + "/EMAR/" + resp.DID + '/');
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
    wheels: function(direction) {
        iotJumpWayWebSoc.publishToDeviceCommands({
            "loc": EMAR.location,
            "zne": EMAR.zone,
            "dvc": EMAR.device,
            "message": {
                "From": EMAR.controller,
                "Type": "Wheels",
                "Value": direction,
                "Message": "Move " + direction
            }
        });
    },
    arm: function(direction) {
        iotJumpWayWebSoc.publishToDeviceCommands({
            "loc": EMAR.location,
            "zne": EMAR.zone,
            "dvc": EMAR.device,
            "message": {
                "From": EMAR.controller,
                "Type": "Arm",
                "Value": direction,
                "Message": "Move " + direction
            }
        });
    }
};

$(document).ready(function() {

    $("#GeniSysAI").on("click", "#emar_update", function(e) {
        e.preventDefault();
        EMAR.Update();
    });

    $("#GeniSysAI").on("click", "#emar_create", function(e) {
        e.preventDefault();
        EMAR.Create();
    });

    $("#GeniSysAI").on("click", "#reset_mqtt", function(e) {
        e.preventDefault();
        EMAR.ResetMqtt();
    });

    $("#GeniSysAI").on("click", "#FORWARD", function(e) {
        e.preventDefault();
        EMAR.wheels("FORWARD");
    });
    $("#GeniSysAI").on("click", "#BACK", function(e) {
        e.preventDefault();
        EMAR.wheels("BACK");
    });
    $("#GeniSysAI").on("click", "#RIGHT", function(e) {
        e.preventDefault();
        EMAR.wheels("RIGHT");
    });
    $("#GeniSysAI").on("click", "#LEFT", function(e) {
        e.preventDefault();
        EMAR.wheels("LEFT");
    });

    $("#GeniSysAI").on("click", "#UP", function(e) {
        e.preventDefault();
        EMAR.arm("UP");
    });
    $("#GeniSysAI").on("click", "#DOWN", function(e) {
        e.preventDefault();
        EMAR.arm("DOWN");
    });
    $("#GeniSysAI").on("click", "#RIGHTA", function(e) {
        e.preventDefault();
        EMAR.arm("RIGHT");
    });
    $("#GeniSysAI").on("click", "#LEFTA", function(e) {
        e.preventDefault();
        EMAR.arm("LEFT");
    });

});