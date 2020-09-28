var NLU = {
    Create: function() {
        $.post(window.location.href, $("#genisysai_create").serialize(), function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("genisysai_create");
                    $('.modal-title').text('GeniSyAI Security Devices');
                    $('.modal-body').html("HIAS GeniSyAI NLU Device ID #" + resp.GDID + " created! Please save the API keys safely. The device's credentials are provided below. The credentials can be reset in the GeniSyAI Security Devices area.<br /><br /><strong>Device ID:</strong> " + resp.DID + "<br /><strong>MQTT User:</strong> " + resp.MU + "<br /><strong>MQTT Password:</strong> " + resp.MP + "<br /><br /><strong>Blockchain User:</strong> " + resp.BU + "<br /><strong>Blockchain Pass:</strong> " + resp.BP + "<br /><br /><strong>App ID:</strong> " + resp.AppID + "<br /><strong>App Key:</strong> " + resp.AppKey + "<br /><br />" + resp.Message);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", "Device ID #" + resp.DID + " created!");
                    break;
                default:
                    msg = "NLU Create Failed: " + resp.Message
                    Logging.logMessage("Core", "NLU", msg);
                    break;
            }
        });
    },
    Update: function() {
        $.post(window.location.href, $("#genisysai_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('.modal-title').text('NLU Update');
                    $('.modal-body').text("NLU Update OK");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "NLU", "NLU Update OK");
                    break;
                default:
                    msg = "NLU Update Failed: " + resp.Message
                    Logging.logMessage("Core", "NLU", msg);
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
                        Logging.logMessage("Core", "Forms", "Device Update OK");
                        $('.modal-title').text('Device Update');
                        $('.modal-body').text(resp.Message);
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

        NLU.mqttu3a = $("#mqttut").text();
        NLU.mqttu3ae = $("#mqttut").text().replace(/\S/gi, '*');
        NLU.mqttp3a = $("#mqttpt").text();
        NLU.mqttp3ae = $("#mqttpt").text().replace(/\S/gi, '*');
        NLU.idappida = $("#idappid").text();
        NLU.idappidae = $("#idappid").text().replace(/\S/gi, '*');
        NLU.bcida = $("#bcid").text();
        NLU.bcidae = $("#bcid").text().replace(/\S/gi, '*');

        $("#mqttut").text(NLU.mqttu3ae);
        $("#mqttpt").text(NLU.mqttp3ae);
        $("#idappid").text(NLU.idappidae);
        $("#bcid").text(NLU.bcidae);
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
                    Logging.logMessage("Core", "NLU", "NLU Stats Updated OK");
                    break;
                default:
                    msg = "NLU Stats Update Failed: " + resp.Message
                    Logging.logMessage("Core", "NLU", msg);
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
            NLU.Create();
        }
    });

    $('#genisysai_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            NLU.Update();
        }
    });

    $("#GeniSysAI").on("click", "#reset_mqtt", function(e) {
        e.preventDefault();
        NLU.ResetMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_apriv", function(e) {
        e.preventDefault();
        NLU.ResetDvcKey();
    });

    $('#idappid').hover(function() {
        $("#idappid").text(NLU.idappida);
    }, function() {
        $("#idappid").text(NLU.idappidae);
    });

    $('#bcid').hover(function() {
        $("#bcid").text(NLU.bcida);
    }, function() {
        $("#bcid").text(NLU.bcidae);
    });

    $('#mqttut').hover(function() {
        $("#mqttut").text(NLU.mqttu3a);
    }, function() {
        $("#mqttut").text(NLU.mqttu3ae);
    });

    $('#mqttpt').hover(function() {
        $("#mqttpt").text(NLU.mqttp3a);
    }, function() {
        $("#mqttpt").text(NLU.mqttp3ae);
    });

    setInterval(function() {
        NLU.GetLife();
    }, 5000);

});