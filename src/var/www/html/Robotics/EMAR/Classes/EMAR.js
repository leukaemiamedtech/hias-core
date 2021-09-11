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
                        Robotics.mqttpa = resp.P;
                        Robotics.mqttpae = resp.P.replace(/\S/gi, '*');
                        $("#idmqttp").text(Robotics.mqttpae);
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
                        Robotics.damqppa = resp.P;
                        Robotics.damqppae = resp.P.replace(/\S/gi, '*');
                        $("#damqpp").text(Robotics.damqppae);
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
            "loc": Robotics.location,
            "zne": Robotics.zone,
            "dvc": Robotics.device,
            "message": {
                "From": Robotics.controller,
                "Type": "Wheels",
                "Value": direction,
                "Message": "Move " + direction
            }
        });
    },
    arm: function(direction) {
        iotJumpWay.publishToDeviceCommands({
            "loc": Robotics.location,
            "zne": Robotics.zone,
            "dvc": Robotics.device,
            "message": {
                "From": Robotics.controller,
                "Type": "Arm",
                "Value": direction,
                "Message": "Move " + direction
            }
        });
    },
    cams: function(direction) {
        iotJumpWay.publishToDeviceCommands({
            "loc": Robotics.location,
            "zne": Robotics.zone,
            "dvc": Robotics.device,
            "message": {
                "From": Robotics.controller,
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

        Robotics.damqpua = $("#damqpu").text();
        Robotics.damqpuae = $("#damqpu").text().replace(/\S/gi, '*');
        Robotics.damqppa = $("#damqpp").text();
        Robotics.damqppae = $("#damqpp").text().replace(/\S/gi, '*');
        Robotics.mqttua = $("#idmqttu").text();
        Robotics.mqttuae = $("#idmqttu").text().replace(/\S/gi, '*');
        Robotics.mqttpa = $("#idmqttp").text();
        Robotics.mqttpae = $("#idmqttp").text().replace(/\S/gi, '*');
        Robotics.idappida = $("#idappid").text();
        Robotics.idappidae = $("#idappid").text().replace(/\S/gi, '*');
        Robotics.bcida = $("#bcid").text();
        Robotics.bcidae = $("#bcid").text().replace(/\S/gi, '*');

        $("#damqpu").text(Robotics.damqpuae);
        $("#damqpp").text(Robotics.damqppae);
        $("#idmqttu").text(Robotics.mqttuae);
        $("#idmqttp").text(Robotics.mqttpae);
        $("#idappid").text(Robotics.idappidae);
        $("#bcid").text(Robotics.bcidae);
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
            Robotics.GetLifes();
        }, 5000);
    },
};

$(document).ready(function() {

    $('#emar_create').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            Robotics.Create();
        }
    });

    $('#emar_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            Robotics.Update();
        }
    });

    $("#GeniSysAI").on("click", "#UPA", function(e) {
        e.preventDefault();
        Robotics.arm("UP");
    });
    $("#GeniSysAI").on("click", "#DOWNA", function(e) {
        e.preventDefault();
        Robotics.arm("DOWN");
    });
    $("#GeniSysAI").on("click", "#RIGHTA", function(e) {
        e.preventDefault();
        Robotics.arm("2UP");
    });
    $("#GeniSysAI").on("click", "#LEFTA", function(e) {
        e.preventDefault();
        Robotics.arm("2DOWN");
    });

    $("#GeniSysAI").on("click", "#FORWARDW", function(e) {
        e.preventDefault();
        Robotics.wheels("FORWARD");
    });
    $("#GeniSysAI").on("click", "#BACKW", function(e) {
        e.preventDefault();
        Robotics.wheels("BACK");
    });
    $("#GeniSysAI").on("click", "#RIGHTW", function(e) {
        e.preventDefault();
        Robotics.wheels("RIGHT");
    });
    $("#GeniSysAI").on("click", "#LEFTW", function(e) {
        e.preventDefault();
        Robotics.wheels("LEFT");
    });

    $("#GeniSysAI").on("click", "#UPC", function(e) {
        e.preventDefault();
        Robotics.cams("UP");
    });
    $("#GeniSysAI").on("click", "#DOWNC", function(e) {
        e.preventDefault();
        Robotics.cams("DOWN");
    });
    $("#GeniSysAI").on("click", "#RIGHTC", function(e) {
        e.preventDefault();
        Robotics.cams("RIGHT");
    });
    $("#GeniSysAI").on("click", "#LEFTC", function(e) {
        e.preventDefault();
        Robotics.cams("LEFT");
    });
    $("#GeniSysAI").on("click", "#CENTERC", function(e) {
        e.preventDefault();
        Robotics.cams("CENTER");
    });

    $("#GeniSysAI").on("click", "#reset_emar_mqtt", function(e) {
        e.preventDefault();
        Robotics.ResetMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_emar_apriv", function(e) {
        e.preventDefault();
        Robotics.ResetDvcKey();
    });

    $("#GeniSysAI").on("click", "#reset_emar_amqp", function(e) {
        e.preventDefault();
        Robotics.ResetDvcAMQP();
    });

    $('.hider').hover(function() {
        $('#' + $(this).attr("id")).attr('type', 'text');
    }, function() {
        $('#' + $(this).attr("id")).attr('type', 'password');
    });

    $('#damqpu').hover(function() {
        $("#damqpu").text(Robotics.damqpua);
    }, function() {
        $("#damqpu").text(Robotics.damqpuae);
    });

    $('#damqpp').hover(function() {
        $("#damqpp").text(Robotics.damqppa);
    }, function() {
        $("#damqpp").text(Robotics.damqppae);
    });

    $('#idmqttu').hover(function() {
        $("#idmqttu").text(Robotics.mqttua);
    }, function() {
        $("#idmqttu").text(Robotics.mqttuae);
    });

    $('#idmqttp').hover(function() {
        $("#idmqttp").text(Robotics.mqttpa);
    }, function() {
        $("#idmqttp").text(Robotics.mqttpae);
    });

    $('#idappid').hover(function() {
        $("#idappid").text(Robotics.idappida);
    }, function() {
        $("#idappid").text(Robotics.idappidae);
    });

    $('#bcid').hover(function() {
        $("#bcid").text(Robotics.bcida);
    }, function() {
        $("#bcid").text(Robotics.bcidae);
    });

});