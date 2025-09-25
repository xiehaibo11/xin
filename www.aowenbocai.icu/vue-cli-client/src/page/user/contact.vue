<template>
    <div>
        <mt-cell v-if="setting.site_name" title="网站名称" :value="setting.site_name"></mt-cell>
        <mt-cell v-if="setting.company_tel" title="客服电话">
            <a :href="'tel:'+ setting.company_tel">{{setting.company_tel}}</a>
        </mt-cell>
        <mt-cell v-if="setting.company_qq" title="客服QQ">
            <span>
                <a :href="'http://wpa.qq.com/msgrd?v=3&uin='+ setting.qq_num + '&site=' + setting.site_name + '&menu=yes'" target="_blank">{{setting.company_qq}}</a>
            </span>
        </mt-cell>
        <mt-cell v-if="setting.company_wx" title="客服微信" :value="setting.company_wx"></mt-cell>
    </div>
</template>
<script>
    export default{
        data() {
            return {

            };
        },
        computed:{
            setting(){
                return this.$store.state.setting
            }
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .bag{
        li{
            padding: 15px;
            .img{
                position: relative;
                margin-right: 15px;
                span{
                    display: inline-block;
                    width:80%;
                    background-color: rgba(0, 0, 0, 0.66);
                    position: absolute;
                    color: #ffffff;
                    bottom: 0;
                    right: 0px;
                    text-align: center;
                    margin-right: 10%;
                    border-radius: 10px;
                }
                img{
                    width: 94px;
                    height: 94px;
                }
            }
            .info{
                .desc{
                    line-height: 1.4;
                    padding: 6px 0;
                    height: 44px;
                }
            }
            .mint-button--small{
                height: 28px;
            }

        }
    }
</style>
