<!--顶部期号+ 倒计时-->
<template>
    <!--头部信息 start-->
    <div class="top-info head">
        <div class="flex-box time-info border-bottom-1px">
            <div class="flex info-kjCode" @click="show = !show">
                <div class="tc kj-title">
                    <template v-if="!expectType">第{{lastIssue | kjIssue}}期开奖 :</template>
                    <template v-else>第{{lastIssue}}期开奖 :</template>
                </div>
                <!--最新开奖号码区-->
                <div class="tc">
                    <!--时时彩，11选5-->
                    <template v-if="cz == 'ssc' || cz == 'syxw'">
                        <em v-if="isGetCode" class="ball-open" v-for="i in 5">{{changeNum}}</em>
                        <em v-if="!isGetCode" class="ball-open" v-for="code in awardNumber">{{code}}</em>
                    </template>
                    <!--时时彩，11选5 end-->
                    <!--pk10-->
                    <template v-if="cz == 'pk10'">
                        <em v-if="isGetCode" class="pk-ball" v-for="i in 10"><em>{{changeNum}}</em></em>
                        <em v-if="!isGetCode" class="pk-ball" v-for="code in awardNumber"><em :class="'code-'+ parseInt(code)">{{code}}</em></em>
                    </template>
                    <!--pk10 end-->
                    <!--幸运28-->
                    <template v-if="cz == 'pc28'">
                        <em v-if="isGetCode" class="pk-ball pc28-code">
                            <em>{{changeNum}}</em><i>+</i><em>{{changeNum}}</em><i>+</i><em>{{changeNum}}</em><i>=</i><em>?</em>
                            <span></span>
                        </em>
                        <em v-if="!isGetCode" class="pk-ball pc28-code">
                            <em>{{awardNumber[0]}}</em><i>+</i><em>{{awardNumber[1]}}</em><i>+</i><em>{{awardNumber[2]}}</em><i>=</i><em class="res" :style="{backgroundColor:CodeXt.bg_color}">{{CodeXt.he}}</em>
                            <span class="f-mini">{{CodeXt.xt}}</span>
                        </em>
                    </template>
                    <!--幸运28 end-->
                    <i class="iconfont icon-jiantou c-3" :class="show ? 'is-active':'no-active'"></i>
                </div>
            </div>
            <div class="info-downTime" :class="{'pk-downTime': cz == 'pk10'}">
                <div class="time-info tc f-sm">
                    <div class="tc kj-title">
                        距第{{sortExpect}}期截止
                    </div>
                    <div class="tc kj-time">
                        {{timer}}
                    </div>
                </div>
            </div>
        </div>
        <div class="layout z-1" v-show="show" @click="show = !show">
            <div class="recent-open-box">
                <template v-if="cz == 'syxw'">
                    <table cellpadding="0" cellspacing="0" class="table-list" v-show="show">
                        <tr>
                            <th width="20%">期号</th>
                            <th width="50%">开奖号码</th>
                            <th width="15%">大小比</th>
                            <th width="15%">奇偶比</th>
                        </tr>
                        <tr v-for="(item,index) in codeArr" :key="index" v-if="index < 10">
                            <td v-if="!expectType">{{item.expect | subStr}}</td>
                            <td v-else>{{item.expect}}</td>
                            <td>
                                <span v-for="num in item.code" class="ball-sm">{{num}}</span>
                            </td>
                            <td>{{item.dx}}</td>
                            <td>{{item.jo}}</td>
                        </tr>
                    </table>
                </template>
                <template v-if="cz == 'ssc'">
                    <table cellpadding="0" cellspacing="0" class="table-list" v-show="show">
                        <tr>
                            <th width="20%">期号</th>
                            <th width="35%">开奖号码</th>
                            <th width="15%">十位</th>
                            <th width="15%">个位</th>
                            <th width="15%">后三</th>
                        </tr>
                        <tr v-for="(item,index) in codeArr" :key="index" v-if="index < 10">
                            <td v-if="!expectType">{{item.expect | subStr}}</td>
                            <td v-else>{{item.expect}}</td>
                            <td>
                                <span v-for="num in item.code" class="ball-sm">{{num}}</span>
                            </td>
                            <td>{{item.shi}}</td>
                            <td>{{item.ge}}</td>
                            <td>{{item.hs}}</td>
                        </tr>
                    </table>
                </template>
                <template v-if="cz == 'pk10'">
                    <table cellpadding="0" cellspacing="0" class="table-list" v-show="show">
                        <tr>
                            <th width="25%">期号</th>
                            <th width="75%">开奖号码</th>
                        </tr>
                        <tr v-for="(item,index) in codeArr" :key="index" v-if="index < 10">
                            <td v-if="!expectType">{{item.expect | subStr}}</td>
                            <td v-else>{{item.expect}}</td>
                            <td>
                                <span v-for="num in item.code" class="pk-ball"><em :class="'code-'+ parseInt(num)">{{num}}</em></span>
                            </td>
                        </tr>
                    </table>
                </template>
                <template v-if="cz == 'pc28'">
                    <table cellpadding="0" cellspacing="0" class="table-list" v-show="show">
                        <tr>
                            <th width="18%">期数</th>
                            <th width="45%">开奖号码</th>
                            <th width="15%">形态</th>
                            <th>色波</th>
                            <th>极值</th>
                            <!--<th width="15%">豹子</th>-->
                        </tr>
                        <tr v-for="(item,index) in codeArr" :key="index" v-if="index < 10">
                            <td v-if="!expectType">{{item.expect | subStr}}</td>
                            <td v-else>{{item.expect}}</td>
                            <td>
                                <span class="ball-sm" style="background-color: #b5b5b5">{{item.code[0]}}</span>
                                <em>+</em>
                                <span class="ball-sm" style="background-color: #b5b5b5">{{item.code[1]}}</span>
                                <em>+</em>
                                <span class="ball-sm" style="background-color: #b5b5b5">{{item.code[2]}}</span>
                                <em>=</em>
                                <span class="ball-sm ball-res" :style="{backgroundColor:$store.getters.pcResult(item.code).bg_color}">{{item.he}}</span>
                            </td>
                            <td>{{item.xt[0]}},{{item.xt[1]}}</td>
                            <td><span class="pc-ball-res" :style="{backgroundColor:$store.getters.pcResult(item.code).bg_color}">{{$store.getters.pcResult(item.code).text_color}}</span></td>
                            <td>{{$store.getters.pcResult(item.code).jz}}</td>
                            <!--<td>{{pcResult(item.code).bz}}</td>-->
                        </tr>
                    </table>
                </template>
                <div class="tc c-3 foot-tip">已显示近10期开奖</div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'downTime',
        data () {
            return {
                show : false,
                changeNum:'',
                num:'',
                websocket: null,
                wsConnected: false,
                statusSync: null,
                lastUpdateTime: 0
            }
        },
        filters:{
            subStr(val){
                return val.toString().slice(4);
            },
            kjIssue(val){
                return val.toString().slice(8);
            }
        },
        computed:{
            //期号类型
            expectType(){
                return this.$store.state.lottery.expect_type
            },
            name(){ //游戏名eg:gd11
                return this.$route.query.name
            },
            cz(){
                let path = this.$route.path.replace('/','')
                return path == 'ssc' || path == 'syxw' || path == 'pk10' || path == 'ks' || path == 'pc28' ? path : this.$route.query.cz
            },
            //当期剩余时间
            downTime(){
                return this.$store.state.lottery.info.time
            },
            //当期期号（短）
            sortExpect(){
                return this.$store.state.lottery.info.sort_expect
            },
            //当期期号
            expect(){
                return this.$store.state.lottery.info.expect
            },
            //上一期期号
            lastIssue(){
                return this.$store.state.lottery.info.lastIssue
            },
            //倒计时显示格式化
            timer(){
                return this.downTime == 0 ? '00:00' : this.$bet.formatTime('i:s',this.downTime)
            },
            intervalTime(){
                return this.$store.state.intervalTime
            },
            url(){
                return '/index/' + this.cz + '/getIssueInfo/name/'+ this.name
            },
            //几分钟一期
            timelong(){
                return this.$store.state.lottery.info.timelong
            },
            //近期开奖数组
            codeArr(){
                return this.$store.state.lottery.recent_open
            },
            isGetCode(){
                return this.$store.state.lottery.isGetCode || false
            },
            //当前开奖号码
            awardNumber(){
                return this.$store.state.lottery.info.awardNumber.split(',')
            },
            //pc28最新一期开奖号码形态
            CodeXt(){
                if(this.cz == 'pc28'){
                    let dxObj = ''
                    let dsObj = ''
                    let a = 0
                    for(let i in this.awardNumber){
                        a = Number(a) + Number(this.awardNumber[i])
                    }
                    dxObj = a >= 14 ? '大' : '小'
                    dsObj = a%2 == 0 ? '双' : '单'
                    let green_w = [1,4,7,10,16,19,22,25]
                    let bule_w = [2,5,8,11,17,20,23,26]
                    let red_w = [3,6,9,12,15,18,21,24]
                    let gray_w = [0,13,14,27]
                    let color = ''
                    if(red_w.indexOf(a) > -1){
                        color = '#ff0000'
                    }
                    if(bule_w.indexOf(a) > -1){
                        color = '#2388f5'
                    }
                    if(green_w.indexOf(a) > -1){
                        color = '#12c231'
                    }
                    if(gray_w.indexOf(a) > -1){
                        color = '#999999'
                    }
                    return {'he': a,'xt': dxObj + ',' + dsObj,'bg_color': color}
                }
            },
        },
        watch:{
            //监听倒计时为零时获取新期号信息
            downTime(val){
                if(val == 0 && val !== ''){
                    this.$store.commit('clearNewCode')  //清除获取开奖号码定时器
                    this.$store.commit('clearRandomNum')  //清除开奖动画
                    
                    // 增加重试机制的期号更新
                    const updatePeriod = (retryCount = 0) => {
                        this.$axios(this.url, {timeout: 10000}).then(({data})=>{
                            this.$store.commit('setBetData',data.data)
                            this.$emit('change-time') //触发追号内容重写
                            this.$messagebox('提示','期次已更改，当前第' + this.sortExpect + '期')
                            setTimeout(()=>{
                                this.$messagebox.close();
                            },8000)
                            this.$store.commit('setAwardNumber',data.data.awardNumber.code); //更新最新开奖号码状态
                            this.$store.commit('setRecentOpen',data.data.open) //更新近10期开奖数组
                            this.$store.commit('isGetNewCode',data.data.getnewcode);

                            this.rMoveNum(); //执行开奖动画

                            let timelong = data.data.timelong;
                            let j = timelong < 5 ? 5000 : 15000 //减少等待时间，更快获取开奖号码
                            var ctime = setTimeout(()=>{
                                this.getNewCode();
                                clearTimeout(ctime);
                            },j)
                        }).catch((error) => {
                            console.log('更新期号失败:', error);
                            if(retryCount < 2) {
                                setTimeout(() => updatePeriod(retryCount + 1), 3000);
                            }
                        })
                    };
                    
                    updatePeriod();
                }
            }
        },
        methods:{
            //倒计时
            timeInterval(){
                // 清除之前的定时器
                this.$store.commit('clearDownTime');
                
                let lastTime = Date.now();
                let drift = 0;
                
                const tick = () => {
                    const now = Date.now();
                    const elapsed = now - lastTime;
                    lastTime = now;
                    
                    // 计算时间漂移并调整
                    drift += elapsed - 1000;
                    
                    // 更新倒计时
                    this.$store.commit('setDownTime');
                    
                    // 检查是否需要同步服务器时间
                    if (this.downTime <= 0) {
                        // 倒计时结束，触发期号更新
                        return;
                    }
                    
                    // 调整下次执行时间，补偿时间漂移
                    const nextInterval = Math.max(0, 1000 - drift);
                    
                    this.$store.state.intervalTime = setTimeout(tick, nextInterval);
                    
                    // 重置漂移（如果超过1秒）
                    if (Math.abs(drift) > 1000) {
                        drift = 0;
                    }
                };
                
                this.$store.state.intervalTime = setTimeout(tick, 1000);
            },
            //获取不和上次随机相同的随机号
            getRandomNum(){
                let items =['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']
                if(this.cz == 'syxw'){
                    items = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11'];
                }
                if(this.cz == 'ssc'){
                    items = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
                }
                if(this.cz == 'pk10'){
                    items = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10'];
                }
                if(this.cz == 'pc28'){
                    items = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10'];
                }
                this.num = this.$bet.getRandomArrayEle(items, 1).join(",")
                if(Number(this.changeNum) == Number(this.num)){
                    this.getRandomNum()
                }else {
                    this.changeNum = this.num
                }
            },
            //开奖动画
            rMoveNum(){
//                this.getRandomNum();
                this.$store.state.rNumTimer = setInterval(() => {
                    this.getRandomNum();
                }, 100);
            },
            //获取开奖号码
            getNewCode(){
                this.$store.commit('clearNewCode')  //清除获取开奖号码定时器
                let timer = this.timelong < 5 ? 2000 : 3000; //减少轮询间隔，提高响应速度
                let retryCount = 0;
                const maxRetries = 3;
                
                const fetchCode = () => {
                    this.$axios.get('/index/'+ this.cz + '/getNewCode',{
                        params:{
                            name:this.name,
                            issue: this.lastIssue
                        },
                        timeout: 8000 // 增加超时设置
                    }).then(({data})=>{
                        if(!data.err){
                            this.$store.commit('setRecentOpen',data.data.tenCode);//更新近10期开奖号码组
                            this.$store.commit('setAwardNumber',data.data.codeOpen); //更新最新一期开奖号码
                            this.$store.commit('clearNewCode')  //清除获取开奖号码定时器
                            this.$store.commit('clearRandomNum')  //清除开奖动画
                            this.$store.commit('isGetNewCode',false);
                            retryCount = 0; // 重置重试计数
                            
                            // 异步更新遗漏数据，不阻塞主流程
                            if(this.cz !== 'ks' && this.cz !== 'pc28'){
                                this.$nextTick(() => {
                                    this.$axios.get('/index/'+ this.cz + '/getmiss',{
                                        params:{
                                            name:this.name
                                        },
                                        timeout: 5000
                                    }).then(({data})=>{
                                        if(!data.err){
                                            this.$store.commit('setMiss',data.data);
                                        }
                                    }).catch(({error})=>{
                                        console.log('更新遗漏数据失败:', error)
                                    })
                                });
                            }
                        } else {
                            // 如果返回错误但还没达到最大重试次数，立即重试
                            if(retryCount < maxRetries) {
                                retryCount++;
                                console.log(`开奖号码获取失败，重试第${retryCount}次`);
                                setTimeout(fetchCode, 1000);
                            }
                        }
                    }).catch(({error})=>{
                        console.log('获取开奖号码失败:', error);
                        // 网络错误重试机制
                        if(retryCount < maxRetries) {
                            retryCount++;
                            console.log(`网络错误，重试第${retryCount}次`);
                            setTimeout(fetchCode, 2000);
                        }
                    })
                };
                
                this.$store.state.newCodeFun = setInterval(fetchCode, timer);
                // 立即执行一次
                fetchCode();
            },
            //初始数据
            initData(){
                if(this.$route.path == '/betOrder'){
                    this.$axios(this.url).then(({data})=>{
                        this.$store.commit('setBetData',data.data)
                        this.$store.commit('setAwardNumber',data.data.awardNumber.code)
                        this.$store.commit('setRecentOpen',data.data.open);
                        this.$store.commit('isGetNewCode',data.data.getnewcode);
                        this.$store.commit('setType',data.data.expect_type);
                        this.$store.commit('setFirstIssue',data.data.firstIssue);
                        this.$store.commit('clearDownTime'); //清除倒计时
                        this.timeInterval();
                        if(data.data.getnewcode){
                            this.$store.commit('clearNewCode'); //清除获取开奖号码
                            this.$store.commit('clearRandomNum')  //清除开奖动画
                            this.getNewCode();
                            this.rMoveNum();
                        }
                    }).catch(({error})=>{
                        console.log(error)
                    })
                }else {
                    this.$store.commit('clearDownTime'); //清除倒计时
                    this.timeInterval();
                    if(this.$store.state.lottery.isGetCode){
                        this.$store.commit('clearNewCode'); //清除获取开奖号码
                        this.$store.commit('clearRandomNum')  //清除开奖动画
                        this.getNewCode();
                        this.rMoveNum();
                    }
                }
            },
            
            //初始化WebSocket连接
            initWebSocket(){
                try {
                    // 动态导入WebSocket工具
                    import('@/utils/websocket').then(({ getWebSocket }) => {
                        this.websocket = getWebSocket();
                        
                        // 监听连接状态
                        this.websocket.on('connection_status', (status) => {
                            this.wsConnected = status.connected;
                            console.log('WebSocket连接状态:', status.connected);
                        });
                        
                        // 监听开奖更新
                        this.websocket.on('lottery_update', (data) => {
                            this.handleWebSocketUpdate(data);
                        });
                        
                        // 订阅当前彩种
                        if (this.name && this.websocket.isConnected()) {
                            this.websocket.subscribe(this.name);
                        }
                        
                        // 连接成功后订阅
                        this.websocket.on('open', () => {
                            if (this.name) {
                                this.websocket.subscribe(this.name);
                            }
                        });
                    }).catch(error => {
                        console.error('WebSocket初始化失败:', error);
                    });
                } catch (error) {
                    console.error('WebSocket模块加载失败:', error);
                }
            },
            
            //处理WebSocket推送的开奖更新
            handleWebSocketUpdate(data) {
                console.log('收到开奖推送:', data);
                
                if (data.lotteryName === this.name) {
                    this.lastUpdateTime = Date.now();
                    
                    // 立即更新开奖号码，减少延迟
                    if (data.data && data.data.code) {
                        this.$store.commit('setAwardNumber', data.data.code);
                    }
                    
                    // 更新近期开奖
                    if (data.data && data.data.recentOpen) {
                        this.$store.commit('setRecentOpen', data.data.recentOpen);
                    }
                    
                    // 停止轮询，使用实时推送
                    this.$store.commit('clearNewCode');
                    this.$store.commit('clearRandomNum');
                    this.$store.commit('isGetNewCode', false);
                    
                    // 强制同步状态
                    if (this.statusSync) {
                        this.statusSync.forcSync();
                    }
                    
                    // 更新遗漏数据
                    if (this.cz !== 'ks' && this.cz !== 'pc28') {
                        this.$nextTick(() => {
                            this.$axios.get('/index/'+ this.cz + '/getmiss',{
                                params: { name: this.name },
                                timeout: 5000
                            }).then(({data})=>{
                                if(!data.err){
                                    this.$store.commit('setMiss',data.data);
                                }
                            }).catch(({error})=>{
                                console.log('更新遗漏数据失败:', error)
                            })
                        });
                    }
                }
            },
            
            //初始化状态同步
            initStatusSync() {
                try {
                    import('@/utils/statusSync').then(({ getStatusSync }) => {
                        this.statusSync = getStatusSync(this.$store, this.$axios);
                        
                        if (this.name) {
                            this.statusSync.startSync(this.name);
                        }
                    }).catch(error => {
                        console.error('状态同步初始化失败:', error);
                    });
                } catch (error) {
                    console.error('状态同步模块加载失败:', error);
                }
            }
        },
        created(){
           this.initData();
           this.initWebSocket();
           this.initStatusSync();
        },
        activated(){
            this.$store.commit('clearRandomNum')  //清除开奖动画
            this.$store.commit('clearNewCode')  //清除获取开奖号码定时器
            if(this.$store.state.lottery.isGetCode){
                this.rMoveNum();
                this.getNewCode();
            }
            
            // 重新订阅WebSocket
            if (this.websocket && this.name) {
                this.websocket.subscribe(this.name);
            }
        },
        
        beforeDestroy() {
            // 清理WebSocket订阅
            if (this.websocket && this.name) {
                this.websocket.unsubscribe(this.name);
            }
            
            // 清理状态同步
            if (this.statusSync) {
                this.statusSync.stopSync();
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .ks-time{
        color: #FFEB3B;
    }
    .top-info{
        box-shadow: 0 2px 8px #eaeaea;
    }
    $ks-color-dark:#e8e8e8;
    /*倒计时+近期开奖*/
    .time-info{
        width: 100%;
        background-color: #fff;
        //border-bottom: 1px solid $ks-color-dark;
        .info-kjCode{
            border-right: 1px solid #ffffff;
            height: 60px;
            i{
                font-size: 14px;
                -webkit-transition: all 0.25s ease-in-out;
                -moz-transition: all 0.25s ease-in-out;
                -o-transition: all 0.25s ease-in-out;
                transition: all 0.25s ease-in-out;
                display: inline-block;
                &.is-active{
                    -webkit-transform: rotate(180deg);
                    -moz-transform: rotate(180deg);
                    -ms-transform:rotate(180deg);
                    -o-transform:rotate(180deg);
                    transform: rotate(180deg);
                }
                &.no-active{
                    transform: rotate(0deg);
                }
            }
        }
        .info-downTime{
            height: 60px;
            border-left: 1px solid #e8e8e8;
            padding: 0 20px;
        }

        .kj-title{
            padding: 4px 0 2px;
            color: #989898;
            font-size: 15px;
        }
        .kj-time{
            font-size:26px;
            color: #333333;
            padding-top: 5px;
            letter-spacing: 3px;
            font-weight: 600;
        }
        /*pk10*/
        .pk-downTime{
             padding:0 5px;
             .kj-title{
                 font-size: 13px;
             }
             .kj-time{
                 font-size: 22px;
             }
         }
    }
    .recent-open-box{
        padding-top: 60px;
        width: 100%;
    }
    .foot-tip{
        padding: 5px;
        background-color: #ffffff;
        font-size: 13px;
    }
    .ball-open{
        background-color: $bColor;
        font-size: 14px;
        color: #ffffff;
        width: 26px;
        height: 26px;
        margin-right: 5px;
        line-height: 26px;
        text-align: center;
        border-radius: 50%;
        display: inline-block;
        font-weight: 500;
        background: $bColor;
        background: linear-gradient(0deg,#f86469,$bColor 75%);
        background: -webkit-linear-gradient(top,#f86469,$bColor 75%);
        box-shadow: 0 2px 1px #bbb59c;
    }
    .pk-ball{
        em{
            display: inline-block;
            width: 20px;
            height: 20px;
            margin: 4px 2px 5px 0;
            font-size: 13px;
            color: #ffffff;
            border-radius:5px;
            line-height: 20px;
            text-align: center;
            background-color: $bColor;
            &.code-1{
                background-color: #e6de00;
                color: #ffffff;
            }
            &.code-2{
                background-color: #0092dd;
                color: #ffffff;
            }
            &.code-3{
                background-color: #4b4b4b;
                color: #ffffff;
            }
            &.code-4{
                background-color: #ff7600;
                color: #ffffff;
            }
            &.code-5{
                background-color: #17e2e5;
                color: #ffffff;
            }
            &.code-6{
                background-color: #5234ff;
                color: #ffffff;
            }
            &.code-7{
                background-color: #bfbfbf;
                color: #ffffff;
            }
            &.code-8{
                background-color: #ff2600;
                color: #ffffff;
            }
            &.code-9{
                background-color: #780b00;
                color: #ffffff;
            }
            &.code-10{
                background-color: #07bf00;
                color: #ffffff;
            }
        }
    }
    .ball-res{
        background-color: #afafaf;
    }
    .pc28-code{
        em{
            background-color: #afafaf;
        }
    }
    .pc-ball-res{
        display: inline-block;height: 20px;line-height: 20px;color: #ffffff;padding: 0 5px;border-radius: 10px
    }
</style>
