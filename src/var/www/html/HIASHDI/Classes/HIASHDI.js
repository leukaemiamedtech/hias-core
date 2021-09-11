var HIASHDI = {
    Monitor: function() {
        setInterval(function() {
            HIASHDI.GetStats();
        }, 15000);
    },
    GetStats: function() {
        $.post(window.location.href, { "get_hiashdi_life": true }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    if (resp.Data["networkStatus"] == "ONLINE") {
                        $("#offline1").removeClass("hide");
                        $("#online1").addClass("hide");
                    } else {
                        $("#offline1").addClass("hide");
                        $("#online1").removeClass("hide");
                    }
                    $("#batteryUsage").text(resp.Data.battery)
                    $("#cpuUsage").text(resp.Data.cpu)
                    $("#memoryUsage").text(resp.Data.mem)
                    $("#hddUsage").text(resp.Data.hdd)
                    $("#temperatureUsage").text(resp.Data.tempr)
                    Logging.logMessage("Core", "HIASHDI", "HIASHDI Stats Update OK");
                    break;
                default:
                    msg = "HIASHDI Stats Update KO"
                    Logging.logMessage("Core", "HIASHDI", msg);
                    break;
            }
        });
    },
    HideSecret: function() {
        $.each($('.hiderstr'), function() {
            $(this).data("hidden", $(this).text());
            $(this).text($(this).text().replace(/\S/gi, '*'));
        });
    },
    HideInputSecret: function() {
        $.each($('.hider'), function() {
            $(this).attr('type', 'password');
        });
    },
    SendConsole: function() {
        window.parent.$('#response').html("Loading....");
        $.post(window.location.href, $("#hiashdi_console_form").serialize(), function(resp, textStatus, jQxhr) {
            var resp = jQuery.parseJSON(resp);
            rCode = resp.Code;
            rHeaders = resp.Headers;
            $("#rcode").text(rCode);
            $("#rcode").text(rCode);
            $("#rlen").text(resp.Body ? resp.Body.length : 0);
            $("#rtime").text(new Date($.now()));
            switch (parseInt(rCode)) {
                case 200:
                    var fjson = JSON.stringify(resp.Body, null, '\t');
                    window.parent.$('#response').html(fjson);
                    window.parent.$('#response_headers').html(rHeaders);
                    message = resp.Message + " Request status code: " + rCode;
                    Logging.logMessage("Core", "HIASHDI", message);
                    break;
                default:
                    window.parent.$('#response').html("");
                    message = resp.Message + "<br /><br />Request status code: " + rCode;
                    $('.modal-title').text('HIASHDI');
                    $('.modal-body').html(message);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "HIASHDI", message);
                    break;
            }
        });
    },
    UpdateCore: function() {
        $.post(window.location.href, $("#update_hiashdi_broker").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            message = resp.Message;
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
                    Logging.logMessage("Core", "HIASHDI", message);
                    $('.modal-title').text('HIASHDI');
                    $('.modal-body').text(message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    Logging.logMessage("Core", "HIASHDI", msg);
                    $('.modal-title').text('HIASHDI');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    UpdateEntity: function() {
        $.post(window.location.href, $("#update_hiashdi_form").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            message = resp.Message;
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
                    Logging.logMessage("Core", "HIASHDI", message);
                    $('.modal-title').text('HIASHDI');
                    $('.modal-body').text(message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    Logging.logMessage("Core", "HIASHDI", msg);
                    $('.modal-title').text('HIASHDI');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    ResetKey: function() {
        $.post(window.location.href, { "reset_hiashdi_key": 1 }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "HIASHDI", "Reset OK");
                    $('.modal-title').text('Reset App Key');
                    $('.modal-body').text("This agent's new key is: " + resp.P);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Reset failed: " + resp.Message
                    Logging.logMessage("Core", "HIASHDI", msg);
                    $('.modal-title').text('Reset App Key');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    ResetMqtt: function() {
        $.post(window.location.href, { "reset_hiashdi_mqtt": 1 }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "Forms", "Reset OK");
                    $("#hiashdi_mqqt_password").text(HIASHDI.P);
                    $("#hiashdi_mqqt_password").text($("#hiashdi_mqqt_password").text().replace(/\S/gi, '*'));
                    $('.modal-title').text('Reset MQTT Password');
                    $('.modal-body').text("This agent's new MQTT password is: " + resp.P);
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
    }
};
$(document).ready(function() {

    $('.hiderstr').hover(function() {
        $(this).text($(this).data("hidden"));
        $(this).removeClass("hiderstr");
    }, function() {
        $(this).text($(this).text().replace(/\S/gi, '*'));
    });

    $('.hider').hover(function() {
        $('#' + $(this).attr("id")).attr('type', 'text');
    }, function() {
        $('#' + $(this).attr("id")).attr('type', 'password');
    });

    $('#hiashdi_console_form').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            HIASHDI.SendConsole();
        }
    });

    $('#update_hiashdi_broker').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            HIASHDI.UpdateCore();
        }
    });

    $('#update_hiashdi_form').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            HIASHDI.UpdateEntity();
        }
    });

    $("#GeniSysAI").on("click", "#reset_hiashdi_key", function(e) {
        e.preventDefault();
        HIASHDI.ResetKey();
    });

    $("#GeniSysAI").on("click", "#reset_hiashdi_mqtt", function(e) {
        e.preventDefault();
        HIASHDI.ResetMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_agent_amqp", function(e) {
        e.preventDefault();
        HIASHDI.ResetAMQP();
    });
});