const Animal = require('./animal');
const User = require('./User');
const DbQuery = require('./DbQuery');

let list = {
    1:[],
    2:[],
    3:[],
    4:[]
};
//房间倍数对应数据
let farm_list = {};
//所有房间数组
// list = [
//     1 :[ {
//         id:  room_id,//房间id
//         user: [],//房间用户
//         animal: //该房间动物
//          }
//        ]
// ]
let room_id = 0;
// for (let i = 0; i < 10; i ++) {
//     list.push({
//         id: room_id + i,
//         user: [],
//         animal: []
//     });
// }

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
    
            // 插入资金明细
            conn.query({
                sql: 'INSERT INTO kr_money_history (userid, money, ext_name, `before`, after, remark, type) VALUES(?, ?, ?, ?, ?, ?, ?)',
                values: [userid, num, 'fish50', beforeMoney, afterMoney, remark, type]
            });
    
            // 更新用户资金
            conn.query({
                sql: 'UPDATE kr_user SET money = ? WHERE id = ?',
                values: [afterMoney, userid]
            });
            data.coin = afterMoney;
            resolve({afterMoney});
        });
    });
}

/**
 * 进入房间
 * 返回房间
 * **/
let addRoom = ({userid, multiple_index}) => {
    if ( list .length == 0 ) {
        createRoom(multiple_index);
    }
    removeRoom(userid);//清除所有房间可能存在该会员的位置
    let roomIndex = allotRoom(multiple_index);
    let userIndex = allotSpace(multiple_index, roomIndex);

    let user = User.findByUserId(userid);
    user.index = userIndex;
    let room = findRoom(multiple_index, roomIndex);
    room.user.push(userid);
    return room;
}

// 根据 index 查找房间
let findRoom = (multiple_index, roomIndex) => {
    return list[multiple_index][roomIndex];
}

// 分配座位
let allotSpace = (multiple_index, roomIndex) => {
    let spaceSolt = [0, 1, 2, 3];
    let room = findRoom(multiple_index, roomIndex);
    for (let userid of room.user) {
        let index = spaceSolt.indexOf(User.findByUserId(userid).index);
        if (index >= 0) {
            spaceSolt.splice(index, 1);
        }
    }
    return spaceSolt[0];
}

// 离开房间
let removeRoom = (userid) => {
    let userIndex = -1;
    let inRoom;
    
    for(let k in list){
        let v = list[k];
        inRoom = v.findIndex((v2) => {
             userIndex = v2.user.indexOf(userid);
            return userIndex >= 0;
        }) 
        if (inRoom >= 0) {
            multiple_index = k;
            break;
        }
    }
    if (inRoom >= 0) {
        let room = list[multiple_index][inRoom];
        room.user.splice(userIndex, 1);
        // delete room.socket[userid];
        if (room.user.length == 0) {//判断该房间用户是否为空  为空则删除房间
            room.fish.free();
            list[multiple_index].splice(inRoom, 1);
        }
    }
    
}

// 查找会员当前的房间倍数
let findUserRoom = (userid) => {
    let userIndex = -1;
    let inRoom;
    let multiple_index = 0;
    for(let k in list){
        let v = list[k];
        inRoom = v.findIndex((v2) => {
             userIndex = v2.user.indexOf(userid);
            return userIndex >= 0;
        }) 
        if (inRoom >= 0) {
            multiple_index = k;
            break;
        }
    }
    return multiple_index;
    
}

// 房间最大人数
let maxCount = 4;

/**
 * 分配房间
 * 返回房间的在list的键值
 * **/
let allotRoom = (multiple_index) => {
    list[multiple_index].sort((a, b) => {//对房间进行用户由少到多的排序
        return a.user.length < b.user.length;
    });
    let index = list[multiple_index].findIndex((v) => {//找出房间中用户少于5个的list键值
        return v.user.length < maxCount;
    });
    if (index == -1) {//房间都满了 重新再建一个房间
        index = createRoom(multiple_index);
    }

    return index;
}

/**
 * 创建房间
 * 返回房间的在list的键值
 * **/
let createRoom = (multiple_index) => {
    
    room_id += 1;
    let animal = new Animal();
    let roomDB = {
        id: room_id,
        user: [],
        fish: animal
    };
    let index = list[multiple_index].push(roomDB);
    fishBoxHook(animal, multiple_index, index - 1);
    return index - 1;
}

