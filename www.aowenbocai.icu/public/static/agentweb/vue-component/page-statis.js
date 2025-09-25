// element-loading-text="拼命加载中..."
// element-loading-spinner="el-icon-loading"

$(function () {
    //分页
    Vue.component('page-nation', {
        props: ['url'],
        template:'<div>\
        <el-table :data="list"  border\
                @sort-change="sortChange" \
                :class="{ \'load\': loading}"  \
                v-loading="loading"\
                element-loading-text="拼命加载中"\
                element-loading-spinner="el-icon-loading">\
            <slot name="s"></slot>\
        </el-table>\
        <slot name="tj" v-if="!loading && list.length" ></slot>\
         <!--分页-->\
            <div v-if="page" align="center" style="margin-top: 15px">\
                <el-pagination @current-change="handleCurrentChange" background :current-page="currentPage" :page-size="pagesize" layout="total, prev, pager, next, jumper" :total="totalCount"></el-pagination>\
            </div>\
            \
        </div>',
        data:function() {
            return {
                list: [],
                loading: true,

                currentPage: 1,
                pagesize:14,
                totalCount: 0,
            }
        },
        computed:{
            page:function(){
                if(this.totalCount / this.pagesize > 1){
                    return true
                }else {
                    return false
                }
            },
            isIndexOf:function(){
                if(this.url.indexOf("?") != -1){
                    return "&"
                }else {
                    return "?"
                }
            }
        },
        watch:{
            url:function(){
                var _this = this;
                _this.loading = true
                $.get(_this.url + _this.isIndexOf + 'page=1' ,function(res){
                    _this.$set(_this,'list',res.data)
                    _this.$set(_this,'totalCount',res.total)
                    _this.$emit('list-data',res)
                    _this.loading = false
                })
            }
        },
        methods:{
            //加载数据
            loadData:function(page){
                var _this = this;
                $.get(_this.url + _this.isIndexOf + 'page='+ page ,function(res){
                    _this.$set(_this,'list',res.data)
                    _this.$set(_this,'currentPage',page)
                    _this.loading = false
                })
            },
            //页码变更
            handleCurrentChange:function(val) {
                this.loading = true
                this.currentPage = val;
                this.loadData(val);
            },
            //排序
            sortChange:function(column, prop, order){
                var _this = this;
                _this.$emit('change-sort',column, prop, order)
            }
        },
        created:function(){
            var _this = this;
            _this.loading = true
            $.get(_this.url + _this.isIndexOf + 'page='+ _this.currentPage ,function(res){
                _this.$set(_this,'list',res.data)
                _this.$emit('list-data',res)
                _this.$set(_this,'totalCount',res.total)
                _this.$set(_this,'pagesize',res.per_page)
                _this.loading = false
            })
        }
    });
})