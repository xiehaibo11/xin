<template>
    <div class="detail">
        <div class="head">
            <mt-header>
                <mt-button icon="back" slot="left" @click.native="goBack"></mt-button>
            </mt-header>
            <div class="select flex-box">
                <span class="f-mini label">游戏</span>
                <span class="chose" @click="show = !show">{{selected}} <i class="iconfont icon-jiantou yellow" :class="show ? 'is-active':'no-active'"></i></span>
            </div>
            <div class="layout z-2 contentH" @click.self="show = false" v-show="show" :style="{height: contentH + 'px'}">
                <ul class="select-grounp-1 clearfloat"  v-show="show">
                    <li v-for="(item,index) in gameNavList">
                         <a @click="toGame(item.id,item.label,item.value)" :class="{'active':cur == item.id}">
                        <i v-if="cur == item.id" class="iconfont icon-anonymous-iconfont gou"></i>{{item.label}}</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="contentH" :style="{height: contentH + 'px'}">
            <!--欢乐捕鱼 fish50-->
            <page-item :url="url"  v-if="value == 'fish50'" key="fish50">
                <template slot-scope="props">
                    <a class="mint-cell" v-for="(item,index) in props.data">
                        <div class="mint-cell-left"></div>
                        <div class="mint-cell-wrapper">
                            <div class="mint-cell-title">
                                <span class="mint-cell-text"><em class="c-3 f-mini">攻击对象：</em>{{item.title}}</span>
                                <span class="mint-cell-label">{{item.create_time}}</span>
                            </div>
                            <div class="mint-cell-value">
                                <span class="red" v-if="item.bouns > 0">奖金 +{{item.bouns}}</span>
                                <span class="c-2" v-else>失败</span>
                                <span class="mint-cell-label">消耗{{item.bet_money}}{{coin}}</span>
                            </div>
                        </div>
                    </a>
                </template>
            </page-item>

            <!--打地鼠 dishu-->
            <page-item :url="url" v-if="value == 'dishu'" key="dishu">
                <template slot-scope="props">
                    <a class="mint-cell" v-for="(item,index) in props.data">
                        <div class="mint-cell-left"></div>
                        <div class="mint-cell-wrapper">
                            <div class="mint-cell-title">
                                <span class="mint-cell-text"><em class="c-3 f-mini">击打地鼠：</em>{{item.playInfo.mouse}}</span>
                                <span class="mint-cell-label">{{item.create_time}}</span>
                            </div>
                            <div class="mint-cell-value">
                                <span class="red" v-if="item.bonus > 0">奖金 +{{item.bonus}}</span>
                                <span class="c-2" v-else>失败</span>
                                <span class="mint-cell-label">消耗{{item.bullet}}{{coin}}</span>
                            </div>
                        </div>
                    </a>
                </template>
            </page-item>

            <!--呆萌动物园 animal-->
            <page-item :url="url" v-if="value == 'animal'" key="animal">
                <template slot-scope="props">
                    <a class="mint-cell" v-for="(animal,index) in props.data">
                        <div class="mint-cell-left"></div>
                        <div class="mint-cell-wrapper">
                            <div class="mint-cell-title">
                                <span class="mint-cell-text"><em class="c-3 f-mini">击打动物：</em>{{animal.playInfo.animal}}</span>
                                <span class="mint-cell-label">{{animal.create_time}}</span>
                            </div>
                            <div class="mint-cell-value">
                                <span class="red" v-if="animal.bonus > 0">奖金 +{{animal.bonus}}</span>
                                <span class="c-2" v-else>失败</span>
                                <span class="mint-cell-label">消耗{{animal.bullet}}{{coin}}</span>
                            </div>
                        </div>
                    </a>
                </template>
            </page-item>

            <!--我要發 fa-->
            <page-item :url="url" v-if="value == 'fa'" key="fa">
                <template slot-scope="props">
                    <a class="mint-cell" v-for="(fa,index) in props.data">
                        <div class="mint-cell-left"></div>
                        <div class="mint-cell-wrapper">
                            <div class="mint-cell-title">
                                <span class="mint-cell-text"><em class="c-3 f-mini"></em>{{fa.playInfo.code}}</span>
                                <span class="mint-cell-label">{{fa.expect}}期 {{fa.create_time}}</span>
                            </div>
                            <div class="mint-cell-value">
                                <span class="red" v-if="fa.bonus > 0">奖金 +{{fa.bonus}}</span>
                                <span class="c-2" v-else>未中奖</span>
                                <span class="mint-cell-label">下注{{fa.money}}{{coin}}</span>
                            </div>
                        </div>
                    </a>
                </template>
            </page-item>

            <!--欢乐攻城 attack-->
            <page-item :url="url" v-if="value == 'attack'" key="attack">
                <template slot-scope="props">
                    <a class="mint-cell" v-for="(item,index) in props.data">
                        <div class="mint-cell-left"></div>
                        <div class="mint-cell-wrapper">
                            <div class="mint-cell-title">
                                <span class="mint-cell-text"><em class="c-3 f-mini"></em>{{item.enemy_betting}} vs {{item.betting}}</span>
                                <span class="mint-cell-label">
                                    <template v-if="item.playInfo == 25">人机对战</template>
                                    <template v-else>对手：{{item.playInfo.nickname}}</template>
                                </span>
                            </div>
                            <div class="mint-cell-value">
                                <span class="red" v-if="item.bonus > 0">奖金 +{{item.bonus}}</span>
                                <span class="c-2" v-else>对战失败</span>
                                <span class="mint-cell-label">出战{{item.money}}{{coin}}</span>
                            </div>
                        </div>
                    </a>
                </template>
            </page-item>

            <!--超级幸运星 lucky-->
            <page-item :url="url" v-if="value == 'lucky'" key="lucky">
                <template slot-scope="props">
                    <a class="mint-cell" v-for="(item,index) in props.data">
                        <div class="mint-cell-left"></div>
                        <div class="mint-cell-wrapper">
                            <div class="mint-cell-title">
                                <span class="mint-cell-text"><em class="c-3 f-mini">下注对象：</em>{{item.playInfo.code}}</span>
                                <span class="mint-cell-label">开奖信息：{{item.playInfo.open}}
                                </span>
                            </div>
                            <div class="mint-cell-value">
                                <span class="red" v-if="item.bouns > 0">奖金 +{{item.bouns}}</span>
                                <span class="c-2" v-else>--</span>
                                <span class="mint-cell-label">消耗{{item.money}}{{coin}}</span>
                            </div>
                        </div>
                    </a>
                </template>
            </page-item>

            <!--欢乐猜猜猜 poker-->
            <page-item :url="url" v-if="value == 'poker'" key="poker">
                <template slot-scope="props">
                    <a class="mint-cell" v-for="(item,index) in props.data">
                        <div class="mint-cell-left"></div>
                        <div class="mint-cell-wrapper">
                            <div class="mint-cell-title">
                                <span class="mint-cell-text">
                                    <em v-for="(info,index) in item.playInfo">{{info.msg}}</em>
                                </span>
                                <span class="mint-cell-label">{{item.create_time}}</span>
                            </div>
                            <div class="mint-cell-value">
                                <span class="red" v-if="item.bouns > 0">奖金 +{{item.bouns}}</span>
                                <span class="c-2" v-else>未中奖</span>
                                <span class="mint-cell-label">下注{{item.bet_money}}{{coin}}</span>
                            </div>
                        </div>
                    </a>
                </template>
            </page-item>

            <!--欢乐夹娃娃 zhuawawa-->
            <page-item :url="url" v-if="value == 'zhuawawa'" key="zhuawawa">
                <template slot-scope="props">
                    <a class="mint-cell" v-for="(item,index) in props.data">
                        <div class="mint-cell-left"></div>
                        <div class="mint-cell-wrapper">
                            <div class="mint-cell-title">
                                <span class="mint-cell-text"><em class="c-3 f-mini">抓取对象：</em>{{item.playInfo.gift}}</span>
                                <span class="mint-cell-label">{{item.create_time}}</span>
                            </div>
                            <div class="mint-cell-value">
                                <span class="suc" v-if="item.is_get == 1">抓取成功</span>
                                <span class="c-2" v-else>抓取失败</span>
                                <span class="mint-cell-label">消耗{{item.bet_money}}{{coin}}</span>
                            </div>
                        </div>
                    </a>
                </template>
            </page-item>

            <!--宝马奔驰 cars-->
            <page-item :url="url" v-if="value == 'cars'" key="cars">
                <template slot-scope="props">
                    <a class="mint-cell" v-for="(item,index) in props.data">
                        <div class="mint-cell-left"></div>
                        <div class="mint-cell-wrapper">
                            <div class="mint-cell-title">
                                <span class="mint-cell-text"><em class="c-3 f-mini">下注对象：</em>{{item.playInfo.code}}</span>
                                <span class="mint-cell-label">期号：{{item.issue}} 开奖结果：{{item.playInfo.open}}</span>
                            </div>
                            <div class="mint-cell-value">
                                <span class="red" v-if="item.bouns > 0">奖金 +{{item.bouns}}</span>
                                <span class="c-2" v-else>未中奖</span>
                                <span class="mint-cell-label">下注{{item.money}}{{coin}}</span>
                            </div>
                        </div>
                    </a>
                </template>
            </page-item>

            <!--幸运转盘 dzp-->
            <page-item :url="url" v-if="value == 'dzp'" key="dzp">
                <template slot-scope="props">
                    <a class="mint-cell" v-for="(item,index) in props.data">
                        <div class="mint-cell-left"></div>
                        <div class="mint-cell-wrapper">
                            <div class="mint-cell-title">
                                <span class="mint-cell-text"><em class="c-3 f-mini">获取物品：</em>{{item.name}}</span>
                                <span class="mint-cell-label">{{item.create_time}}</span>
                            </div>
                            <div class="mint-cell-value">
                                <span class="c-2 red" v-if="item.num > 0">数量 +{{item.num}}</span>
                            </div>
                        </div>
                    </a>
                </template>
            </page-item>
        </div>
    </div>
