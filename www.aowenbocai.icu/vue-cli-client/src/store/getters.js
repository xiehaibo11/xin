import Vue from 'vue';
import bet from 'assets/js/lottery.js'  //投注相关方法
export default{
    //未读系统消息数
    getMsgNum(state){
        let num=0
        for(let i in state.systemMsg){
            if(!state.systemMsg[i].status){
                num +=1
            }
        }
        return num
    },
    //获取滚动内容高度
    getScrollHeight(state){
        return (val)=>{
            return state.clientHeight - val
        }
    },
    // ********高频彩相关 ******** start
    //计算投注总注数
    getTotalNotes(state){
        let count=0
        for(let i in state.lottery.betArr){
            count = count + state.lottery.betArr[i].notes
        }
        return count
    },
    //计算投注单位 ： 元 的比例
    getScale(state){
        const obj={
            1 : 1,
            2 : 10,
            3 : 100,
            4 : 1000,
        }
        return obj[state.lottery.value]
    },
    //返点值
    rebateVal(state){
        return bet.accSub(Number(state.lottery.userRebate),Number(state.lottery.sliderValue))
    },
    //最高奖金计算百分比
    maxRebate(state){
        return bet.accSub(1,(bet.accAdd(bet.accAdd(state.lottery.bonus_base,bet.accSub(Number(state.lottery.userRebate),Number(state.lottery.sliderValue))),state.lottery.upUserRebate) * 100 / 10000),5)
    },

    //计算pc28【形态，色波，背景颜色,极值，豹子】
    pcResult(){
        return (codeArr)=>{
            let dxObj = ''
            let dsObj = ''
            let a = 0
            for(let i in codeArr){
                a = Number(a) + Number(codeArr[i])
            }
            dxObj = a >= 14 ? '大' : '小'
            dsObj = a%2 == 0 ? '双' : '单'
            let green_w = [1,4,7,10,16,19,22,25]
            let bule_w = [2,5,8,11,17,20,23,26]
            let red_w = [3,6,9,12,15,18,21,24]
            let gray_w = [0,13,14,27]
            let color = ''
            let text_color = ''
            if(red_w.indexOf(a) > -1){
                color = '#ff0000'
                text_color = '红波'
            }
            if(bule_w.indexOf(a) > -1){
                color = '#2388f5'
                text_color = '蓝波'
            }
            if(green_w.indexOf(a) > -1){
                color = '#12c231'
                text_color = '绿波'
            }
            if(gray_w.indexOf(a) > -1){
                color = '#999999'
                text_color = '灰波'
            }
            let jz = ''
            if(a >= 22 && a<=27 ){jz = '极大'}
            if(a >= 0 && a<=5 ){jz = '极小'}
            let bz = ''
            if(codeArr[0] == codeArr[1] &&  codeArr[1] == codeArr[2]){
                bz = '豹子'
            }
            return {'he':a , 'xt':dxObj + ',' + dsObj ,'bg_color':color , 'text_color':text_color,'jz':jz ,'bz':bz}
        }
    },
    // ********高频彩相关***** end

    // ********竞彩相关 ******** start
    // ********竞彩相关 ******** end

    // ********数字彩相关 ******** start
    //计算投注总注数
    getSzcTotalNotes(state){
        let count=0
        for(let i in state.shuzicai.plan){
            count = count + state.shuzicai.plan[i].notes
        }
        return count
    },
    // ********数字彩相关 ******** end
}
