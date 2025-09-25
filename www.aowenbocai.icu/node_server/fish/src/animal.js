/*let allAnimal = [{
    id:12,
    animalType: 8,
    speedX:-5,
    speedY:0,
    spaceX:640,
    spaceY:60,
}],*/
const conn = require('./conn');
const Rank = require('./Rank');
let allAnimal = [];
conn.query('SELECT * FROM kr_plugin_fish_data where status = 1 ORDER BY id ASC', (err, rows, fields) => {
    if (!err) {
        allAnimal = rows;
    }
});


let isHit = function(hit) {
    let rand = Math.random() * 1000;
    if (rand < hit) {
        return true;
    }
    return false;
}

// 舞台大小
let stage = {
    width: 1280,
    height: 720
};
let stageAuto = 400;
class Animal {
    // nextId = 1;
    /**
     * 1分钟出一个boss
     * 30秒以后 1分钟出一个炸弹人，秒出一个炸弹人
     * 30秒出一个皮卡丘
     */
    constructor() {
        this.nextId = 1;
        this.list = [];
        this.iceBeaginTime = 0;//冰冻开始时间
        this.hook = {};
        this._width = 740;
        this._fps = 60;
        this.iceTime = 30 * 1000;
        this.rank = new Rank;
        this.lastUserid = 0;
        this.iceStartTime = new Date().getTime();
        // this.addBoss();

        // setTimeout(() => {
        //     this.addBoom();
        // }, 30 * 1000);

        // this.addElectric();

        this.addAnimal();

        this.rank.on('load', (id, data) => {
            this.listenRank(id, data);
        })
    }

    createMore() {
        for(let i = 0; i < 12; i ++) {
            let info = {
                spaceX: 0,
                spaceY: 0
            }

            info.spaceY = Math.floor(Math.random() * stage.height);
            info.spaceX = Math.floor(Math.random() * stage.width);

            let _info = this.add(info);

            let id = this.rank.add({
                time: _info.life
            }, {type: 'remove'});
            _info.rank_id = id;
        }
    }

    isIce() {
        return this.getIceSurplus() < this.iceTime;
    }
    //获取剩余冰冻时间
    getIceSurplus() {
        let now = new Date().getTime();
        let res = this.iceTime - (now - this.iceBeaginTime);
        if (res <= 0) {
            return 0;
        }
        return res;
    }

    listenRank(id, data) {
        if (data.type == 'add') {
            this.addAnimal();
            let info = this.add(data.animalType);
            data.type = 'remove';
            let id = this.rank.add({
                time: info.life
            }, data);
            info.rank_id = id;
        } else if (data.type == 'remove') {
            this.autoRemove(id);
        }
        if (id == 1) {
            this.addBoss();
        } else if (id == 2) {
            this.addBoom();
        }
    }

    addBoss() {
        this.rank.add({
            time: 60 * 1000
        }, {
            type: 'add',
            name: 'boss',
            animalType: 1
        });
    }

    addBoom() {
        this.rank.add({
            time: 60 * 1000
        }, {
            type: 'add',
            name: 'boom',
            animalType: 2
        });
    }

    addElectric() {
        this.rank.add({
            time: 30 * 1000
        }, {
            type: 'add',
            name: 'electric',
            animalType: 3
        });
    }

    addAnimal() {
        this.rank.add({
            time: 500
        }, {
            type: 'add',
            name: 'animal'
        });
    }

    // 添加动物
    add(info) {
    
        let animal = this.create();
        animal.id = this.nextId ++;
        
        // if (animalType) {
        //     animal.animalType = animalType;
        // } else {
        //     let tp = Math.floor(Math.random() * (allAnimal.length - 3));
        //     animal.animalType = 4 + tp;
        // }
        // let life = 1000 * (this._width + 250) / Math.abs(animal.speedX) / this._fps;
        if (info) {
            animal = Object.assign(animal, info);
            // console.log(animal);
        }
        let index = this.list.push({
            info: animal,
            life: animal.life
        });
        if (this.hook['add']) this.hook['add'](animal);
        return this.list[index-1];
    }



