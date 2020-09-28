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
        lid: 0,
        aid: 0,
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
        lwt.destinationName = iotJumpWay.mqttOptions.lid + "/Applications/" + iotJumpWay.mqttOptions.aid + "/Status";
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
        $("#status").prepend("<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>Connected to Local iotJumpWay Broker</strong></span><br />" + new Date($.now()) + "</p>");
        iotJumpWay.publishToApplicationStatus();
        iotJumpWay.subscribeToAll({
            lid: iotJumpWay.mqttOptions.lid
        });
    },
    failed: function(message) {
        this.connected = false;
        $("#status").prepend("<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>iotJumpWay connection failed:</strong></span><br />" + message.errorMessage + "<br />" + new Date($.now()) + "</p>");
    },
    lost: function(responseObject) {
        this.connected = false;
        if (responseObject.errorCode !== 0) {
            $("#status").prepend("<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>iotJumpWay connection lost:</strong></span><br />" + responseObject.errorMessage + "<br />" + new Date($.now()) + "</p>");
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
    },
    subscribeToAll: function() {
        this.client.subscribe(iotJumpWay.mqttOptions.lid + "/Devices/#", { qos: 0 });
        this.client.subscribe(iotJumpWay.mqttOptions.lid + "/Applications/#", { qos: 0 });
        $("#status").prepend("<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>Subscribed to:</strong></span>" + iotJumpWay.mqttOptions.lid + "/Devices/#<br />" + new Date($.now()) + "</p>");
    },
    publishToApplicationStatus: function() {
        message = new Paho.MQTT.Message("ONLINE");
        message.destinationName = iotJumpWay.mqttOptions.lid + "/Applications/" + iotJumpWay.mqttOptions.aid + "/Status";
        this.client.send(message);
        $("#status").prepend("<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>Published to:</strong></span> " + iotJumpWay.mqttOptions.lid + "/Applications/" + iotJumpWay.mqttOptions.aid + "/Status<br />" + new Date($.now()) + "</p>");
        Logging.logMessage("Core", "iotJumpWay", "Published to: " + iotJumpWay.mqttOptions.lid + "/Applications/" + iotJumpWay.mqttOptions.aid + "/Status");
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
};
$(document).ready(function() {
    iotJumpWay.raise();
    setInterval(function() {
        iotJumpWay.getStats();
    }, 5000);
});