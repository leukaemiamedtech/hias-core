var EMAR = {
    location: 0,
    zone: 0,
    device: 0,
    controller: 0,
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
    HideInputs: function() {
        $('#ip').attr('type', 'password');
        $('#ip2').attr('type', 'password');
        $('#ip3').attr('type', 'password');
        $('#mac').attr('type', 'password');
        $('#mac2').attr('type', 'password');
        $('#mac3').attr('type', 'password');
        $('#sport').attr('type', 'password');
        $('#sport2').attr('type', 'password');
        $('#sport3').attr('type', 'password');
        $('#sportf').attr('type', 'password');
        $('#sportf2').attr('type', 'password');
        $('#sportf3').attr('type', 'password');
        $('#sckport').attr('type', 'password');
        $('#sckport2').attr('type', 'password');
        $('#sckport3').attr('type', 'password');
        $('#sdir').attr('type', 'password');
        $('#sdir2').attr('type', 'password');
        $('#sdir3').attr('type', 'password');

        EMAR.mqttua = $("#mqttu").text();
        EMAR.mqttuae = $("#mqttu").text().replace(/\S/gi, '*');
        EMAR.mqttpa = $("#mqttp").text();
        EMAR.mqttpae = $("#mqttp").text().replace(/\S/gi, '*');

        EMAR.mqttu2a = $("#mqttu2").text();
        EMAR.mqttu2ae = $("#mqttu2").text().replace(/\S/gi, '*');
        EMAR.mqttp2a = $("#mqttp2").text();
        EMAR.mqttp2ae = $("#mqttp2").text().replace(/\S/gi, '*');

        EMAR.mqttu3a = $("#mqttu3").text();
        EMAR.mqttu3ae = $("#mqttu3").text().replace(/\S/gi, '*');
        EMAR.mqttp3a = $("#mqttp3").text();
        EMAR.mqttp3ae = $("#mqttp3").text().replace(/\S/gi, '*');

        $("#mqttu").text(EMAR.mqttuae);
        $("#mqttp").text(EMAR.mqttpae);

        $("#mqttu2").text(EMAR.mqttu2ae);
        $("#mqttp2").text(EMAR.mqttp2ae);

        $("#mqttu3").text(EMAR.mqttu3ae);
        $("#mqttp3").text(EMAR.mqttp3ae);
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
                    if (resp.ResponseData["status2"] == "ONLINE") {
                        $("#offline2").removeClass("hide");
                        $("#online2").addClass("hide");
                    } else {
                        $("#offline2").addClass("hide");
                        $("#online2").removeClass("hide");
                    }
                    if (resp.ResponseData["status3"] == "ONLINE") {
                        $("#offline3").removeClass("hide");
                        $("#online3").addClass("hide");
                    } else {
                        $("#offline3").addClass("hide");
                        $("#online3").removeClass("hide");
                    }
                    $("#ecpuU").text(resp.ResponseData.cpu)
                    $("#ememU").text(resp.ResponseData.mem)
                    $("#ehddU").text(resp.ResponseData.hdd)
                    $("#etempU").text(resp.ResponseData.tempr)
                    $("#ecpuU2").text(resp.ResponseData.cpu2)
                    $("#ememU2").text(resp.ResponseData.mem2)
                    $("#ehddU2").text(resp.ResponseData.hdd2)
                    $("#etempU2").text(resp.ResponseData.tempr2)
                    $("#ecpuU3").text(resp.ResponseData.cpu3)
                    $("#ememU3").text(resp.ResponseData.mem3)
                    $("#ehddU3").text(resp.ResponseData.hdd3)
                    $("#etempU3").text(resp.ResponseData.tempr3)

                    Logging.logMessage("Core", "EMAR", "EMAR Stats Updated OK");
                    break;
                default:
                    msg = "EMAR Stats Update Failed: " + resp.Message
                    Logging.logMessage("Core", "EMAR", msg);
                    break;
            }
        });
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

    $("#GeniSysAI").on("click", ".reset_mqtt", function(e) {
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
    $("#GeniSysAI").on("click", "#OPEN", function(e) {
        e.preventDefault();
        EMAR.arm("OPEN");
    });
    $("#GeniSysAI").on("click", "#CLOSE", function(e) {
        e.preventDefault();
        EMAR.arm("CLOSE");
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

    $('#mqttu2').hover(function() {
        $("#mqttu2").text(EMAR.mqttu2a);
    }, function() {
        $("#mqttu2").text(EMAR.mqttu2ae);
    });

    $('#mqttp2').hover(function() {
        $("#mqttp2").text(EMAR.mqttp2a);
    }, function() {
        $("#mqttp2").text(EMAR.mqttp2ae);
    });

    $('#mqttu3').hover(function() {
        $("#mqttu3").text(EMAR.mqttu3a);
    }, function() {
        $("#mqttu3").text(EMAR.mqttu3ae);
    });

    $('#mqttp3').hover(function() {
        $("#mqttp3").text(EMAR.mqttp3a);
    }, function() {
        $("#mqttp3").text(EMAR.mqttp3ae);
    });

});