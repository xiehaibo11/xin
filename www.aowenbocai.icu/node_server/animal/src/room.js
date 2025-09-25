const Animal = require('./animal');

let list = [];//所有房间数组
// list = [
//     {
//         id:  room_id,//房间id
//         user: [],//房间用户
//         animal: //该房间动物
//     }
// ]
let room_id = 0;
// for (let i = 0; i < 10; i ++) {
//     list.push({
//         id: room_id + i,
//         user: [],
//         animal: []
//     });
// }

/**
 * 进入房间
 * 返回房间
 * **/
let addRoom = (userid) => {
    if ( list .length ==0 ) {
        createRoom();
    }
    removeRoom(userid);//清除所有房间可能存在该会员的位置
    let roomIndex = allotRoom();
    
    let room = list[roomIndex];
    room.user.push(userid);
    return room;
}

// 离开房间
let removeRoom = (userid) => {
    let userIndex = -1;
    let inRoom = list.findIndex((v) => {//查找房间里 是否存在会员 返回房间的键值
        userIndex = v.user.indexOf(userid);
        return userIndex >= 0;
    });
    if (inRoom >= 0) {
        let room = list[inRoom];
        room.user.splice(userIndex, 1);
        if (room.user.length == 0) {//判断该房间用户是否为空  为空则删除房间
            list.splice(inRoom, 1);
        }
    }
}

/**
 * 分配房间
 * 返回房间的在list的键值
 * **/
let allotRoom = () => {
    list.sort((a, b) => {//对房间进行用户由少到多的排序
        return a.user.length < b.user.length;
    });
    let index = list.findIndex((v) => {//找出房间中用户少于5个的list键值
        return v.user.length < 5;
    });
    
    if (index == -1) {//房间都满了 重新再建一个房间
        index = createRoom();
    }
    return index;
}

/**
 * 创建房间
 * 返回房间的在list的键值
 * **/
let createRoom = () => {
    room_id += 1;
    let index = list.push({
        id: room_id,
        user: [],
        animal: new Animal()//初始化动物
    });
    
    return index - 1;
}

module.exports = {
    list,
    allot: allotRoom,
    add: addRoom,
    remove: removeRoom
};