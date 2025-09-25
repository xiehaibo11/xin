$(function() {
    Vue.component('loading-btn', {
        props: ['status'],
        template: `<div class="loading_btn flex-box">
            <div v-show="status" class="spinner">
              <div class="rect1"></div>
              <div class="rect2"></div>
              <div class="rect3"></div>
              <div class="rect4"></div>
              <div class="rect5"></div>
            </div>
            <div><slot></slot></div>
        </div>`
    });

    Vue.component('loading-box', {
        template: `<div>
            <slot :list="list"></slot>
            <loading-btn @click.native="loadMore()" class="list_more" :class="{'btn-more btn-lg': status == 0, 'load_gray': status != 0}" :status="loadStatus">{{loadStr}}</loading-btn>
        </div>`,
        data() {
            var loadPage = new LoadPage(this.url);
            return {
                list: [],
                loadPage,
                status: 1,
                pagesize: 10,
                noData: false,
            }
        },
        props: {
            url: {  // 必须提供字段
                required: true
            },
            loadmore: {
                default: '加载更多...'
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
            search() {
                this.loadPage.setWord(this.word);
                this.loadPage.first();
            },
            loadMore() {
                if (this.status != 0) return;
                this.status = 1;
                this.loadPage.next();
            },
            dataStatus(length) {
                this.status = length < this.pagesize ? 2 : 0;
            }
        },
        computed: {
            loadStr() {
                var str = [this.loadmore, this.load, this.loaded, this.nodata];
                return str[this.status];
            },
            loadStatus() {
                return this.status == 1 ? true : false;
            }
        },
        created() {
            this.loadPage.on('first', (res) => {
                res.data.data.length == 0 ? this.status = 3 : this.dataStatus(res.data.data.length);
                this.$set(this, 'list', res.data.data);
            });
            this.loadPage.on('next', (res) => {
                this.dataStatus(res.data.data.length);
                for (var p in res.data.data) this.list.push(res.data.data[p]);
            });
            this.loadPage.first();
        }
    });
});
