const express = require('express');
const http = require('http');
const conn = require('./conn');
const app = express();
const server = http.createServer(app);
const io = require('socket.io').listen(server);

const config = {
    port: 8927
}

server.listen(config.port, () => {
    console.log('SOCKET/HTTP 服务端口: ' + config.port);
});					//socket 监听端口

let dishuSetting = [
    {
        id: 1,
        hit: 450,
        sp: 2
    },
    {
        id: 2,
        hit: 190,
        sp: 5
    },
    {
        id: 3,
        hit: 95,
        sp: 10
    },
    {
        id: 4,
        hit: 47,
        sp: 20
    },
    {
        id: 5,
        hit: 19,
        sp: 50
    },
];

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
                hit: value.hit,
                sp: value.sp
            }
        }
    });
}

// getFishData();
let bulletCoin = [100, 500, 1000, 5000];

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

let addHis = function(userid, dishu_id, bet_money, money) {
    conn.query({
        sql: 'INSERT kr_game_dishu (userid, dishu_id, bet_money, bouns) VALUES(?, ?, ?, ?)',
        values: [userid, dishu_id, bet_money, money]
    })
}

let createFishBase = function(userid) {
    conn.query({
        sql: 'INSERT INTO kr_plugin_fish (userid) VALUES(?)',
        values: [userid]
    });
}

let getCard = function(userid, callback) {
    conn.query({
        sql: 'SELECT * FROM kr_plugin_fish WHERE userid = ?',
        values: [userid]
    },
    (err, rows, fields) => {
        if (err) {
            callback({
                jsk: 0,
                bdk: 0
            });
            return;
        }
        let value = rows[0];
        callback({
            jsk: value.jsk,
            bdk: value.bdk
        });
    })
}
io.sockets.on('connection', (socket) => {
    console.log('连接成功: ' + socket.id);

    let addMoney = function({userid, num, remark, type = 0}) {
        return new Promise((resolve, reject) => {
            // 获取用户资金
            conn.query({
                sql: 'SELECT money FROM kr_user where id = ?',
                values: [userid]
            }, (err, rows, fields) => {
                let beforeMoney = rows[0].money;
                let afterMoney = beforeMoney + num;
                if (afterMoney < 0) {
                    return;
                }
        
                // 插入资金明细
                conn.query({
                    sql: 'INSERT INTO kr_money_history (userid, money, ext_name, `before`, after, remark, type) VALUES(?, ?, ?, ?, ?, ?, ?)',
                    values: [userid, num, 'game/dishu', beforeMoney, afterMoney, remark, type]
                });
        
                // 更新用户资金
                conn.query({
                    sql: 'UPDATE kr_user SET money = ? WHERE id = ?',
                    values: [afterMoney, userid]
                });
                data.coin = afterMoney;
                resolve();
                socket.emit('setMoney', {coin: afterMoney});
            });
        });
    }
    
    // socket.emit('login');

    let userid = 0;
    let user = {
        nickanme: '游客',
        id: 0
    };

    let useCardStatus = {
        bdk: {
            time: 0,
            cd: 30 * 1000
        },
        jsk: {
            time: 0,
            cd: 30 * 1000
        }
    };

    let data = {
        coin: 0,
        jsk: 0,
        bdk: 0
    };

    let fishList = [];

    let on = {
        disconnect() {
            console.log('断开连接: ' + user.nickname + ' [id: ' +user.id + ']');            
        },
        init() {
            socket.emit('init', {
                coinCount: data.coin
            });
        },
        async attackFish(res) {
            let coin = bulletCoin[res.betIndex];
            console.log('attackFishCOin:', data.coin, res.betIndex, coin);
            
            if (data.coin < coin) {
                socket.emit('noefMoney');
                return;
            }
            let req = {};
            console.log('attackFish:', res.dishuId);
            let fish = dishuSetting.find(fish => fish.id == res.dishuId);
            let hit = isHit(fish.hit);
            await addMoney({userid, num: -coin, remark: '击打地鼠 id_' + res.dishuId});

            req = {
                boxIndex: res.boxIndex
            }

            // 奖金
            let b = 0;
            if (hit) {
                b = Math.floor(fish.sp * coin); //奖金计算
                await addMoney({userid, num: b, remark: '击中地鼠 id_' + res.dishuId, type: 1});
            }

            req.coinCount = data.coin;
            req.bouns = b;

            // 添加游戏记录
            addHis(userid, res.dishuId, coin, b);
            socket.emit('atkDishu', req);
        }
    }

    socket.on('login', ({token}) => {
        conn.query({
            sql: 'SELECT * FROM kr_user WHERE sid = ?', 
            values: [token]
        },
        (err, rows, fields) => {
            if (err) {
                console.log(err);
                socket.emit('login', {
                    code: 0
                });
                return;
            }
            user = rows[0];
            userid = rows[0].id;
            for(let p in on) {
                socket.on(p, on[p]);
            }
            console.log(user.nickname + ' [id: ' +user.id + '] 登陆成功，事件注册完毕');
            
            data.coin = rows[0].money;

            // getCard(userid, (cardData) => {
            //     data.jsk = cardData.jsk;
            //     data.bdk = cardData.bdk;
                
                on.init();
            // })

            socket.emit('loginok', {
                code: 1
            });
            
        });
    });
});