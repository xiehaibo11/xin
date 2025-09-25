<template>
    <div class="lottery-bg">
        <div class="loading" v-if="loading">
            <mt-spinner :type="3"></mt-spinner>
        </div>
        <div v-if="!loading" class="lottery-box">
            <stop-sale-tips :is-stop="isStop"></stop-sale-tips>
            <!--顶部-->
            <div class="head">
                <mt-header>
                    <mt-button icon="back" slot="left" @click.native="$router.goBack(-1)"></mt-button>
                    <mt-button icon="more" slot="right" @click.native = "showMore = !showMore;show = false"></mt-button>
                </mt-header>
                <div class="select flex-box">
                    <span class="f-mini label">玩法</span>
                    <span class="chose" @click="show = !show">{{selected}}<em v-if="type != 7 && type != 8">-{{topText}}</em><i class="iconfont icon-jiantou yellow" :class="show ? 'is-active':'no-active'"></i></span>
                </div>
                <transition name="fade">
                <div class="layout z-3" @click.self="showMore = false" v-show="showMore">
                    <div class="head-list border-1px">
                        <ul >
                            <li class="border-bottom-1px"  @click="showPlayInfo">中奖说明</li>
                            <li class="border-bottom-1px" @click="toPlayNews">玩法介绍</li>
                            <li class="border-bottom-1px" @click="toHistoryCode">历史开奖</li>
                            <li class="border-bottom-1px" @click="toTrade">走势图</li>
                            <li class="border-bottom-1px" @click="toRecord">投注记录</li>
                        </ul>
                    </div>
                </div>
                </transition>
                <div class="layout z-2 contentH" @click.self="show = false" v-show="show" :style="{height:selctHeight + 'px'}">
                    <div class="select-grounp">
                        <ul class="clearfloat bet-fir">
                            <template v-for="(item,index) in play">
                                <li @click="chosePlay(item.name,item.type,item.gain)" :class="{'active':type == item.type}">{{item.name}}</li>
                            </template>
                        </ul>
                        <template v-if="type == 1"><!--五星-->
                            <div class="bet-list" v-if="small[1][1].isOpen !== 1 || small[1][3].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">通选</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio1 == 1}" @click="radio1 = 1;show = false" v-if="small[1][1].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio1 == 3}" @click="radio1 = 3;show = false" v-if="small[1][3].isOpen !== 1">单式</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="bet-list" v-if="small[1][2].isOpen !== 1 || small[1][4].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">直选</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio1 == 2}" @click="radio1 = 2;show = false" v-if="small[1][2].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio1 == 4}" @click="radio1 = 4;show = false" v-if="small[1][4].isOpen !== 1">单式</li>
                                    </ul>
                                </div>
                            </div>
                        </template>
                        <template v-if="type == 2"><!--前三-->
                            <div class="bet-list" v-if="small[2][1].isOpen !== 1 || small[2][2].isOpen !== 1 || small[2][11].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">直选</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio2 == 1}" @click="radio2 = 1;show = false" v-if="small[2][1].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio2 == 11}" @click="radio2 = 11;show = false" v-if="small[2][11].isOpen !== 1">单式</li>
                                        <li :class="{'active': radio2 == 2}" @click="radio2 = 2;show = false" v-if="small[2][2].isOpen !== 1">和值</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="bet-list" v-if="small[2][3].isOpen !== 1 || small[2][4].isOpen !== 1 || small[2][5].isOpen !== 1 || small[2][12].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">组三</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio2 == 3}" @click="radio2 = 3;show = false" v-if="small[2][3].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio2 == 12}" @click="radio2 = 12;show = false" v-if="small[2][12].isOpen !== 1">单式</li>
                                        <li :class="{'active': radio2 == 4}" @click="radio2 = 4;show = false" v-if="small[2][4].isOpen !== 1">胆拖</li>
                                        <li :class="{'active': radio2 == 5}" @click="radio2 = 5;show = false" v-if="small[2][5].isOpen !== 1">和值</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="bet-list" v-if="small[2][6].isOpen !== 1 || small[2][7].isOpen !== 1 || small[2][8].isOpen !== 1 || small[2][13].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">组六</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio2 == 6}" @click="radio2 = 6;show = false" v-if="small[2][6].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio2 == 13}" @click="radio2 = 13;show = false" v-if="small[2][13].isOpen !== 1">单式</li>
                                        <li :class="{'active': radio2 == 7}" @click="radio2 = 7;show = false" v-if="small[2][7].isOpen !== 1">胆拖</li>
                                        <li :class="{'active': radio2 == 8}" @click="radio2 = 8;show = false" v-if="small[2][8].isOpen !== 1">和值</li>
                                    </ul>
                                </div>
                            </div>
                        </template>
                        <template v-if="type == 3"><!--中三-->
                            <div class="bet-list" v-if="small[3][1].isOpen !== 1 || small[3][2].isOpen !== 1 || small[3][11].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">直选</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio3 == 1}" @click="radio3 = 1;show = false" v-if="small[3][1].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio3 == 11}" @click="radio3 = 11;show = false" v-if="small[3][11].isOpen !== 1">单式</li>
                                        <li :class="{'active': radio3 == 2}" @click="radio3 = 2;show = false" v-if="small[3][2].isOpen !== 1">和值</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="bet-list" v-if="small[3][3].isOpen !== 1 || small[3][4].isOpen !== 1 || small[3][5].isOpen !== 1 || small[3][12].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">组三</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio3 == 3}" @click="radio3 = 3;show = false" v-if="small[3][3].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio3 == 12}" @click="radio3 = 12;show = false" v-if="small[3][12].isOpen !== 1">单式</li>
                                        <li :class="{'active': radio3 == 4}" @click="radio3 = 4;show = false" v-if="small[3][4].isOpen !== 1">胆拖</li>
                                        <li :class="{'active': radio3 == 5}" @click="radio3 = 5;show = false" v-if="small[3][5].isOpen !== 1">和值</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="bet-list" v-if="small[3][6].isOpen !== 1 || small[3][7].isOpen !== 1 || small[3][8].isOpen !== 1 || small[3][13].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">组六</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio3 == 6}" @click="radio3 = 6;show = false" v-if="small[3][6].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio3 == 13}" @click="radio3 = 13;show = false" v-if="small[3][13].isOpen !== 1">单式</li>
                                        <li :class="{'active': radio3 == 7}" @click="radio3 = 7;show = false" v-if="small[3][7].isOpen !== 1">胆拖</li>
                                        <li :class="{'active': radio3 == 8}" @click="radio3 = 8;show = false" v-if="small[3][8].isOpen !== 1">和值</li>
                                    </ul>
                                </div>
                            </div>
                        </template>
                        <template v-if="type == 4"><!--后三-->
                            <div class="bet-list" v-if="small[4][1].isOpen !== 1 || small[4][2].isOpen !== 1 || small[4][11].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">直选</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio4 == 1}" @click="radio4 = 1;show = false" v-if="small[4][1].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio4 == 11}" @click="radio4 = 11;show = false" v-if="small[4][11].isOpen !== 1">单式</li>
                                        <li :class="{'active': radio4 == 2}" @click="radio4 = 2;show = false" v-if="small[4][2].isOpen !== 1">和值</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="bet-list" v-if="small[4][3].isOpen !== 1 || small[4][4].isOpen !== 1 || small[4][5].isOpen !== 1 || small[4][12].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">组三</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio4 == 3}" @click="radio4 = 3;show = false" v-if="small[4][3].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio4 == 12}" @click="radio4 = 12;show = false" v-if="small[4][12].isOpen !== 1">单式</li>
                                        <li :class="{'active': radio4 == 4}" @click="radio4 = 4;show = false" v-if="small[4][4].isOpen !== 1">胆拖</li>
                                        <li :class="{'active': radio4 == 5}" @click="radio4 = 5;show = false" v-if="small[4][5].isOpen !== 1">和值</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="bet-list" v-if="small[4][6].isOpen !== 1 || small[4][7].isOpen !== 1 || small[4][8].isOpen !== 1 || small[4][13].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">组六</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio4 == 6}" @click="radio4 = 6;show = false" v-if="small[4][6].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio4 == 13}" @click="radio4 = 13;show = false" v-if="small[4][13].isOpen !== 1">单式</li>
                                        <li :class="{'active': radio4 == 7}" @click="radio4 = 7;show = false" v-if="small[4][7].isOpen !== 1">胆拖</li>
                                        <li :class="{'active': radio4 == 8}" @click="radio4 = 8;show = false" v-if="small[4][8].isOpen !== 1">和值</li>
                                    </ul>
                                </div>
                            </div>
                        </template>
                        <template v-if="type == 10"><!--前二-->
                            <div class="bet-list" v-if="small[10][1].isOpen !== 1 || small[10][2].isOpen !== 1 || small[10][5].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">直选</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio10 == 1}" @click="radio10 = 1;show = false" v-if="small[10][1].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio10 == 5}" @click="radio10 = 5;show = false" v-if="small[10][5].isOpen !== 1">单式</li>
                                        <li :class="{'active': radio10 == 2}" @click="radio10 = 2;show = false" v-if="small[10][2].isOpen !== 1">和值</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="bet-list" v-if="small[10][3].isOpen !== 1 || small[10][4].isOpen !== 1 || small[10][6].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">组选</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio10 == 3}" @click="radio10 = 3;show = false" v-if="small[10][3].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio10 == 6}" @click="radio10 = 6;show = false" v-if="small[10][6].isOpen !== 1">单式</li>
                                        <li :class="{'active': radio10 == 4}" @click="radio10 = 4;show = false" v-if="small[10][4].isOpen !== 1">和值</li>
                                    </ul>
                                </div>
                            </div>
                        </template>
                        <template v-if="type == 5"><!--后二-->
                            <div class="bet-list" v-if="small[5][1].isOpen !== 1 || small[5][2].isOpen !== 1 || small[5][5].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">直选</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio5 == 1}" @click="radio5 = 1;show = false" v-if="small[5][1].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio5 == 5}" @click="radio5 = 5;show = false" v-if="small[5][5].isOpen !== 1">单式</li>
                                        <li :class="{'active': radio5 == 2}" @click="radio5 = 2;show = false" v-if="small[5][2].isOpen !== 1">和值</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="bet-list" v-if="small[5][3].isOpen !== 1 || small[5][4].isOpen !== 1 || small[5][6].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">组选</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio5 == 3}" @click="radio5 = 3;show = false" v-if="small[5][3].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio5 == 6}" @click="radio5 = 6;show = false" v-if="small[5][6].isOpen !== 1">单式</li>
                                        <li :class="{'active': radio5 == 4}" @click="radio5 = 4;show = false" v-if="small[5][4].isOpen !== 1">和值</li>
                                    </ul>
                                </div>
                            </div>
                        </template>
                        <template v-if="type == 6"><!--一星-->
                            <div class="bet-list" v-if="small[6][1].isOpen !== 1 || small[6][2].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">一星</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio6 == 1}" @click="radio6 = 1;show = false" v-if="small[6][1].isOpen !== 1">复式</li>
                                        <li :class="{'active': radio6 == 2}" @click="radio6 = 2;show = false" v-if="small[6][2].isOpen !== 1">单式</li>
                                    </ul>
                                </div>
                            </div>
                        </template>
                        <template v-if="type == 7"><!--大小单双-->
                            <div class="bet-list" v-if="small[7][1].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">大小单双</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio7 == 1}" @click="radio7 = 1;show = false" v-if="small[7][1].isOpen !== 1">复式</li>
                                    </ul>
                                </div>
                            </div>
                        </template>
                        <template v-if="type == 8"><!--定位胆-->
                            <div class="bet-list" v-if="small[8][1].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">定位胆</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio8 == 1}" @click="radio8 = 1;show = false" v-if="small[8][1].isOpen !== 1">复式</li>
                                    </ul>
                                </div>
                            </div>
                        </template>
                        <template v-if="type == 9"><!--龙虎-->
                            <div class="bet-list" v-if="small[9][1].isOpen !== 1 || small[9][2].isOpen !== 1 || small[9][3].isOpen !== 1 || small[9][4].isOpen !== 1 || small[9][5].isOpen !== 1 ||
                            small[9][6].isOpen !== 1 || small[9][7].isOpen !== 1 || small[9][8].isOpen !== 1 || small[9][9].isOpen !== 1 || small[9][11].isOpen !== 1">
                                <div class="flex-box flex-start chose-l">
                                    <span class="l-tit">龙虎</span>
                                    <ul class="l-chose flex clearfloat">
                                        <li :class="{'active': radio9 == 1}" @click="radio9 = 1;show = false" v-if="small[9][1].isOpen !== 1">万千</li>
                                        <li :class="{'active': radio9 == 2}" @click="radio9 = 2;show = false" v-if="small[9][2].isOpen !== 1">万百</li>
                                        <li :class="{'active': radio9 == 3}" @click="radio9 = 3;show = false" v-if="small[9][3].isOpen !== 1">万十</li>
                                        <li :class="{'active': radio9 == 4}" @click="radio9 = 4;show = false" v-if="small[9][4].isOpen !== 1">万个</li>
                                        <li :class="{'active': radio9 == 5}" @click="radio9 = 5;show = false" v-if="small[9][5].isOpen !== 1">千百</li>
                                        <li :class="{'active': radio9 == 6}" @click="radio9 = 6;show = false" v-if="small[9][6].isOpen !== 1">千十</li>
                                        <li :class="{'active': radio9 == 7}" @click="radio9 = 7;show = false" v-if="small[9][7].isOpen !== 1">千个</li>
                                        <li :class="{'active': radio9 == 8}" @click="radio9 = 8;show = false" v-if="small[9][8].isOpen !== 1">百十</li>
                                        <li :class="{'active': radio9 == 9}" @click="radio9 = 9;show = false" v-if="small[9][9].isOpen !== 1">百个</li>
                                        <li :class="{'active': radio9 == 11}" @click="radio9 = 11;show = false" v-if="small[9][11].isOpen !== 1">十个</li>
                                    </ul>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <!--顶部 end-->
            <!--倒计时+ 期号 + 近期开奖 start-->
            <down-time></down-time>
            <!--倒计时+ 期号 + 近期开奖 end-->
            <!--投注操作-->
            <div class="bet-cont">
                <!--5星-->
                <div class="bet-item" v-show="type == 1">
                    <ssc-fs v-show="radio1 == 1" :type="sign" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-fs> <!--直选 复式-->
                    <ssc-fs v-show="radio1 == 2" :type="sign" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-fs> <!--sign 复式-->
                    <ssc-copy-bet v-show="radio1 == 3" :type="sign" :n="5" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--直选 单式-->
                    <ssc-copy-bet v-show="radio1 == 4" :type="sign" :n="5" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--通选 单式-->
                </div>
                <!--前三-->
                <div class="bet-item" v-show="type == 2">
                    <ssc-fs v-show="radio2 == 1" :type="sign" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-fs><!--直选 复式-->
                    <ssc-hz v-show="radio2 == 2" :type="sign" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-hz><!--直选 和值-->
                    <ssc-zx v-show="radio2 == 3" :type="sign" :number="2" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-zx><!--组三 复式-->
                    <ssc-dt v-show="radio2 == 4" :type="sign" :number="2" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-dt><!--组三 胆拖-->
                    <ssc-hz v-show="radio2 == 5" :type="sign" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-hz><!--组三 和值-->
                    <ssc-zx v-show="radio2 == 6"  :type="sign" :number="3" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-zx><!--组六 复式-->
                    <ssc-dt v-show="radio2 == 7"  :type="sign" :number="3" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-dt><!--组六 胆拖-->
                    <ssc-hz v-show="radio2 == 8"  :type="sign" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-hz><!--组六 和值-->
                    <ssc-copy-bet v-show="radio2 == 11" :type="sign" :n="3" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--直选 单式-->
                    <ssc-copy-bet v-show="radio2 == 12" :type="sign" :n="2" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--组三 单式-->
                    <ssc-copy-bet v-show="radio2 == 13" :type="sign" :n="3" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--组六 单式-->
                </div>
                <!--中三-->
                <div class="bet-item" v-show="type == 3">
                    <ssc-fs v-show="radio3 == 1" :type="sign" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-fs><!--直选 复式-->
                    <ssc-hz v-show="radio3 == 2" :type="sign" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-hz><!--直选 和值-->
                    <ssc-zx v-show="radio3 == 3" :type="sign" :number="2" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-zx><!--组三 复式-->
                    <ssc-dt v-show="radio3 == 4" :type="sign" :number="2" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-dt><!--组三 胆拖-->
                    <ssc-hz v-show="radio3 == 5" :type="sign" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-hz><!--组三 和值-->
                    <ssc-zx v-show="radio3 == 6"  :type="sign" :number="3" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-zx><!--组六 复式-->
                    <ssc-dt v-show="radio3 == 7"  :type="sign" :number="3" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-dt><!--组六 胆拖-->
                    <ssc-hz v-show="radio3 == 8"  :type="sign" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-hz><!--组六 和值-->
                    <ssc-copy-bet v-show="radio3 == 11" :type="sign" :n="3" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--直选 单式-->
                    <ssc-copy-bet v-show="radio3 == 12" :type="sign" :n="2" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--组三 单式-->
                    <ssc-copy-bet v-show="radio3 == 13" :type="sign" :n="3" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--组六 单式-->
                </div>
                <!--后三-->
                <div class="bet-item" v-show="type == 4">
                    <ssc-fs v-show="radio4 == 1" :type="sign" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-fs><!--直选 复式-->
                    <ssc-hz v-show="radio4 == 2" :type="sign" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-hz><!--直选 和值-->
                    <ssc-zx v-show="radio4 == 3" :type="sign" :number="2" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-zx><!--组三 复式-->
                    <ssc-dt v-show="radio4 == 4" :type="sign" :number="2" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-dt><!--组三 胆拖-->
                    <ssc-hz v-show="radio4 == 5" :type="sign" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-hz><!--组三 和值-->
                    <ssc-zx v-show="radio4 == 6"  :type="sign" :number="3" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-zx><!--组六 复式-->
                    <ssc-dt v-show="radio4 == 7"  :type="sign" :number="3" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-dt><!--组六 胆拖-->
                    <ssc-hz v-show="radio4 == 8"  :type="sign" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-hz><!--组六 和值-->
                    <ssc-copy-bet v-show="radio4 == 11" :type="sign" :n="3" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--直选 单式-->
                    <ssc-copy-bet v-show="radio4 == 12" :type="sign" :n="2" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--组三 单式-->
                    <ssc-copy-bet v-show="radio4 == 13" :type="sign" :n="3" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--组六 单式-->
                </div>

                <!--前二-->
                <div class="bet-item" v-show="type == 10">
                    <ssc-fs v-show="radio10 == 1" :type="sign" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-fs><!--直选 复式-->
                    <ssc-hz v-show="radio10 == 2" :type="sign" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-hz><!--直选 和值-->
                    <ssc-zx v-show="radio10 == 3" :type="sign" :number="2" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-zx><!--组选 复式-->
                    <ssc-hz v-show="radio10 == 4" :type="sign" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-hz><!--组选 和值-->
                    <ssc-copy-bet v-show="radio10 == 5" :type="sign" :n="2" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--直选 单式-->
                    <ssc-copy-bet v-show="radio10 == 6" :type="sign" :n="2" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--组选 单式-->
                </div>
                <!--后二-->
                <div class="bet-item" v-show="type == 5">
                    <ssc-fs v-show="radio5 == 1" :type="sign" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-fs><!--直选 复式-->
                    <ssc-hz v-show="radio5 == 2" :type="sign" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-hz><!--直选 和值-->
                    <ssc-zx v-show="radio5 == 3" :type="sign" :number="2" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-zx><!--组选 复式-->
                    <ssc-hz v-show="radio5 == 4" :type="sign" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-hz><!--组选 和值-->
                    <ssc-copy-bet v-show="radio5 == 5" :type="sign" :n="2" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--直选 单式-->
                    <ssc-copy-bet v-show="radio5 == 6" :type="sign" :n="2" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--组选 单式-->
                </div>

                <!--一星-->
                <div class="bet-item" v-show="type == 6">
                    <ssc-fs v-show="radio6 == 1" :type="sign" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-fs><!--复式-->
                    <ssc-copy-bet v-show="radio6 == 2" :type="sign" :n="1" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-copy-bet> <!--单式-->
                </div>

                <!--大小单双-->
                <div class="bet-item" v-show="type == 7">
                    <ssc-dxds v-show="radio7 == 1" :type="sign" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-dxds>
                </div>

                <!--定位胆-->
                <div class="bet-item" v-show="type == 8">
                    <ssc-fs v-show="radio8 == 1" :type="sign" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></ssc-fs>
                </div>
                <!--龙虎-->
                <div class="bet-item" v-show="type == 9">
                    <ssc-lh v-show="radio9 == 1" :type="sign" :gain="lhGain" :title="title" :text="text" :name="name" :bet-model="betModel" @change-gain="getHhGain" :max-gain="maxGain"></ssc-lh><!-- 万千-->
                    <ssc-lh v-show="radio9 == 2" :type="sign" :gain="lhGain" :title="title" :text="text" :name="name" :bet-model="betModel" @change-gain="getHhGain" :max-gain="maxGain"></ssc-lh><!-- 万百-->
                    <ssc-lh v-show="radio9 == 3" :type="sign" :gain="lhGain" :title="title" :text="text" :name="name" :bet-model="betModel" @change-gain="getHhGain" :max-gain="maxGain"></ssc-lh><!-- 万十-->
                    <ssc-lh v-show="radio9 == 4" :type="sign" :gain="lhGain" :title="title" :text="text" :name="name" :bet-model="betModel" @change-gain="getHhGain" :max-gain="maxGain"></ssc-lh><!-- 万个-->
                    <ssc-lh v-show="radio9 == 5" :type="sign" :gain="lhGain" :title="title" :text="text" :name="name" :bet-model="betModel" @change-gain="getHhGain" :max-gain="maxGain"></ssc-lh><!-- 千百-->
                    <ssc-lh v-show="radio9 == 6" :type="sign" :gain="lhGain" :title="title" :text="text" :name="name" :bet-model="betModel" @change-gain="getHhGain" :max-gain="maxGain"></ssc-lh><!-- 千十-->
                    <ssc-lh v-show="radio9 == 7" :type="sign" :gain="lhGain" :title="title" :text="text" :name="name" :bet-model="betModel" @change-gain="getHhGain" :max-gain="maxGain"></ssc-lh><!-- 千个-->
                    <ssc-lh v-show="radio9 == 8" :type="sign" :gain="lhGain" :title="title" :text="text" :name="name" :bet-model="betModel" @change-gain="getHhGain" :max-gain="maxGain"></ssc-lh><!-- 百十-->
                    <ssc-lh v-show="radio9 == 9" :type="sign" :gain="lhGain" :title="title" :text="text" :name="name" :bet-model="betModel" @change-gain="getHhGain" :max-gain="maxGain"></ssc-lh><!-- 百个-->
                    <ssc-lh v-show="radio9 == 11" :type="sign" :gain="lhGain" :title="title" :text="text" :name="name" :bet-model="betModel" @change-gain="getHhGain" :max-gain="maxGain"></ssc-lh><!-- 十个-->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import DownTime from 'components/lottery/DownTime.vue' //顶部期号+ 倒计时组件
    import RecentOpen from 'components/lottery/RecentOpen.vue' //近期开奖
    import SscFs from 'components/lottery/ssc/SscFs.vue' //复式投注
    import SscZx from 'components/lottery/ssc/SscZx.vue' //组选投注
    import SscDxds from 'components/lottery/ssc/SscDxds.vue' //大小单双
    import SscDt from 'components/lottery/ssc/SscDt.vue' //胆拖投注
    import SscHz from 'components/lottery/ssc/SscHz.vue' //和值投注
    import SscLh from 'components/lottery/ssc/SscLh.vue' //龙虎投注
    import SscCopyBet from 'components/lottery/ssc/CopyBet.vue' //单式
    import StopSaleTips from 'components/lottery/StopSaleTips.vue' //暂停销售
    export default {
        name:'ssc',
        data () {
            return {
                betModel:1,
                upUserRebate:'', //上级
                userRebate:'', //自己
                bonus_base:'', //网站

                show:false, //选择玩法
                showMore:false, //右侧列表
                selected:'',//选中的玩法名
                gain:'',//选中的奖金设置值
                type:'',//选中的玩法type值
                title:'',
                small:'',//玩法开启否
                isStop:false,

                loading: true,
                play:[],
                info:{}, //初始数据

                hhGain:'',//龙虎奖金组字符串

                radio1 : 1,
                radio2 : 1,
                radio3 : 1,
                radio4 : 1,
                radio5 : 1,
                radio6 : 1,
                radio7 : 1,
                radio8 : 1,
                radio9 : 1,
                radio10 : 1,
            }
        },
        components:{
            DownTime,
            RecentOpen,
            SscFs,
            SscZx,
            SscDxds,
            SscDt,
            SscHz,
            SscLh,
            SscCopyBet,
            StopSaleTips
        },
        computed:{
            //期号类型
            expectType(){
                return this.$store.state.lottery.expect_type
            },
            miss(){
                return this.$store.state.lottery.miss
            },
            lhGain(){
                for(let i in this.play){
                    if(this.play[i].type  == 9){
                        return this.play[i].gain
                    }else {
                        return 0
                    }
                }
            },
            //返点设置是否开启
            rebateIsOpen(){
                return this.$store.state.setting.rebate_isOpen == 1 ? true : false
            },
            //比例
            scale(){
                return this.$store.getters.getScale
            },
            //最高奖金计算百分比
            maxRebate () {
                return this.$store.getters.maxRebate
            },
            //返点后最高奖金
            maxGain () {
                let a = this.betModel == 1 ? 1 : 2; //模式2下奖金减半
                const valObj = {
                    '1': this.radio1,
                    '2': this.radio2,
                    '3': this.radio3,
                    '4': this.radio4,
                    '5': this.radio5,
                    '6': this.radio6,
                    '7': this.radio7,
                    '8': this.radio8,
                    '9': this.radio9,
                    '10': this.radio10,
                }
                let gain = this.small[this.type][valObj[this.type]].gain
                let arr = this.type == 9 ? this.hhGain.length ? this.hhGain.split(",") : gain.split(",") : gain.split(",")
                let s = this.$bet.getMaxMin(arr,'max')
                let res = this.rebateIsOpen ?  this.$bet.accDiv(this.$bet.accDiv(this.$bet.accMul(Number(s),Number(this.maxRebate)),Number(this.scale)),a,5) : this.$bet.accDiv(s,a,5) //保留4位小数
                return res
            },
            //龙虎奖金
            lhGain(){
                let val = ''
                for(let i in this.play){
                    if(this.play[i].type == 9){
                        val = this.play[i].gain
                    }
                }
                return val;
            },
            //投注单位
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit;
            },
            name(){ //游戏名eg:gd11
                return this.$route.query.name
            },
            cz(){
                return this.$route.path.replace('/','')
            },
            //玩法匹配
            nameObj () {
                return {
                    1 :{ name:'五星',radio : this.radio1},
                    2 :{ name:'前三',radio : this.radio2},
                    3 :{ name:'中三',radio : this.radio3},
                    4 :{ name:'后三',radio : this.radio4},
                    5 :{ name:'后二',radio : this.radio5},
                    6 :{ name:'一星',radio : this.radio6},
                    7 :{ name:'大小单双',radio : this.radio7},
                    8 :{ name:'定位胆',radio : this.radio8},
                    9 :{ name:'龙虎',radio : this.radio9},
                    10 :{ name:'前二',radio : this.radio10},
                }
            },
            //小玩法对应值
            radioObj(){
                let type = this.type
                var radioObj = {
                    1 : ''
                }
                if (type == 1) {
                    radioObj = {
                        1 : '通选-复式',
                        2 : '直选-复式',
                        3 : '通选-单式',
                        4 : '直选-单式',
                    }
                }
                if (type == 2 || type == 3 || type == 4) {
                    radioObj = {
                        1: '直选-复式',
                        2: '直选-和值',
                        3: '组三-复式',
                        4: '组三-胆拖',
                        5: '组三-和值',
                        6: '组六-复式',
                        7: '组六-胆拖',
                        8: '组六-和值',
                        11: '直选-单式',
                        12: '组三-单式',
                        13: '组六-单式'
                    }
                }
                if (type == 5 || type == 10) {
                    radioObj = {
                        1 : '直选-复式',
                        2 : '直选-和值',
                        3 : '组选-复式',
                        4 : '组选-和值',
                        5 : '直选-单式',
                        6 : '组选-单式'
                    }
                }
                if (type == 6) {
                    radioObj = {
                        1 : '复式',
                        2 : '单式'
                    }
                }
                if (type == 9) {
                    radioObj = {
                        1 : '万千',
                        2 : '万百',
                        3 : '万十',
                        4 : '万个',
                        5 : '千百',
                        6 : '千十',
                        7 : '千个',
                        8 : '百十',
                        9 : '百个',
                        11 : '十个'
                    }
                }
                return radioObj
            },
            //投注文字显示处理
            text(){
                return this.nameObj[this.type].name + this.radioObj[this.nameObj[this.type].radio].replace('-','')
            },
            //顶部玩法显示
            topText(){
                return this.radioObj[this.nameObj[this.type].radio]
            },
            //sign投注type
            sign(){
                return this.type + '.' + this.nameObj[this.type].radio
            },
            //玩法下拉高度
            selctHeight(){
                return this.$store.state.clientHeight - 40
            },
        },
        methods:{
            //混合奖金字符串
            getHhGain (emitVal) {
                this.hhGain = emitVal
            },
            //选择玩法
            chosePlay(name,type,gain){
                this.selected = name
                this.type = type
                this.gain = gain
            },
            //中奖说明
            showPlayInfo(){
                this.showMore = false
                this.$router.push({
                    path:'/playInfo',
                    query:{
                        cz:this.cz,
                        name:this.name,
                        model:this.betModel
                    }
                })
            },
            //玩法介绍
            toPlayNews(){
                this.$store.commit('setLoadStatus',true)
                this.$axios.get('/index/news/getAppBetNew',{
                    params:{
                        name:this.name
                    }
                }).then(({data})=>{
                    this.$store.commit('setLoadStatus',false)
                    this.showMore = false
                    if(!data.err){
                        this.$router.push({
                            path:'/news/detail',
                            query:{
                                cz:this.cz,
                                id:data.data.id,
                                navid:data.data.nav_id
                            }
                        })
                    }else {
                        this.$toast('暂无相关玩法介绍!')
                    }
                }).catch(({error})=>{
                    console.log(error)
                })
            },
            //查看历史详情
            toHistoryCode(){
                this.showMore = false
                this.$router.push({
                    path:'/historyCode',
                    query:{
                        name:this.name,
                        cz:this.cz,
                        title:this.title,
                        expectType:this.expectType
                    }
                })
            },
            //进入走势图
            toTrade(){
                this.showMore = false
                this.$router.push({
                    path:'/lottery/trade',
                    query:{
                        name : this.name,
                        title : this.title,
                        cz:this.cz,
                        expectType:this.expectType
                    }
                })
            },
            //进入投注记录
            toRecord(){
                this.showMore = false
                this.$router.push({
                    path:'/lotteryRecord',
                    query:{
                        name : this.name,
                        cz:this.cz
                    }
                })
            }
        },
        filters:{
            strSplit(value){ //奖金显示处理 取最后一个值
                var arr = value.split(',')
                return arr[arr.length-1]
            },
            strSplit1(value){ //奖金显示处理 取第一个值
                var arr = value.split(',')
                return arr[0]
            },
            strSplit2(value){ //奖金显示处理 取第二个值
                var arr = value.split(',')
                return arr[1]
            },
            strSplit3(value){ //奖金显示处理 取第三个值
                var arr = value.split(',')
                return arr[2]
            }
        },
        created(){
            this.loading = true
            this.$axios('/index/Ssc/getLotteryInfo/name/' + this.name).then(({data})=>{
                this.$store.commit('setType',data.lottery.expect_type);
                this.$store.commit('setFirstIssue',data.firstIssue);
                this.$set(this,'info',data.info);
                this.$set(this,'betModel',data.mode);
                this.$store.commit('setBetData',data.info);
                this.$store.commit('setMiss',data.miss);
                this.$set(this,'play',data.play);
                this.$store.commit('setPlayInfo',data.play);
                this.$set(this,'title',data.title);
                this.$store.commit('setRecentOpen',data.ten);
                this.$store.commit('isGetNewCode',data.getnewcode);
                this.$set(this,'small',data.small);
                this.$store.commit('setSmallInfo',data.small);
                for(let i in data.play){
                    if( data.play[i].type == data.info.type){
                        this.$set(this,'selected',data.play[i].name);
                    }
                }
                this.$set(this,'type',data.info.type);
                this.$set(this,'gain',data.info.gain);
                this.$store.commit('setAwardNumber',data.info.awardNumber.code);
                this.$store.commit('setRebateInfo',data);
                this.isStop = data.lottery.pause
                this.loading = false
            })
            this.$store.commit('setKeepAlivePage','ssc')
        },
        //返回首页时清除倒计时等
        beforeRouteLeave(to, from, next){
            if(to.path =='/betOrder' || to.path == '/playInfo' || to.path == '/historyCode'|| to.path == '/news/detail'|| to.path == '/lottery/trade'|| to.path == '/lotteryRecord'){
                this.$store.commit('clearNewCode')  //清除获取开奖号码定时器
                this.$store.commit('clearRandomNum')  //清除开奖动画
                this.$store.commit('setKeepAlivePage','ssc')
                next();
            }else {
                this.$store.commit('delKeepAlivePage','ssc')
                this.$store.commit('clearDownTime'); //清除倒计时定时器
                this.$store.commit('clearBetData'); //清除初始数据
                this.$store.commit('clearBetNum'); //清除投注数据
                sessionStorage.removeItem('betinfo');
                this.$store.commit('clearNewCode')  //清除获取开奖号码定时器
                this.$store.commit('clearRandomNum')  //清除开奖动画
                next();
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .loading{
        width: 28px;
        height: 28px;
        margin: 30px auto;
    }
</style>
