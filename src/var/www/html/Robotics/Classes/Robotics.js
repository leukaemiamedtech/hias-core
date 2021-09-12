var Robotics = {
    location: $("#lentity").val(),
    zone: $("#zentity").val(),
    device: $("#dentity").val(),
    controller: $("#uidentifier").val(),
    Debug: false,
    GraphType: "Life",
    LifeInterval: null,
    SensorsInterval: null,
    CreateRobotics: function() {
        $.post(window.location.href, $("#robotics_create_form").serialize(), function(resp) {
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    HIAS.ResetForm("robotics_create_form");
                    $('.modal-title').text('HIAS Robotics');
                    $('.modal-body').html("Robtics Unit ID #" + resp.AID + " created! Please save the credentials safely. The credentials are provided below and can be reset in the iotJumpWay Robotics area.<br /><br /><strong>Robotics ID:</strong> " + resp.DID + "<br /><br /><strong>MQTT User:</strong> " + resp.MU + "<br /><strong>MQTT Password:</strong> " + resp.MP + "<br /><br /><strong>Blockchain Address:</strong> " + resp.BU + " < br /> <strong>Blockchain Password:</strong> " + resp.BP + "<br /><br /><strong>App ID:</strong> " + resp.AppID + "<br /><strong>App Key:</strong> " + resp.AppKey + "<br /><br />" + resp.Message + "<br /><br />");
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Forms", "Robotics ID #" + resp.DID + " created!");
                    break;
                default:
                    msg = "Robtics Unit Create Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('HIAS Robtics Unit');
                    $('.modal-body').html(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    UpdateRobotics: function() {
        $.post(window.location.href, $("#robotics_update_form").serialize(), function(resp) {
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
                    Logging.logMessage("Core", "Forms", "Robotics Update OK");
                    $('.modal-title').text('Robotics');
                    $('.modal-body').html(resp.Message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Robotics Update Failed: " + resp.Message
                    Logging.logMessage("Core", "Forms", msg);
                    $('.modal-title').text('Robotics');
                    $('.modal-body').html(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    updateRoboticsSensorsGraph: function() {
        $.post(window.location.href, { "update_robotics_sensors_graph": 1, "currentSensor": $("#currentSensor").val() }, function(resp) {
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
    updateRoboticsLifeGraph: function() {
        $.post(window.location.href, { "update_robotics_life_graph": 1, "roboticsGraphs": $("#roboticsGraphs").val() }, function(resp) {
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
    changeRoboticsGraph: function(changeTo) {
        var eChart_1 = echarts.init(document.getElementById('e_chart_1'));
        eChart_1.clear()
        if (changeTo === "Life") {
            clearInterval(Robotics.SensorsInterval);
            $.post(window.location.href, { "update_robotics_life_graph": 1, "roboticsGraphs": $("#roboticsGraphs").val() }, function(resp) {
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

            Robotics.LifeInterval = setInterval(function() {
                Robotics.updateRoboticsLifeGraph();
            }, 1000);
        } else if (changeTo === "Sensors") {
            clearInterval(Robotics.LifeInterval);
            $.post(window.location.href, { "update_robotics_sensors_graph": 1, "roboticsGraphs": $("#roboticsGraphs").val() }, function(resp) {
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

            Robotics.SensorsInterval = setInterval(function() {
                Robotics.updateRoboticsSensorsGraph();
            }, 1000);

        }
    },
    changeRoboticsHistory: function(changeTo) {
        $.post(window.location.href, { "update_robotics_history": 1, "roboticsHistory": $("#roboticsHistory").val(), "RobotAddress": $("#bcid").data("hidden") }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $("#roboticsHistoryContainer").html(resp.Data);
                    break;
                default:
                    $("#roboticsHistoryContainer").html("");
                    break;
            }
        });
    },
    ResetEmarKey: function() {
        $.post(window.location.href, { "reset_emar_key": 1 },
            function(resp) {
                if (Robotics.Debug === true) {
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
    ResetEmarMQTT: function() {
        $.post(window.location.href, { "reset_emar_mqtt": 1 }, function(resp) {
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
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
    wheels: function(direction) {
        iotJumpWay.publishToEmarCommands({
            "loc": Robotics.location,
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
        iotJumpWay.publishToEmarCommands({
            "loc": Robotics.location,
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
        iotJumpWay.publishToEmarCommands({
            "loc": Robotics.location,
            "dvc": Robotics.device,
            "message": {
                "From": Robotics.controller,
                "Type": "Head",
                "Value": direction,
                "Message": "Move " + direction
            }
        });
    },
    imgError: function(image) {
        $("#" + image).removeClass("hide");
        $("#" + image + "on").addClass("hide");
        return true;
    },
};
$(document).ready(function() {

    $('#robotics_create_form').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            Robotics.CreateRobotics();
        }
    });

    $('#robotics_update_form').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            Robotics.UpdateRobotics();
        }
    });

    $("#GeniSysAI").on("click", "#reset_emar_apriv", function(e) {
        e.preventDefault();
        Robotics.ResetEmarKey();
    });

    $("#GeniSysAI").on("click", "#reset_emar_mqtt", function(e) {
        e.preventDefault();
        Robotics.ResetEmarMQTT();
    });

    $('#roboticsGraphs').on('change', function(e) {
        e.preventDefault();
        Robotics.changeRoboticsGraph($(this).val());
    });

    $('#roboticsHistory').on('change', function(e) {
        e.preventDefault();
        Robotics.changeRoboticsHistory($(this).val());
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


});