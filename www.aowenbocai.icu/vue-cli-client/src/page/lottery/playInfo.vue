<!--中奖说明-->
<template>
    <div class="play-info">
        <h4>开奖时间</h4>
        <div>{{timelong}}分钟一期，每天{{totalIssue}}期</div>
        <h4>玩法规则</h4>
        <template v-if="cz=='syxw'">
            <div>每期从01~11开出5个号码作为中奖号码</div>
            <h4>奖项设置</h4>
            <div>
                <table cellpadding="0" cellspacing="0" class="table-list border-1px play-list">
                    <tr>
                        <th width="15%">玩法</th>
                        <th width="68%">中奖条件</th>
                        <th width="22%" v-if="model == 2">赔率</th>
                        <th width="22%" v-else>奖金</th>
                    </tr>
                    <tr v-for="(item,index) in play">
                        <td>{{item.name}}</td>
                        <td style="text-align: left;">
                            <div v-if="item.type == 13">选1个号码，猜中开奖号码任意1个数字</div>
                            <div v-if="item.type == 1">选2个号码，猜中开奖号码任意2个数字</div>
                            <div v-if="item.type == 2">选3个号码，猜中开奖号码任意3个数字</div>
                            <div v-if="item.type == 3">选4个号码，猜中开奖号码任意4个数字</div>
                            <div v-if="item.type == 4">选5个号码，猜中开奖号码任意5个数字</div>
                            <div v-if="item.type == 5">选6个号码，猜中开奖号码的全部5个数字</div>
                            <div v-if="item.type == 6">选7个号码，猜中开奖号码的全部5个数字</div>
                            <div v-if="item.type == 7">选8个号码，猜中开奖号码的全部5个数字</div>
                            <div v-if="item.type == 8">选1个号码，猜中开奖号码第1个数字</div>
                            <div v-if="item.type == 9">选2个号码与开奖的前2个号码相同</div>
                            <div v-if="item.type == 10">选2个号码与开奖的前2个号码相同且顺序一致</div>
                            <div v-if="item.type == 11">选3个号码与开奖的前3个号码相同</div>
                            <div v-if="item.type == 12">选3个号码与开奖的前3个号码相同且顺序一致</div>
                        </td>
                        <td><em class="red">{{handleGain(item.gain)}}</em>{{lotteryUnit}}</td>
                    </tr>
                </table>
            </div>
        </template>
        <template v-if="cz == 'ssc'">
            <div>每期从0~9开出5个号码作为中奖号码</div>
            <h4>奖项设置</h4>
            <div>
                <table cellpadding="0" cellspacing="0" class="table-list border-1px play-list">
                    <tr>
                        <th width="16%">玩法</th>
                        <th width="62%">中奖条件</th>
                        <th width="22%" v-if="model == 2">赔率</th>
                        <th width="22%" v-else>奖金</th>
                    </tr>
                    <tr v-if="small[1][2]['isOpen'] != 1">
                        <td>五星直选</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：1 2 3 4 5</p>
                            <p>选5个号码，与开奖号码完全按位全部相符</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[1][2]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[1][1]['isOpen'] != 1">
                        <td rowspan="3">五星通选</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：1 2 3 4 5</p>
                            <p>选5个号码，与开奖号码完全按位全部相符</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(wxGain[5])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[1][1]['isOpen'] != 1">
                        <td style="text-align: left">
                            <p class="c-2">投注示例：1 2 3 * * 或者 * * 3 4 5</p>
                            <p>选5个号码，与开奖号码前三位或后三位按位相符</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(wxGain[3])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[1][1]['isOpen'] != 1">
                        <td style="text-align: left">
                            <p class="c-2">投注示例：1 2 * * * 或 * * * 4 5</p>
                            <p>选5个号码，与开奖号码前二位或后二位按位相符</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(wxGain[2])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[2][1]['isOpen'] != 1 || small[2][2]['isOpen'] != 1 || small[2][11]['isOpen'] != 1">
                        <td>前三直选</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：3 4 5 - -</p>
                            <p>从万位、千位、百位选3个号码，与开奖号码连续后三位按位相符</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[2][1]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[2][3]['isOpen'] != 1 || small[2][4]['isOpen'] != 1 || small[2][5]['isOpen'] != 1 || small[2][12]['isOpen'] != 1">
                        <td>前三组三</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：3 4 4 - -</p>
                            <p>从万位、千位、百位选2个号码，开出组三且投注号与开奖号码的数字相同，顺序不限(组三是指开奖号码前三位任意两位号码相同，如188。)</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[2][3]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[2][6]['isOpen'] != 1 || small[2][7]['isOpen'] != 1 || small[2][8]['isOpen'] != 1 || small[2][13]['isOpen'] != 1">
                        <td>前三组六</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：3 4 5 - -</p>
                            <p>从万位、千位、百位选3个号码，开出组六且投注号与开奖号码后三位相同，顺序不限（组六是指开奖号码前三位三个号码各不相同，如135。）</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[2][6]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[3][1]['isOpen'] != 1 || small[3][2]['isOpen'] != 1 || small[3][11]['isOpen'] != 1">
                        <td>中三直选</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：- 3 4 5 -</p>
                            <p>从千位、百位、十位选3个号码，与开奖号码连续后三位按位相符</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[3][1]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[3][3]['isOpen'] != 1 || small[3][4]['isOpen'] != 1 || small[3][5]['isOpen'] != 1 || small[3][12]['isOpen'] != 1">
                        <td>中三组三</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：- 3 4 4 -</p>
                            <p>从千位、百位、十位选2个号码，开出组三且投注号与开奖号码的数字相同，顺序不限(组三是指开奖号码中间三位任意两位号码相同，如188。)</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[3][3]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[3][6]['isOpen'] != 1 || small[3][7]['isOpen'] != 1 || small[3][8]['isOpen'] != 1 || small[3][13]['isOpen'] != 1">
                        <td>中三组六</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：- 3 4 5 -</p>
                            <p>从千位、百位、十位选3个号码，开出组六且投注号与开奖号码后三位相同，顺序不限（组六是指开奖号码中间三位三个号码各不相同，如135。）</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[3][6]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[4][1]['isOpen'] != 1 || small[4][2]['isOpen'] != 1 || small[4][11]['isOpen'] != 1">
                        <td>后三直选</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：- - 3 4 5</p>
                            <p>从百位、十位、个位选3个号码，与开奖号码连续后三位按位相符</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[4][1]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[4][3]['isOpen'] != 1 || small[4][4]['isOpen'] != 1 || small[4][5]['isOpen'] != 1 || small[4][12]['isOpen'] != 1">
                        <td>后三组三</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：- - 3 4 4</p>
                            <p>从百位、十位、个位选2个号码，开出组三且投注号与开奖号码的数字相同，顺序不限(组三是指开奖号码后三位任意两位号码相同，如188。)</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[4][3]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[4][6]['isOpen'] != 1 || small[4][7]['isOpen'] != 1 || small[4][8]['isOpen'] != 1 || small[4][13]['isOpen'] != 1">
                        <td>后三组六</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：- - 3 4 5</p>
                            <p>从百位、十位、个位选3个号码，开出组六且投注号与开奖号码后三位相同，顺序不限（组六是指开奖号码后三位三个号码各不相同，如135。）</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[4][6]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[10][1]['isOpen'] != 1 || small[10][2]['isOpen'] != 1 || small[10][5]['isOpen'] != 1">
                        <td>前二直选</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：1 2 - - -</p>
                            <p>选2个号码，与开奖号码连续前二位按位相符</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[10][1]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[10][3]['isOpen'] != 1 || small[10][4]['isOpen'] != 1 || small[10][6]['isOpen'] != 1">
                        <td>前二组选</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：1 2 - - - 或 2 1 - - -</p>
                            <p>选2个号码，与开奖号码连续前二位相符</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[10][3]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[5][1]['isOpen'] != 1 || small[5][2]['isOpen'] != 1 || small[5][5]['isOpen'] != 1">
                        <td>后二直选</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：- - - 4 5</p>
                            <p>选2个号码，与开奖号码连续后二位按位相符</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[5][1]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[5][3]['isOpen'] != 1 || small[5][4]['isOpen'] != 1 || small[5][6]['isOpen'] != 1">
                        <td>后二组选</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：- - - 4 5 或 - - - 5 4</p>
                            <p>选2个号码，与开奖号码连续后二位相符</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[5][3]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[6][1]['isOpen'] != 1">
                        <td>一星</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：- - - - 5</p>
                            <p>选1个号码，与开奖号码个位相符</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[6][1]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[7][1]['isOpen'] != 1">
                        <td>大小单双</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：双单(或双大、小单、小大)</p>
                            <p>与开奖号码后二位数字属性按位相符</p>
                            <p class="c-3 f-mini">(注:大号码为5---9;小号码为:0---4;单数为:13579;双数为:02468)</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[7][1]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <tr v-if="small[8][1]['isOpen'] != 1">
                        <td>定位胆</td>
                        <td style="text-align: left">
                            <p class="c-2">投注示例：1 - - - -（或- 1 - - - 、- - 1 - - 、- - - 1 - 、- - - -1）</p>
                            <p>如：定万位为1，开奖号码为1****即为中奖。</p>
                            <p>如：定千位为2，开奖号码为*2***即为中奖。</p>
                            <p>如：定百位为3，开奖号码为**3**即为中奖。</p>
                            <p>如：定十位为4，开奖号码为***4*即为中奖。</p>
                            <p>如：定个位为5，开奖号码为****5即为中奖。</p>
                        </td>
                        <td>
                            <p class="tr"><em class="red">{{handleGain(small[8][1]['gain'])}}</em>{{lotteryUnit}}</p>
                        </td>
                    </tr>
                    <template v-if="lhGain[0] > 0">
                        <tr>
                            <td rowspan="7">龙虎</td>
                            <td style="text-align: left">
                                <p class="c-2">投注示例：龙</p>
                                <p>龙虎是以开奖结果的五个数字作为基准，取任意位置（万、千、百、十、个）的数字进行组合大小比对的一种玩法； 开奖结果以万千为基准， 万位大于千位为龙，
                                    千位大于万位为虎，二者相同为和。</p>
                            </td>
                            <td>
                                <p class="tr"><em class="red">{{handleGain(lhGain[0])}}</em>{{lotteryUnit}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: left">
                                <p class="c-2">投注示例：虎</p>
                                <p>龙虎是以开奖结果的五个数字作为基准，取任意位置（万、千、百、十、个）的数字进行组合大小比对的一种玩法； 开奖结果以万千为基准，
                                    万位大于千位为龙，千位大于万位为虎，二者相同为和。</p>
                            </td>
                            <td>
                                <p class="tr"><em class="red">{{handleGain(lhGain[1])}}</em>{{lotteryUnit}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: left">
                                <p class="c-2">投注示例：和</p>
                                <p>龙虎是以开奖结果的五个数字作为基准，取任意位置（万、千、百、十、个）的数字进行组合大小比对的一种玩法；
                                    开奖结果以万千为基准，万位大于千位为龙，千位大于万位为虎，二者相同为和。</p>
                            </td>
                            <td>
                                <p class="tr"><em class="red">{{handleGain(lhGain[2])}}</em>{{lotteryUnit}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: left">
                                <p class="c-2">投注示例：大</p>
                                <p>开奖结果以万千为基准，万千总和的个位数5-9为大，即为中奖。</p>
                            </td>
                            <td>
                                <p class="tr"><em class="red">{{handleGain(lhGain[3])}}</em>{{lotteryUnit}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: left">
                                <p class="c-2">投注示例：小</p>
                                <p>开奖结果以万千为基准，万千总和的个位数0-4为小，即为中奖。</p>
                            </td>
                            <td>
                                <p class="tr"><em class="red">{{handleGain(lhGain[4])}}</em>{{lotteryUnit}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: left">
                                <p class="c-2">投注示例：单</p>
                                <p>开奖结果以万千为基准，万千总和的个位数1,3,5,7,9为单，即为中奖。</p>
                            </td>
                            <td>
                                <p class="tr"><em class="red">{{handleGain(lhGain[5])}}</em>{{lotteryUnit}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: left">
                                <p class="c-2">投注示例：双</p>
                                <p>开奖结果以万千为基准，万千总和的个位数0,2,4,6,8,为双，即为中奖。</p>
                            </td>
                            <td>
                                <p class="tr"><em class="red">{{handleGain(lhGain[6])}}</em>{{lotteryUnit}}</p>
                            </td>
                        </tr>
                    </template>
                </table>
            </div>
        </template>
        <template v-if="cz == 'ks'">
            <div>每期从1~6开出3个号码作为中奖号码</div>
            <h4>奖项设置</h4>
            <div>
                <table cellpadding="0" cellspacing="0" class="table-list-ks play-list">
                    <tr>
                        <th width="16%">玩法组</th>
                        <th width="16%">玩法</th>
                        <th width="34%">玩法说明</th>
                        <th width="34%">示例</th>
                    </tr>
                    <tr>
                        <td rowspan="3">和值</td>
                        <td>和值</td>
                        <td style="text-align: left">3个开奖号码相加的和，即为和值。</td>
                        <td style="text-align: left">如：下注12，开奖号码为246，即为中奖。</td>
                    </tr>
                    <tr>
                        <td style="background-color: #f8f8f8">和值大小</td>
                        <td style="text-align: left;background-color: #f8f8f8">3个开奖号码相加的和，3-10为小，11-18为大。</td>
                        <td style="text-align: left;background-color: #f8f8f8">如：下注小，开奖号码为113，即为中奖。</td>
                    </tr>
                    <tr>
                        <td>和值单双</td>
                        <td style="text-align: left">3个开奖号码相加的和，3、5、7、9、11、13、15、17为单；4、6、8、10、12、14、16、18为双。</td>
                        <td style="text-align: left">如：下注双，开奖号码为116，即为中奖。</td>
                    </tr>
                    <tr>
                        <td rowspan="2">三同号</td>
                        <td>三同号通选</td>
                        <td style="text-align: left">开奖号码全部相同，即为三同号；对所有三同号进行投注，开出任意三同号，即为中奖。</td>
                        <td style="text-align: left">如：开奖号码为三个相同号码，即为中奖。</td>
                    </tr>
                    <tr>
                        <td style="background-color: #efefef">三同号单选</td>
                        <td style="text-align: left;background-color: #efefef">对三同号中的任意一个进行投注，所选号码开出，即为中奖。</td>
                        <td style="text-align: left;background-color: #efefef">如:下注111，开奖号码为111，即为中奖。</td>
                    </tr>
                    <tr>
                        <td style="background-color: #f8f8f8">三不同号</td>
                        <td style="background-color: #f8f8f8">三不同号</td>
                        <td style="text-align: left;background-color: #f8f8f8">开奖号码全部不相同，即为三不同号；从1-6中任选3个或3个以上的号码，所选号码与开奖号码的3个号码相同，即为中奖。</td>
                        <td style="text-align: left;background-color: #f8f8f8">如：下注123，开奖号码为123，即为中奖。</td>
                    </tr>
                    <tr>
                        <td style="background-color: #efefef">三连号</td>
                        <td style="background-color: #efefef">三连号通选</td>
                        <td style="text-align: left;background-color: #efefef">对所有3个相连的号码（123、234、345、456）进行投注，开出任意相连号码，即为中奖。</td>
                        <td style="text-align: left;background-color: #efefef">如：开奖号码为三个相连的号码，即为中奖。</td>
                    </tr>
                    <tr>
                        <td rowspan="2" style="background-color: #f8f8f8">二同号</td>
                        <td style="background-color: #f8f8f8">二同号复选</td>
                        <td style="text-align: left;background-color: #f8f8f8">开奖号码有两个相同，即为二同号；对二同号中的任意一个进行投注，所选号码开出，即为中奖。（不含豹子）</td>
                        <td style="text-align: left;background-color: #f8f8f8">如：下注11*，开奖号码112，113，114，115，116，即为中奖。</td>
                    </tr>
                    <tr>
                        <td style="background-color: #f8f8f8">二同号单选</td>
                        <td style="text-align: left;background-color: #f8f8f8">选择1对同号和1个不同号投注，选号与开奖号相同，即为中奖。</td>
                        <td style="text-align: left;background-color: #f8f8f8">如：下注112，开奖号码为112，即为中奖。</td>
                    </tr>
                    <tr>
                        <td>二不同号</td>
                        <td>二不同号</td>
                        <td style="text-align: left">至少选择2个不同的号码投注，开奖号码包含选中的2个不同号码，即为中奖。</td>
                        <td style="text-align: left">如：下注12，开奖号码为12*，即为中奖。</td>
                    </tr>
                </table>
            </div>
        </template>
        <template v-if="cz == 'pc28'">
            <div>每期开出3个号码作为开奖号码，3个号码之和即为特码</div>
            <h4>奖项设置</h4>
            <div>
                <table cellpadding="0" cellspacing="0" class="table-list-ks play-list">
                    <tr>
                        <th width="16%">玩法组</th>
                        <th width="16%">玩法</th>
                        <th width="34%">玩法说明</th>
                    </tr>
                    <tr>
                        <td rowspan="6" align="center"><span>混合</span></td>
                        <td><div class="cell tc">大小</div></td>
                        <td><div class="cell tl">3个开奖号码相加的和为14~27为大，0~13为小，即为中奖。</div></td>
                    </tr>
                    <tr class="el-table__row">
                        <td><div class="cell tc">极大小</div></td>
                        <td><div class="cell tl">3个开奖号码相加的和为22~27为极大，0~5为极小，即为中奖。</div></td>
                    </tr>
                    <tr class="el-table__row">
                        <td><div class="cell tc">大单</div></td>
                        <td><div class="cell tl">3个开奖号码相加的和为15、17、19、21、23、25、27，即为中奖。</div></td>
                    </tr>
                    <tr class="el-table__row">
                        <td><div class="cell tc">大双</div></td>
                        <td><div class="cell tl">3个开奖号码相加的和为14、16、18、20、22、24、26，即为中奖。</div></td>
                    </tr>
                    <tr class="el-table__row">
                        <td><div class="cell tc">小单</div></td>
                        <td><div class="cell tl">3个开奖号码相加的和为1、3、5、7、9、11、13，即为中奖。</div></td>
                    </tr>
                    <tr class="el-table__row">
                        <td><div class="cell tc">小双</div></td>
                        <td><div class="cell tl">3个开奖号码相加的和为0、2、4、6、8、10、12，即为中奖。</div></td>
                    </tr>
                    <tr class="el-table__row">
                        <td rowspan="4" align="center"><span>波色</span></td>
                        <td><div class="cell tc">绿波</div></td>
                        <td><div class="cell tl">3个开奖号码相加的和为1、4、7、10、16、19、22、25，即为中奖。</div></td>
                    </tr>
                    <tr class="el-table__row">
                        <td><div class="cell tc">蓝波</div></td>
                        <td><div class="cell tl">3个开奖号码相加的和为2、5、8、11、17、20、23、26，即为中奖。</div></td>
                    </tr>
                    <tr class="el-table__row">
                        <td><div class="cell tc">红波</div></td>
                        <td><div class="cell tl">3个开奖号码相加的和为3、6、9、12、15、18、21、24，即为中奖。</div></td>
                    </tr>
                    <tr class="el-table__row">
                        <td><div class="cell tc">灰波</div></td>
                        <td><div class="cell tl">3个开奖号码相加的和为0、13、14、27（若开出灰波号码，则投注任何波色均视为不中奖）。</div></td>
                    </tr>
                    <tr class="el-table__row">
                        <td align="center">豹子</td>
                        <td align="center">豹子</td>
                        <td>投注当期的3个开奖号为豹子号码。3个开奖号码为同一号码，即为中奖。</td>
                    </tr>
                </table>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
            }
        },
        computed:{
            //返点设置是否开启
            rebateIsOpen(){
                return this.$store.state.setting.rebate_isOpen == 1 ? true : false
            },
            //当前用户最高奖金返点百分比
            percent(){
                return this.$store.getters.maxRebate
            },

            cz(){
                return this.$route.query.cz
            },
            totalIssue(){
                return this.$store.state.lottery.info.totalIssue
            },
            timelong(){
                return this.$store.state.lottery.info.timelong
            },
            play(){
                return this.$store.state.lottery.play
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            small(){
                return this.$store.state.lottery.small
            },
            model(){
                return this.$route.query.model || 1
            },
            divVal () {
                return this.$route.query.model == 1 ? 1 : 2//模式2下奖金减半
            },
            wxGain(){
                return this.small[1][1]['gain'].split(',')
            },
            lhGain(){
                let n = 0
                let str = ''
                for(var i in this.play){
                    if(this.play[i].type == 9){
                        n +=1
                        str = this.play[i].gain
                    }
                }
                if(n > 0){
                    return str.split(',')
                }else {
                    return [0,0,0,0,0,0,0]
                }
            }
        },
        methods:{
            //奖金计算方法
            handleGain(g) {
                return this.rebateIsOpen ? this.$bet.accDiv(this.$bet.accMul(Number(g),this.percent),this.divVal,5) : this.$bet.accDiv(Number(g),this.divVal,5)
            },
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .play-info{
        padding: 10px;
        font-size: 15px;
        line-height: 2;
    }
    .table-list-ks{
        tr th{
            border:1px solid #e2e2e2;
        }
        tr td{
            border:1px solid #e2e2e2;
            font-size: 12px;
        }
    }
</style>
