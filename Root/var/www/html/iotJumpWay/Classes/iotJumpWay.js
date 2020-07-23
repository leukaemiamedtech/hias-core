var iotJumpWayWebSoc = {
    client: null,
    connected: false,
    host: "",
    port: 9001,
    useTLS: true,
    cleansession: true,
    mqttOptions: {
        locationID: 0,
        applicationID: 0,
        applicationName: "",
        userName: "",
        passwd: ""
    },
    connect: function() {
        var reconnectTimeout = 2000;
        this.thisLocationID = iotJumpWayWebSoc.mqttOptions.locationID;

        this.client = new Paho.MQTT.Client(
            this.host,
            this.port,
            iotJumpWayWebSoc.mqttOptions.applicationName
        );

        var lwt = new Paho.MQTT.Message("OFFLINE");
        lwt.destinationName =
            iotJumpWayWebSoc.mqttOptions.locationID +
            "/Applications/" +
            iotJumpWayWebSoc.mqttOptions.applicationID +
            "/Status";
        lwt.qos = 0;
        lwt.retained = false;

        this.client.onConnectionLost = this.onConnectionLost;
        this.client.onMessageArrived = this.onMessageArrived;

        this.client.connect({
            userName: iotJumpWayWebSoc.mqttOptions.userName,
            password: iotJumpWayWebSoc.mqttOptions.passwd,
            timeout: 10,
            useSSL: this.useTLS,
            cleanSession: this.cleansession,
            onSuccess: this.onConnect,
            onFailure: this.onFail,
            willMessage: lwt
        });
    },
    onConnect: function() {
        this.connected = true;
        $("#status").prepend(
            "<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>Connected to Local iotJumpWay Broker</strong></span><br />" +
            new Date($.now()) +
            "</p>"
        );
        iotJumpWayWebSoc.publishToApplicationStatus();
        iotJumpWayWebSoc.subscribeToAll({
            locationID: iotJumpWayWebSoc.mqttOptions.locationID
        });
    },
    onFail: function(message) {
        this.connected = false;
        $("#status").prepend(
            "<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>iotJumpWay connection failed:</strong></span><br />" +
            message.errorMessage +
            "<br />" +
            new Date($.now()) +
            "</p>"
        );
    },
    onConnectionLost: function(responseObject) {
        this.connected = false;
        if (responseObject.errorCode !== 0) {
            $("#status").prepend(
                "<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>iotJumpWay connection lost:</strong></span><br />" +
                responseObject.errorMessage +
                "<br />" +
                new Date($.now()) +
                "</p>"
            );
        }
    },
    onMessageArrived: function(message) {
        var messageObj = {
            topic: message.destinationName,
            retained: message.retained,
            qos: message.qos,
            payload: message.payloadString,
            timestamp: moment()
        };
        $("#status").prepend(
            "<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>iotJumpWay communication on " +
            message.destinationName +
            ":</strong></span><br />" +
            message.payloadString +
            " with QoS: " +
            message.qos +
            "<br />" +
            new Date($.now()) +
            "</p>"
        );
    },
    disconnect: function() {
        this.client.disconnect();
        $("#status").prepend(
            "<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>Disconnected from iotJumpWay</strong></span><br />" +
            new Date($.now()) +
            "</p>"
        );
    },
    subscribeToAll: function() {
        this.client.subscribe(
            iotJumpWayWebSoc.mqttOptions.locationID + "/Devices/#", { qos: 0 }
        );
        this.client.subscribe(
            iotJumpWayWebSoc.mqttOptions.locationID + "/Applications/#", { qos: 0 }
        );
        $("#status").prepend(
            "<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>Subscribed to:</strong></span>" +
            iotJumpWayWebSoc.mqttOptions.locationID +
            "/Devices/#<br />" +
            new Date($.now()) +
            "</p>"
        );
    },
    publishToApplicationStatus: function() {
        message = new Paho.MQTT.Message("ONLINE");
        message.destinationName =
            iotJumpWayWebSoc.mqttOptions.locationID +
            "/Applications/" +
            iotJumpWayWebSoc.mqttOptions.applicationID +
            "/Status";
        this.client.send(message);
        $("#status").prepend(
            "<p class='iotJumpWayText'><span class='iotJumpWayTextTitle'><strong>Published to:</strong></span> " +
            iotJumpWayWebSoc.mqttOptions.locationID +
            "/Applications/" +
            iotJumpWayWebSoc.mqttOptions.applicationID +
            "/Status<br />" +
            new Date($.now()) +
            "</p>"
        );
        Logging.logMessage("Core", "iotJumpWay", "Published to: " + iotJumpWayWebSoc.mqttOptions.locationID + "/Applications/" + iotJumpWayWebSoc.mqttOptions.applicationID + "/Status");
    },
    publishToDeviceCommands: function(params) {
        message = new Paho.MQTT.Message(JSON.stringify(params.message));
        message.destinationName = params.loc + "/Devices/" + params.zne + "/" + params.dvc + "/Commands";
        this.client.send(message);
        $("#status").prepend(
            "<p class='iotJumpWayText'>" + new Date($.now()) + " | iotJumpWay | STATUS | Published to: " + params.loc + "/Devices/" + params.zne + "/" + params.dvc + "/Command</p>"
        );
    },
    getStats: function() {
        $.post(window.location.href, { "getServerStats": 1 },
            function(resp) {
                var resp = jQuery.parseJSON(resp);
                $("#up_cpu").text(resp.cpu)
                $("#up_mem").text(resp.mem)
                $("#up_hdd").text(resp.hdd)
                $("#up_tempr").text(resp.tempr)
                Logging.logMessage("Core", "Server", "Server Stats OK");
            });
    }
};
$(document).ready(function() {
    iotJumpWayWebSoc.connect();
    setInterval(function() {
        iotJumpWayWebSoc.getStats();
    }, 5000);
});