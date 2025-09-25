const express = require('express');
const http = require('http');
const conn = require('./conn');
const app = express();
const server = http.createServer(app);
const io = require('socket.io').listen(server, {
    "serveClient": false ,
    "transports":['websocket']
});
const Fish = require('./animal');
const Room = require('./Room');
const User = require('./User');
const DbQuery = require('./DbQuery');

const config = {
    port: 8925
}

server.listen(config.port, () => {
    console.log('SOCKET/HTTP 服务端口: ' + config.port);
});					//socket 监听端口

let fishAll = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'long', 'renyu', 'yinsha'];

let fishData = {

};

let fishMax = 20;

let base = {
    createFish() {
        let id = base.generateUUID();
        let j = GetRandomNum(0, 14);
        let name = fishAll[j];
        let hp = 5000;
        let hpMax = hp;
        return {id, name, hp, hpMax, bouns: 55, boom: 60}//1000
    },
    generateUUID() {
        var d = new Date().getTime();
        var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
          var r = (d + Math.random()*16)%16 | 0;
          d = Math.floor(d/16);
          return (c=='x' ? r : (r&0x3|0x8)).toString(16);
        });
        return uuid;
    }
}

function removeArr(arr, val) {
    for(var i=0; i<arr.length; i++) {
        if(arr[i] == val) {
        arr.splice(i, 1);
        break;
        }
    }
}

function GetRandomNum(Min,Max) {
    var Range = Max - Min;   
    var Rand = Math.random();   
    return(Min + Math.floor(Rand * Range));   
} 

function getFishData() {
    conn.query({
        sql: 'SELECT * FROM kr_plugin_fish_data',
        values: []
    }, (err, rows, fields) => {
        for (let value of rows) {
            fishData[value.name] = {
                title: value.title,
                hit: value.hit,
                sp: value.sp
            }
        }
    });
}

getFishData();
let bulletCoin = [1, 5, 10, 20, 50];

let getBao = function() {

    let rand = Math.floor(Math.random() * 99);

    if (rand < 80) {
        return 10000;
    }


    if (rand < 4) {
        return 10;
    }

    if (rand < 9) {
        return 5;
    }
    
    if (rand < 19) {
        return 3;
    }		

    if (rand < 39) {
        return 2;
    }
            
    return 1;
}

let isHit = function(hit) {
    let rand = Math.floor(Math.random() * 999);
    if (rand < hit) {
        return true;
    }
    return false;
}

let addMoney = function(userid, num) {
    conn.query({
        sql: 'UPDATE kr_user SET money = money + ? WHERE id = ? AND money + ? > 0',
        values: [num, userid, num]
    })
}

let addCard = function(userid, num, tp) {
    conn.query({
        sql: 'UPDATE kr_plugin_fish SET ' + tp + ' = ' + tp + ' + ? WHERE userid = ?',
        values: [num, userid]
    })
}

let createFishBase = function(userid, param_name = ['fish_jsk', 'fish_bdk', 'fish_sdk']) {
    if (param_name.length) {
        for(let v of param_name) {
            conn.query({
                sql: 'INSERT INTO kr_user_bag (userid, ext_name, param_name) VALUES(?, ?, ?)',
                values: [userid, '/fish50', v]
            });
        }   
    } 
}

let getCard = function(userid, callback) {
    conn.query({
        sql: 'SELECT * FROM kr_user_bag WHERE userid = ? and ext_name in (?, ?)',
        values: [userid,'fish50','/fish50']
    },
    (err, rows, fields) => {
        let res_data = {
            fish_jsk: 0,
            fish_bdk: 0,
            fish_sdk: 0,
        };
        let card_data = ['fish_jsk', 'fish_bdk', 'fish_sdk'];
        if (err || rows.length == 0) {
            createFishBase(userid);
            callback({
                jsk: res_data.fish_jsk,
                bdk: res_data.fish_bdk,
                sdk: res_data.fish_sdk,
            });
            return;
        } else {
            for(let v of rows) {
                res_data[v.param_name] = v.num;
                removeArr(card_data, v.param_name);
            }
        }
        createFishBase(userid, card_data);
        callback({
            jsk: res_data.fish_jsk,
            bdk: res_data.fish_bdk,
            sdk: res_data.fish_sdk,
        });
    })
}

