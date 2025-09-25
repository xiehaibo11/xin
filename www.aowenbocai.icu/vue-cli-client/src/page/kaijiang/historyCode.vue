<!--历史开奖-->
<template>
    <div>
        <div class="contentH" :style="{height: contentH + 'px'}">
        <!--累加型-->
        <template v-if="expectType">
            <page-item :url="pkUrl">
                <template slot-scope="props">
                    <template v-if="cz=='ssc'">
                        <table cellpadding="0" cellspacing="0" class="his-table-list mt-sm">
                            <tr>
                                <th width="26%">期号</th>
                                <th>开奖号码</th>
                                <th width="12%">十位</th>
                                <th width="12%">个位</th>
                                <th width="12%">后三</th>
                            </tr>
                            <tr v-for="(item, index) in props.data" :key="index">
                                <td>{{item.expect}}</td>
                                <td><em class="red">{{item.code}}</em></td>
                                <td>{{sscStatus(item.code).shi}}</td>
                                <td>{{sscStatus(item.code).ge}}</td>
                                <td><em :class="sscStatus(item.code).bg_color">{{sscStatus(item.code).hs}}</em></td>
                            </tr>
                        </table>
                    </template>
                    <template v-if="cz=='syxw'">
                        <table cellpadding="0" cellspacing="0" class="his-table-list mt-sm">
                            <tr>
                                <th width="26%">期号</th>
                                <th>开奖号码</th>
                                <th width="15%">大小比</th>
                                <th width="15%">奇偶比</th>
                            </tr>
                            <tr v-for="(item, index) in props.data" :key="index">
                                <td>{{item.expect}}</td>
                                <td><em class="red">{{item.code}}</em></td>
                                <td>{{syxwStatus(item.code).dx}}</td>
                                <td>{{syxwStatus(item.code).jo}}</td>
                            </tr>
                        </table>
                    </template>
                    <template v-if="cz=='ks'">
                        <table cellpadding="0" cellspacing="0" class="his-table-list mt-sm">
                            <tr>
                                <th width="26%">期号</th>
                                <th>开奖号码</th>
                                <th width="12%">和值</th>
                                <th width="15%">形态</th>
                                <th width="15%">类型</th>
                            </tr>
                            <tr v-for="(item, index) in props.data" :key="index">
                                <td>{{item.expect}}</td>
                                <td><em class="red">{{item.code}}</em></td>
                                <td>{{ksStatus(item.code).he}}</td>
                                <td>{{ksStatus(item.code).xt}}</td>
                                <td>{{ksStatus(item.code).lx}}</td>
                            </tr>
                        </table>
                    </template>
                    <template v-if="cz=='pc28'">
                        <table cellpadding="0" cellspacing="0" class="his-table-list mt-sm">
                            <tr>
                                <th width="26%">期号</th>
                                <th>开奖号码</th>
                                <th width="12%">形态</th>
                                <th width="12%">极值</th>
                                <th width="15%">色波</th>
                            </tr>
                            <tr v-for="(item, index) in props.data" :key="index">
                                <td width="20%" class="c-1">{{item.expect}}</td>
                                <td width="30%" class="num">
                                    <div class="pc28-tencode">
                                        {{getCodeArr(item.code)[0]}} + {{getCodeArr(item.code)[1]}} + {{getCodeArr(item.code)[2]}} = <span class="pc28-he" :style="{'backgroundColor' : pcStatus(item.code).bg_color}">{{pcStatus(item.code).he}}</span>
                                    </div>
                                </td>
                                <td width="15%">{{pcStatus(item.code).xt}}</td>
                                <td>{{pcStatus(item.code).jz}}</td>
                                <td width="15%"><span class="pc28-sb" :style="{'backgroundColor' : pcStatus(item.code).bg_color}">{{pcStatus(item.code).text_color}}</span></td>
                            </tr>
                        </table>
                    </template>
                    <template v-if="cz=='pk10'">
                        <table cellpadding="0" cellspacing="0" class="his-table-list mt-sm">
                            <tr>
                                <th width="15%">期号</th>
                                <th width="50%">开奖号码</th>
                                <th>开奖时间</th>
                            </tr>
                            <tr v-for="(item, index) in props.data" :key="index">
                                <td>{{item.expect}}</td>
                                <td>{{item.code}}</td>
                                <td style="font-size: 12px">{{item.create_time}}</td>
                            </tr>
                        </table>
                    </template>
                </template>
            </page-item>
        </template>
        <!--日期型-->
        <template v-else>
            <div class="f-sm mt-sm tc">
                日期 <mt-button @click.native="openSpicker" size="small"> <i slot="icon" class="iconfont icon-rili"></i> {{start}}</mt-button>
            </div>
            <div>
                <mt-datetime-picker
                    ref="picker"
                    type="date"
                    :value="startData"
                    year-format="{value} 年"
                    month-format="{value} 月"
                    date-format="{value} 日"
                    :start-date = 'startData'
                    :end-date = 'endData'
                    v-model="startBefore" @confirm="handleConfirm">
                </mt-datetime-picker>
            </div>
            <table cellpadding="0" cellspacing="0" class="his-table-list mt-sm">
                <tr>
                    <template v-if="cz=='ssc'">
                        <th width="26%">期号</th>
                        <th>开奖号码</th>
                        <th width="12%">十位</th>
                        <th width="12%">个位</th>
                        <th width="12%">后三</th>
                    </template>
                    <template v-if="cz=='syxw'">
                        <th width="26%">期号</th>
                        <th>开奖号码</th>
                        <th width="15%">大小比</th>
                        <th width="15%">奇偶比</th>
                    </template>
                    <template v-if="cz=='ks'">
                        <th width="26%">期号</th>
                        <th>开奖号码</th>
                        <th width="12%">和值</th>
                        <th width="15%">形态</th>
                        <th width="15%">类型</th>
                    </template>
                    <template v-if="cz=='pc28'">
                        <th width="26%">期号</th>
                        <th>开奖号码</th>
                        <th width="12%">形态</th>
                        <th width="12%">极值</th>
                        <th width="15%">色波</th>
                    </template>
                    <template v-if="cz=='pk10'">
                        <th width="26%">期号</th>
                        <th>开奖号码</th>
                    </template>
                </tr>
                <template v-if="loading">
                    <tr>
                        <td colspan="8">
                            <mt-spinner :type="3" class="loading"></mt-spinner>
                        </td>
                    </tr>
                </template>
                <template v-if="!loading">
                    <template v-if="codeData.length == 0">
                        <tr>
                            <td colspan="8" class="tc f-sm c-3" style="border: none;font-size: 14px;color: #b1b1b1;line-height: 50px">
                                所选日期暂无任何开奖号码!
                            </td>
                        </tr>
                    </template>
                    <template v-for="(item, key, index) in codeData" v-else>
                        <tr >
                            <template v-if="cz=='ssc'">
                                <td>{{key}}</td>
                                <td><em class="red">{{item}}</em></td>
                                <td>{{sscStatus(item).shi}}</td>
                                <td>{{sscStatus(item).ge}}</td>
                                <td><em :class="sscStatus(item).bg_color">{{sscStatus(item).hs}}</em></td>
                            </template>
                            <template v-if="cz=='syxw'">
                                <td>{{key}}</td>
                                <td><em class="red">{{item}}</em></td>
                                <td>{{syxwStatus(item).dx}}</td>
                                <td>{{syxwStatus(item).jo}}</td>
                            </template>
                            <template v-if="cz=='ks'">
                                <td>{{key}}</td>
                                <td><em class="red">{{item}}</em></td>
                                <td>{{ksStatus(item).he}}</td>
                                <td>{{ksStatus(item).xt}}</td>
                                <td>{{ksStatus(item).lx}}</td>
                            </template>
                            <template v-if="cz=='pc28'">
                                <td width="20%" class="c-1">{{key}}</td>
                                <td width="30%" class="num">
                                    <div class="pc28-tencode">
                                        {{getCodeArr(item)[0]}} + {{getCodeArr(item)[1]}} + {{getCodeArr(item)[2]}} = <span class="pc28-he" :style="{'backgroundColor' : pcStatus(item).bg_color}">{{pcStatus(item).he}}</span>
                                    </div>
                                </td>
                                <td width="15%">{{pcStatus(item).xt}}</td>
                                <td>{{pcStatus(item).jz}}</td>
                                <td width="15%"><span class="pc28-sb" :style="{'backgroundColor' : pcStatus(item).bg_color}">{{pcStatus(item).text_color}}</span></td>
                            </template>
                            <template v-if="cz=='pk10'">
                                <td>{{key}}</td>
                                <td><em class="red">{{item}}</em></td>
                            </template>
                        </tr>
                    </template>
                </template>
            </table>
        </template>
        </div>
        <template v-if="isLink">
            <div class="history-foot">
                <router-link :to="'/' + this.$route.query.cz + '?name=' + this.name">
                    <mt-button size="large" type="primary">投注{{this.$route.query.title}}</mt-button>
                </router-link>
            </div>
        </template>
    </div>