    // 生成动物
    create() {
        let info = {};
        let index = Math.floor(Math.random() * 4);
        let life = (8 + Math.random() * 2) * 2000; //生命 毫秒 浮动2000毫秒

        // 理想情况下的属性 左上角到右下角
        info.spaceX = - stageAuto;
        info.spaceY = - stageAuto;
        info.speedX = stage.width / life;
        info.speedY = stage.height / life;
        info.life = 5000 + life;//5000 +  * 1000;
        // 0 左边 1右边 2上边 3下边
        if (index == 0 || index == 1) {
            //speedX > 0;
            //speedY 区间不能超过边界 
            info.spaceY = Math.floor(Math.random() * stage.height);
            if (info.spaceY < stage.height / 2) {
                info.speedY = this.setRandSpeed((stage.height - info.spaceY) / life);
            } else {
                info.speedY = this.setRandSpeed(- info.spaceY / life);
            }

            // 从左边出来
            if (index == 1) {
                info.spaceX = stage.width + stageAuto;
                info.speedX = -info.speedX;
            }
        } else {
            //speedY > 0;
            //speedX 区间不能超过边界 
            info.spaceX = Math.floor(Math.random() * stage.width);
            if (info.spaceX < stage.width / 2) {
                info.speedX = this.setRandSpeed((stage.width - info.spaceX) / life);
            } else {
                info.speedX = this.setRandSpeed(- info.spaceX / life);
            }

            // 从左边出来
            if (index == 3) {
                info.spaceY = stage.height + stageAuto;
                info.speedY = -info.speedY;
            }
        }
        info.createTime = new Date().getTime();
        let bigFish = this.getRandom(10);
        let fish;
        if (bigFish < 1) {
            fish = allAnimal.length + this.getRandom(4) - 4;
        } else {
            fish = this.getRandom(allAnimal.length - 4); //低级鱼
        }
        // console.log(fish, allAnimal[fish].name, allAnimal[fish].title);
        return  Object.assign(info, allAnimal[fish]);
    }

    getRandom(max) {
        return Math.floor(this.setRandSpeed(max));
    }

    setRandSpeed(max) {
        return Math.random() * max;
    }

    //获取实时坐标
    getInitList() {
        let now = new Date().getTime();
        let surplusTime = this.getIceSurplus();
        let cacheLish = [];
        this.list.forEach((v, k) => {
            let goTime;
            if (surplusTime) {
                goTime =  now - v.info.createTime + this.iceTime - (now - this.iceBeaginTime);
                if (goTime < 0) {
                    goTime = 0;
                }
                console.log(goTime);
            } else {
                goTime =  now - v.info.createTime;
            }
            // console.log(goTime , v.info.speedX);
            v.info.createTime = now + surplusTime;
            v.info.spaceX += goTime * v.info.speedX;
            v.info.spaceY += goTime * v.info.speedY;
            // if (v.info.spaceY < 0) {//判断动物是否超过边界
            //     v.info.spaceY = 0;
            // } else if (v.info.spaceY > 500){
            //     v.info.spaceY = 500;
            // }
            cacheLish.push(v.info);
        });
        return {
            list:cacheLish,
            surplusTime:surplusTime
        };
    }

    removeRest (animal, index){
        if (animal.info.animalType == 1) {
            this.addBoss();
        } else if (animal.info.animalType == 2) {
            this.addBoom();
        } else if (animal.info.animalType == 3) {
            this.addElectric();
        }
    }

    //数组删除指定元素
    arrayRemove (array, v){
        array.forEach((mv, k) => {
            if (mv == v) {
                array.splice(k, 1);
            }
        })
    }

    autoRemove(id) {
        // console.log('autoRemove');
        let index = this.list.findIndex((v) => {
            return v.rank_id == id;
        });
        let animal = this.list[index];
        if (this.hook['autoRemove']) this.hook['autoRemove'](animal.info.id);

        this.list.splice(index, 1);
        // this.removeRest(animal, index);
    }

