var iotJumpWay = {
    client: null,
    connected: false,
    host: "",
    port: 9001,
    useTLS: true,
    cleansession: true,
    bc: {
        id: "",
        addr: ""
    },
    mqttOptions: {
        lid: "",
        aid: "",
        an: "",
        un: "",
        uc: ""
    },
    connect: function() {

        this.client = new Paho.MQTT.Client(
            this.host,
            this.port,
            iotJumpWay.mqttOptions.an
        );

        var lwt = new Paho.MQTT.Message("OFFLINE");
        lwt.destinationName = iotJumpWay.mqttOptions.lid + "/Staff/" + iotJumpWay.mqttOptions.aid + "/Status";
        lwt.qos = 0;
        lwt.retained = false;

        this.client.onConnectionLost = this.lost;
        this.client.onMessageArrived = this.arrived;

        this.client.connect({
            userName: iotJumpWay.mqttOptions.un,
            password: iotJumpWay.mqttOptions.uc,
            timeout: 10,
            useSSL: this.useTLS,
            cleanSession: this.cleansession,
            onSuccess: this.connection,
            onFailure: this.failed,
            willMessage: lwt
        });
    },
    connection: function() {
        this.connected = true;
        $("#status").prepend("<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>Connected to HIAS iotJumpWay MQTT Broker</strong></span><br />" + new Date($.now()) + "</p>");
        Logging.logMessage("Core", "iotJumpWay", "Connected to HIAS iotJumpWay MQTT Broker");
        iotJumpWay.publishToApplicationStatus();
        iotJumpWay.subscribeToAll({
            lid: iotJumpWay.mqttOptions.lid
        });
    },
    failed: function(message) {
        this.connected = false;
        $("#status").prepend("<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>iotJumpWay connection failed:</strong></span><br />" + message.errorMessage + "<br />" + new Date($.now()) + "</p>");
        Logging.logMessage("Core", "iotJumpWay", "iotJumpWay connection failed: " + message.errorMessage);
    },
    lost: function(responseObject) {
        this.connected = false;
        if (responseObject.errorCode !== 0) {
            $("#status").prepend("<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>iotJumpWay connection lost:</strong></span><br />" + responseObject.errorMessage + "<br />" + new Date($.now()) + "</p>");
            Logging.logMessage("Core", "iotJumpWay", "iotJumpWay connection lost: " + responseObject.errorMessage);
        }
    },
    arrived: function(message) {
        var messageObj = {
            topic: message.destinationName,
            retained: message.retained,
            qos: message.qos,
            payload: message.payloadString,
            timestamp: moment()
        };
        $("#status").prepend("<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>iotJumpWay communication on " + message.destinationName + ":</strong></span><br />" + message.payloadString + " with QoS: " + message.qos + "<br />" + new Date($.now()) + "</p>");
    },
    disconnect: function() {
        this.client.disconnect();
        $("#status").prepend("<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>Disconnected from iotJumpWay</strong></span><br />" + new Date($.now()) + "</p>");
        Logging.logMessage("Core", "iotJumpWay", "Disconnected from iotJumpWay");
    },
    subscribeToAll: function() {
        this.client.subscribe(iotJumpWay.mqttOptions.lid + "/#", { qos: 0 });
        $("#status").prepend("<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>Subscribed to HIAS iotJumpWay MQTT topics<br />" + new Date($.now()) + "</p>");
        Logging.logMessage("Core", "iotJumpWay", "Subscribed to HIAS iotJumpWay MQTT topics");
    },
    publishToApplicationStatus: function() {
        message = new Paho.MQTT.Message("ONLINE");
        message.destinationName = iotJumpWay.mqttOptions.lid + "/Staff/" + iotJumpWay.mqttOptions.aid + "/Status";
        this.client.send(message);
        $("#status").prepend("<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>Published to:</strong></span> " + iotJumpWay.mqttOptions.lid + "/Staff/" + iotJumpWay.mqttOptions.aid + "/Status<br />" + new Date($.now()) + "</p>");
        Logging.logMessage("Core", "iotJumpWay", "Published to: " + iotJumpWay.mqttOptions.lid + "/Staff/" + iotJumpWay.mqttOptions.aid + "/Status");
    },
    publishToApplicationCommands: function(data) {
        message = new Paho.MQTT.Message(JSON.stringify(data));
        message.destinationName = iotJumpWay.mqttOptions.lid + "/Staff/" + iotJumpWay.mqttOptions.aid + "/Commands";
        this.client.send(message);
        $("#status").prepend("<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>Published to:</strong></span> " + iotJumpWay.mqttOptions.lid + "/Staff/" + iotJumpWay.mqttOptions.aid + "/Commands<br />" + new Date($.now()) + "</p>");
        Logging.logMessage("Core", "iotJumpWay", "Published to: " + iotJumpWay.mqttOptions.lid + "/Staff/" + iotJumpWay.mqttOptions.aid + "/Commands");
    },
    publishToEmarCommands: function(params) {
        message = new Paho.MQTT.Message(JSON.stringify(params.message));
        message.destinationName = params.loc + "/Robotics/" + params.dvc + "/Commands";
        this.client.send(message);
        $("#status").prepend("<p class='iotJumpWayText'>" + new Date($.now()) + " | iotJumpWay | STATUS | Published to: " + params.loc + "/Robotics/" + params.dvc + "/Commands</p>");
    },
    publishToDeviceCommands: function(params) {
        message = new Paho.MQTT.Message(JSON.stringify(params.message));
        message.destinationName = params.loc + "/Devices/" + params.zne + "/" + params.dvc + "/Commands";
        this.client.send(message);
        $("#status").prepend("<p class='iotJumpWayText'>" + new Date($.now()) + " | iotJumpWay | STATUS | Published to: " + params.loc + "/Devices/" + params.zne + "/" + params.dvc + "/Command</p>");
    },
    getStats: function() {
        $.post(window.location.href, { "getServerStats": 1 }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            $(".up_cpu").text(resp.cpu)
            $(".up_mem").text(resp.mem)
            $(".up_hdd").text(resp.hdd)
            $(".up_tempr").text(resp.tempr)
            Logging.logMessage("Core", "Server", "Server Stats OK");
        });
    },
    raise: function() {
        $.post(window.location.href, { "raise": 1 }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            iotJumpWay.mqttOptions.lid = resp.lid;
            iotJumpWay.mqttOptions.aid = resp.aid;
            iotJumpWay.mqttOptions.an = resp.an;
            iotJumpWay.mqttOptions.un = resp.un;
            iotJumpWay.mqttOptions.uc = resp.uc;
            iotJumpWay.bc.id = resp.bcid;
            iotJumpWay.bc.addr = resp.bcaddr;
            iotJumpWay.connect();
        });
    },
    updateServerLifeGraph: function() {
        $.post(window.location.href, { "update_server_life_graph": 1, "currentSensor": $("#currentSensor").val() }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            eChart_1.setOption({
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
                series: resp[1]
            })
        });
    },
};
$(document).ready(function() {
    iotJumpWay.raise();
    setInterval(function() {
        iotJumpWay.getStats();
    }, 15000);

    $('#iotJumpWay_console').validator().on('submit', function(e) {
        e.preventDefault();
        iotJumpWay.publishToApplicationCommands({
            "Command": "Verify",
            "Use": "Device",
            "From": iotJumpWay.mqttOptions.aid,
            "Zone": $("#zid").val(),
            "To": $("#did").val(),
            "Name": $("#name").val(),
            "Type": $("#type").val(),
            "Value": $("#value").val(),
            "Message": $("#message").val()
        });
    });
});