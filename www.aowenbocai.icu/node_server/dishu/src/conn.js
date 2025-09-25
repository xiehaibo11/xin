var mysql = require('mysql');
const config = require('./datebase.json');

var conn;
function handleError () {
	console.log("--> 数据库连接中...");
    conn = mysql.createPool(config);

    //连接错误，2秒重试
    // conn.connect(function (err) {
    //     if (err) {
	// 		console.log("--> 数据库连接错误");
    //         setTimeout(handleError , 2000);
    //     }else{
        	console.log("--> 数据库连接成功");
    //     }
    // });

    conn.on('error', function (err) {
		console.log("--> 数据库连接断开");
        //console.log('db error', err);
        // 如果是连接断开，自动重新连接
        if (err.code === 'PROTOCOL_CONNECTION_LOST') {
			console.log("--> 数据库重连中...");
            handleError();
        } else {
            throw err;
        }
    });
}
handleError();
module.exports = conn;