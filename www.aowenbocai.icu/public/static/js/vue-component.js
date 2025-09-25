'use strict';

$(function () {
    Vue.component('loading-btn', {
        props: ['status'],
        template: '<div class="loading_btn flex-box">\n            <div v-show="status" class="spinner">\n              <div class="rect1"></div>\n              <div class="rect2"></div>\n              <div class="rect3"></div>\n              <div class="rect4"></div>\n              <div class="rect5"></div>\n            </div>\n            <div><slot></slot></div>\n        </div>'
    });

    Vue.component('loading-box', {
        template: '<div>\n            <slot :list="list"></slot>\n            <loading-btn @click.native="loadMore()" class="list_more" :class="{\'btn-more btn-lg\': status == 0, \'load_gray\': status != 0}" :status="loadStatus">{{loadStr}}</loading-btn>\n        </div>',
        data: function data() {
            var loadPage = new LoadPage(this.url);
            return {
                list: [],
                loadPage: loadPage,
                status: 1,
                pagesize: 10,
                noData: false
            };
        },

        props: {
            url: { // 必须提供字段
                required: true
            },
            loadmore: {
                default: '点击加载更多'
            },
            load: {
                default: '加载中...'
            },
            loaded: {
                default: '全部数据加载完成'
            },
            nodata: {
                default: '没有数据'
            }
        },
        methods: {
            search: function search() {
                this.loadPage.setWord(this.word);
                this.loadPage.first();
            },
            loadMore: function loadMore() {
                if (this.status != 0) return;
                this.status = 1;
                this.loadPage.next();
            },
            dataStatus: function dataStatus(length) {
                this.status = length < this.pagesize ? 2 : 0;
            }
        },
        computed: {
            loadStr: function loadStr() {
                var str = [this.loadmore, this.load, this.loaded, this.nodata];
                return str[this.status];
            },
            loadStatus: function loadStatus() {
                return this.status == 1 ? true : false;
            }
        },
        created: function created() {
            var _this = this;

            this.loadPage.on('first', function (res) {
                if(res.err ==1){
                    _this.status = 3
                    return false
                }else {
                    res.data.length == 0 ? _this.status = 3 : _this.dataStatus(res.data.length);
                    _this.$set(_this, 'list', res.data);
                }
            });
            this.loadPage.on('next', function (res) {
                _this.dataStatus(res.data.length);
                for (var p in res.data) {
                    _this.list.push(res.data[p]);
                }
            });
            this.loadPage.first();
        }
    });
});