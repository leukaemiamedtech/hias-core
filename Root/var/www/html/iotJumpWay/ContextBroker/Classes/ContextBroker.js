var ContextBroker = {
    Update: function() {
        $.post(window.location.href, $("#update_context_broker").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "Forms", "Location Update OK");
                    $('.modal-title').text('HDSI Context Broker');
                    $('.modal-body').text("Context Broker Update OK");
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Context Broker Update Failed: " + resp.Message
                    $('.modal-title').text('HDSI Context Broker');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", msg);
                    break;
            }
        });
    },
    CreateAgent: function() {
        $.post(window.location.href, $("#iot_agent_create").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    GeniSys.ResetForm("iot_agent_create");
                    $('.modal-title').text('HDSI IoT Agent');
                    $('.modal-body').html("HDSI IoT Agent ID #" + resp.AID + " created! Please save the credentials safely. The credentials are provided below and can be reset in the Context Broker IoT Agents area.<br /><br /><strong>Agent/Application ID:</strong> " + resp.AID + "<br /><br /><strong>MQTT User:</strong> " + resp.MU + "<br /><strong>MQTT Password:</strong> " + resp.MP + "<br /><br /><strong>Blockchain Address:</strong> " + resp.BU + " < br /> <strong>Blockchain Password:</strong> " + resp.BP + "<br /><br /><strong>App ID:</strong> " + resp.AppID + "<br /><strong>App Key:</strong> " + resp.AppKey + "<br /><br />" + resp.Message + "<br /><br />");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", "IoT Agent ID #" + resp.AID + " created!");
                    break;
                default:
                    msg = "IoT Agent Create Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('HDSI IoT Agent');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    UpdateAgent: function() {
        $.post(window.location.href, $("#iot_agent_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
                    Logging.logMessage("Core", "Forms", "Application Update OK");
                    $('.modal-title').text('HDSI IoT Agent');
                    $('.modal-body').html(resp.Message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "IoT Agent Update Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('HDSI IoT Agent');
                    $('.modal-body').html(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    ResetKey: function() {
        $.post(window.location.href, { "reset_agent_apriv": 1 },
            function(resp) {
                console.log(resp)
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        $('.modal-title').text('Reset App Key');
                        $('.modal-body').text("This agent's new key is: " + resp.P);
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
    ResetMqtt: function() {
        $.post(window.location.href, { "reset_agent_mqtt": 1 },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        ContextBroker.amqttpa = resp.P;
                        ContextBroker.amqttpae = resp.P.replace(/\S/gi, '*');
                        $("#amqttp").text(ContextBroker.amqttpae);
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
    },
    ResetAMQP: function() {
        $.post(window.location.href, { "reset_agent_amqp": 1 },
            function(resp) {
                console.log(resp)
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        ContextBroker.aamqppa = resp.P;
                        ContextBroker.aamqppae = resp.P.replace(/\S/gi, '*');
                        $("#appamqpp").text(ContextBroker.aamqppae)
                        Logging.logMessage("Core", "Forms", "Reset OK");
                        $('.modal-title').text('Reset App AMQP Key');
                        $('.modal-body').text("This agent's new AMQP key is: " + resp.P);
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
    HideAgentInputs: function() {
        $('#ip').attr('type', 'password');
        $('#mac').attr('type', 'password');

        ContextBroker.aamqpua = $("#appamqpu").text();
        ContextBroker.aamqpuae = $("#appamqpp").text().replace(/\S/gi, '*');
        ContextBroker.aamqppa = $("#appamqpp").text();
        ContextBroker.aamqppae = $("#appamqpp").text().replace(/\S/gi, '*');
        ContextBroker.amqttua = $("#amqttu").text();
        ContextBroker.amqttuae = $("#amqttu").text().replace(/\S/gi, '*');
        ContextBroker.amqttpa = $("#amqttp").text();
        ContextBroker.amqttpae = $("#amqttp").text().replace(/\S/gi, '*');
        ContextBroker.appida = $("#appid").text();
        ContextBroker.appidae = $("#appid").text().replace(/\S/gi, '*');
        ContextBroker.bcida = $("#bcid").text();
        ContextBroker.bcidae = $("#bcid").text().replace(/\S/gi, '*');

        $("#appamqpu").text(ContextBroker.aamqpuae);
        $("#appamqpp").text(ContextBroker.aamqppae);
        $("#amqttu").text(ContextBroker.amqttuae);
        $("#amqttp").text(ContextBroker.amqttpae);
        $("#appid").text(ContextBroker.appidae);
        $("#bcid").text(ContextBroker.bcidae);
    },
};
$(document).ready(function() {

    $('#update_context_broker').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            ContextBroker.Update();
        }
    });

    $('#iot_agent_create').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            ContextBroker.CreateAgent();
        }
    });

    $('#iot_agent_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            ContextBroker.UpdateAgent();
        }
    });

    $("#GeniSysAI").on("click", "#reset_agent_apriv", function(e) {
        e.preventDefault();
        ContextBroker.ResetKey();
    });

    $("#GeniSysAI").on("click", "#reset_agent_mqtt", function(e) {
        e.preventDefault();
        ContextBroker.ResetMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_agent_amqp", function(e) {
        e.preventDefault();
        ContextBroker.ResetAMQP();
    });

    $('#bcid').hover(function() {
        $("#bcid").text(ContextBroker.bcida);
    }, function() {
        $("#bcid").text(ContextBroker.bcidae);
    });

    $('#idappid').hover(function() {
        $("#idappid").text(ContextBroker.idappida);
    }, function() {
        $("#idappid").text(ContextBroker.idappidae);
    });

    $('#appid').hover(function() {
        $("#appid").text(ContextBroker.appida);
    }, function() {
        $("#appid").text(ContextBroker.appidae);
    });

    $('#idmqttu').hover(function() {
        $("#idmqttu").text(ContextBroker.mqttua);
    }, function() {
        $("#idmqttu").text(ContextBroker.mqttuae);
    });

    $('#idmqttp').hover(function() {
        $("#idmqttp").text(ContextBroker.mqttpa);
    }, function() {
        $("#idmqttp").text(ContextBroker.mqttpae);
    });

    $('#amqttu').hover(function() {
        $("#amqttu").text(ContextBroker.amqttua);
    }, function() {
        $("#amqttu").text(ContextBroker.amqttuae);
    });

    $('#amqttp').hover(function() {
        $("#amqttp").text(ContextBroker.amqttpa);
    }, function() {
        $("#amqttp").text(ContextBroker.amqttuae);
    });

    $('#appamqpu').hover(function() {
        $("#appamqpu").text(ContextBroker.aamqpua);
    }, function() {
        $("#appamqpu").text(ContextBroker.aamqpuae);
    });

    $('#appamqpp').hover(function() {
        $("#appamqpp").text(ContextBroker.aamqppa);
    }, function() {
        $("#appamqpp").text(ContextBroker.aamqppae);
    });

    $('#damqpu').hover(function() {
        $("#damqpu").text(ContextBroker.damqpua);
    }, function() {
        $("#damqpu").text(ContextBroker.damqpuae);
    });

    $('#damqpp').hover(function() {
        $("#damqpp").text(ContextBroker.damqppa);
    }, function() {
        $("#damqpp").text(ContextBroker.damqppae);
    });


});