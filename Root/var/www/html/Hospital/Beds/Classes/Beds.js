var Beds = {
    Create: function() {
        $.post(window.location.href, $("#bed_create").serialize(), function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("bed_create");
                    $('.modal-title').text('HIAS Bed');
                    $('.modal-body').html("HIAS Bed ID #" + resp.BID + " created! The bed's credentials are provided below. The credentials can be reset in the beds area.<br /><br /><strong>User ID:</strong> " + resp.BID + "<br /><strong>Username:</strong> " + resp.Uname + "<br /><br /><strong>MQTT User:</strong> " + resp.MU + "<br /><strong>MQTT Password:</strong> " + resp.MP + "<br /><br /><strong>Blockchain User:</strong> " + resp.BCU + "<br /><strong>Blockchain Pass:</strong> " + resp.BCP + "<br /><br /><strong>App ID:</strong> " + resp.AppID + "<br /><strong>App Key:</strong> " + resp.AppKey + "<br />");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", "Bed ID #" + resp.UID + " created!");
                    break;
                default:
                    msg = "Bed Create Failed: " + resp.Message
                    $('.modal-title').text('HIAS Bed');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Beds", msg);
                    break;
            }
        });
    },
    Update: function() {
        $.post(window.location.href, $("#bed_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('.modal-title').text('Bed Update');
                    $('.modal-body').text(resp.Message);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Beds", "Bed Updated OK");
                    break;
                default:
                    msg = "Bed Update Failed: " + resp.Message
                    $('.modal-title').text('Bed Update');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Beds", msg);
                    break;
            }
        });
    },
    ResetMqtt: function() {
        $.post(window.location.href, { "reset_mqtt_bed": 1, "id": $("#id").val() }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('.modal-title').text('New Password');
                    $('.modal-body').text("This bed's new MQTT password is: " + resp.P);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Beds", "Bed MQTT Reset OK");
                    $("#mqttp").text(resp.P)
                    break;
                default:
                    msg = "Bed MQTT Reset Failed: " + resp.Message
                    Logging.logMessage("Core", "Beds", msg);
                    break;
            }
        });
    },
    ResetAppKey: function() {
        $.post(window.location.href, { "reset_bd_apriv": 1, "identifier": $("#identifier").val(), "bid": $("#id").val(), "id": $("#did").val() },
            function(resp) {
                console.log(resp)
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "API", "User API Key Reset OK");
                        $('.modal-title').text('New API Key');
                        $('.modal-body').text("This bed's new API Key is: " + resp.P);
                        $('#responsive-modal').modal('show');
                        break;
                    default:
                        msg = "User API Key Reset Failed: " + resp.Message
                        Logging.logMessage("Core", "API", msg);
                        break;
                }
            });
    },
    HideInputs: function() {

        $('#ip').attr('type', 'password');
        $('#mac').attr('type', 'password');

        Beds.mqttua = $("#bedmqttu").text();
        Beds.mqttuae = $("#bedmqttu").text().replace(/\S/gi, '*');
        Beds.mqttpa = $("#bedmqttp").text();
        Beds.mqttpae = $("#bedmqttp").text().replace(/\S/gi, '*');
        Beds.bcida = $("#bcid").text();
        Beds.bcidae = $("#bcid").text().replace(/\S/gi, '*');
        Beds.idappida = $("#idappid").text();
        Beds.idappidae = $("#idappid").text().replace(/\S/gi, '*');

        $("#bedmqttu").text(Beds.mqttuae);
        $("#bedmqttp").text(Beds.mqttpae);
        $("#bcid").text(Beds.bcidae);
        $("#idappid").text(Beds.idappidae);
    },
};
$(document).ready(function() {

    $('.hider').hover(function() {
        $('#' + $(this).attr("id")).attr('type', 'text');
    }, function() {
        $('#' + $(this).attr("id")).attr('type', 'password');
    });

    $('#bedmqttu').hover(function() {
        $("#bedmqttu").text(Beds.mqttua);
    }, function() {
        $("#bedmqttu").text(Beds.mqttuae);
    });

    $('#bedmqttp').hover(function() {
        $("#bedmqttp").text(Beds.mqttpa);
    }, function() {
        $("#bedmqttp").text(Beds.bcidae);
    });

    $('#bcid').hover(function() {
        $("#bcid").text(Beds.bcida);
    }, function() {
        $("#bcid").text(Beds.bcidae);
    });

    $('#idappid').hover(function() {
        $("#idappid").text(Beds.idappida);
    }, function() {
        $("#idappid").text(Beds.idappidae);
    });

    $('#bed_create').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            Beds.Create();
        }
    });

    $('#bed_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            Beds.Update();
        }
    });

    $("#GeniSysAI").on("click", "#reset_bed_mqtt", function(e) {
        e.preventDefault();
        Beds.ResetMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_bd_apriv", function(e) {
        e.preventDefault();
        Beds.ResetAppKey();
    });

});