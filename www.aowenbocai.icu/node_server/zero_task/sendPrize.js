var network = require("./network.js");
var config = require("./config.js");

module.exports.createSendPrize = function() {
    this.times = 0;
    this.send_data = '';
    this.stop = 1;
    this.run = function () {
        this.stop = 0;
        network.get(config.url + 'lottery/open_award/setPrize?' + this.send_data, (res) => {
            console.log( '[' + this.times + ']' + this.send_data);
            var data = res.data;
            try {
                data = eval('(' + data + ')');
            } catch (error) {
                data = "";
            }
            if (data != "" && data.err == 2) {
                console.log( '[' + this.send_data + ']:' + res.data);
                if (this.times < 10) {
                    setTimeout(() => {
                        this.run();
                    }, 1000)
                    this.times++;
                }
            } else {
                this.stop = 1;
            }
        }, 1)
    }
    this.init = function (send_data) {
        this.times = 0;
        this.send_data = send_data;
        if (this.stop) {
            this.run();
        }
    }
}
