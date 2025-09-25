// element-loading-text="拼命加载中..."
// element-loading-spinner="el-icon-loading"

$(function () {
    //分页
    Vue.component('page-nation', {
        props: ['url'],
        template:'<div>\
        <el-table :data="list" size="small" @sort-change="sortChange" stripe>\
            <slot></slot>\
        </el-table>\
         <!--分页-->\
            <div v-if="page" align="center" style="margin-top: 15px">\
                <el-pagination @current-change="handleCurrentChange" :current-page="currentPage" :page-size="pagesize" layout="total, prev, pager, next, jumper" :total="totalCount"></el-pagination>\
            </div>\
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
               // this.loading = true
                var _this = this;
                $.get(_this.url + _this.isIndexOf + 'page=1' ,function(res){
                    _this.$set(_this,'list',res.data)
                    _this.$set(_this,'totalCount',res.total)
                //    this.loading = false
                    _this.currentPage = 1
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
                 //   this.loading = false
                })
            },
            //页码变更
            handleCurrentChange:function(val) {
                //this.loading = true
                this.currentPage = val;
                this.loadData(val);
            },
            //排序
            sortChange:function(column, prop, order){
                var _this = this;
                _this.$emit('game-record',column, prop, order)
            }
        },
        created:function(){
            // this.loading = true
            var _this = this;
            $.get(_this.url + _this.isIndexOf + 'page='+ _this.currentPage ,function(res){
                _this.$set(_this,'list',res.data)
                _this.$set(_this,'totalCount',res.total)
                _this.$set(_this,'pagesize',res.per_page)
             //   this.loading = false
            })
        }
    });
})