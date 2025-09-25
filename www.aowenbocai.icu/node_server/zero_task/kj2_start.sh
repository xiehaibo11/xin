PATH=/www/server/nodejs/v16.20.2/bin:/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH

export 
export NODE_PROJECT_NAME="kj2"
cd /www/wwwroot/www.caih8.cfd/node_server/zero_task
nohup /www/server/nodejs/v16.20.2/bin/node /www/wwwroot/www.caih8.cfd/node_server/zero_task/index.js  &>> /www/wwwlogs/nodejs/kj2.log &
echo $! > /www/server/nodejs/vhost/pids/kj2.pid
