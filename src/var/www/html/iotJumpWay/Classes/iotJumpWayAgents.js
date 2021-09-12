var iotJumpwayAgents = {
    Debug: false,
    GraphType: "Life",
    LifeInterval: null,
    SensorsInterval: null,
    CreateAgent: function() {
        $.post(window.location.href, $("#iot_agent_create").serialize(), function(resp) {
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    HIAS.ResetForm("iot_agent_create");
                    $('.modal-title').text('iotJumpWay Agent');
                    $('.modal-body').html("Agent ID #" + resp.AppID + " created! Please save the credentials safely. The credentials are provided below and can be reset in the iotJumpWay Agent area.<br /><br /><strong>Agent ID:</strong> " + resp.AID + "<br /><br /><strong>MQTT User:</strong> " + resp.MU + "<br /><strong>MQTT Password:</strong> " + resp.MP + "<br /><br /><strong>Blockchain Address:</strong> " + resp.BU + " < br /> <strong>Blockchain Password:</strong> " + resp.BP + "<br /><br /><strong>App ID:</strong> " + resp.AppID + "<br /><strong>App Key:</strong> " + resp.AppKey + "<br /><br />" + resp.Message + "<br /><br />");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", "Agent ID #" + resp.AID + " created!");
                    break;
                default:
                    msg = "iotJumpWay IoT Agent Update Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('iotJumpWay IoT Agent');
                    $('.modal-body').html(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    UpdateAgent: function() {
        $.post(window.location.href, $("#iot_agent_update").serialize(), function(resp) {
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
                    Logging.logMessage("Core", "Forms", "Agent Update OK");
                    $('.modal-title').text('iotJumpWay IoT Agent');
                    $('.modal-body').html(resp.Message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "iotJumpWay IoT Agent Update Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('iotJumpWay IoT Agent');
                    $('.modal-body').html(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    updateAgentLifeGraph: function() {
        $.post(window.location.href, { "update_agent_life_graph": 1, "agentGraphs": $("#agentGraphs").val() }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            if (resp[0].length > 0) {
                eChart_1.setOption({
                    xAxis: {
                        type: 'category',
                        axisLabel: {
                            textStyle: {
                                color: '#ffffff'
                            },
                            interval: 1,
                            rotate: 45
                        },
                        data: resp[0]
                    },
                    series: resp[1]
                })
            } else {
                eChart_1.clear()
            }
        });
    },
    updateAgentSensorsGraph: function() {
        $.post(window.location.href, { "update_agent_sensors_graph": 1, "currentSensor": $("#currentSensor").val() }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            if (resp[0].length > 0) {
                eChart_1.setOption({
                    xAxis: {
                        type: 'category',
                        axisLabel: {
                            textStyle: {
                                color: '#ffffff'
                            },
                            interval: 1,
                            rotate: 45
                        },
                        data: resp[0]
                    },
                    series: resp[1]
                })
            } else {
                eChart_1.clear()
            }
        });
    },
    changeAgentGraph: function(changeTo) {
        var eChart_1 = echarts.init(document.getElementById('e_chart_1'));
        eChart_1.clear()
        if (changeTo === "Life") {
            clearInterval(iotJumpwayAgents.SensorsInterval);
            $.post(window.location.href, { "update_agent_life_graph": 1, "agentGraphs": $("#agentGraphs").val() }, function(resp) {
                var resp = jQuery.parseJSON(resp);
                if (resp[0].length > 0) {

                    var eChart_1 = echarts.init(document.getElementById('e_chart_1'));

                    var option = {
                        tooltip: {
                            trigger: 'axis',
                            backgroundColor: 'rgba(33,33,33,1)',
                            borderRadius: 0,
                            padding: 10,
                            axisPointer: {
                                type: 'cross',
                                label: {
                                    backgroundColor: 'rgba(33,33,33,1)'
                                }
                            },
                            textStyle: {
                                color: '#fff',
                                fontStyle: 'normal',
                                fontWeight: 'normal',
                                fontFamily: "'Montserrat', sans-serif",
                                fontSize: 12
                            }
                        },
                        xAxis: {
                            type: 'category',
                            name: 'Time',
                            nameLocation: 'middle',
                            nameGap: 50,
                            axisLabel: {
                                textStyle: {
                                    color: '#fff',
                                    fontStyle: 'normal',
                                    fontWeight: 'normal',
                                    fontFamily: "'Montserrat', sans-serif",
                                    fontSize: 12
                                },
                                interval: 1,
                                rotate: 45
                            },
                            data: resp[0]
                        },
                        yAxis: {
                            axisLabel: {
                                textStyle: {
                                    color: '#fff',
                                    fontStyle: 'normal',
                                    fontWeight: 'normal',
                                    fontFamily: "'Montserrat', sans-serif",
                                    fontSize: 12
                                }
                            },
                            type: 'value',
                            name: 'Y-Axis',
                            nameLocation: 'center',
                            nameGap: 50
                        },
                        grid: {
                            top: 10,
                            left: 0,
                            right: 0,
                            bottom: 100,
                            containLabel: true
                        },
                        series: resp[2]
                    };
                    eChart_1.setOption(option);
                    eChart_1.resize();
                }
            });

            iotJumpwayAgents.LifeInterval = setInterval(function() {
                iotJumpwayAgents.updateAgentLifeGraph();
            }, 1000);
        } else if (changeTo === "Sensors") {
            clearInterval(iotJumpwayAgents.LifeInterval);
            $.post(window.location.href, { "update_agent_sensors_graph": 1, "agentGraphs": $("#agentGraphs").val() }, function(resp) {
                if (iotJumpwayAgents.Debug === true) {
                    console.log(resp);
                }
                var resp = jQuery.parseJSON(resp);
                if (resp[0].length > 0) {

                    var option = {
                        tooltip: {
                            trigger: 'axis',
                            backgroundColor: 'rgba(33,33,33,1)',
                            borderRadius: 0,
                            padding: 10,
                            axisPointer: {
                                type: 'cross',
                                label: {
                                    backgroundColor: 'rgba(33,33,33,1)'
                                }
                            },
                            textStyle: {
                                color: '#fff',
                                fontStyle: 'normal',
                                fontWeight: 'normal',
                                fontFamily: "'Montserrat', sans-serif",
                                fontSize: 12
                            }
                        },
                        xAxis: {
                            type: 'category',
                            name: 'Time',
                            nameLocation: 'middle',
                            nameGap: 50,
                            axisLabel: {
                                textStyle: {
                                    color: '#fff',
                                    fontStyle: 'normal',
                                    fontWeight: 'normal',
                                    fontFamily: "'Montserrat', sans-serif",
                                    fontSize: 12
                                },
                                interval: 1,
                                rotate: 45
                            },
                            data: resp[0]
                        },
                        yAxis: {
                            axisLabel: {
                                textStyle: {
                                    color: '#fff',
                                    fontStyle: 'normal',
                                    fontWeight: 'normal',
                                    fontFamily: "'Montserrat', sans-serif",
                                    fontSize: 12
                                }
                            },
                            type: 'value',
                            name: 'Y-Axis',
                            nameLocation: 'center',
                            nameGap: 50
                        },
                        grid: {
                            top: 10,
                            left: 0,
                            right: 0,
                            bottom: 100,
                            containLabel: true
                        },
                        series: resp[2]
                    };
                    eChart_1.setOption(option);
                    eChart_1.resize();
                }
            });

            iotJumpwayAgents.SensorsInterval = setInterval(function() {
                iotJumpwayAgents.updateAgentSensorsGraph();
            }, 1000);

        }
    },
    changeAgentHistory: function(changeTo) {
        $.post(window.location.href, { "update_agent_history": 1, "agentHistory": $("#agentHistory").val(), "AgentAddress": $("#bcid").data("hidden") }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $("#agentHistoryContainer").html(resp.Data);
                    break;
                default:
                    $("#agentHistoryContainer").html("");
                    break;
            }
        });
    },
    ResetAgentKey: function() {
        $.post(window.location.href, { "reset_agent_key": 1 }, function(resp) {
            if (iotJumpwayAgents.Debug === true) {
                console.log(resp);
            }
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    Logging.logMessage("Core", "Forms", "Reset OK");
                    $('.modal-title').text('Reset Agent Key');
                    $('.modal-body').html("This agent's new key is: <b>" + resp.P + "</b>.<br /><br />Please save the key as you will not be able to recover it. If you forget your key you will need to reset it.<br /><br /><b>PLEASE NOTE:</b> You need to update your key in any software that uses it.");
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Reset failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('Reset Agent Key');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    ResetAgentAMQP: function() {
        $.post(window.location.href, { "reset_agent_amqp_key": 1 },
            function(resp) {
                if (iotJumpwayAgents.Debug === true) {
                    console.log(resp);
                }
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":

                        $("#agentamqpp").data("hidden", resp.P);
                        $("#agentamqpp").text(resp.P);
                        $("#agentamqpp").text($("#agentamqpp").text().replace(/\S/gi, '*'));
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
    }
};
$(document).ready(function() {

    $('#iot_agent_create').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            iotJumpwayAgents.CreateAgent();
        }
    });

    $('#iot_agent_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            iotJumpwayAgents.UpdateAgent();
        }
    });

    $("#GeniSysAI").on("click", "#reset_agent_key", function(e) {
        e.preventDefault();
        iotJumpwayAgents.ResetAgentKey();
    });

    $("#GeniSysAI").on("click", "#reset_agent_amqp", function(e) {
        e.preventDefault();
        iotJumpwayAgents.ResetAgentAMQP();
    });

    $('#agentGraphs').on('change', function(e) {
        e.preventDefault();
        iotJumpwayAgents.changeAgentGraph($(this).val());
    });

    $('#agentHistory').on('change', function(e) {
        e.preventDefault();
        iotJumpwayAgents.changeAgentHistory($(this).val());
    });

});