function GetRandomNum(Min,Max) {
    var Range = Max - Min;   
    var Rand = Math.random();   
    return(Min + Math.floor(Rand * Range));   
} 


function getRankCard(userid) {
    let req = {skillCard: 0};
    let rand = GetRandomNum(1,1000);
    let jsk_chance = DbQuery.setting_config.jsk_chance;
    let bdk_chance = DbQuery.setting_config.bdk_chance;
    let sdk_chance = DbQuery.setting_config.sdk_chance;
    let zs_chance = DbQuery.setting_config.zs_chance;

    let bdk_chance_area = jsk_chance + bdk_chance;//冰冻卡区域
    let sdk_chance_area = bdk_chance_area + sdk_chance;//锁定卡区域
    let zs_chance_area = sdk_chance_area + zs_chance;//钻石区域
    let fish_screen = DbQuery.setting_config.fish_screen;
    let multiple_index = findUserRoom(userid);

    if (jsk_chance && rand <= jsk_chance) {//加速卡掉落概率判断
        req.skillCard = 'jsk';
        DbQuery.addCard(userid, 1, req.skillCard);
    }

    if (bdk_chance && rand > jsk_chance && rand <= bdk_chance_area) {//冰冻卡掉落概率判断
        req.skillCard = 'bdk';
        DbQuery.addCard(userid, 1, req.skillCard);                    
    }

    if (sdk_chance && rand > bdk_chance_area && rand <= sdk_chance_area) {//锁定卡掉落概率判断
        req.skillCard = 'sdk';
        DbQuery.addCard(userid, 1, req.skillCard);                    
    }
    if (zs_chance && rand > sdk_chance_area && rand <= zs_chance_area) {//钻石掉落概率判断
        req.skillCard = 'zs';
        let zs_num = fish_screen[multiple_index - 1].zs_num;
        zs_num = zs_num ? zs_num : 1;
        DbQuery.addDiamonds({userid:userid, num:zs_num, remark:'掉落钻石增加', type:1});   
    }
    return req.skillCard;
}
function fishBoxHook(fishBox, multiple_index, roomIndex) {
    let room = findRoom(multiple_index, roomIndex);
    fishBox.on('add', (fish) => {
        room.user.forEach(userid => {
            User.findByUserId(userid).socket.emit('createFish', fish);
        });
    });

    fishBox.on('autoRemove', (fish) => {
        room.user.forEach(userid => {
            User.findByUserId(userid).socket.emit('autoRemoveFish', fish);
        });
    });

    fishBox.on('remove', (fish) => {
        // console.log(fish);
        DbQuery.addMoney({userid: fish.lastUserid, num: -fish.betMoney, remark: '击打鱼：' + fish.info.title}).then((afterMoney, resolve) => {
            return DbQuery.addMoney({userid: fish.lastUserid, num: fish.bouns, remark: '击中鱼：' + fish.info.title, type: 1});
        }).then(({afterMoney}) => {
            room.user.forEach(userid => {
                let user = User.findByUserId(userid);
                user.socket.emit('setMoney', {userid: fish.lastUserid, afterMoney});
            });
        });
        DbQuery.addHis(fish.lastUserid, fish.info.name, fish.info.title, fish.betMoney, fish.bouns);
        fish.info.skillCard = getRankCard(fish.lastUserid);
        room.user.forEach(userid => {
            let user = User.findByUserId(userid);
            fish.info.userid = fish.lastUserid;
            user.socket.emit('attackFish', fish.info);
        });
    });

    fishBox.on('noRemove', (fish) => {
        
        DbQuery.addMoney({userid: fish.lastUserid, num: -fish.betMoney, remark: '击打鱼：' + fish.info.title});
        DbQuery.addHis(fish.lastUserid, fish.info.name, fish.info.title, fish.betMoney, 0);
        room.user.forEach(userid => {
            let user = User.findByUserId(userid);
            user.socket.emit('attackFish', fish.info);
        });
    });
    fishBox.createMore();
}
module.exports = {
    list,
    allot: allotRoom,
    add: addRoom,
    remove: removeRoom
};