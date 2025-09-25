// element-loading-text="拼命加载中..."
// element-loading-spinner="el-icon-loading"

$(function () {
    //分页
    'use strict';

    Vue.component('page-nation', {
        props: ['url', 'list', 'totalCount', 'pagesize', 'currentPage'],
        template: ' <div>\
                    <el-table :data="list" \
                        size="small"\
                        @sort-change="sortChange"\
                    >\
                        <slot></slot>\
                    </el-table>\
                    <!--分页-->\
                    <div v-if="page" align="center" style="margin-top: 15px">\
                        <el-pagination\
                                @current-change="handleCurrentChange"\
                                :current-page="currentPage"\
                                :page-size="pagesize"\
                                layout="total, prev, pager, next, jumper"\
                                :total="totalCount">\
                        </el-pagination>\
                    </div>\
                 </div>',
        data: function data() {
            return {
                // list: [],
                loading: true
            };
        },

        computed: {
            page: function page() {
                if (this.totalCount / this.pagesize > 1) {
                    return true;
                } else {
                    return false;
                }
            }
        },
        methods: {
            //页码变更
            handleCurrentChange: function handleCurrentChange(val) {
                this.$emit('change-page', val);
            },

            //排序
            sortChange: function sortChange(column, prop, order) {
                this.$emit('change-sort', column, prop, order);
            }
        }
    });
})