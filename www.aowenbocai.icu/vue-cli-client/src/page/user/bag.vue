<template>
    <div>
        <ul class="clearfloat bag card">
            <li v-if="bagList.length" v-for="(item,index) in bagList" :key="index" class="flex-box flex-start border-bottom-1px">
                <div class="img">
                    <img :src="item.pic" alt="">
                    <span class="f-mini">{{item.num}}</span>
                </div>
                <div class="info">
                    <p>{{item.name}} <em class="f-mini">(剩余<em class="red">{{item.num}}</em>张)</em></p>
                    <p class="c-3 f-mini desc">{{item.desc}}</p>
                    <a :href="item.ext_name"><mt-button  type="primary" size="small">立即使用</mt-button> </a>
                </div>
            </li>
            <li v-else class="tc c-3 f-sm" style="padding: 8px">暂无相关数据</li>
        </ul>
    </div>
</template>
<script>
    export default{
        data() {
            return {

            };
        },
        computed:{
            bagList(){
                return this.$store.state.bag
            }
        },
        created(){
            this.$store.dispatch('getBagList')
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
