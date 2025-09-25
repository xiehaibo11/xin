let Db = {};

function add({userid, nickname, socket, betIndex, money, diamonds, sdkFishId, index, card}) {
    Db[userid] = {userid, nickname, socket, betIndex, money, diamonds, sdkFishId, index, card}
}

function findByUserId(userid) {
    return Db[userid];
}
module.exports = {
    add,
    findByUserId
};