const http = require("http");
const conn = require('./conn');
class Task {
    constructor(userid, ext) {
        this.userid = userid;
        this.ext = ext;//任务模块
        this.list = [];
        this.order_list = [];//任务排序之后的列表  父->子
        this.init();
    }

    //获取任务
    init() {
        conn.query({
            sql: 'SELECT * FROM kr_active_log WHERE userid = ? and to_days(create_time) = to_days(now()) and ext in (?, ?) ',
            values: [this.userid, this.ext, '/' + this.ext]
        },(err, rows, fields) => {
            if (err) {
                return '';
            }
            if (rows.length) {//任务排序
                this.getAll(0, rows);                   
            }
            return ;
        });
    }

    //格式化输出任务 isAll 是否获取全部任务 
    getTaskList(isAll = false) {
        let task_level = 1;
        let single_res = {};
        let all_res = [];
        for (let v of this.order_list) {
            let my_v = {};
            if (v.getaward == 2) {
                task_level += 1;
            }
            my_v = {
                activeid: v.activeid,
                active_pid: v.info.pid,
                info: v.info.info,
                content: v.info.content,
                logo: v.info.logo,
                countValue: v.info.countvalue,
                ext: v.ext,
                getaward: v.getaward,
                over: v.active
            };
            if (!isAll) { 
                if (v.active < v.info.countvalue || v.getaward == 1) {
                    single_res.data = my_v;
                    single_res.data.task_level = task_level;
                    return single_res;
                }
            }
            if (isAll) {
                all_res.push(my_v);
            }
        }
        if (isAll) {
            return all_res;
        } else {
            return { data:{task_level:0}};
        }
        
    }

    //获取当前任务
    getNow() {        
      for (let k in this.order_list) {
        let v = this.order_list[k];
        if (v.getaward == 0) {
            return v;
        }
      }
      return false;

    }

    

    //获取上级
    getTaskByid(activeid) {
      for (let v of this.order_list) {
        if (v.activeid == activeid) {
            return v;
        }
      }
    }

    //任务列表ID重新赋值
    setTaskByid(id, value = '', num = 0, callback) {
      for (let k in this.order_list) {
        if (this.order_list[k].id == id) {
            if (value) {
                this.order_list[k] = value;
            } else if (num) {//任务数量增加
               this.order_list[k].active = parseInt(this.order_list[k].active) + parseInt(num); 
               //判断会员是否完成该任务
               if (this.order_list[k].active >= this.order_list[k].info.countvalue) {
                   this.changeAward(id, ()=>{
                       this.order_list[k].getaward = 1;
                       callback();
                   });
               } else {
                   callback();
               }
            }
        }
      }
    }

    //任务数量+1  id为任务日志id
    add(callback) {
        let now_task = this.getNow();
        let id = now_task.id;
        if (parseInt(now_task.active) >= now_task.info.countvalue) {
            for (let k in this.order_list) {
                if (this.order_list[k].id == id) {
                    this.changeAward(id, ()=>{
                        this.order_list[k].getaward = 1;
                        callback();
                    });
                }
            }
            return;
        }
        /**判断该任务的上级任务是否完成，未领奖则不进行该任务的完成*/
        if (now_task.info.pid) {
            let parent_task = this.getTaskByid(now_task.info.pid);
            if (parent_task.info.getaward != 2) {
                this.getTask(parent_task.id, (row) => {
                    if (row.getaward != 2) {
                        return; 
                    } else {
                        this.doAdd(id, callback);
                    }
                })
            } else {
                this.doAdd(id, callback);
            }
        } else {
            this.doAdd(id, callback);
        }       
    }

    //数量增加数据库操作
    doAdd(id, callback) {
        conn.query({
            sql: 'UPDATE kr_active_log SET active = active + 1 WHERE id = ?',
            values: [id]
        },(err, rows) => {
            if (!err) { 
                this.setTaskByid(id, '', 1, callback);
            }
        });
    }

    //任务完成，改变任务的getaward为1
    changeAward(id, callback) {
        conn.query({
            sql: 'UPDATE kr_active_log SET getaward = 1 WHERE id = ? and getaward = 0',
            values: [id]
        },(err, rows) => {
            if (!err) { 
                callback();
            }
        });
    }

    //通过id查询数据库获取任务
    getTask(id, callback) {
        conn.query({
            sql: 'SELECT * FROM kr_active_log WHERE id = ?',
            values: [id]
        },(err, rows, fields) => {
            if (err) {
                return '';
            }
            if (rows.length) {
                this.getTaskInfo(rows[0].activeid, (row) => {
                    let new_row = {};
                    new_row = rows[0];
                    new_row['info'] = row;
                    this.setTaskByid(new_row.id, new_row);
                    callback(new_row);
                });     
            }
            return ;
        });        
    }

    //任务日志追加任务信息
    getAll(i, rows) {
        if(i == rows.length){
            this.orderTask();
            return;
        }
        this.getTaskInfo(rows[i]['activeid'], (row) => {
            let new_v = rows[i];
            new_v['info'] = row;
            this.list.push(new_v);
            this.getAll(i+1, rows);
        });          
    }

    //任务排序
    orderTask(active_id = '') {
       for (let v of this.list) {
            if (!active_id) {
                if (v.info.pid == 0) {
                    this.order_list.push(v);
                    this.orderTask(v['activeid']);
                    return;
                }
            } else {
                if (active_id == v.info.pid) {
                    this.order_list.push(v);
                    this.orderTask(v['activeid']);
                }
            }
        }
    }

    //获取任务信息
    getTaskInfo(activeid, callback) {
        conn.query({
            sql: 'SELECT * FROM kr_active WHERE id = ?',
            values: [activeid]
        },(err, rows, fields) => {
            if (err) {
                return '';
            }
            if (rows.length) {
                callback(rows[0]);
            }
            
        });
    }


    
}
module.exports = Task;
