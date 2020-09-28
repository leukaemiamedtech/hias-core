var GeniSysAI = {
    Create: function() {
        $.post(window.location.href, $("#genisysai_create").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("genisysai_create");
                    $('.modal-title').text('GeniSyAI Security Devices');
                    $('.modal-body').html("HIAS GeniSyAI Security Device ID #" + resp.GDID + " created! Please save the API keys safely. The device's credentials are provided below. The credentials can be reset in the GeniSyAI Security Devices area.<br /><br /><strong>Device ID:</strong> " + resp.DID + "<br /><strong>MQTT User:</strong> " + resp.MU + "<br /><strong>MQTT Password:</strong> " + resp.MP + "<br /><br /><strong>Blockchain User:</strong> " + resp.BU + "<br /><strong>Blockchain Pass:</strong> " + resp.BP + "<br /><br /><strong>App ID:</strong> " + resp.AppID + "<br /><strong>App Key:</strong> " + resp.AppKey + "<br /><br />" + resp.Message);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", "Device ID #" + resp.DID + " created!");
                    break;
                default:
                    msg = "GeniSysAI Create Failed: " + resp.Message
                    Logging.logMessage("Core", "GeniSysAI", msg);
                    break;
            }
        });
    },
    Update: function() {
        $.post(window.location.href, $("#genisysai_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "Forms", "Device Update OK");
                    $('.modal-title').text('Device Update');
                    $('.modal-body').text(resp.Message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "GeniSysAI Update Failed: " + resp.Message
                    Logging.logMessage("Core", "GeniSysAI", msg);
                    break;
            }
        });
    },
    ResetMqtt: function() {
        $.post(window.location.href, { "reset_mqtt": 1, "id": $("#did").val(), "lid": $("#lid").val(), "zid": $("#zid").val() },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        GeniSysAI.mqttpa = resp.P;
                        GeniSysAI.mqttpae = resp.P.replace(/\S/gi, '*');
                        $("#idmqttp").text(GeniSysAI.mqttpae);
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
        $.post(window.location.href, { "reset_key": 1, "id": $("#did").val(), "lid": $("#lid").val(), "zid": $("#zid").val() },
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
    HideInputs: function() {
        $('#ip').attr('type', 'password');
        $('#mac').attr('type', 'password');
        $('#sport').attr('type', 'password');
        $('#sportf').attr('type', 'password');
        $('#sckport').attr('type', 'password');

        GeniSysAI.mqttu3a = $("#mqttut").text();
        GeniSysAI.mqttu3ae = $("#mqttut").text().replace(/\S/gi, '*');
        GeniSysAI.mqttp3a = $("#mqttpt").text();
        GeniSysAI.mqttp3ae = $("#mqttpt").text().replace(/\S/gi, '*');
        GeniSysAI.idappida = $("#idappid").text();
        GeniSysAI.idappidae = $("#idappid").text().replace(/\S/gi, '*');
        GeniSysAI.bcida = $("#bcid").text();
        GeniSysAI.bcidae = $("#bcid").text().replace(/\S/gi, '*');

        $("#mqttut").text(GeniSysAI.mqttu3ae);
        $("#mqttpt").text(GeniSysAI.mqttp3ae);
        $("#idappid").text(GeniSysAI.idappidae);
        $("#bcid").text(GeniSysAI.bcidae);
    },
    GetLife: function() {
        $.post(window.location.href, { "get_tlife": 1, "device": $("#did").val() }, function(resp) {
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
                    Logging.logMessage("Core", "GeniSysAI", "GeniSysAI Stats Updated OK");
                    break;
                default:
                    msg = "GeniSysAI Stats Update Failed: " + resp.Message
                    Logging.logMessage("Core", "GeniSysAI", msg);
                    break;
            }
        });
    },
};
$(document).ready(function() {

    $('.hider').hover(function() {
        $('#' + $(this).attr("id")).attr('type', 'text');
    }, function() {
        $('#' + $(this).attr("id")).attr('type', 'password');
    });

    $('#genisysai_create').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            GeniSysAI.Create();
        }
    });

    $('#genisysai_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            GeniSysAI.Update();
        }
    });

    $("#GeniSysAI").on("click", "#reset_mqtt", function(e) {
        e.preventDefault();
        GeniSysAI.ResetMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_apriv", function(e) {
        e.preventDefault();
        GeniSysAI.ResetDvcKey();
    });

    $('#mqttut').hover(function() {
        $("#mqttut").text(GeniSysAI.mqttu3a);
    }, function() {
        $("#mqttut").text(GeniSysAI.mqttu3ae);
    });

    $('#mqttpt').hover(function() {
        $("#mqttpt").text(GeniSysAI.mqttp3a);
    }, function() {
        $("#mqttpt").text(GeniSysAI.mqttp3ae);
    });

    $('#idappid').hover(function() {
        $("#idappid").text(GeniSysAI.idappida);
    }, function() {
        $("#idappid").text(GeniSysAI.idappidae);
    });

    $('#bcid').hover(function() {
        $("#bcid").text(GeniSysAI.bcida);
    }, function() {
        $("#bcid").text(GeniSysAI.bcidae);
    });

    setInterval(function() {
        GeniSysAI.GetLife();
    }, 5000);

});