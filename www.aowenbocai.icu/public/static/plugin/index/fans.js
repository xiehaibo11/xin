'use strict';

$(function () {

    Vue.component('fans-list', {
        props: ['item'],
        template: '#fansList',
        methods: {
            care: function care() {
                var _this = this;
                var userid = this.item.userid || this.item.to_userid;
                $.get('/index/fans/care/?to_userid=' + userid, function (res) {
                    $.msg(res.msg);
                    if (res.code) {
                        _this.item.user.care_status = !_this.item.user.care_status;
                    }
                }, 'json');
            }
        },
        computed: {
            care_str: function care_str() {
                return this.item.user.care_status ? '取消关注' : '关注';
            }
        }
    });

    Vue.component('list-group', {
        data: function data() {
            var loadPage = new LoadPage(this.url);
            return {
                word: '',
                list: [],
                loadPage: loadPage,
                status: 1,
                pagesize: 10,
                noData: false
            };
        },

        props: ['url'],
        template: '#list-group',
        methods: {
            search: function search() {
                this.loadPage.setWord(this.word);
                this.loadPage.first();
            },
            loadMore: function loadMore() {
                this.status = 1;
                this.loadPage.next();
            },
            dataStatus: function dataStatus(length) {
                this.status = length < this.pagesize ? 2 : 0;
            }
        },
        computed: {
            loadStr: function loadStr() {
                var str = ['加载更多...', '加载中...', '全部数据加载完成', '没有数据'];
                return str[this.status];
            },
            loadStatus: function loadStatus() {
                return this.status == 1 ? true : false;
            }
        },
        created: function created() {
            var _this2 = this;

            this.loadPage.on('first', function (res) {
                res.data.length == 0 ? _this2.status = 3 : _this2.dataStatus(res.data.length);
                _this2.$set(_this2, 'list', res.data);
            });
            this.loadPage.on('next', function (res) {
                _this2.dataStatus(res.data.length);
                for (var p in res.data) {
                    _this2.list.push(res.data[p]);
                }
            });
            this.loadPage.first();
        }
    });

    var vm = new Vue({
        el: '#app',
        data: {
            info: {}
        },
        created: function created() {
            var _this3 = this;

            var userid = $.urlGet()['userid'] || '0';
            $.get('/index/user/show/?userid=' + userid, function (res) {
                _this3.$set(_this3, 'info', res.data);
                $('#app').removeClass('app_init');
            });
        }
    });
});