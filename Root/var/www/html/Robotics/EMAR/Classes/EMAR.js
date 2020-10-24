var EMAR = {
    location: $("#lentity").val(),
    zone: $("#zentity").val(),
    device: $("#dentity").val(),
    controller: $("#uidentifier").val(),
    Create: function() {
        $.post(window.location.href, $("#emar_create").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("emar_create");
                    $('.modal-title').text('EMAR Devices');
                    $('.modal-body').html("EMAR Device ID #" + resp.EDID + " created! Please save the API keys safely. The device's credentials are provided below. The credentials can be reset in the GeniSyAI Security Devices area.<br /><br /><strong>Device ID:</strong> " + resp.DID + "<br /><strong>MQTT User:</strong> " + resp.MU + "<br /><strong>MQTT Password:</strong> " + resp.MP + "<br /><br /><strong>Blockchain User:</strong> " + resp.BU + "<br /><strong>Blockchain Pass:</strong> " + resp.BP + "<br /><br /><strong>App ID:</strong> " + resp.AppID + "<br /><strong>App Key:</strong> " + resp.AppKey + "<br /><br />" + resp.Message);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", "Device ID #" + resp.DID + " created!");
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
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
                    msg = resp.Message
                    Logging.logMessage("Core", "EMAR", msg);
                    $('.modal-title').text('EMAR Update');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = resp.Message
                    Logging.logMessage("Core", "EMAR", msg);
                    $('.modal-title').text('EMAR Update');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    ResetMqtt: function() {
        $.post(window.location.href, { "reset_emar_mqtt": 1 },
            function(resp) {
                console.log(resp)
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        EMAR.mqttpa = resp.P;
                        EMAR.mqttpae = resp.P.replace(/\S/gi, '*');
                        $("#idmqttp").text(EMAR.mqttpae);
                        $('.modal-title').text('New MQTT Password');
                        $('.modal-body').text("This device's new MQTT password is: " + resp.P);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "Credentials reset failed: " + resp.Message
                        Logging.logMessage("Core", "Credentials", msg);
                        break;
                }
            });
    },
    ResetDvcKey: function() {
        $.post(window.location.href, { "reset_emar_key": 1 },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        $('.modal-title').text('New Device Key');
                        $('.modal-body').text("This device's new key is: " + resp.P);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "Reset failed: " + resp.Message
                        Logging.logMessage("Core", "Forms", msg);
                        break;
                }
            });
    },
    ResetDvcAMQP: function() {
        $.post(window.location.href, { "reset_emar_amqp": 1 },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        EMAR.damqppa = resp.P;
                        EMAR.damqppae = resp.P.replace(/\S/gi, '*');
                        $("#damqpp").text(EMAR.damqppae);
                        Logging.logMessage("Core", "Forms", resp.Message);
                        $('.modal-title').text('Reset Device AMQP Key');
                        $('.modal-body').text("This device's new AMQP key is: " + resp.P);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "Reset failed: " + resp.Message
                        Logging.logMessage("Core", "Forms", msg);
                        $('.modal-title').text('Reset Device AMQP Key');
                        $('.modal-body').text(msg);
                        $('#responsive-modal').modal('show');
                        break;
                }
            });
    },
    wheels: function(direction) {
        iotJumpWay.publishToDeviceCommands({
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
        iotJumpWay.publishToDeviceCommands({
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
        iotJumpWay.publishToDeviceCommands({
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

        EMAR.damqpua = $("#damqpu").text();
        EMAR.damqpuae = $("#damqpu").text().replace(/\S/gi, '*');
        EMAR.damqppa = $("#damqpp").text();
        EMAR.damqppae = $("#damqpp").text().replace(/\S/gi, '*');
        EMAR.mqttua = $("#idmqttu").text();
        EMAR.mqttuae = $("#idmqttu").text().replace(/\S/gi, '*');
        EMAR.mqttpa = $("#idmqttp").text();
        EMAR.mqttpae = $("#idmqttp").text().replace(/\S/gi, '*');
        EMAR.idappida = $("#idappid").text();
        EMAR.idappidae = $("#idappid").text().replace(/\S/gi, '*');
        EMAR.bcida = $("#bcid").text();
        EMAR.bcidae = $("#bcid").text().replace(/\S/gi, '*');

        $("#damqpu").text(EMAR.damqpuae);
        $("#damqpp").text(EMAR.damqppae);
        $("#idmqttu").text(EMAR.mqttuae);
        $("#idmqttp").text(EMAR.mqttpae);
        $("#idappid").text(EMAR.idappidae);
        $("#bcid").text(EMAR.bcidae);
    },
    GetLifes: function() {
        $.post(window.location.href, { "get_lifes": 1 }, function(resp) {
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

    $("#GeniSysAI").on("click", "#reset_emar_mqtt", function(e) {
        e.preventDefault();
        EMAR.ResetMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_emar_apriv", function(e) {
        e.preventDefault();
        EMAR.ResetDvcKey();
    });

    $("#GeniSysAI").on("click", "#reset_emar_amqp", function(e) {
        e.preventDefault();
        EMAR.ResetDvcAMQP();
    });

    $('.hider').hover(function() {
        $('#' + $(this).attr("id")).attr('type', 'text');
    }, function() {
        $('#' + $(this).attr("id")).attr('type', 'password');
    });

    $('#damqpu').hover(function() {
        $("#damqpu").text(EMAR.damqpua);
    }, function() {
        $("#damqpu").text(EMAR.damqpuae);
    });

    $('#damqpp').hover(function() {
        $("#damqpp").text(EMAR.damqppa);
    }, function() {
        $("#damqpp").text(EMAR.damqppae);
    });

    $('#idmqttu').hover(function() {
        $("#idmqttu").text(EMAR.mqttua);
    }, function() {
        $("#idmqttu").text(EMAR.mqttuae);
    });

    $('#idmqttp').hover(function() {
        $("#idmqttp").text(EMAR.mqttpa);
    }, function() {
        $("#idmqttp").text(EMAR.mqttpae);
    });

    $('#idappid').hover(function() {
        $("#idappid").text(EMAR.idappida);
    }, function() {
        $("#idappid").text(EMAR.idappidae);
    });

    $('#bcid').hover(function() {
        $("#bcid").text(EMAR.bcida);
    }, function() {
        $("#bcid").text(EMAR.bcidae);
    });

});