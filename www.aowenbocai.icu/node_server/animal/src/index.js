const express = require('express');
const http = require('http');
const conn = require('./conn');
const app = express();
const server = http.createServer(app);
const io = require('socket.io').listen(server, {
   "serveClient": false ,
   "transports":['websocket']
 });
const Room = require('./room');
const Task = require('./task');

const config = {
    port: 8926
}

server.listen(config.port, () => {
    console.log('SOCKET/HTTP 服务端口: ' + config.port);
});//socket 监听端口

function GetRandomNum(Min,Max) {
    var Range = Max - Min;   
    var Rand = Math.random();   
    return(Min + Math.floor(Rand * Range));   
} 


// 通过id 获取用户信息
let getIceCd = (userid, callback) => {
    conn.query({
        sql: 'SELECT * FROM kr_game_animal_ice WHERE userid = ?',
        values: [userid]
    }, (err, rows, fields) => {
        if (err) {
            return;
        }
        if (rows.length) {
            callback(rows[0].ice_cd);
        }
    })
}

let getMoney = (userid, callback) =>  {//获得用户金豆
    conn.query({
        sql: 'SELECT * FROM kr_user WHERE userid = ?',
        values: [userid]
    },(err, rows, fields) => {
        if (err) {
            return;
        }
        if (rows.length) {
            callback(rows[0].money);
        }
    });
}

let addIceCd = (data) =>  {//添加冰冻cd
    conn.query('INSERT INTO kr_game_animal_ice(userid ,ice_cd) VALUES(?,?)',data);
}
let updateIceCd = (data) =>  {//更新用户冰冻cd
    conn.query('UPDATE kr_game_animal_ice SET ice_cd = ? WHERE userid = ?',data);
}
let addHis = ({userid, animal_type, bullet, bonus}) => {
    conn.query('INSERT INTO kr_game_animal_record(userid ,animal_type,bullet,bonus) VALUES(?,?,?,?)',[userid, animal_type, bullet, bonus], (err, result) => {

    });
}


let userInfo = {};
let record_id = {};
let Loacal_url='https://www.aowenbocai.icu/';//https://www.aowenbocai.icu/
//let Loacal_url='http://192.168.0.104:81/';//https://www.aowenbocai.icu/

io.clientFind = (socketid) => {//查找客户端
    return io.sockets.sockets[socketid];
}

