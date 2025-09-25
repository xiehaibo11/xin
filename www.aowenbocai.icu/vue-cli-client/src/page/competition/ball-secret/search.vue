<template>
    <div>
        <div class="searchBar flex-box">
            <i class="mintui mintui-back" @click="back"></i>
            <div class="seaInput flex-box">
                <i class="mintui mintui-search c-3"></i>
                <input placeholder="请输入红人昵称" type="text" class="flex" v-model="words" @keyup.enter="doSearch">
            </div>
            <a class="search_clear" @click="doSearch">搜索</a>
        </div>
        <div class="contentH" :style="{'height':contentH + 'px'}">
            <div class="bg_fff">
                <h3 class="search_title">热搜：</h3>
                <ul class="search_hot cf">
                    <li>卡卡解盘</li>
                    <li>吴可荐彩</li>
                    <li>支羽解盘</li>
                    <li>剑叔jingcai</li>
                    <li>许健</li>
                    <li>匣内金刀</li>
                    <li>戴维</li>
                    <li>明哥</li>
                </ul>
            </div>
            <template v-if="historyWords.length">
                <div class="bg_fff mt-sm">
                    <h3 class="search_title border-bottom-1px">历史搜索：</h3>
                    <ul class="cf search_history">
                        <li v-for="item in historyWords" class="border-bottom-1px">{{item}}</li>
                    </ul>
                </div>
                <div class="btn-box">
                    <mt-button type="danger" size="large" @click.native="clearHistory">清空历史记录</mt-button>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
                words: '',
                historyWords: JSON.parse(sessionStorage.getItem("m_jc_search_words")) || [],
            }
        },
        computed:{
            //屏幕高度
            contentH(){
                return  this.$store.state.clientHeight - 65
            }
        },
        methods:{
            //返回
            back(){
                this.$router.goBack(-1);//返回上一层
            },
            //搜索
            doSearch(){
                let n = this.historyWords.indexOf(this.words)
                if(n> -1){
                    this.historyWords.splice(n,1)
                }
                this.historyWords.unshift(this.words);
                this.historyWords.splice(9,this.historyWords.length - 9) //最多存储8个历史记录
                sessionStorage.setItem('m_jc_search_words',JSON.stringify(this.historyWords));
            },
            //清空历史记录
            clearHistory(){
                this.$set(this,'historyWords',[]);
                sessionStorage.removeItem('m_jc_search_words')
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .mint-button--danger {
        color: #949494;
        background-color: #dedede;
    }
    .searchBar {
        position: relative;
        padding: 15px;
        background: $bColor;
        i.mintui-back{
            color: #ffffff;
        }
        .seaInput{
            height: 35px;
            background: #fff;
            border-radius: 3px;
            padding: 0 10px;
            margin-left: 15px;
            i{
                vertical-align: middle;
                margin-top: 1px;
                margin-right: 0;
            }
            input{
                display: inline-block;
                vertical-align: middle;
                width: 86%;
                padding-left: 5px;
                height: 34px;
                color: #111;
                font-size: 14px;
                line-height: 16px;
                border: 0;
            }
        }
        .search_clear{
            width: 40px;
            height: 35px;
            line-height: 35px;
            text-align: right;
            font-size: 15px;
            color: #fff;
        }
    }
    .bg_fff{
        background-color: #ffffff;
        .search_title{
            height: 40px;
            line-height: 40px;
            padding-left: 20px;
            color: #333333;
            font-size: 14px;
        }
        .search_hot{
            padding: 0 15px 8px;
            li{
                white-space: nowrap;
                overflow: hidden;
                float: left;
                margin-right: 2.8%;
                width: 31.46%;
                height: 30px;
                line-height: 30px;
                background-color: #f5f5f5;
                color: #666;
                font-size: 13px;
                text-align: center;
                margin-bottom: 10px;
                border-radius: 3px;
                &:nth-child(3n) {
                    margin-right: 0;
                }
            }
        }
        .search_history{
            padding: 0 15px;
            li{
                padding: 15px;
                text-align: left;
                color: #333333;
            }
        }
    }
</style>
