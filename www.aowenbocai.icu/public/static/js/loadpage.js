var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var LoadPage = function () {
    function LoadPage(url) {
        _classCallCheck(this, LoadPage);

        this.url = url;
        this.page = 1;
        this.count = 99999;
        this.hook = {};
    }

    _createClass(LoadPage, [{
        key: "load",
        value: function load(page, callback) {
            if (typeof page == "number") this.page = page;
            var data = {
                page: this.page
            };
            if (this.word) data.word = this.word;
            $.get(this.url, data, function (res) {
                if (typeof page == "function") page(res);
                if (typeof callback == "function") callback(res);
            });
        }
    }, {
        key: "on",
        value: function on(name, callback) {
            this.hook[name] = callback;
        }
    }, {
        key: "first",
        value: function first(callback) {
            var _this = this;

            this.page = 1;
            this.load(function (res) {
                if (_this.hook['first']) _this.hook['first'](res);
                if (callback) callback(res);
            });
        }
    }, {
        key: "last",
        value: function last(callback) {
            var _this2 = this;

            this.page = this.count; //最后一页占时没用
            this.load(function (res) {
                if (_this2.hook['last']) _this2.hook['last'](res);
                if (callback) callback(res);
            });
        }
    }, {
        key: "prev",
        value: function prev(callback) {
            var _this3 = this;

            if (this.page > 1) this.page -= 1;
            this.load(function (res) {
                if (_this3.hook['prev']) _this3.hook['prev'](res);
                if (callback) callback(res);
            });
        }
    }, {
        key: "next",
        value: function next(callback) {
            var _this4 = this;

            if (this.page < this.count) this.page += 1;
            this.load(function (res) {
                if (_this4.hook['next']) _this4.hook['next'](res);
                if (callback) callback(res);
            });
        }
    }, {
        key: "setPage",
        value: function setPage(page) {
            this.page = page;
        }
    }, {
        key: "getPage",
        value: function getPage(word) {
            return this.page;
        }
    }, {
        key: "setWord",
        value: function setWord(word) {
            this.word = word;
        }
    }, {
        key: "getWord",
        value: function getWord() {
            return this.word;
        }

        /**
         * 重置参数
         * @return reset
         */

    }, {
        key: "reset",
        value: function reset() {
            this.page = 1;
            this.word = '';
        }
    }]);

    return LoadPage;
}();