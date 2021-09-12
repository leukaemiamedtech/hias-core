var AiAgents = {
    Debug: false,
    GraphType: "Life",
    LifeInterval: null,
    SensorsInterval: null,
    StartAgentLife: function() {
        setInterval(function() {
            AiAgents.GetLife();
        }, 20000);
    },
    GetLife: function() {
        $.post(window.location.href, { "get_ai_agent_life": 1 }, function(resp) {
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
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
                    Logging.logMessage("Core", "Stats", "Agent Stats Updated OK");
                    break;
                default:
                    msg = "Agent Stats Update Failed: " + resp.Message
                    Logging.logMessage("Core", "Stats", msg);
                    break;
            }
        });
    },
    CreateAgent: function() {
        $.post(window.location.href, $("#ai_agent_create").serialize(), function(resp) {
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    HIAS.ResetForm("ai_agent_create");
                    $('.modal-title').text('HIAS AI Agent');
                    $('.modal-body').html("AI Agent ID #" + resp.AID + " created! Please save the credentials safely. The credentials are provided below and can be reset in the iotJumpWay Agent area.<br /><br /><strong>Agent ID:</strong> " + resp.AID + "<br /><br /><strong>MQTT User:</strong> " + resp.MU + "<br /><strong>MQTT Password:</strong> " + resp.MP + "<br /><br /><strong>Blockchain Address:</strong> " + resp.BU + " < br /> <strong>Blockchain Password:</strong> " + resp.BP + "<br /><br /><strong>App ID:</strong> " + resp.AppID + "<br /><strong>App Key:</strong> " + resp.AppKey + "<br /><br />" + resp.Message + "<br /><br />");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", "Agent ID #" + resp.AID + " created!");
                    break;
                default:
                    msg = "AI Agent Update Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('HIAS AI Agent');
                    $('.modal-body').html(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    UpdateAgent: function() {
        $.post(window.location.href, $("#ai_agent_update").serialize(), function(resp) {
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
    changeAgentGraph: function(changeTo) {
        var eChart_1 = echarts.init(document.getElementById('e_chart_1'));
        eChart_1.clear()
        if (changeTo === "Life") {
            clearInterval(AiAgents.SensorsInterval);
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

            AiAgents.LifeInterval = setInterval(function() {
                AiAgents.updateAgentLifeGraph();
            }, 1000);
        } else if (changeTo === "Sensors") {
            clearInterval(AiAgents.LifeInterval);
            $.post(window.location.href, { "update_agent_sensors_graph": 1, "agentGraphs": $("#agentGraphs").val() }, function(resp) {
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

            AiAgents.SensorsInterval = setInterval(function() {
                AiAgents.updateAgentSensorsGraph();
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
    deleteData: function() {
        $.post(window.location.href, { "deleteData": 1 }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('#dataBlock').empty();
                    $('#dataBlock').html("<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><p>Please upload your test dataset.</p></div>");
                    break;
                default:
                    break;
            }
        });
    },
    prepareUploadForm: function() {

        var upper = document.querySelector('#dataup'),
            form = new FormData(),
            xhr = new XMLHttpRequest();

        form.append('uploadAllData', 1);

        upper.addEventListener('change', function(event) {
            event.preventDefault();

            var files = this.files;
            for (var i = 0, n = files.length; i < n; i++) {
                var file = files[i];

                form.append('alldata[]', file, file.name);

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var resp = jQuery.parseJSON(xhr.response);
                        if (resp.Response === "OK") {
                            $('#dataBlock').empty();
                            $('#dataBlock').html(resp.Data);
                            AiAgents.setOpacity();
                            Logging.logMessage("Core", "Forms", resp.Message);
                            $('.modal-title').text('Data Upload OK');
                            $('.modal-body').text(resp.Message);
                            $('#responsive-modal').modal('show');
                        } else {
                            Logging.logMessage("Core", "Forms", resp.Message);
                            $('.modal-title').text('Data Upload Failed');
                            $('.modal-body').text(resp.Message);
                            $('#responsive-modal').modal('show');
                        }
                    }
                }

                xhr.open('POST', '');
                xhr.send(form);
            }
        });
    },
    setOpacity: function() {
        $('.classify').css("opacity", "1.0");
        $('.classify').hover(function() {
                $(this).stop().animate({ opacity: 0.2 }, "fast");
            },
            function() {
                $(this).stop().animate({ opacity: 1.0 }, "fast");
            });
    },
    classify: function(im) {

        var classification = '';
        $('#imageView').html("<img src='../../../" + im + "' style='width: 100%;' />");
        $("#imName").text(im);
        $("#imClass").html("<strong>Diagnosis:</strong> WAITING FOR RESPONSE");
        $("#imResult").html("<strong>Result:</strong> WAITING FOR RESPONSE");

        $.post(window.location.href, { "classifyData": 1, "im": im }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    if (im.includes("_0") && resp.Diagnosis == "Negative") {
                        classification = "True Negative";
                    } else if (im.includes("_0") && resp.Diagnosis == "Positive") {
                        classification = "False Positive";
                    } else if (im.includes("_1") && resp.Diagnosis == "Positive") {
                        classification = "True Positive";
                    } else if (im.includes("_1") && resp.Diagnosis == "Negative") {
                        classification = "False Negative";
                    }
                    if (im.includes("Non-Covid") && resp.Diagnosis == "Negative") {
                        classification = "True Negative";
                    } else if (im.includes("Non-Covid") && resp.Diagnosis == "Positive") {
                        classification = "False Positive";
                    } else if (im.includes("Covid") && resp.Diagnosis == "Positive") {
                        classification = "True Positive";
                    } else if (im.includes("Covid") && resp.Diagnosis == "Negative") {
                        classification = "False Negative";
                    }
                    $("#imClass").html("<strong>Diagnosis:</strong> " + resp.Diagnosis);
                    if (resp.Confidence) {
                        $("#imConf").html("<strong>Confidence:</strong> " + resp.Confidence);
                    } else {
                        $("#imConf").hide();
                    }
                    $("#imResult").html("<strong>Result:</strong> " + classification);
                    break;
                default:
                    break;
            }
        });

    },
    Chat: function() {
        $("#chatWindow").prepend("<div style='width: 100%;'><span style='color: #fff;'><strong>YOU:</strong></span> " + $("#GeniSysAiChat").val() + "</div>");
        $.post(window.location.href, $("#genisysai_chat").serialize(),
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                switch (resp.Response) {
                    case "OK":
                        $("#genisysai_chat")[0].reset();
                        $("#chatWindow").prepend("<div style='width: 100%;'><span style='color: #fff;'><strong>GeniSysAI:</strong></span> " + resp.Message + "</div>");
                        break;
                    default:
                        break;
                }
            });
    },
};
$(document).ready(function() {

    $('#ai_agent_create').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            AiAgents.CreateAgent();
        }
    });

    $('#ai_agent_update').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            AiAgents.UpdateAgent();
        }
    });

    $('#agentGraphs').on('change', function(e) {
        e.preventDefault();
        AiAgents.changeAgentGraph($(this).val());
    });

    $('#agentHistory').on('change', function(e) {
        e.preventDefault();
        AiAgents.changeAgentHistory($(this).val());
    });

    $("#GeniSysAI").on("click", "#uploadData", function(e) {
        e.preventDefault();
        $('#dataup').trigger('click');
    });

    $("#GeniSysAI").on("click", "#deleteData", function(e) {
        e.preventDefault();
        AiAgents.deleteData();
    });

    $("#GeniSysAI").on("click", ".classify", function(e) {
        e.preventDefault();
        AiAgents.classify($(this).attr("id"));
    });

    $("#GeniSysAI").on("click", "#send_chat", function(e) {
        e.preventDefault();
        AiAgents.Chat();
    });

    $("#GeniSysAiChat").on("keydown", function(event) {
        if (event.which == 13) {
            if ($("#GeniSysAiChat").val() !== "") {
                AiAgents.Chat();
            }
        }
    });


});