io.sockets.on('connection', (socket) => {
    console.log('连接成功: ' + socket.id);

    // socket.emit('login');
    let userid = 0;
    let sid = 0;
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
    let last_time = new Date().getTime();//上一次请求时间
	let animal_id = 0;
    let thisRoom = {};
    let api_token = 'jfdoismdo32';
    let all_send = (fn, data) => {
        thisRoom.user.forEach((v) => {
            let user = userInfo[v];
            if (user.socketid){
                io.clientFind(user.socketid).emit(fn, data);
            }
        });
    }
    let addMoney = function({userid, num, remark, type = 0}) {
        return new Promise(function(resolve, reject) {

            conn.query({
                sql: 'SELECT money FROM kr_user where id = ?',
                values: [userid]
            }, (err, rows, fields) => {
                let beforeMoney = rows[0].money;
                let afterMoney = beforeMoney + num;
                if (afterMoney < 0) {
                    return;
                }
        
                // 更新用户资金
                conn.query({
                    sql: 'UPDATE kr_user SET money = ? WHERE id = ?',
                    values: [afterMoney, userid]
                }, (err, rows, fields) => {
                    // 插入资金明细
                    conn.query({
                        sql: 'INSERT INTO kr_money_history (userid, money, ext_name, `before`, after, remark, type) VALUES(?, ?, ?, ?, ?, ?, ?)',
                        values: [userid, num, 'animal', beforeMoney, afterMoney, remark, type]
                    }, (err, rows, fields) => {
                        resolve();
                    });
                });
                data.coin = afterMoney;
                userInfo[userid].socket.emit('setMoney', {money: afterMoney});
                
            });
        });
    }
    
    let on = {
        disconnect() {
            Room.remove(userid);
            delete userInfo[userid];
            on.resetRoom();
            console.log('断开连接: ' + user.nickname + ' [id: ' + user.id + ']');
        },
        init() {
            // thisRoom = addRoom(userid);
            
            socket.emit('getUser', {
                money: data.coin,
                user_id: userid
            });
            on.addRoom();
            
			// setInterval(() => {
			// 	on.addAn();
			// }, 1000)
        },
        addRoom() {//玩家进入房间  并注册动物事件
            thisRoom = Room.add(userid);
            userInfo[userid].socketid = socket.id;
            let user_task = new Task(userid, 'animal');
            userInfo[userid].user_task = user_task;
            on.resetRoom();

            let initData = thisRoom.animal.getInitList();
            socket.emit('initAnimal', initData);//前台动物初始化

            getIceCd(userid, function(get_ice){//获取初始化的冰cd
                if (!userInfo[userid] || get_ice == undefined) return;
                userInfo[userid]['ice_cd'] = get_ice;
                socket.emit('iceInfo', {
                    ice_cd: get_ice
                });
            });
            thisRoom.animal.on('add', (data) => {//添加动物
                all_send('addAnimal', data);
            });
            thisRoom.animal.on('autoRemove', (data) => {//自动删除
                all_send('removeAnimal', data.info.id);
            });
            thisRoom.animal.on('remove', (data) => {//击杀
                data.bouns = data.bouns * userInfo[data.lastUserid].bullet/100;
                data.child_money = data.child_money * userInfo[data.lastUserid].bullet/100;
                let bonus = data.bouns + data.child_money;
                addMoney({userid: data.lastUserid, num: -userInfo[data.lastUserid].bullet, remark: '击打动物', type: 0})
                .then(() => {
                    addMoney({userid: data.lastUserid, num: bonus, remark: '击中动物', type: 1})
                })

                addHis({
                    userid: data.lastUserid, 
                    animal_type: data.info.animalType,
                    bullet: userInfo[data.lastUserid].bullet,
                    bonus
                });
                
                let user_task = userInfo[data.lastUserid].user_task;
                //判断任务
                let task = user_task.getNow();
                if (task != false) {
                    if (data.info.animalType == task['info']['info']) {
                        user_task.add(() => {
                            let task_list = user_task.getTaskList();
                            userInfo[data.lastUserid]['socket'].emit('hitTask', task_list);
                        });
                    }
                    data.children.forEach((v, k) => {
                        if (v.info.animalType == task['info']['info']) {
                            user_task.add(() => {
                                let task_list = user_task.getTaskList();
                                userInfo[data.lastUserid]['socket'].emit('hitTask', task_list);
                            });
                        }
                    })
                }

                all_send('hitAnimal', {
                    id: data.info.id,
                    user_id: data.lastUserid,
                    money: data.bouns,
                    children: data.children,
                    ice_cd: userInfo[data.lastUserid].ice_cd
                });
            });
            thisRoom.animal.on('noRemove', (data) => {//未击中返回
                if (data.lastIceUserid != data.lastUserid) {
                    if (userInfo[data.lastUserid].ice_cd == -1) {//玩家第一次进游戏击打第一次
                        userInfo[data.lastUserid].ice_cd = 18;
                        addIceCd([data.lastUserid, 18]);
                    } else { 
                        if (userInfo[data.lastUserid].ice_cd < 360) {
                            userInfo[data.lastUserid].ice_cd += 18;
                            updateIceCd([userInfo[data.lastUserid].ice_cd, data.lastUserid]);//击打一次增加18的cd
                        }       
                    }
                }

                addMoney({userid: data.lastUserid, num: -userInfo[data.lastUserid].bullet, remark: '击打动物', type: 0});
                addHis({
                    userid: data.lastUserid, 
                    animal_type: data.info.animalType,
                    bullet: userInfo[data.lastUserid].bullet,
                    bonus: 0
                });

                all_send('noHitAnimal', {
                    id: data.info.id,
                    user_id: data.lastUserid,
                    bullet: userInfo[data.lastUserid].bullet,
                    ice_cd: userInfo[data.lastUserid].ice_cd
                });
            });
            thisRoom.animal.on('ice', (data) => {//冰冻
                userInfo[data.lastUserid].ice_cd = 0;
                updateIceCd([0, data.lastUserid]);
                all_send('ice', {
                    user_id: data.lastUserid
                });
            });
        },
        resetRoom() {//向所有该房间用户推送用户变化信息
            let info = [];
            thisRoom.user.forEach((v) => {
                let user = userInfo[v];
                info.push({
                    'user_id': user.id,
                    'photo': user.photo,
                    'nickname': user.nickname,
                });
            });
            all_send('userChange', info);
        },
		pushAnimal({id}) {
            let now = new Date().getTime();
            if ((now - last_time) < 100) {
                return;
            }
            last_time = now;
            let animal_index = thisRoom.animal.getById(id);
            if (animal_index == -1)  return;
            if (!userInfo[userid]) return;
            if (data.coin < userInfo[userid].bullet) {
                return;
            }

            thisRoom.animal.atk(id, userid);


		},
        ice() {
            if (userInfo[userid].ice_cd >= 360) {//判断冰cd
                thisRoom.animal.ice(userid);
            }
        },
        Bullet({bullet_code}) {//当前用户选择的子弹
            if (bullet_code < 100 ) {
                bullet_code = 100;
            } else if (bullet_code > 10000 ) {
                bullet_code = 10000;
            }
            if (userInfo[userid].bullet) {
                userInfo[userid]['bullet'] = bullet_code;
            }
            
        },

    }

    socket.on('login', ({token}) => {
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
            if (userInfo[userid] && userInfo[userid].socketid != socket.socketid) {
                io.clientFind(userInfo[userid].socketid).disconnect();
            }

            userInfo[userid] = {
                id: userid,
                nickname: user.nickname,
                photo: user.photo,
                sid: token,
                socket: socket,
                bullet: 100,//子弹
                ice_cd: -1//冰cd  -1为数据库不存在记录
            };

            for(let p in on) {
                socket.on(p, on[p]);
            }
            console.log(user.nickname + ' [id: ' +user.id + '] 登陆成功，事件注册完毕');
            
            data.coin = rows[0].money;       
            socket.emit('loginok', {
                code: 1
            });
            on.init();
            
        });
    });
});