</template>
<script>
    import pageItem from 'components/common/PageItem.vue' //分页
    export default{
        components: {
            pageItem
        },
        data() {
            return {
                selected:'',
                cur :0 ,//选中项
                show:false,
                value:"" //游戏key
            };
        },
        computed:{
            gameId:{
                get(){
                    return this.$route.params.gameid
                },
                set(){}
            },
            gameNavList(){
                return this.$store.state.gameNav
            },
            url(){
                return '/web/details/gameother?gameid=' + this.gameId;
            },
            coin(){
                return this.$store.state.coinName;
            },
            //列表高度
            contentH(){
                return this.$store.state.clientHeight - 40
            },
        },
        methods:{
            //具体彩种
            toGame(gameid,select,value){
                this.gameId = gameid
                this.selected = select
                this.cur = gameid
                this.value = value
                this.$router.replace({
                    name: 'gameRecord',
                    params:{
                        gameid:gameid,
                    }
                });
                setTimeout(()=>{
                    this.show = false
                },200)
            },
            goBack(){
                this.$router.goBack(-1)
            }
        },
        mounted(){
            if(this.gameId){
                for(let i in this.gameNavList){
                    if(this.gameId == this.gameNavList[i].id){
                        this.$set(this,'selected',this.gameNavList[i].label)
                        this.$set(this,'cur',this.gameNavList[i].id)
                        this.$set(this,'value',this.gameNavList[i].value)
                    }
                }
            }else {
                this.$set(this,'selected',this.gameNavList[0].label)
                this.$set(this,'cur',this.gameNavList[0].id)
                this.$set(this,'gameid',this.gameNavList[0].id)
                this.$set(this,'value',this.gameNavList[0].value)
            }
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .detail{
        .mint-cell{
            .mint-cell-value{
                display: block;
                text-align: right;
            }
        }
    }
</style>