</template>

<script>
    import pageItem from 'components/common/PageItem.vue'
    export default {
        name: '',
        components:{
            pageItem
        },
        data () {
            return {
                loading:true,
                codeData:[],

                startBefore: '',
                start: this.$bet.formatTime('Y-m-d',new Date().getTime()/1000),
            }
        },
        computed:{
            expectType(){
                return Number(this.$route.query.expectType)
            },
            name(){
                return this.$route.query.name
            },
            cz(){
                return this.$route.query.cz
            },
            //日期的最小可选值
            startData() {
                return new Date("January 1,2018");
            },
            //日期的最大可选值
            endData() {
                return new Date();
            },
            isLink(){
                return this.$route.path == '/viewCode' ? true : false
            },
            //内容高度
            contentH(){
                let h = this.isLink ? 81 : 40
                return this.$store.state.clientHeight - h
            },
            //所有列表地址
            pkUrl(){
                return '/index/history/get_list?name=' + this.name
            },

            //时时彩个位、十位、后三形态
            sscStatus () {
                return (codeStr)=>{
                    var shi = ''
                    var ge = ''
                    var hs = ''
                    var bg_color = ''
                    var patrn= /.*[\u4e00-\u9fa5]+.*$/;
                    if(codeStr && !patrn.test(codeStr)){
                        var codeArr = codeStr.split(',')
                        shi = this.sscCodeXt(codeArr[3])
                        ge = this.sscCodeXt(codeArr[4])
                        if(this.countRepeat(codeArr.slice(-3)) == 1){
                            hs = '组六'}
                        if(this.countRepeat(codeArr.slice(-3)) == 2){
                            hs = '组三';
                            bg_color = 'org'}
                        if(this.countRepeat(codeArr.slice(-3)) == 3){
                            hs = '豹子';
                            bg_color = 'suc'}
                    }
                    return {'shi':shi,'ge': ge, 'hs' : hs, 'bg_color' : bg_color}
                }
            },
            //11选5大小比、奇偶比
            syxwStatus() {
                return (codeStr)=>{
                    var patrn= /.*[\u4e00-\u9fa5]+.*$/;
                    if(codeStr && !patrn.test(codeStr)){
                        var codeArr = codeStr.split(',')
                        var da = 0 , xiao = 0 ,js = 0 , os = 0
                        for(var i=0 ; i< codeArr.length ; i++){
                            if(Number(codeArr[i]) > 5){
                                da += 1
                            }else {
                                xiao += 1
                            }
                            if(Number(codeArr[i]) % 2 == 1){
                                js += 1
                            }else {
                                os += 1
                            }
                        }
                        return {'dx': da + ':' + xiao, 'jo' : js + ':' + os}
                    }else {
                        return {'dx': '', 'jo' : ''}
                    }
                }
            },
            //快3和值、形态、类型
            ksStatus() {
                return (codeStr)=>{
                    var patrn= /.*[\u4e00-\u9fa5]+.*$/;
                    if(codeStr && !patrn.test(codeStr)){
                        var codeArr = codeStr.split(',')
                        var xt = '' , he = 0 , lx = ''
                        for(var i=0 ; i< codeArr.length ; i++){
                            he += Number(codeArr[i])
                        }
                        var dx = he >= 11 ? '大' : '小'
                        var ds = he%2 == 1 ? '单' : '双'
                        if(this.countRepeat(codeArr) == 1){
                            lx = '三不同号'
                        }
                        if(this.countRepeat(codeArr) == 2){
                            lx = '二同号'
                        }
                        if(this.countRepeat(codeArr) == 3){
                            lx = '三同号'
                        }
                        return {'he': he, 'xt' : dx+''+ds, 'lx': lx}
                    }else {
                        return {'he': '', 'xt' : '', 'lx': ''}
                    }
                }
            },
            //计算pc28【和、形态、颜色，色波】
            pcStatus(){
                return (codeStr) =>{
                    var patrn= /.*[\u4e00-\u9fa5]+.*$/;
                    if(codeStr && !patrn.test(codeStr)){
                        var codeArr = codeStr.split(',')
                        var green_w = [1,4,7,10,16,19,22,25]
                        var bule_w = [2,5,8,11,17,20,23,26]
                        var red_w = [3,6,9,12,15,18,21,24]
                        var gray_w = [0,13,14,27]
                        var color = ''
                        var text_color = ''
                        var he = 0
                        for(var i=0 ; i< codeArr.length ; i++){
                            he += Number(codeArr[i])
                        }
                        if(red_w.indexOf(he) > -1){
                            color = '#ff0000'
                            text_color = '红波'
                        }
                        if(bule_w.indexOf(he) > -1){
                            color = '#2388f5'
                            text_color = '蓝波'
                        }
                        if(green_w.indexOf(he) > -1){
                            color = '#12c231'
                            text_color = '绿波'
                        }
                        if(gray_w.indexOf(he) > -1){
                            color = '#999999'
                            text_color = '灰波'
                        }
                        var dxObj = he >= 14 ? '大' : '小'
                        var dsObj = he%2 == 0 ? '双' : '单'
                        var jz = ''
                        if(he >= 22 && he<=27 ){jz = '极大'}
                        if(he >= 0 && he<=5 ){jz = '极小'}
                        var bz = this.countRepeat(codeArr) == 3 ? '豹子' : ''
                        return {'he' : he , 'xt':dxObj + ',' + dsObj,'bg_color': color,'text_color' : text_color,'jz':jz ,'bz': bz}
                    }else {
                        return {'he' : '' , 'xt': '','bg_color': '','text_color' : '','jz':'' ,'bz': ''}
                    }
                }
            },
            //开奖号码转换成数组
            getCodeArr () {
                return (codeStr)=>{
                    var patrn= /.*[\u4e00-\u9fa5]+.*$/;
                    if(codeStr && !patrn.test(codeStr)) {
                        return codeStr.split(',')
                    }else {
                        return []
                    }
                }
            }
        },
        watch:{
            start(){
                this.getData();
            }
        },
        methods:{
            sscCodeXt(num) {
                var dx = '-'
                var ds = '-'
                if(num){
                    dx = num >= 5 ? '大' : '小'
                    ds = num % 2 == 1 ? '单' : '双'
                }
                return dx + ds
            },
            countRepeat(_arr) {
                var _res = []; //
                _arr.sort();
                for (var i = 0; i < _arr.length;) {
                    var count = 0;
                    for (var j = i; j < _arr.length; j++) {
                        if (_arr[i] == _arr[j]) {
                            count++;
                        }
                    }
                    _res.push([_arr[i], count]);
                    i += count;
                }
                var _newArr = [];
                for (var i = 0; i < _res.length; i++) {
                    _newArr.push(_res[i][1]);
                }

                return _newArr.sort()[_newArr.length-1]
            },
            //选择时间
            openSpicker() {
                if (this.startBefore == '') {
                    this.startBefore = this.endData;
                }
                this.$refs.picker.open();
            },
            //点击确定按钮之后
            handleConfirm(){
                this.start = this.$bet.formatTime('Y-m-d',this.startBefore/1000);
            },
            //获取开奖列表
            getData(){
                this.loading = true
                this.$axios('/index/history/historycode?name=' + this.name + '&times=' + this.start).then(({data})=>{
                    this.$set(this,'codeData',data.data);
                    this.loading = false
                })
            }
        },
        created(){
            if(this.name !== 'pk10'|| this.name !== 'bjks' || this.cz !== 'pc28'){
                this.getData();
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .loading{
        display: inline-block;
        width: 28px;
        height: 28px;
        margin: 10px auto;
    }
    .his-table-list{
        border:solid #e2e2e2;
        border-width: 1px 0 0 1px;
        width: 100%;
        th,td{
            border:solid #e2e2e2;
            border-width: 0px 1px 1px 0px;
            font-size: 12px;
            text-align: center;
            height: 24px;
            line-height: 24px;
        }
        th{
            background-color: #ECEFF1;
        }
        td{
            color: #666666;
        }
        tr:nth-child(odd){
            background-color: #ececec;
        }
    }
    .pc28-tencode{
        .pc28-he{
            display: inline-block;
            width: 20px;
            height: 20px;
            line-height: 20px;
            @include rounded(50%);
            color: #ffffff;
            text-align: center;
        }
    }
    .pc28-sb{
        display: inline-block;
        width: 40px;
        height: 20px;
        line-height: 20px;
        @include rounded(10px);
        color: #ffffff;
        text-align: center;
    }
</style>
