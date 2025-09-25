let generateUUID = () => {
    var d = new Date().getTime();
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
      var r = (d + Math.random()*16)%16 | 0;
      d = Math.floor(d/16);
      return (c=='x' ? r : (r&0x3|0x8)).toString(16);
    });
    return uuid;
}
class Rank {
    constructor() {
        this.list = [];
        this.list2 = [];
        this.firstId;
        this.hook = {};
        // this.timer = setTimeout();
    }

    //添加到列队
    add({id, time}, data = {}) {
        let now = new Date().getTime();
        if (!id) {
            id = generateUUID();
        }
        this.list.push({
            id: id,
            time: time,//剩余时间
            createTime: now,
            updateTime: now,
            data
        });
        this.restart();
        return id;
    }

    //判断是否重置
    isReset() {
        return this.firstId != this.list[0].id;
    }

    //重置剩余时间 剩余时间从大到小排序
    parse() {
        let now = new Date().getTime();
        this.list.forEach((value, key) => {
            let lostTime = value.time - (now - value.updateTime);
            value.updateTime = now;
            value.time = lostTime > 0 ? lostTime : 0;
        });

        this.list.sort(function(a, b) {//由小到大
            return a.time - b.time
        });
    }

    //移除列队
    remove(id) {
        let index = this.list.findIndex((value) => {
            return value.id == id;
        });
        this.list.splice(index ,1);
        this.restart();
    }

    restart() {
        if (this.list.length) {
            this.parse();
            if(this.isReset()) this.reset()
        }
    }

    //重置列队
    reset() {
        let list = this.list[0];//取得最小剩余时间的值
        if (!list) {
            return;
        }
        this.firstId = list.id;
        if (this.timer) clearTimeout(this.timer);
                // console.log('load111');

        this.timer = setTimeout(() => {
            this.load(list.id);
        }, list.time);
    }

    load(id) {
        let data = this.list[0].data;
        this.list.splice(0, 1);//删除第一个
        this.firstId = '';
        this.restart();
        if (this.hook['load']) this.hook['load'](id, data);
    }

    //返回列队
    on(name, callback) {
        this.hook[name] = callback;
    }
}
module.exports = Rank;
