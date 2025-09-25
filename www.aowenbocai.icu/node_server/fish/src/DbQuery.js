const conn = require('./conn');
const User = require('./User');
let setting_config = {
    'fish_screen':'',
    'bdk_chance':'',
    'sdk_chance':'',
    'jsk_chance':'',
    'hf_chance':'',
    'hb_chance':'',
    'zs_chance':''
};
conn.query('SELECT * FROM kr_plugin_fish_setting', (err, rows, fields) => {
    if (!err) {
        if (!rows.length) {
            return;
        }
        for (let v of rows) {
            if (v.name == 'fish_screen') {
                setting_config[v.name] = JSON.parse(v.value);
            } else {
                setting_config[v.name] = v.value;
            }
            

        }
    }
});

module.exports = {
    setting_config,
    addMoney({userid, num, remark, type = 0}) {
        return new Promise(function(resolve, reject) {
    
            conn.query({
                sql: 'SELECT money FROM kr_user where id = ?',
                values: [userid]
            }, (err, rows, fields) => {
                let beforeMoney = rows[0].money;
                let afterMoney = beforeMoney + parseInt(num);
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
                User.findByUserId(userid).money = afterMoney;
                // socket.emit('setMoney', {coin: afterMoney});
                resolve({afterMoney});
            });
        });
    },
    addDiamonds({userid, num, remark, type = 0}) {
        return new Promise(function(resolve, reject) {
    
            conn.query({
                sql: 'SELECT diamonds FROM kr_user where id = ?',
                values: [userid]
            }, (err, rows, fields) => {
                let beforeMoney = rows[0].diamonds;
                let afterMoney = beforeMoney + parseInt(num);
                if (afterMoney < 0) {
                    return;
                }
        
                // 插入资金明细
                conn.query({
                    sql: 'INSERT INTO kr_diamonds_history (userid, money, ext_name, `before`, after, remark, type) VALUES(?, ?, ?, ?, ?, ?, ?)',
                    values: [userid, num, 'fish50', beforeMoney, afterMoney, remark, type]
                });
        
                // 更新用户资金
                conn.query({
                    sql: 'UPDATE kr_user SET diamonds = ? WHERE id = ?',
                    values: [afterMoney, userid]
                });
                User.findByUserId(userid).diamonds = afterMoney;
                // socket.emit('setMoney', {coin: afterMoney});
                resolve({afterMoney});
            });
        });
    },
    addHis(userid, name, title, bet_money, money) {
        conn.query({
            sql: 'INSERT kr_plugin_fish_his (userid, name, title, bet_money, bouns) VALUES(?, ?, ?, ?, ?)',
            values: [userid, name, title, bet_money, money]
        })
    },
    addCard(userid, num, tp) {
        let tt = {'jsk': 'quick', 'bdk': 'ice', 'sdk': 'lock'};
        User.findByUserId(userid).card[tt[tp]] += num;
        conn.query({
            sql: 'UPDATE kr_user_bag SET num = num + ? WHERE userid = ? and param_name = ?',
            values: [num, userid, 'fish_' + tp]
        })
    }
};