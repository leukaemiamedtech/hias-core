var EMAR = {
    location: 0,
    zone: 0,
    device: 0,
    controller: 0,
    LifeDevice: 1,
    Create: function() {
        $.post(window.location.href, $("#emar_create").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("emar_create");
                    Logging.logMessage("Core", "EMAR", "EMAR Create OK");
                    window.location.replace(location.protocol + "//" + location.hostname + "/Robotics/EMAR/" + resp.DID + '/');
                    break;
                default:
                    msg = "EMAR Create Failed: " + resp.Message
                    Logging.logMessage("Core", "EMAR", msg);
                    break;
            }
        });
    },
    Update: function() {
        $.post(window.location.href, $("#emar_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('.modal-title').text('EMAR Update');
                    $('.modal-body').text("EMAR Update OK");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "EMAR", "EMAR Update OK");
                    break;
                default:
                    msg = "EMAR Update Failed: " + resp.Message
                    Logging.logMessage("Core", "EMAR", msg);
                    break;
            }
        });
    },
    ResetMqtt: function() {

        var id = 1;
        var conta = "#mqttp";

        if ($(this).attr("id") == 1) {
            id = $("#did").val();
            conta = "#mqttp"
        } else if ($(this).attr("id") == 2) {
            id = $("#did2").val();
            conta = "#mqttp2"
        } else if ($(this).attr("id") == 3) {
            id = $("#did3").val();
            conta = "#mqttp3"
        }

        $.post(window.location.href, { "reset_mqtt": 1, "id": id }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "EMAR", "MQTT Reset OK");
                    $(conta).text(resp.P)
                    break;
                default:
                    msg = "MQTT Reset Failed: " + resp.Message
                    Logging.logMessage("Core", "EMAR", msg);
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
    },
    cams: function(direction) {
        iotJumpWayWebSoc.publishToDeviceCommands({
            "loc": EMAR.location,
            "zne": EMAR.zone,
            "dvc": EMAR.device,
            "message": {
                "From": EMAR.controller,
                "Type": "Head",
                "Value": direction,
                "Message": "Move " + direction
            }
        });
    },
    HideInputs: function() {
        $('#ip').attr('type', 'password');
        $('#mac').attr('type', 'password');
        $('#sport').attr('type', 'password');
        $('#sportf').attr('type', 'password');
        $('#sckport').attr('type', 'password');
        $('#sdir').attr('type', 'password');

        EMAR.mqttua = $("#mqttu").text();
        EMAR.mqttuae = $("#mqttu").text().replace(/\S/gi, '*');
        EMAR.mqttpa = $("#mqttp").text();
        EMAR.mqttpae = $("#mqttp").text().replace(/\S/gi, '*');

        $("#mqttu").text(EMAR.mqttuae);
        $("#mqttp").text(EMAR.mqttpae);
    },
    GetLifes: function() {
        $.post(window.location.href, { "get_lifes": 1, "device": $("#id").val() }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    if (resp.ResponseData["status"] == "ONLINE") {
                        $("#offline1").removeClass("hide");
                        $("#online1").addClass("hide");
                    } else {
                        $("#offline1").addClass("hide");
                        $("#online1").removeClass("hide");
                    }
                    $("#ecpuU").text(resp.ResponseData.cpu)
                    $("#ememU").text(resp.ResponseData.mem)
                    $("#ehddU").text(resp.ResponseData.hdd)
                    $("#etempU").text(resp.ResponseData.tempr)

                    Logging.logMessage("Core", "EMAR", "EMAR Stats Updated OK");
                    break;
                default:
                    msg = "EMAR Stats Update Failed: " + resp.Message
                    Logging.logMessage("Core", "EMAR", msg);
                    break;
            }
        });
    },
    imgError: function(image) {
        $("#" + image).removeClass("hide");
        $("#" + image + "on").addClass("hide");
        return true;
    },
    UpdateLife: function() {
        setInterval(function() {
            EMAR.GetLifes();
        }, 5000);
    },
};

$(document).ready(function() {

    $('#emar_create').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            EMAR.Create();
        }
    });

    $('#emar_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            EMAR.Update();
        }
    });

    $("#GeniSysAI").on("click", "#UPA", function(e) {
        e.preventDefault();
        EMAR.arm("UP");
    });
    $("#GeniSysAI").on("click", "#DOWNA", function(e) {
        e.preventDefault();
        EMAR.arm("DOWN");
    });
    $("#GeniSysAI").on("click", "#RIGHTA", function(e) {
        e.preventDefault();
        EMAR.arm("2UP");
    });
    $("#GeniSysAI").on("click", "#LEFTA", function(e) {
        e.preventDefault();
        EMAR.arm("2DOWN");
    });

    $("#GeniSysAI").on("click", "#FORWARDW", function(e) {
        e.preventDefault();
        EMAR.wheels("FORWARD");
    });
    $("#GeniSysAI").on("click", "#BACKW", function(e) {
        e.preventDefault();
        EMAR.wheels("BACK");
    });
    $("#GeniSysAI").on("click", "#RIGHTW", function(e) {
        e.preventDefault();
        EMAR.wheels("RIGHT");
    });
    $("#GeniSysAI").on("click", "#LEFTW", function(e) {
        e.preventDefault();
        EMAR.wheels("LEFT");
    });

    $("#GeniSysAI").on("click", "#UPC", function(e) {
        e.preventDefault();
        EMAR.cams("UP");
    });
    $("#GeniSysAI").on("click", "#DOWNC", function(e) {
        e.preventDefault();
        EMAR.cams("DOWN");
    });
    $("#GeniSysAI").on("click", "#RIGHTC", function(e) {
        e.preventDefault();
        EMAR.cams("RIGHT");
    });
    $("#GeniSysAI").on("click", "#LEFTC", function(e) {
        e.preventDefault();
        EMAR.cams("LEFT");
    });
    $("#GeniSysAI").on("click", "#CENTERC", function(e) {
        e.preventDefault();
        EMAR.cams("CENTER");
    });

    $("#GeniSysAI").on("click", ".reset_mqtt", function(e) {
        e.preventDefault();
        EMAR.ResetMqtt();
    });

    $('.hider').hover(function() {
        $('#' + $(this).attr("id")).attr('type', 'text');
    }, function() {
        $('#' + $(this).attr("id")).attr('type', 'password');
    });

    $('#mqttu').hover(function() {
        $("#mqttu").text(EMAR.mqttua);
    }, function() {
        $("#mqttu").text(EMAR.mqttuae);
    });

    $('#mqttp').hover(function() {
        $("#mqttp").text(EMAR.mqttpa);
    }, function() {
        $("#mqttp").text(EMAR.mqttpae);
    });

});