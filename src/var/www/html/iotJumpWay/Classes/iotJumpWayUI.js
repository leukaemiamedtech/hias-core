var iotJumpwayUI = {
    Debug: false,
    GraphType: "Life",
    LifeInterval: null,
    SensorsInterval: null,
    AppLifeInterval: null,
    AppSensorsInterval: null,
    HideSecret: function() {
        $.each($('.hiderstr'), function() {
            $(this).data("hidden", $(this).text());
            $(this).text($(this).text().replace(/\S/gi, '*'));
        });
    },
    updateHomeSensors: function() {
        $.post(window.location.href, { "get_environment_sensors": 1, "currentSensor": $("#currentSensor").val() }, function(resp) {
            var resp = jQuery.parseJSON(resp);
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
                series: [{
                    name: 'Temperature',
                    data: resp[1],
                    type: 'line',
                    smooth: true,
                    color: ['red', '#0f0', 'rgb(0, 0, 255)'],
                }, {
                    name: 'Humidity',
                    data: resp[2],
                    type: 'line',
                    smooth: true
                }, {
                    name: 'Light',
                    data: resp[3],
                    type: 'line',
                    smooth: true,
                    color: ['cyan'],
                }, {
                    name: 'Smoke',
                    data: resp[4],
                    type: 'line',
                    smooth: true,
                    color: ['gray'],
                }]
            })
        });
    },
    Update: function() {
        $.post(window.location.href, $("#location_update").serialize(), function(resp) {
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
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
                    $('.modal-title').text('Location Update');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    CreateZone: function() {
        $.post(window.location.href, $("#zone_create").serialize(), function(resp) {
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    HIAS.ResetForm("zone_create");
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
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
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
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    var fjson = JSON.stringify(resp.Schema, null, '\t');
                    window.parent.$('#schema').html(fjson);
                    HIAS.ResetForm("device_create");
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
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
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
        $.post(window.location.href, { "reset_key_dvc": 1 }, function(resp) {
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
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
        $.post(window.location.href, { "reset_mqtt_dvc": 1 }, function(resp) {
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
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
        $.post(window.location.href, { "reset_dvc_amqp": 1 }, function(resp) {
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
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
    updateDeviceLifeGraph: function() {
        $.post(window.location.href, { "update_device_life_graph": 1, "deviceGraphs": $("#deviceGraphs").val() }, function(resp) {
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
    updateDeviceSensorsGraph: function() {
        $.post(window.location.href, { "update_device_sensors_graph": 1, "currentSensor": $("#currentSensor").val() }, function(resp) {
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
    changeDeviceGraph: function(changeTo) {
        var eChart_1 = echarts.init(document.getElementById('e_chart_1'));
        eChart_1.clear()
        if (changeTo === "Life") {
            clearInterval(iotJumpwayUI.SensorsInterval);
            $.post(window.location.href, { "update_device_life_graph": 1, "deviceGraphs": $("#deviceGraphs").val() }, function(resp) {
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

            iotJumpwayUI.LifeInterval = setInterval(function() {
                iotJumpwayUI.updateDeviceLifeGraph();
            }, 1000);
        } else if (changeTo === "Sensors") {
            clearInterval(iotJumpwayUI.LifeInterval);
            $.post(window.location.href, { "update_device_sensors_graph": 1, "deviceGraphs": $("#deviceGraphs").val() }, function(resp) {
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

            iotJumpwayUI.SensorsInterval = setInterval(function() {
                iotJumpwayUI.updateDeviceSensorsGraph();
            }, 1000);

        }
    },
    changeDeviceHistory: function(changeTo) {
        $.post(window.location.href, { "update_device_history": 1, "deviceHistory": $("#deviceHistory").val(), "DeviceAddress": $("#bcid").data("hidden") }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $("#deviceHistoryContainer").html(resp.Data);
                    break;
                default:
                    $("#deviceHistoryContainer").html("");
                    break;
            }
        });
    },
    CreateApplication: function() {
        $.post(window.location.href, $("#application_create").serialize(), function(resp) {
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    HIAS.ResetForm("application_create");
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
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
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
    updateApplicationLifeGraph: function() {
        $.post(window.location.href, { "update_application_life_graph": 1, "applicationGraphs": $("#applicationGraphs").val() }, function(resp) {
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
    updateApplicationSensorsGraph: function() {
        $.post(window.location.href, { "update_application_sensors_graph": 1, "currentSensor": $("#currentSensor").val() }, function(resp) {
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
    changeApplicationGraph: function(changeTo) {
        var eChart_1 = echarts.init(document.getElementById('e_chart_1'));
        eChart_1.clear()
        if (changeTo === "Life") {
            clearInterval(iotJumpwayUI.AppSensorsInterval);
            $.post(window.location.href, { "update_application_life_graph": 1, "applicationGraphs": $("#applicationGraphs").val() }, function(resp) {
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

            iotJumpwayUI.AppLifeInterval = setInterval(function() {
                iotJumpwayUI.updateApplicationLifeGraph();
            }, 1000);
        } else if (changeTo === "Sensors") {
            clearInterval(iotJumpwayUI.LifeInterval);
            $.post(window.location.href, { "update_application_sensors_graph": 1, "applicationGraphs": $("#applicationGraphs").val() }, function(resp) {
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

            iotJumpwayUI.AppSensorsInterval = setInterval(function() {
                iotJumpwayUI.updateApplicationSensorsGraph();
            }, 1000);

        }
    },
    changeApplicationHistory: function(changeTo) {
        $.post(window.location.href, { "update_application_history": 1, "applicationHistory": $("#applicationHistory").val(), "ApplicationAddress": $("#bcid").data("hidden") }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $("#applicationHistoryContainer").html(resp.Data);
                    break;
                default:
                    $("#applicationHistoryContainer").html("");
                    break;
            }
        });
    },
    ResetAppKey: function() {
        $.post(window.location.href, { "reset_app_apriv": 1 },
            function(resp) {
                if (iotJumpwayUI.Debug === true) {
                    console.log(resp);
                }
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
    ResetAppAMQP: function() {
        $.post(window.location.href, { "reset_app_amqp": 1 },
            function(resp) {
                if (iotJumpwayUI.Debug === true) {
                    console.log(resp);
                }
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
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
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
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
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
    GetLife: function() {
        $.post(window.location.href, { "get_life": 1 }, function(resp) {
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
    GetEtype: function(etype) {
        $(".refresh").remove();
        $.post(window.location.href, { "get_e_type": true, "etype": etype }, function(resp) {
            if (iotJumpwayUI.Debug === true) {
                console.log(resp);
            }
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $("#rules").append(resp.ResponseData);
                    break;
                default:
                    break;
            }
        });
    },
    GetStype: function(etype, stype) {
        $(".srefresh").remove();
        $.post(window.location.href, { "get_s_type": true, "etype": etype, "stype": stype }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $("#rules").append(resp.ResponseData);
                    break;
                default:
                    break;
            }
        });
    },
    GetSRange: function(etype, stype) {
        $(".srrefresh").remove();
        $.post(window.location.href, { "get_sv_range": true, "etype": etype, "stype": stype }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $("#rules").append(resp.ResponseData);
                    break;
                default:
                    break;
            }
        });
    },
    GetODevice: function(dtype) {
        $(".orefresh").remove();
        $.post(window.location.href, { "get_out_device": true, "dtype": dtype }, function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $("#rules").append(resp.ResponseData);
                    break;
                default:
                    break;
            }
        });
    },
    GetODeviceACommands: function(dtype, atype) {
        $(".orefresh").remove();
        $.post(window.location.href, { "get_out_device_actuator": true, "dtype": dtype, "atype": atype }, function(resp) {
            console.log(resp)
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $("#rules").append(resp.ResponseData);
                    break;
                default:
                    break;
            }
        });
    },
};
$(document).ready(function() {

    iotJumpwayUI.HideLocationInputs();

    $('#currentSensor').on('change', function(e) {
        e.preventDefault();
        iotJumpwayUI.updateHomeSensors();
    });

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

    $('#deviceGraphs').on('change', function(e) {
        e.preventDefault();
        iotJumpwayUI.changeDeviceGraph($(this).val());
    });

    $('#deviceHistory').on('change', function(e) {
        e.preventDefault();
        iotJumpwayUI.changeDeviceHistory($(this).val());
    });

    $("#GeniSysAI").on("click", "#reset_dvc_mqtt", function(e) {
        e.preventDefault();
        iotJumpwayUI.ResetDvcMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_dvc_apriv", function(e) {
        e.preventDefault();
        iotJumpwayUI.ResetDvcKey();
    });

    $("#GeniSysAI").on("click", "#reset_dvc_amqp", function(e) {
        e.preventDefault();
        iotJumpwayUI.ResetDvcAMQP();
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

    $('#applicationGraphs').on('change', function(e) {
        e.preventDefault();
        iotJumpwayUI.changeApplicationGraph($(this).val());
    });

    $('#applicationHistory').on('change', function(e) {
        e.preventDefault();
        iotJumpwayUI.changeApplicationHistory($(this).val());
    });

    $("#GeniSysAI").on("click", "#reset_app_mqtt", function(e) {
        e.preventDefault();
        iotJumpwayUI.ResetAppMqtt();
    });

    $("#GeniSysAI").on("click", "#reset_app_apriv", function(e) {
        e.preventDefault();
        iotJumpwayUI.ResetAppKey();
    });

    $("#GeniSysAI").on("click", "#reset_app_amqp", function(e) {
        e.preventDefault();
        iotJumpwayUI.ResetAppAMQP();
    });

    $('.hider').hover(function() {
        $('#' + $(this).attr("id")).attr('type', 'text');
    }, function() {
        $('#' + $(this).attr("id")).attr('type', 'password');
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

    $("#GeniSysAI").on("change", "#e_type", function(e) {
        if ($(this).val() !== "") {
            iotJumpwayUI.GetEtype($(this).val());
        }
    });

    $("#GeniSysAI").on("change", "#s_type", function(e) {
        if ($(this).val() !== "") {
            iotJumpwayUI.GetStype($("#e_type").val(), $(this).val());
        }
    });

    $("#GeniSysAI").on("change", "#s_sensor", function(e) {
        if ($(this).val() !== "") {
            iotJumpwayUI.GetSRange($("#e_type").val(), $("#s_type").val(), $(this).val());
        }
    });

    $("#GeniSysAI").on("change", "#o_device", function(e) {
        if ($(this).val() !== "") {
            iotJumpwayUI.GetODevice($(this).val());
        }
    });

    $("#GeniSysAI").on("change", "#o_a_type", function(e) {
        if ($(this).val() !== "") {
            iotJumpwayUI.GetODeviceACommands($("#o_device").val(), $(this).val());
        }
    });

    $('.hiderstr').hover(function() {
        $(this).text($(this).data("hidden"));
        $(this).removeClass("hiderstr");
    }, function() {
        $(this).text($(this).text().replace(/\S/gi, '*'));
    });

});