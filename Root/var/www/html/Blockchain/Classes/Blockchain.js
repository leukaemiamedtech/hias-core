var Blockchain = {
    logData: function(data) {
        $("#dataLog").prepend("- " + data + "<br />");
    },
    accounts: [],
    connect: function(server) {
        if (typeof web3 !== 'undefined') {
            msg = 'Web3 Detected! ' + web3.currentProvider.constructor.name;
            Logging.logMessage("Core", "Blockchain", msg);
            Blockchain.logData(msg);
            window.web3 = new Web3(web3.currentProvider);
        } else {
            msg = "Connecting to HIAS Blockchain using HTTP Provider...";
            Logging.logMessage("Core", "Blockchain", msg);
            Blockchain.logData(msg);
            window.web3 = new Web3(new Web3.providers.HttpProvider(server));
        }
    },
    isConnected: function() {
        return web3.eth.net.isListening();
    },
    getAccounts: async function() {
        await web3.eth.getAccounts(function(error, accounts) {
            Blockchain.accounts = accounts;
        });
    },
    unlockAccount: async function(u, p, t) {
        addr = u.toString().toLowerCase().replace('0x', '');
        await web3.eth.personal.unlockAccount(web3.utils.toHex(addr), p, t)
            .then((response) => {
                msg = "Account unlocked!";
                Logging.logMessage("Core", "Blockchain", msg);
                Blockchain.logData(msg);
            }).catch((error) => {
                msg = "Account unlocking failed!";
                Logging.logMessage("Core", "Blockchain", msg);
                Blockchain.logData(msg);
            });
    },
    getBalance: function(address) {
        try {
            web3.eth.getBalance(address, function(error, wei) {
                if (!error) {
                    var balance = web3.utils.fromWei(wei, 'ether');
                    msg = "Your Balance: " + balance;
                    Logging.logMessage("Core", "Blockchain", msg);
                    Blockchain.logData(msg);
                }
            });
        } catch (err) {
            Logging.logMessage("Core", "Blockchain", "Get balance failed! ERR: " + err);
        }
    },
    deployContract: async function(acc, p, name, abi, bin) {
        var acc = web3.utils.toHex(acc);
        var abi = JSON.parse(abi);
        var bin = web3.utils.toHex('0x' + bin);
        var txid = "";
        await web3.eth.personal.unlockAccount(acc, p, 1000)
            .then((response) => {
                Blockchain.getBalance(acc);
                newContract = new web3.eth.Contract(abi);
                newContract.deploy({ data: bin })
                    .send({
                        from: acc,
                        gas: 8000000
                    })
                    .on('transactionHash', function(transactionHash) {
                        txid = transactionHash;
                    })
                    .then((newContractInstance) => {
                        newContract.options.address = newContractInstance.options.address
                        Blockchain.storeContract(newContract.options.address, abi, acc, name, txid);
                    });
            }).catch((error) => {});
    },
    storeContract: function(address, abi, acc, name, hash) {
        $.post(window.location.href, { "store_contract": 1, "id": address, "acc": acc, "abi": JSON.stringify(abi), "name": name, "txid": hash }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    msg = "Contract " + address + " created and stored!";
                    Logging.logMessage("Core", "Blockchain", msg);
                    Blockchain.logData(msg);
                    $("#usr").val("");
                    $("#p").val("");
                    $("#name").val("");
                    $("#abi").val("");
                    $("#bin").val("");
                    $('.modal-title').text('HIAS Blockchain');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Contract deployment failed: "
                    $('.modal-title').text('HIAS Blockchain');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Blockchain", msg);
                    Blockchain.logData(msg);
                    $('.modal-title').text('HIAS Blockchain');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    replenish: async function(acc, p, address, abi, amount) {
        var address = web3.utils.toHex(address);
        var acc = web3.utils.toHex(acc);
        var abi = JSON.parse(abi);
        await web3.eth.personal.unlockAccount(acc, p, 1000)
            .then((response) => {
                msg = "HIAS Blockchain account unlocked!";
                Logging.logMessage("Core", "Blockchain", msg);
                Blockchain.logData(msg);
                Blockchain.getBalance(acc);
                web3.eth.handleRevert = true;
                msg = "Replenishing HIAS Blockchain Contract Ether with 1000 Ether...";
                Logging.logMessage("Core", "Blockchain", msg);
                Blockchain.logData(msg);
                existingContract = new web3.eth.Contract(abi, address);
                existingContract.methods.deposit(web3.utils.toWei(amount, "ether")).send({
                        "to": address,
                        "from": acc,
                        "value": web3.utils.toWei(amount, "ether")
                    })
                    .then(receipt => {
                        msg = "HIAS Blockchain Contract Ether replenished successfully!";
                        Logging.logMessage("Core", "Blockchain", msg);
                        Blockchain.logData(msg);
                        msg2 = "Receipt: " + JSON.stringify(receipt);
                        Logging.logMessage("Core", "Blockchain", msg2);
                        Blockchain.logData(msg2);
                        Blockchain.getBalance(acc);
                        hash = receipt.transactionHash;
                        msg3 = "Txn: " + hash;
                        Blockchain.storeTransaction("Contract Replenishment", $("#id").val(), hash, msg, msg3);
                    }).catch((error) => {
                        msg = "HIAS Blockchain Contract Ether replenish failed!";
                        Logging.logMessage("Core", "Blockchain", msg);
                        Blockchain.logData(msg);
                        msg2 = "ERROR: " + JSON.stringify(error);
                        Logging.logMessage("Core", "Blockchain", msg2);
                        Blockchain.logData(msg2);
                        $('.modal-title').text('HIAS Blockchain');
                        $('.modal-body').html(msg + "<br /><br />" + msg2);
                        $('#responsive-modal').modal('show');
                    });
            }).catch((error) => {
                console.log(error);
            });

    },
    interact: async function(acc, p, func, address, abi, type, data) {
        var address = web3.utils.toHex(address);
        var acc = web3.utils.toHex(acc);
        var abi = JSON.parse(abi);
        await web3.eth.personal.unlockAccount(acc, p, 1000)
            .then((response) => {
                msg = "HIAS Blockchain account unlocked!";
                Logging.logMessage("Core", "Blockchain", msg);
                Blockchain.logData(msg);
                Blockchain.getBalance(acc);
                for (var i = 0; i < data.length; i++) {
                    if (typeof data[i] == "object" || typeof data[i] == "array")
                        data[i] = JSON.stringify(data[i]);
                }
                if (!data.length) {
                    data = "";
                }
                web3.eth.handleRevert = true;
                existingContract = new web3.eth.Contract(abi, address, 2);
                if (type == "Send") {
                    msg = "Sending data to HIAS Blockchain Contract function: " + func;
                    Logging.logMessage("Core", "Blockchain", msg);
                    Blockchain.logData(msg);
                    existingContract.methods[func](...data).send({ from: acc })
                        .then(receipt => {
                            msg = "Send to HIAS Blockchain Contract function successful!";
                            Logging.logMessage("Core", "Blockchain", msg);
                            Blockchain.logData(msg);
                            msg2 = "Receipt: " + JSON.stringify(receipt);
                            Logging.logMessage("Core", "Blockchain", msg2);
                            Blockchain.logData(msg2);
                            Blockchain.getBalance(acc);
                            hash = receipt.transactionHash;
                            msg3 = "Txn: " + hash;
                            Blockchain.storeTransaction("Send to " + func, $("#id").val(), hash, msg, msg3);
                        }).catch((error) => {
                            msg = "Contract function call failed!";
                            Logging.logMessage("Core", "Blockchain", msg);
                            Blockchain.logData(msg);
                            msg2 = "ERROR: " + JSON.stringify(error);
                            Logging.logMessage("Core", "Blockchain", msg2);
                            Blockchain.logData(msg2);
                            $('.modal-title').text('HIAS Blockchain');
                            $('.modal-body').html(msg + "<br /><br />" + msg2);
                            $('#responsive-modal').modal('show');
                        });
                } else {
                    msg = "Calling HIAS Blockchain Contract function: " + func;
                    Logging.logMessage("Core", "Blockchain", msg);
                    Blockchain.logData(msg);
                    existingContract.methods[func](...data).call({ from: acc })
                        .then(response => {
                            msg = "Call to HIAS Blockchain Contract function successful!";
                            Logging.logMessage("Core", "Blockchain", msg);
                            Blockchain.logData(msg);
                            msg2 = "Receipt: " + response;
                            Logging.logMessage("Core", "Blockchain", msg2);
                            Blockchain.logData(msg2);
                            Blockchain.getBalance(acc);
                            Blockchain.storeTransaction("Call to " + func, $("#id").val(), "", msg, "", response);
                        }).catch((error) => {
                            msg = "Contract function call failed!";
                            Logging.logMessage("Core", "Blockchain", msg);
                            Blockchain.logData(msg);
                            msg2 = "ERROR: " + JSON.stringify(error);
                            Logging.logMessage("Core", "Blockchain", msg2);
                            Blockchain.logData(msg2);
                            $('.modal-title').text('HIAS Blockchain');
                            $('.modal-body').html(msg + "<br /><br />" + msg2);
                            $('#responsive-modal').modal('show');
                        });
                }
            }).catch((error) => {
                console.log(error);
            });
    },
    updateConfig: function() {
        $.post(window.location.href, $("#blockchain_update").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    msg = "HIAS Blockchain settings updated!"
                    $('.modal-title').text('HIAS Blockchain');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Blockchain", msg);
                    break;
                default:
                    msg = "HIAS Blockchain settings update failed: "
                    $('.modal-title').text('HIAS Blockchain');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Blockchain", msg);
                    break;
            }
        });
    },
    storeTransaction: function(action, contract, hash, msg, msg2, response = "") {
        $.post(window.location.href, { "store_transaction": 1, "action": action, "hash": hash, "contract": contract }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    text = msg2 ? msg2 + "<br /><br />Response: " + response : "Response: " + response;
                    $('.modal-title').text('HIAS Blockchain');
                    $('.modal-body').html(msg + "<br /><br />" + text);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Contract deployment failed: "
                    $('.modal-title').text('HIAS Blockchain');
                    $('.modal-body').html("Transaction Storage Failed!");
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    transfer: function() {
        $.post(window.location.href, $("#send").serialize(), function(resp) {
            var resp = jQuery.parseJSON(resp);
            switch (resp.Response) {
                case "OK":
                    $('.modal-title').text('HIAS Blockchain');
                    $('.modal-body').html(resp.Message);
                    $('#responsive-modal').modal('show');
                    break;
                default:
                    msg = "Contract deployment failed: "
                    $('.modal-title').text('HIAS Blockchain');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    Logging.logMessage("Core", "Blockchain", msg);
                    Blockchain.logData(msg);
                    $('.modal-title').text('HIAS Blockchain');
                    $('.modal-body').text(msg);
                    $('#responsive-modal').modal('show');
                    break;
            }
        });
    },
    checkDataIntegrity: function(elem) {
        var abi = JSON.parse($("#abi").html());
        var acc = web3.utils.toHex(elem.data("user"));
        var address = $("#address").html();
        existingContract = new web3.eth.Contract(abi, address, 2);
        existingContract.methods.getHash(elem.data("key")).call({ from: acc })
            .then(response => {
                msg = "Call to HIAS Blockchain Contract function successful!";
                Logging.logMessage("Core", "Blockchain", msg);
                msg2 = "Receipt: " + response;
                Logging.logMessage("Core", "Blockchain", msg2);
                hashed = web3.utils.hexToAscii(response.dataHash)
                Blockchain.compareHash(elem.data("hash"), hashed);
            }).catch((error) => {
                msg = "Contract function call failed!";
                Logging.logMessage("Core", "Blockchain", msg);
                msg2 = "ERROR: " + JSON.stringify(error);
                Logging.logMessage("Core", "Blockchain", msg2);
                $('.modal-title').text('HIAS Blockchain');
                $('.modal-body').html(msg + "<br /><br />" + msg2);
                $('#responsive-modal').modal('show');
            });
    },
    compareHash: function(current, hash) {
        $.post(window.location.href, { "check_hash": 1, "current": current, "hash": hash }, function(resp) {
            var resp = jQuery.parseJSON(resp);
            checked = resp.Check === true ? "PASSED!" : "FAILED!"
            $('.modal-title').text('HIAS Blockchain');
            $('.modal-body').html("Data Integrtity Check: " + checked + "<br /><br />Data String: " + resp.String + "<br />Data Hash: " + resp.Hash);
            $('#responsive-modal').modal('show');
        });
    }
};
$(document).ready(function() {
    $("#GeniSysAI").on("click", "#contract_deploy", function(e) {
        e.preventDefault();
        Blockchain.deployContract($("#usr").val(), $("#p").val(), $("#name").val(), $("#abi").val(), $("#bin").val());
    });
    $("#GeniSysAI").on("click", "#interact", function(e) {
        e.preventDefault();
        Blockchain.interact($("#acc").val(), $("#p").val(), $("#func").val(), $("#contract").val(), $("#abi").val(), $("input[name='type']:checked").val(), $("#data").val() ? jQuery.parseJSON($("#data").val()) : {});
    });
    $("#GeniSysAI").on("click", "#update_blockchain", function(e) {
        e.preventDefault();
        Blockchain.updateConfig();
    });
    $("#GeniSysAI").on("click", "#replenish", function(e) {
        e.preventDefault();
        Blockchain.replenish($("#acc").val(), $("#p").val(), $("#contract").val(), $("#abi").val(), "1000");
    });
    $("#GeniSysAI").on("click", "#transfer_ether", function(e) {
        e.preventDefault();
        Blockchain.transfer();
    });
    $("#GeniSysAI").on("click", ".verify", function(e) {
        e.preventDefault();
        Blockchain.checkDataIntegrity($(this));
    });
});