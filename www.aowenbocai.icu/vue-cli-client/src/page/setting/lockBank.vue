<template>
    <div class="bank">
        <div class="input-title">请添加与您实名制相同姓名的银行卡（储蓄卡）</div>
        <div>
            <mt-field label="持卡人" placeholder="" v-model="lockInfo.is_name" type="text" disabled></mt-field>
            <mt-cell title="银行" class="chose-bank" is-link @click.native="popupVisible = !popupVisible">
                <span v-if="!bank.length" class="org">请选择银行</span>
                <span v-else >{{bank}}</span>
            </mt-cell>
            <mt-popup
                v-model="popupVisible"
                position="bottom" class="chose-bank">
                <h3 class="border-bottom-1px" style="padding-bottom: 10px;margin-bottom: 10px">请选择银行</h3>
                <ul class="bank-list">
                    <li v-for="(item,index) in banks" @click="choseBank(item.name)" :class="{'active': item.name == bank}">
                        <svg class="icon" aria-hidden="true">
                            <use :xlink:href="'#' + item.icon"></use>
                        </svg>
                        <span>{{item.name}}</span>
                    </li>
                </ul>
            </mt-popup>
            <mt-field v-if="bank == '其他'" label="银行名称" placeholder="请输入银行名称" v-model = "editName" type="text"></mt-field>
            <mt-field label="银行卡号" placeholder="请输入银行卡卡号" v-model = "num" type="text"></mt-field>
        </div>
        <div class="btn-box">
            <mt-button @click.native="sumbit" type="danger" size="large">
                <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                保 存
            </mt-button>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                loading:false,
                bank:'',
                num:'',
                editName:'',
                popupVisible: false,
                banks:[{name:'中国工商银行',icon:'icon-zhongguogongshangyinhang'},
                    {name:'中国农业银行',icon:'icon-nongyeyinhang-'},
                    {name:'中国银行',icon:'icon-boc'},
                    {name:'中国建设银行',icon:'icon-jiansheyinhang1'},
                    {name:'招商银行',icon:'icon-zhaoshang'},
                    {name:'交通银行',icon:'icon-jiaotongyinhang-'},
                    {name:'中国邮政',icon:'icon-zhongguoyouzheng'},
                    {name:'兴业银行',icon:'icon-xingyeyinhang'},
                    {name:'浦发银行',icon:'icon-pufayinhang'},
                    {name:'中国民生银行',icon:'icon-icon_zhongguominshengyinhang'},
                    {name:'中信银行',icon:'icon-zhongxinyinhang'},
                    {name:'中国光大银行',icon:'icon-ziyuan'},
                    {name:'华夏银行',icon:'icon-huaxiayinhang'},
                    {name:'平安银行',icon:'icon-pinganyinhang'},
                    {name:'广发银行|CGB',icon:'icon-guangfayinhang'},
                    {name:'其他',icon:'icon-qita'}
                ],
            }
        },
        computed:{
            lockInfo(){
                return this.$store.state.lockInfo
            }
        },
        methods:{
            //选择银行
            choseBank(name) {
                this.bank = name;
                this.popupVisible = !this.popupVisible;
            },
            //提交
            sumbit(){
                if(!this.bank){
                    this.$toast('请选择银行');
                    return;
                }
                if(this.bank == '其他' && !this.editName) {
                    this.$toast('银行名称不能为空');
                    return;
                }
                if(!this.num) {
                    this.$toast('银行卡号不能为空');
                    return;
                }
                this.loading = true
                this.$axios.post('/web/user/lockBank',{
                    type: 1,
                    name: '银行卡',
                    openname: this.bank == '其他' ? this.editName : this.bank,
                    numbers: this.num
                }).then(({data}) =>{
                    if(!data.err){
                        this.$toast(data.msg)
                        this.$store.dispatch('getLockInfo')//更新用户绑定信息
                        this.$store.dispatch('getBanks')//更新绑定银行卡信息
                        setTimeout(()=>{
                            this.$router.goBack(-1);
                        },1000)
                    }else {
                        this.$messagebox('提示',data.msg);
                    }
                    this.loading = false
                }).catch(function (error) {
                    console.log(error);
                })
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped type="text/scss">
    .bank-list{
        li{
            float: left;
            width: 46%;
            margin: 2px 2%;
            height: 35px;
            line-height: 34px;
            padding: 0 5px;
            border:1px solid #eeeeee;
            @include rounded(5px);
            font-size: 14px;
            &.active{
                border:1px solid $bColor;
            }
            .icon{
                width: 20px;
                height: 20px;
                vertical-align: -4px;
            }
        }
    }
</style>