    remove(animalId, betIndex, xy) {
        // console.log('remove');

        let index = this.getById(animalId);
        let animal = this.list[index];
        this.rank.remove(animal.rank_id);
        this.list.splice(index, 1);
        // this.removeRest(animal, index);
        // let dbAnimal = this.getInfoById(animalId);

        animal.bouns = Math.floor(animal.info.sp * betIndex);
        animal.info.bouns = animal.bouns;
        animal.info.x = xy.x;
        animal.info.y = xy.y;
        if (animal.info.sp >= 20) {
            animal.info.isMore = true;
        }
        // animal.children = [];
        // animal.child_money = 0;
        // if (children) {
        //     animal.children = children.child;
        //     animal.child_money = children.money;
        // }
        animal.lastUserid = this.lastUserid;
        if (this.hook['remove']) this.hook['remove'](animal);
    }
    //炸弹人或皮卡丘击中的其他动物
    //type = 0;炸弹人  type =1 皮卡丘
    _remove(type) {
        let child = [];
        let all_list = [];
        this.list.forEach((v, k) => {
            all_list.push(v);
        })
        all_list.forEach((v, k) => {     
            let dbAnimal = allAnimal[v.info.animalType - 1];
            if (type == 1 && dbAnimal.sp < 20 && dbAnimal.sp !=0 && v.info.animalType !=3 ) {
                child.push({list: v});
                this.arrayRemove(this.list, v);
                this.removeRest(v, k);
                this.rank.remove(v.rank_id);
            } else if (type == 0 && dbAnimal.sp != 0) {
                child.push({list: v});
                this.arrayRemove(this.list, v);
                this.removeRest(v, k);
                this.rank.remove(v.rank_id);
            }
        });

        let ra = [];
        let total_money = 0;
        child.forEach((v) => {
            let dbAnimal = allAnimal[v.list.info.animalType - 1];
            let money = dbAnimal.sp * 100;
            total_money += money;
            ra.push({
                id: v.list.info.id,
                money:money,
                info:v.list.info
            });
        });
        return {child:ra,money:total_money};
    }

    atk(animalId, userid, betIndex, xy) {
        // console.log(animalId, userid, betIndex);
        this.lastUserid = userid;
        let index = this.getById(animalId);
        if(index == -1) return;
        let animal = this.list[index];

        let dbAnimal = this.getInfoByName(animal.info.name);//allAnimal[animal.info.animalType - 1];
        // console.log(animal, dbAnimal);
        animal.betMoney = betIndex;
        animal.lastUserid = this.lastUserid;
        if (isHit(dbAnimal.hit)) {
            this.remove(animalId, betIndex, xy);
        } else {
            if (this.hook['noRemove']) this.hook['noRemove'](animal);
        }

    }


    getById(animalId) {
        let index = this.list.findIndex((v) => {
            return v.info.id == animalId;
        });
        return index;
    }

    getInfoById(id) {       
        let index = this.list.findIndex((v) => {
            return v.info.id == id;
        });
        return index == -1 ? false : this.list[index];
    }

    getInfoByName(name) {
        let index = allAnimal.findIndex((v) => {
            return v.name == name;
        });
        return allAnimal[index];
    }

    ice(userid) {
        this.lastUserid = userid;
        let lostTime = this.iceTime - this.getIceSurplus();
        // if (!this.isIce()) {
        //     return;
        // }
        // console.log(lostTime);
        this.rank.list.forEach((v) => {
            v.time += lostTime;
        });
        this.rank.parse();
        this.rank.reset();
        this.iceBeaginTime = new Date().getTime();
        this.list.forEach((v, k) => {
            v.info.createTime += lostTime;
        });
        if (this.hook['ice']) this.hook['ice']({lastUserid:this.lastUserid});
    }

    getIceTime() {
        let lostTime = this.iceTime - new Date().getTime() - this.iceStartTime;
        return lostTime > 0 ? lostTime : 0;
    }

    on(name, callback) {
        // if (!this.hook[name]) this.hook[name] = [];
        this.hook[name] = callback;
    }

    free() {
        this.rank.list.forEach((v) => {
            this.rank.remove(v.id);
        });
        delete this;
    }
}


module.exports = Animal;