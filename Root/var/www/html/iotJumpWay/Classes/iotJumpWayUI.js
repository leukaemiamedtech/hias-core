var iotJumpwayUI = {
    Update: function() {
        $.post(window.location.href, $("#location_update").serialize(), function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "Forms", "Location Update OK");
                    $('.modal-title').text('Location Update');
                    $('.modal-body').text("Location Update OK");
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Location Update Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    CreateZone: function() {
        $.post(window.location.href, $("#zone_create").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    window.location.replace(location.protocol + "//" + location.hostname + "/iotJumpWay/" + resp.LID + "/Zones/" + resp.ZID);
                    Logging.logMessage("Core", "Forms", "Zone Create OK");
                    break;
                default:
                    msg = "Zone Create Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    UpdateZone: function() {
        $.post(window.location.href, $("#zone_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "Forms", "Zone Update OK");
                    $('.modal-title').text('Zone Update');
                    $('.modal-body').text("Zone Update OK");
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Update failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    CreateDevice: function() {
        $.post(window.location.href, $("#device_create").serialize(), function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                case "OK":
                    GeniSys.ResetForm("device_create");
                    $('.modal-title').text('iotJumpWay Devices');
                    $('.modal-body').html("HIAS Device ID #" + resp.DID + " created! Please save the API keys safely. The device's credentials are provided below. The credentials can be reset in the devices area.<br /><br /><strong>Device ID:</strong> " + resp.DID + "<br /><strong>MQTT User:</strong> " + resp.MU + "<br /><strong>MQTT Password:</strong> " + resp.MP + "<br /><br /><strong>Blockchain User:</strong> " + resp.BU + "<br /><strong>Blockchain Pass:</strong> " + resp.BP + "<br /><br /><strong>App ID:</strong> " + resp.AppID + "<br /><strong>App Key:</strong> " + resp.AppKey + "<br /><br />" + resp.Message);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", "Device ID #" + resp.DID + " created!");
                    break;
                default:
                    msg = "Create Device Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    UpdateDevice: function() {
        $.post(window.location.href, $("#device_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "Forms", "Device Update OK");
                    $('.modal-title').text('Device Update');
                    $('.modal-body').text(resp.Message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Device Update Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    ResetDvcMqtt: function() {
        $.post(window.location.href, { "reset_mqtt_dvc": 1, "id": $("#id").val(), "lid": $("#lid").val(), "zid": $("#zid").val() },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        iotJumpwayUI.mqttpa = resp.P;
                        iotJumpwayUI.mqttpae = resp.P.replace(/\S/gi, '*');
                        $("#idmqttp").text(iotJumpwayUI.mqttpae);
                        $('.modal-title').text('New MQTT Password');
                        $('.modal-body').text("This device's new MQTT password is: " + resp.P);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "Reset failed: " + resp.Message
                        Logging.logMessage("Core", "Forms", msg);
                        break;
                }
            });
    },
    ResetDvcKey: function() {
        $.post(window.location.href, { "reset_key_dvc": 1, "id": $("#id").val(), "lid": $("#lid").val(), "zid": $("#zid").val() },
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
    CreateApplication: function() {
        $.post(window.location.href, $("#application_create").serialize(), function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("application_create");
                    $('.modal-title').text('iotJumpWay Applications');
                    $('.modal-body').html("Application ID #" + resp.AID + " created! Please save the credentials safely. The credentials are provided below and can be reset in the iotJumpWay Applications area.<br /><br /><strong>Application ID:</strong> " + resp.AID + "<br /><br /><strong>MQTT User:</strong> " + resp.MU + "<br /><strong>MQTT Password:</strong> " + resp.MP + "<br /><br /><strong>Blockchain Address:</strong> " + resp.BU + " < br /> <strong>Blockchain Password:</strong> " + resp.BP + "<br /><br /><strong>App ID:</strong> " + resp.AppID + "<br /><strong>App Key:</strong> " + resp.AppKey + "<br /><br />" + resp.Message + "<br /><br />");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", "Application ID #" + resp.AID + " created!");
                    break;
                default:
                    msg = "Application Create Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    UpdateApplication: function() {
        $.post(window.location.href, $("#application_update").serialize(), function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "Forms", "Application Update OK");
                    $('.modal-title').text('Application Update');
                    $('.modal-body').html(resp.Message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Application Update Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    ResetAppMqtt: function() {
        $.post(window.location.href, { "reset_mqtt_app": 1, "id": $("#id").val(), "lid": $("#lid").val() },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        iotJumpwayUI.amqttpa = resp.P;
                        iotJumpwayUI.amqttpae = resp.P.replace(/\S/gi, '*');
                        $("#amqttp").text(iotJumpwayUI.amqttpae);
                        $('.modal-title').text('New MQTT Password');
                        $('.modal-body').text("This application's new MQTT password is: " + resp.P);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "Reset failed: " + resp.Message
                        Logging.logMessage("Core", "Forms", msg);
                        break;
                }
            });
    },
    ResetAppKey: function() {
        $.post(window.location.href, { "reset_app_apriv": 1, "identifier": $("#identifier").val(), "id": $("#id").val(), "lid": $("#lid").val() },
            function(resp) {
                console.log(resp)
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        $('.modal-title').text('New App Key Password');
                        $('.modal-body').text("This application's new key is: " + resp.P);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "Reset failed: " + resp.Message
                        Logging.logMessage("Core", "Forms", msg);
                        break;
                }
            });
    },
    CreateSensor: function() {
        $.post(window.location.href, $("#sensor_create").serialize(), function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    window.location.replace(location.protocol + "//" + location.hostname + "/iotJumpWay/Sensors/" + resp.SID + '/');
                    Logging.logMessage("Core", "Forms", "Sensor/Actuator Create OK");
                    break;
                default:
                    msg = "Sensor/Actuator Create Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    UpdateSensor: function() {
        $.post(window.location.href, $("#sensor_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "Forms", "Sensor Update OK");
                    $('.modal-title').text('Sensor/Actuator Update');
                    $('.modal-body').text("Sensor/Actuator Update OK");
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Sensor/Actuator Update Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    HideLocationInputs: function() {
        $('#ip').attr('type', 'password');
        $('#mac').attr('type', 'password');
    },
    HideDeviceInputs: function() {
        $('#ip').attr('type', 'password');
        $('#mac').attr('type', 'password');

        iotJumpwayUI.mqttua = $("#idmqttu").text();
        iotJumpwayUI.mqttuae = $("#idmqttu").text().replace(/\S/gi, '*');
        iotJumpwayUI.mqttpa = $("#idmqttp").text();
        iotJumpwayUI.mqttpae = $("#idmqttp").text().replace(/\S/gi, '*');
        iotJumpwayUI.idappida = $("#idappid").text();
        iotJumpwayUI.idappidae = $("#idappid").text().replace(/\S/gi, '*');
        iotJumpwayUI.bcida = $("#bcid").text();
        iotJumpwayUI.bcidae = $("#bcid").text().replace(/\S/gi, '*');

        $("#idmqttu").text(iotJumpwayUI.mqttuae);
        $("#idmqttp").text(iotJumpwayUI.mqttpae);
        $("#idappid").text(iotJumpwayUI.idappidae);
        $("#bcid").text(iotJumpwayUI.bcidae);
    },
    HideApplicationInputs: function() {
        $('#ip').attr('type', 'password');
        $('#mac').attr('type', 'password');

        iotJumpwayUI.amqttua = $("#amqttu").text();
        iotJumpwayUI.amqttuae = $("#amqttu").text().replace(/\S/gi, '*');
        iotJumpwayUI.amqttpa = $("#amqttp").text();
        iotJumpwayUI.amqttpae = $("#amqttp").text().replace(/\S/gi, '*');
        iotJumpwayUI.appida = $("#appid").text();
        iotJumpwayUI.appidae = $("#appid").text().replace(/\S/gi, '*');
        iotJumpwayUI.bcida = $("#bcid").text();
        iotJumpwayUI.bcidae = $("#bcid").text().replace(/\S/gi, '*');

        $("#amqttu").text(iotJumpwayUI.amqttuae);
        $("#amqttp").text(iotJumpwayUI.amqttpae);
        $("#appid").text(iotJumpwayUI.appidae);
        $("#bcid").text(iotJumpwayUI.bcidae);
    },
    GetLife: function() {
        $.post(window.location.href, { "get_life": 1, "device": $("#id").val() }, function(resp) {
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
                    $("#idecpuU").text(resp.ResponseData.cpu)
                    $("#idememU").text(resp.ResponseData.mem)
                    $("#idehddU").text(resp.ResponseData.hdd)
                    $("#idetempU").text(resp.ResponseData.tempr)
                    Logging.logMessage("Core", "Stats", "Device Stats Updated OK");
                    break;
                default:
                    msg = "Device Stats Update Failed: " + resp.Message
                    Logging.logMessage("Core", "Stats", msg);
                    break;
            }
        });
    },
    GetAppLife: function() {
        $.post(window.location.href, { "get_alife": 1, "application": $("#id").val() }, function(resp) {
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
                    $("#idecpuU").text(resp.ResponseData.cpu)
                    $("#idememU").text(resp.ResponseData.mem)
                    $("#idehddU").text(resp.ResponseData.hdd)
                    $("#idetempU").text(resp.ResponseData.tempr)
                    Logging.logMessage("Core", "Stats", "Application Stats Updated OK");
                    break;
                default:
                    msg = "Application Stats Update Failed: " + resp.Message
                    Logging.logMessage("Core", "Stats", msg);
                    break;
            }
        });
    },
    GetStaffLife: function() {
        $.post(window.location.href, { "get_slife": 1, "application": $("#id").val() }, function(resp) {
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
                    $("#idecpuU").text(resp.ResponseData.cpu)
                    $("#idememU").text(resp.ResponseData.mem)
                    $("#idehddU").text(resp.ResponseData.hdd)
                    $("#idetempU").text(resp.ResponseData.tempr)
                    Logging.logMessage("Core", "Stats", "Staff Stats Updated OK");
                    break;
                default:
                    msg = "Staff Stats Update Failed: " + resp.Message
                    Logging.logMessage("Core", "Stats", msg);
                    break;
            }
        });
    },
    StartDeviceLife: function() {
        setInterval(function() {
            iotJumpwayUI.GetLife();
        }, 5000);
    },
    StartApplicationLife: function() {
        setInterval(function() {
            iotJumpwayUI.GetAppLife();
        }, 5000);
    },
    StartStaffLife: function() {
        setInterval(function() {
            iotJumpwayUI.GetStaffLife();
        }, 5000);
    },
};
$(document).ready(function() {

    $('#location_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            iotJumpwayUI.Update();
        }
    });

    $('#zone_create').validator().on('submit', function(e) {
        e.preventDefault();
        iotJumpwayUI.CreateZone();
    });

    $('#zone_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            iotJumpwayUI.UpdateZone();
        }
    });

    $('#device_create').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            iotJumpwayUI.CreateDevice();
        }
    });

    $('#device_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            iotJumpwayUI.UpdateDevice();
        }
    });

    $('#sensor_create').on('click', function(e) {
        e.preventDefault();
        $(this).closest("form").submit();
    });

    $('#sensor_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            iotJumpwayUI.UpdateSensor();
        }
    });

    $('#application_create').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            iotJumpwayUI.CreateApplication();
        }
    });

    $('#application_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            iotJumpwayUI.UpdateApplication();
        }
    });

    $("#GeniSysAI").on("click", "#reset_app_mqtt", function(e) {
        e.preventDefault();
        iotJumpwayUI.ResetAppMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_dvc_mqtt", function(e) {
        e.preventDefault();
        iotJumpwayUI.ResetDvcMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_dvc_apriv", function(e) {
        e.preventDefault();
        iotJumpwayUI.ResetDvcKey();
    });

    $("#GeniSysAI").on("click", "#reset_app_apriv", function(e) {
        e.preventDefault();
        iotJumpwayUI.ResetAppKey();
    });

    $('.hider').hover(function() {
        $('#' + $(this).attr("id")).attr('type', 'text');
    }, function() {
        $('#' + $(this).attr("id")).attr('type', 'password');
    });

    $('#bcid').hover(function() {
        $("#bcid").text(iotJumpwayUI.bcida);
    }, function() {
        $("#bcid").text(iotJumpwayUI.bcidae);
    });

    $('#idappid').hover(function() {
        $("#idappid").text(iotJumpwayUI.idappida);
    }, function() {
        $("#idappid").text(iotJumpwayUI.idappidae);
    });

    $('#appid').hover(function() {
        $("#appid").text(iotJumpwayUI.appida);
    }, function() {
        $("#appid").text(iotJumpwayUI.appidae);
    });

    $('#idmqttu').hover(function() {
        $("#idmqttu").text(iotJumpwayUI.mqttua);
    }, function() {
        $("#idmqttu").text(iotJumpwayUI.mqttuae);
    });

    $('#idmqttp').hover(function() {
        $("#idmqttp").text(iotJumpwayUI.mqttpa);
    }, function() {
        $("#idmqttp").text(iotJumpwayUI.mqttpae);
    });

    $('#amqttu').hover(function() {
        $("#amqttu").text(iotJumpwayUI.amqttua);
    }, function() {
        $("#amqttu").text(iotJumpwayUI.amqttuae);
    });

    $('#amqttp').hover(function() {
        $("#amqttp").text(iotJumpwayUI.amqttpa);
    }, function() {
        $("#amqttp").text(iotJumpwayUI.amqttuae);
    });

});