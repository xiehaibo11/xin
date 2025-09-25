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
conn.query('SELECT * FROM kr_game_animal_data where status = 1 ORDER BY id ASC', (err, rows, fields) => {
    if (!err) {
        allAnimal = rows;
        console.log(allAnimal);
    }
});


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
        this.iceBeaginTime = 0;//最近冰冻开始时间
        this.hook = {};
        this._width = 740;
        this._fps = 30;
        this.iceTime = 10 * 1000;
        this.rank = new Rank;
        this.lastUserid = 0;
        this.lastIceUserid = 0;//最近冰冻用户名

        //this.addBoss();
        this.addElectric();
        // setTimeout(() => {
        //     this.addBoom();
        // }, 30 * 1000);

        
        this.addAnimal();
        this.initFirst();

        this.rank.on('load', (id, data) => {
            this.listenRank(id, data);
        })
    }

    isHit (animalType, hit) {
        let total_hit = 0;
        if (animalType == 2) {//炸弹人击杀几率  场上所有动物赔率之和
            let total_sp = 0;
            this.list.forEach((v, k) => {
                let dbAnimal = allAnimal[v.info.animalType - 1];
                total_sp += dbAnimal.sp;     
            });
            total_hit = (950/total_sp)*100;
        } else if (animalType == 3) {//恐龙击杀几率  场上赔率小于50的
            let pkqAnimal = allAnimal[2];
            let total_sp = pkqAnimal.sp;
            this.list.forEach((v, k) => {
                let dbAnimal = allAnimal[v.info.animalType - 1];
                if (dbAnimal.sp < 50 && dbAnimal.sp !=0) {
                    total_sp += dbAnimal.sp;     
                }
            });
            total_hit = (950/total_sp)*100;
        } else {
            total_hit = hit;
        }
        
        let rand = Math.floor(Math.random() * 100000);
        if (rand < total_hit) {
            return true;
        }
        return false;
    }

    getIceUser() {
        let sy_time = this.getIceSurplus();
        if (sy_time < this.iceTime && sy_time > 0) {
            return this.lastIceUserid;
        } else {
            return 0;
        }
    }

    isIce() {
        let sy_time = this.getIceSurplus();
        return sy_time > 0;
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
            if ((!data.animalType && !data.init)) this.addAnimal();
            let info = this.add(data.animalType, data.init);
            data.type = 'remove';
            let id = this.rank.add({
                time: info.life
            }, data);
            info.rank_id = id;
        } else if (data.type == 'remove') {
            this.autoRemove(id);
        }
        // if (data.animalType == 1) {
        //     this.addBoss();
        // } else if (data.animalType == 2) {
        //     this.addBoom();
        // }
    }

    addBoss() {//熊猫和炸弹
        this.rank.add({
            time: 30 * 1000
        }, {
            type: 'add',
            name: 'boss',
            animalType: 1
        });
    }

    addBoom() {//炸弹
        this.rank.add({
            time: 30 * 1000
        }, {
            type: 'add',
            name: 'boom',
            animalType: 2
        });
    }

    addElectric() {//皮卡丘
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
            time: 1400
        }, {
            type: 'add',
            name: 'animal'
        });
    }

    //第一批动物 无延迟加载
    addInitAnimal() {
        this.rank.add({
            time: 0
        }, {
            type: 'add',
            name: 'animal',
            init: true
        });
    }

    // 添加动物
    add(animalType, init) {
        let animal = this.create(animalType); 
        animal.id = this.nextId ++;

        if (animalType) {
            animal.animalType = animalType;
        } else {
            // let tp = Math.floor(Math.random() * (allAnimal.length - 3));
            // animal.animalType = 4 + tp;
            animal.animalType = this.GetRandomNum(4, allAnimal.length);
        }
        
        let life = 1000 * (this._width + 200) / Math.abs(animal.speedX) / this._fps;  
        animal.life = life;
        animal.go_life = 0;//动物消耗了的周期
        animal.init_life = 0;//动物消耗了的周期
        let remove_life = life;
        if (init) {
            let lifr_r = this.GetRandomNum(2,8);
            remove_life -= (lifr_r/10) * life;//删除动物的剩余时间
            animal.go_life = (lifr_r/10) * life;//动物已走的时间
            animal.init_life = (lifr_r/10) * life;//动物已走的时间
        }
        let index = this.list.push({
            info: animal,
            life: remove_life
        });
        if (this.hook['add']) this.hook['add'](animal);
        return this.list[index-1];
    }

    // 生成动物
    create(animalType) {
        let info = {};
        let index = Math.floor(Math.random() * 2);
        
        // if (animalType) {//恐龙和炸弹人和熊猫人从最边上出来
             
        // } else {
        //     if (index) {
        //         info.spaceX = this.GetRandomNum(this._width - 400, this._width);
        //     } else {
        //         info.spaceX = this.GetRandomNum(-100, 300);
        //     }
        // }      
        info.spaceX = index ? this._width : -100;
        info.spaceY = this.GetRandomNum(0, 550);
        let speedX = Math.random() * 1.2;
        info.speedX = index ? -speedX - 1 : speedX + 1;
        info.createTime = new Date().getTime();
        let indexY = Math.floor(Math.random() * 2);
        let speedY = Math.random() * 1;
        info.speedY = indexY ? -speedY : speedY;
        return info;
    }

    //获取实时坐标
    getInitList() {
        let now = new Date().getTime();
        let surplusTime = this.getIceSurplus();
        this.list.forEach((v, k) => {
            if (v.info.init_life) {
                v.info.go_life = v.info.init_life + now - (v.info.createTime - surplusTime);
            } else {
                v.info.go_life = now - (v.info.createTime - surplusTime);
            }
            
        });
        return {
            list:this.list,
            surplusTime:surplusTime
        };
    }

    //动物第一批 满场  初始10个在场上
    initFirst() {
        for (let i=1; i<10; i++) {
            this.addInitAnimal();
        }
    }

    removeRest (animal, index){
        if (animal.info.animalType == 1) {
            this.addBoom();
        } else if (animal.info.animalType == 2) {
            this.addElectric();
        } else if (animal.info.animalType == 3) {
            this.addBoss();
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
        let index = this.list.findIndex((v) => {
            return v.rank_id == id;
        });
        let animal = this.list[index];
        this.list.splice(index, 1);
        this.removeRest(animal, index);
        if (this.hook['autoRemove']) this.hook['autoRemove'](animal);
    }

    remove(animalId, children) {
        let index = this.getById(animalId);
        let animal = this.list[index];
        this.rank.remove(animal.rank_id);
        this.list.splice(index, 1);
        this.removeRest(animal, index);
        let dbAnimal = allAnimal[animal.info.animalType - 1];
        animal.bouns = dbAnimal.sp * 100;
        animal.children = [];
        animal.child_money = 0;
        if (children) {
            animal.children = children.child;
            animal.child_money = children.money;
        }
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

    atk(animalId, userid) {
        this.lastUserid = userid;
        let index = this.getById(animalId);
        if(index == -1) return;
        let animal = this.list[index];
        let dbAnimal = allAnimal[animal.info.animalType - 1];
        if (this.isHit(animal.info.animalType, dbAnimal.hit)) {
            if (animal.info.animalType == 2) {//击中炸弹人
                let child = this._remove(0);
                this.remove(animalId, child);
            } else if (animal.info.animalType == 3) {//击中皮卡丘
                let child = this._remove(1);
                this.remove(animalId, child);
            } else {
                this.remove(animalId);
            }
        } else {
            animal.lastUserid = this.lastUserid;
            animal.lastIceUserid = this.getIceUser();
            
            if (this.hook['noRemove']) this.hook['noRemove'](animal);
        }

    }


    getById(animalId) {       
        let index = this.list.findIndex((v) => {
            return v.info.id == animalId;
        });
        return index;
    }

    ice(userid) {
        this.lastUserid = userid;
        if (this.isIce()) {
            return;
        }    
        this.rank.list.forEach((v) => {
            v.time += this.iceTime;
        });
        this.rank.parse();
        this.rank.reset();
        this.iceBeaginTime = new Date().getTime();
        this.lastIceUserid = userid;
        this.list.forEach((v, k) => {
            v.info.createTime += this.iceTime;
        });
        if (this.hook['ice']) this.hook['ice']({lastUserid:this.lastUserid});
    }

    /**
     * 随机号码
     */
    GetRandomNum(Min,Max)
    {   
        var Range = Max - Min;   
        var Rand = Math.random();   
        return(Min + Math.round(Rand * Range));   
    }   

    on(name, callback) {
        // if (!this.hook[name]) this.hook[name] = [];
        this.hook[name] = callback;
    }
}


module.exports = Animal;