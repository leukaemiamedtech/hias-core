var iotJumpwayUI = {
    Update: function() {
        $.post(window.location.href, $("#location_update").serialize(), function(resp) {
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
                    GeniSys.ResetForm("zone_create");
                    msg = "Create OK: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('Zone Create');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Create Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('Zone Create');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    UpdateZone: function() {
        $.post(window.location.href, $("#zone_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    msg = "Update OK: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('Zone Update');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Update failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('Zone Update');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    CreateDevice: function() {
        $.post(window.location.href, $("#device_create").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
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
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
                    Logging.logMessage("Core", "Forms", resp.Message);
                    $('.modal-title').text('Device Update');
                    $('.modal-body').text(resp.Message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    Logging.logMessage("Core", "Forms", resp.Message);
                    $('.modal-title').text('Device Update');
                    $('.modal-body').text(resp.Message);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    ResetDvcKey: function() {
        $.post(window.location.href, { "reset_key_dvc": 1 },
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
    ResetDvcMqtt: function() {
        $.post(window.location.href, { "reset_mqtt_dvc": 1 },
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
                        $('.modal-title').text('New MQTT Password');
                        $('.modal-body').text(msg);
                        $('#responsive-modal').modal('show');
                        break;
                }
            });
    },
    ResetDvcAMQP: function() {
        $.post(window.location.href, { "reset_dvc_amqp": 1 },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        iotJumpwayUI.damqppa = resp.P;
                        iotJumpwayUI.damqppae = resp.P.replace(/\S/gi, '*');
                        $("#damqpp").text(iotJumpwayUI.damqppae);
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
    CreateApplication: function() {
        $.post(window.location.href, $("#application_create").serialize(), function(resp) {
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
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
                    Logging.logMessage("Core", "Forms", "Application Update OK");
                    $('.modal-title').text('Application Update');
                    $('.modal-body').html(resp.Message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Application Update Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('Application Update');
                    $('.modal-body').html(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    ResetAppKey: function() {
        $.post(window.location.href, { "reset_app_apriv": 1 },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        $('.modal-title').text('Reset App Key');
                        $('.modal-body').text("This application's new key is: " + resp.P);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "Reset failed: " + resp.Message
                        Logging.logMessage("Core", "Forms", msg);
                        $('.modal-title').text('Reset App Key');
                        $('.modal-body').text(msg);
                        $('#responsive-modal').modal('show');
                        break;
                }
            });
    },
    ResetAppMqtt: function() {
        $.post(window.location.href, { "reset_mqtt_app": 1 },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        iotJumpwayUI.amqttpa = resp.P;
                        iotJumpwayUI.amqttpae = resp.P.replace(/\S/gi, '*');
                        $("#amqttp").text(iotJumpwayUI.amqttpae);
                        $('.modal-title').text('Reset MQTT Password');
                        $('.modal-body').text("This application's new MQTT password is: " + resp.P);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "Reset failed: " + resp.Message
                        Logging.logMessage("Core", "Forms", msg);
                        $('.modal-title').text('Reset MQTT Password');
                        $('.modal-body').text(msg);
                        $('#responsive-modal').modal('show');
                        break;
                }
            });
    },
    ResetAppAMQP: function() {
        $.post(window.location.href, { "reset_app_amqp": 1 },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        iotJumpwayUI.aamqppa = resp.P;
                        iotJumpwayUI.aamqppae = resp.P.replace(/\S/gi, '*');
                        $("#appamqpp").text(iotJumpwayUI.aamqppae)
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        $('.modal-title').text('Reset App AMQP Key');
                        $('.modal-body').text("This application's new AMQP key is: " + resp.P);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "Reset failed: " + resp.Message
                        Logging.logMessage("Core", "Forms", msg);
                        $('.modal-title').text('Reset App AMQP Key');
                        $('.modal-body').text(msg);
                        $('#responsive-modal').modal('show');
                        break;
                }
            });
    },
    CreateSensor: function() {
        $.post(window.location.href, $("#sensor_create").serialize(), function(resp) {
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
        $.each($('.hider'), function() {
            $(this).attr('type', 'password');
        });
    },
    HideDeviceInputs: function() {
        $('#ip').attr('type', 'password');
        $('#mac').attr('type', 'password');

        iotJumpwayUI.damqpua = $("#damqpu").text();
        iotJumpwayUI.damqpuae = $("#damqpu").text().replace(/\S/gi, '*');
        iotJumpwayUI.damqppa = $("#damqpp").text();
        iotJumpwayUI.damqppae = $("#damqpp").text().replace(/\S/gi, '*');
        iotJumpwayUI.mqttua = $("#idmqttu").text();
        iotJumpwayUI.mqttuae = $("#idmqttu").text().replace(/\S/gi, '*');
        iotJumpwayUI.mqttpa = $("#idmqttp").text();
        iotJumpwayUI.mqttpae = $("#idmqttp").text().replace(/\S/gi, '*');
        iotJumpwayUI.idappida = $("#idappid").text();
        iotJumpwayUI.idappidae = $("#idappid").text().replace(/\S/gi, '*');
        iotJumpwayUI.bcida = $("#bcid").text();
        iotJumpwayUI.bcidae = $("#bcid").text().replace(/\S/gi, '*');

        $("#damqpu").text(iotJumpwayUI.damqpuae);
        $("#damqpp").text(iotJumpwayUI.damqppae);
        $("#idmqttu").text(iotJumpwayUI.mqttuae);
        $("#idmqttp").text(iotJumpwayUI.mqttpae);
        $("#idappid").text(iotJumpwayUI.idappidae);
        $("#bcid").text(iotJumpwayUI.bcidae);
    },
    HideApplicationInputs: function() {
        $('#ip').attr('type', 'password');
        $('#mac').attr('type', 'password');

        iotJumpwayUI.aamqpua = $("#appamqpu").text();
        iotJumpwayUI.aamqpuae = $("#appamqpp").text().replace(/\S/gi, '*');
        iotJumpwayUI.aamqppa = $("#appamqpp").text();
        iotJumpwayUI.aamqppae = $("#appamqpp").text().replace(/\S/gi, '*');
        iotJumpwayUI.amqttua = $("#amqttu").text();
        iotJumpwayUI.amqttuae = $("#amqttu").text().replace(/\S/gi, '*');
        iotJumpwayUI.amqttpa = $("#amqttp").text();
        iotJumpwayUI.amqttpae = $("#amqttp").text().replace(/\S/gi, '*');
        iotJumpwayUI.appida = $("#appid").text();
        iotJumpwayUI.appidae = $("#appid").text().replace(/\S/gi, '*');
        iotJumpwayUI.bcida = $("#bcid").text();
        iotJumpwayUI.bcidae = $("#bcid").text().replace(/\S/gi, '*');

        $("#appamqpu").text(iotJumpwayUI.aamqpuae);
        $("#appamqpp").text(iotJumpwayUI.aamqppae);
        $("#amqttu").text(iotJumpwayUI.amqttuae);
        $("#amqttp").text(iotJumpwayUI.amqttpae);
        $("#appid").text(iotJumpwayUI.appidae);
        $("#bcid").text(iotJumpwayUI.bcidae);
    },
    GetLife: function() {
        $.post(window.location.href, { "get_life": 1 }, function(resp) {
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
                    $("#idebatU").text(resp.ResponseData.battery)
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
        $.post(window.location.href, { "get_alife": 1 }, function(resp) {
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
        $.post(window.location.href, { "get_slife": 1 }, function(resp) {
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
        }, 20000);
    },
    StartApplicationLife: function() {
        setInterval(function() {
            iotJumpwayUI.GetAppLife();
        }, 20000);
    },
    StartStaffLife: function() {
        setInterval(function() {
            iotJumpwayUI.GetStaffLife();
        }, 20000);
    },
};
$(document).ready(function() {

    iotJumpwayUI.HideLocationInputs();

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

    $("#GeniSysAI").on("click", "#reset_app_amqp", function(e) {
        e.preventDefault();
        iotJumpwayUI.ResetAppAMQP();
    });

    $("#GeniSysAI").on("click", "#reset_dvc_amqp", function(e) {
        e.preventDefault();
        iotJumpwayUI.ResetDvcAMQP();
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

    $('#appamqpu').hover(function() {
        $("#appamqpu").text(iotJumpwayUI.aamqpua);
    }, function() {
        $("#appamqpu").text(iotJumpwayUI.aamqpuae);
    });

    $('#appamqpp').hover(function() {
        $("#appamqpp").text(iotJumpwayUI.aamqppa);
    }, function() {
        $("#appamqpp").text(iotJumpwayUI.aamqppae);
    });

    $('#damqpu').hover(function() {
        $("#damqpu").text(iotJumpwayUI.damqpua);
    }, function() {
        $("#damqpu").text(iotJumpwayUI.damqpuae);
    });

    $('#damqpp').hover(function() {
        $("#damqpp").text(iotJumpwayUI.damqppa);
    }, function() {
        $("#damqpp").text(iotJumpwayUI.damqppae);
    });

    $("#GeniSysAI").on("click", ".removeProperty", function(e) {
        e.preventDefault();
        $('#property-' + $(this).data('id')).fadeOut(300, function() { $(this).remove(); });
    });

    $("#GeniSysAI").on("click", ".removeCommand", function(e) {
        e.preventDefault();
        $('#command-' + $(this).data('id')).fadeOut(300, function() { $(this).remove(); });
    });

    $("#GeniSysAI").on("click", ".removeState", function(e) {
        e.preventDefault();
        $('#state-' + $(this).data('id')).fadeOut(300, function() { $(this).remove(); });
    });

    $("#GeniSysAI").on("click", "#addProperty", function(e) {
        e.preventDefault();
        $('.modal-title').text('Add Property');
        $('.modal-footer button').text('OK');
        $('#buttonId').button('option', 'label', 'OK');
        $('.modal-body').html("<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>Property: </div><div class='col-lg-8 col-md-8 col-sm-8 col-xs-8'><input type ='text' id='addPropertyKey' class='form-control' /></div></div>");
        $('#responsive-modal').modal('show');
        $('#responsive-modal').on('hide.bs.modal', function() {
            if ($("#addPropertyKey").val()) {
                var addProperty = '<div class="row" style="margin-bottom: 5px;" id = "property-' + $("#addPropertyKey").val() + '"><div class="col-lg-11 col-md-11 col-sm-11 col-xs-11"><input type="text" class="form-control" id="properties[]" name="properties[]" placeholder="' + $("#addPropertyKey").val() + '" value="' + $("#addPropertyKey").val() + '" required></div><div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"><a href="javascript:void(0);" class="removeProperty" data-id="' + $("#addPropertyKey").val() + '"><i class="fas fa-trash-alt"></i></a></div></div>';
                $("#propertyContent").append(addProperty);
                $('.modal-body').html("");
            }
        })
    });

    $("#GeniSysAI").on("click", "#addCommand", function(e) {
        e.preventDefault();
        $('.modal-title').text('Add Command');
        $('.modal-footer button').text('OK');
        $('#buttonId').button('option', 'label', 'OK');
        $('.modal-body').html("<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>Command Name: </div><div class='col-lg-8 col-md-8 col-sm-8 col-xs-8'><input type ='text' id='addCommandKey' class='form-control' /></div></div><div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>Commands: </div><div class='col-lg-8 col-md-8 col-sm-8 col-xs-8'><input type ='text' id='addCommandValue' class='form-control' /></div></div>");
        $('#responsive-modal').modal('show');
        $('#responsive-modal').on('hide.bs.modal', function() {
            if ($("#addCommandKey").val() && $("#addCommandValue").val()) {
                var addCommand = '<div class= "row" style="margin-bottom: 5px;" id="command-' + $("#addCommandKey").val() + '"><div class="col-lg-11 col-md-11 col-sm-11 col-xs-11"><strong>' + $("#addCommandKey").val() + '</strong><input type="text" class="form-control" name="commands[' + $("#addCommandKey").val() + ']" placeholder="Commands as comma separated string" value="' + $("#addCommandValue").val() + '" required></div><div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"><br /><a href="javascript:void(0);" class="removeCommand" data-id="' + $("#addCommandKey").val() + '"><i class="fas fa-trash-alt"></i></a></div></div>';
                $("#commandsContent").append(addCommand);
                $('.modal-body').html("");
            }
        })
    });

    $("#GeniSysAI").on("click", "#addState", function(e) {
        e.preventDefault();
        $('.modal-title').text('Add State');
        $('.modal-footer button').text('OK');
        $('#buttonId').button('option', 'label', 'OK');
        $('.modal-body').html("<div class='row'><div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>State Value: </div><div class='col-lg-8 col-md-8 col-sm-8 col-xs-8'><input type ='text' id='addStateValue' class='form-control' /></div></div>");
        $('#responsive-modal').modal('show');
        $('#responsive-modal').on('hide.bs.modal', function() {
            if ($("#addStateValue").val()) {
                var key = (parseInt($("#lastState").text()) + 1);
                var addState = '<div class="row" style="margin-bottom: 5px;" id="state-' + key + '"><div class="col-lg-11 col-md-11 col-sm-11 col-xs-11"><input type="text" class="form-control" name="states[]" placeholder="State" value="' + $("#addStateValue").val() + '" required /></div><div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"><a href="javascript:void(0);" class="removeState" data-id="' + key + '"><i class="fas fa-trash-alt"></i></a></div></div >';
                $("#stateContent").append(addState);
                $('.modal-body').html("");
                $("#lastState").text(key);
            }
        })
    });

    $("#sensorSelect").change(function() {
        if ($(this).val()) {
            var key = parseInt($("#lastSensor").text()) != 0 ? (parseInt($("#lastSensor").text()) + 1) : 0;
            var addSensor = '<div class="row form-control" style="margin-bottom: 5px; margin-left: 0.5px;" id="sensor-' + key + '"><div class="col-lg-11 col-md-11 col-sm-11 col-xs-11"><strong>' + $(this).find("option:selected").text() + '</strong></div><div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"><a href="javascript:void(0);" class="removeSensor" data-id="' + key + '"><i class="fas fa-trash-alt"></i></a></div><input type="hidden" class="form-control" name="sensors[]" value="' + $(this).val() + '" required ></div >';
            $("#sensorContent").append(addSensor);
            $("#lastSensor").text(key);
            $('#sensorSelect').prop('selectedIndex', 0);
        }
    });

    $("#GeniSysAI").on("click", ".removeSensor", function(e) {
        e.preventDefault();
        $('#sensor-' + $(this).data('id')).fadeOut(300, function() { $(this).remove(); });
    });

    $("#actuatorSelect").change(function() {
        if ($(this).val()) {
            var key = parseInt($("#lastActuator").text()) != 0 ? (parseInt($("#lastActuator").text()) + 1) : 0;
            var addActuator = '<div class="row form-control" style="margin-bottom: 5px; margin-left: 0.5px;" id="actuator-' + key + '"><div class="col-lg-11 col-md-11 col-sm-11 col-xs-11"><strong>' + $(this).find("option:selected").text() + '</strong></div><div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"><a href="javascript:void(0);" class="removeActuator" data-id="' + key + '"><i class="fas fa-trash-alt"></i></a></div><input type="hidden" class="form-control" name="actuators[]" value="' + $(this).val() + '" required ></div >';
            $("#actuatorContent").append(addActuator);
            $("#lastActuator").text(key);
            $('#actuatorSelect').prop('selectedIndex', 0);
        }
    });

    $("#GeniSysAI").on("click", ".removeActuator", function(e) {
        e.preventDefault();
        $('#actuator-' + $(this).data('id')).fadeOut(300, function() { $(this).remove(); });
    });

});