let addHis = function(userid, name, title, bet_money, money) {
    conn.query({
        sql: 'INSERT kr_plugin_fish_his (userid, name, title, bet_money, bouns) VALUES(?, ?, ?, ?, ?)',
        values: [userid, name, title, bet_money, money]
    })
}


io.sockets.on('connection', (socket) => {
    console.log('连接成功: ' + socket.id);

    let fishBox;
    
    let addMoney = function({userid, num, remark, type = 0}) {
        return new Promise(function(resolve, reject) {

            conn.query({
                sql: 'SELECT money FROM kr_user where id = ?',
                values: [userid]
            }, (err, rows, fields) => {
                let beforeMoney = rows[0].money;
                let afterMoney = beforeMoney + num;
                if (afterMoney < 0) {
                    reject();
                    return;
                }
        
                // 更新用户资金
                conn.query({
                    sql: 'UPDATE kr_user SET money = ? WHERE id = ?',
                    values: [afterMoney, userid]
                },  (err, rows, fields) => {
                    // 插入资金明细
                    conn.query({
                        sql: 'INSERT INTO kr_money_history (userid, money, ext_name, `before`, after, remark, type) VALUES(?, ?, ?, ?, ?, ?, ?)',
                        values: [userid, num, 'fish50', beforeMoney, afterMoney, remark, type]
                    }, (err, rows, fields) => {
                        resolve();
                    });
                });

                data.coin = afterMoney;
                socket.emit('setMoney', {coin: afterMoney});
            });
        });
    }
    
    // socket.emit('login');
    let multiple_index = 1; //倍数场次
    let multiple_array;//倍数数组  0 最小值  1 最大值
    let betBase = 50;// 基本子弹

    let userid = 0;
    let user = {
        nickanme: '游客',
        id: 0
    };
    let skillDiamonds = 20;
    let useCardStatus = {
        bdk: {
            time: 0,
            cd: 30 * 1000
        },
        jsk: {
            time: 0,
            cd: 30 * 1000
        },
        sdk: {
            time: 0,
            cd: 30 * 1000
        }
    };

    let data = {
        coin: 0,//金豆
        diamonds: 0,//钻石
        jsk: 0,
        bdk: 0,
        sdk: 0
    };

    let fishList = [];
    let room;

    let on = {
        disconnect() {
            Room.remove(userid);
            let my = User.findByUserId(userid);
            room.user.forEach(_userid => {
                let user = User.findByUserId(_userid);
                user.socket.emit('removeUser', {userid, index: my.index});
            });
            console.log('断开连接: ' + user.nickname + ' [id: ' +user.id + ']');
        },
        init() {
            // 添加用户数据到缓存
            //{userid, socket, betIndex, money, index}
            let oldUser = User.findByUserId(userid);
            if (oldUser) {
                oldUser.socket.disconnect();// emit('logout');
                socket.emit('relogin');
                // return;
            }
            User.add({
                userid,
                nickname: user.nickname,
                socket,
                betIndex: 1,
                money: data.coin,
                diamonds: data.diamonds,
                index: 0,
                card: {
                    ice: data.bdk,
                    quick: data.jsk,
                    lock: data.sdk
                }
            });
            room = Room.add({userid, multiple_index});
            socket.emit('init', {
                userid: userid,
                coinCount: data.coin,
                diamondsCount: data.diamonds,
                jskCount: data.jsk,
                bdkCount: data.bdk,
                sdkCount: data.sdk,
                serverTime: new Date().getTime()
            });
            let initFish = room.fish.getInitList();
            socket.emit('initFish', initFish);
        },
        initUser() {
            let allUserData = [];
            room.user.forEach(userid => {
                let user = User.findByUserId(userid);
                // {userid, socket, betIndex, money, index}

                allUserData.push({
                    userid,
                    nickname: user.nickname,
                    betIndex: user.betIndex,
                    money: user.money,
                    diamonds: user.diamonds,
                    sdkFishId: user.sdkFishId,
                    index: user.index
                });
            });
            socket.emit('addUser', {
                allUserData
            });

            let my = User.findByUserId(userid);
            room.user.forEach(_userid => {
                if (_userid != userid) {
                    let user = User.findByUserId(_userid);
                    user.socket.emit('joinUser', {
                        userid,
                        nickname: my.nickname,
                        betIndex: my.betIndex,
                        money: my.money,
                        diamonds: my.diamonds,
                        sdkFishId: my.sdkFishId,
                        index: my.index
                    });
                }
            });
        },
        changeLockFish(res) {
            // 改变的锁定
            User.findByUserId(userid).sdkFishId = res.sdkFishId;
            room.user.forEach(_userid => {
                let user = User.findByUserId(_userid);
                user.socket.emit('changeLockFish', {userid:userid, sdkFishId:res.sdkFishId});
            });
            
        },
        attackFish(res) {
            // bet 1 - 20 (50 - 1000)
            // 子弹错误
            res.betIndex = Math.floor(res.betIndex);
            if (!res.betIndex || typeof res.betIndex != 'number') {
                return;
            }
            let coin = parseInt(multiple_array[0]) + betBase * (res.betIndex - 1);
            
            if (coin < multiple_array[0] || coin > multiple_array[1]) return;
            if (data.coin < coin) return;
            let fish = room.fish.getInfoById(res.id).info;
            if (!fish) {
                console.log('不存在鱼：' + res.id);
                return;
            }
            

            room.fish.atk(res.id, userid, coin, {x: res.x, y: res.y});
        },
        useSkill({skillName}) {
            let tt = {'jsk': 'quick', 'bdk': 'ice', 'sdk': 'lock'};

            let user = User.findByUserId(userid);
            let skillNum = user.card[tt[skillName]];
            if (skillNum <= 0 && user.diamonds < skillDiamonds) {
                return;
            }
            useCardStatus[skillName].time = new Date().getTime();
            
            if (skillName == 'bdk') {
                room.fish.ice(userid);
                ice();
            }
            if (skillNum <= 0) {
                DbQuery.addDiamonds({userid: userid, num:-skillDiamonds, remark:'使用技能卡'});
            } else {
                DbQuery.addCard(userid, -1, skillName);
            }
            
        },
        addBullet() {
            socket.emit('addBullet');
        },
        decBullet() {
            socket.emit('decBullet');
        },
        shoot({rotation, betIndex, lockFishId}) {
            room.user.forEach(_userid => {
                if (_userid != userid) {
                    let user = User.findByUserId(_userid);
                    user.socket.emit('shoot', {userid, rotation, betIndex, lockFishId});
                }
            });
            // for(let p in room.user) {
            //     if (p == userid) continue;
            //     socket.emit('shoot', {userid, rotation});
            // }
        }
    }
    
    function ice() {
        let time = room.fish.getIceSurplus()
        // let time = useCardStatus['bdk'].time + 30 * 1000 - new Date().getTime();
        room.user.forEach(_userid => {
            let user = User.findByUserId(_userid);
            user.socket.emit('startIce', {time});
        });
    }

    function getRankCard() {
        let req = {skillCard: 0};
        let rand = GetRandomNum(0,99);
        if (rand == 0) {
            req.skillCard = 'jsk';
            data.jsk ++;
            addCard(userid, 1, req.skillCard);
        }

        if (rand == 1) {
            req.skillCard = 'bdk';
            data.bdk ++;
            addCard(userid, 1, req.skillCard);                    
        }
        return req.skillCard;
    }
    //multiple_index 倍数场次  1 初级 2中级 3高级 4大师
    socket.on('login', ({token, multiple}) => {
        conn.query({
            sql: 'SELECT * FROM kr_user WHERE sid = ?', 
            values: [token]
        },
        (err, rows, fields) => {
            if (err || rows.length == 0) {
                socket.emit('login', {
                    code: 0
                });
                return;
            }
            user = rows[0];
            userid = rows[0].id;
            multiple_index = multiple;
            //初始化倍数场信息
            let fish_screen = DbQuery.setting_config.fish_screen;
            let screen = fish_screen[multiple_index - 1].screen;
            multiple_array = screen.split('-');
            if (multiple_index == 1) {
			    betBase = 50;
            } else if (multiple_index == 2) {
                betBase = 100;	
            } else if (multiple_index == 3) {
                betBase = 500;	
            } else if (multiple_index == 4) {
                betBase = 1000;	
            }

            for(let p in on) {
                socket.on(p, on[p]);
            }
            console.log(user.nickname + ' [id: ' +user.id + '] 登陆成功，事件注册完毕');
            
            data.coin = rows[0].money;
            data.diamonds = rows[0].diamonds;

            getCard(userid, (cardData) => {
                data.jsk = cardData.jsk;
                data.bdk = cardData.bdk;
                data.sdk = cardData.sdk;
                on.init();
            })
            socket.emit('loginok', {
                code: 1
            });
            
        });
    });
});