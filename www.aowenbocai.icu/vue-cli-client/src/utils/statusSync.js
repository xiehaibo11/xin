/**
 * 状态同步工具
 * 确保前端状态与服务器同步，解决延迟和不一致问题
 */

class StatusSync {
    constructor(store, axios) {
        this.store = store;
        this.axios = axios;
        this.syncInterval = null;
        this.lastSyncTime = 0;
        this.syncFrequency = 30000; // 30秒同步一次
        this.maxTimeDrift = 5000; // 最大时间漂移5秒
        this.retryCount = 0;
        this.maxRetries = 3;
    }

    /**
     * 开始状态同步
     */
    startSync(lotteryName) {
        this.lotteryName = lotteryName;
        this.stopSync(); // 先停止之前的同步
        
        // 立即执行一次同步
        this.syncStatus();
        
        // 设置定期同步
        this.syncInterval = setInterval(() => {
            this.syncStatus();
        }, this.syncFrequency);
        
        console.log(`开始状态同步: ${lotteryName}`);
    }

    /**
     * 停止状态同步
     */
    stopSync() {
        if (this.syncInterval) {
            clearInterval(this.syncInterval);
            this.syncInterval = null;
        }
        this.retryCount = 0;
    }

    /**
     * 执行状态同步
     */
    async syncStatus() {
        if (!this.lotteryName) return;

        try {
            const response = await this.axios.get('/index/api/syncStatus', {
                params: {
                    name: this.lotteryName,
                    clientTime: Date.now(),
                    lastSync: this.lastSyncTime
                },
                timeout: 10000
            });

            if (response.data && !response.data.err) {
                this.handleSyncResponse(response.data.data);
                this.retryCount = 0; // 重置重试计数
            } else {
                throw new Error(response.data?.msg || '同步失败');
            }

        } catch (error) {
            console.warn('状态同步失败:', error.message);
            this.handleSyncError(error);
        }
    }

    /**
     * 处理同步响应
     */
    handleSyncResponse(data) {
        const now = Date.now();
        this.lastSyncTime = now;

        // 同步服务器时间
        if (data.serverTime) {
            const timeDrift = Math.abs(now - data.serverTime);
            if (timeDrift > this.maxTimeDrift) {
                console.warn(`时间漂移过大: ${timeDrift}ms，进行校正`);
                this.correctTimeDrift(data.serverTime - now);
            }
        }

        // 同步期号信息
        if (data.currentExpect) {
            this.syncExpectInfo(data.currentExpect);
        }

        // 同步开奖号码
        if (data.latestCode) {
            this.syncLotteryCode(data.latestCode);
        }

        // 同步进度状态
        if (data.progressStatus) {
            this.syncProgressStatus(data.progressStatus);
        }

        console.log('状态同步完成:', this.lotteryName);
    }

    /**
     * 校正时间漂移
     */
    correctTimeDrift(drift) {
        const currentTime = this.store.state.lottery.info.time;
        if (currentTime > 0) {
            const correctedTime = Math.max(0, currentTime + Math.floor(drift / 1000));
            this.store.commit('setBetData', {
                ...this.store.state.lottery.info,
                time: correctedTime
            });
        }
    }

    /**
     * 同步期号信息
     */
    syncExpectInfo(expectInfo) {
        const currentExpect = this.store.state.lottery.info.expect;
        
        if (expectInfo.expect !== currentExpect) {
            console.log(`期号变更: ${currentExpect} -> ${expectInfo.expect}`);
            
            // 更新期号信息
            this.store.commit('setBetData', {
                expect: expectInfo.expect,
                sort_expect: expectInfo.sort_expect,
                time: expectInfo.down_time,
                lastIssue: expectInfo.lastIssue,
                timelong: expectInfo.timelong
            });

            // 触发期号变更事件
            this.store.dispatch('onExpectChange', expectInfo);
        } else {
            // 同步倒计时
            const serverTime = expectInfo.down_time;
            const clientTime = this.store.state.lottery.info.time;
            const timeDiff = Math.abs(serverTime - clientTime);
            
            // 如果时间差超过3秒，进行同步
            if (timeDiff > 3) {
                console.log(`倒计时同步: ${clientTime} -> ${serverTime}`);
                this.store.commit('setBetData', {
                    ...this.store.state.lottery.info,
                    time: serverTime
                });
            }
        }
    }

    /**
     * 同步开奖号码
     */
    syncLotteryCode(codeInfo) {
        const currentCode = this.store.state.lottery.info.awardNumber;
        
        if (codeInfo.code && codeInfo.code !== currentCode) {
            console.log(`开奖号码更新: ${currentCode} -> ${codeInfo.code}`);
            
            this.store.commit('setAwardNumber', codeInfo.code);
            
            if (codeInfo.recentCodes) {
                this.store.commit('setRecentOpen', codeInfo.recentCodes);
            }

            // 停止获取新开奖号码的轮询
            this.store.commit('clearNewCode');
            this.store.commit('isGetNewCode', false);
        }
    }

    /**
     * 同步进度状态
     */
    syncProgressStatus(progressInfo) {
        // 同步开奖进度
        if (progressInfo.drawProgress !== undefined) {
            this.store.commit('setDrawProgress', progressInfo.drawProgress);
        }

        // 同步系统状态
        if (progressInfo.systemStatus) {
            this.store.commit('setSystemStatus', progressInfo.systemStatus);
        }

        // 同步网络状态
        if (progressInfo.networkStatus) {
            this.store.commit('setNetworkStatus', progressInfo.networkStatus);
        }
    }

    /**
     * 处理同步错误
     */
    handleSyncError(error) {
        this.retryCount++;
        
        if (this.retryCount >= this.maxRetries) {
            console.error(`状态同步失败次数过多，停止同步: ${this.lotteryName}`);
            this.stopSync();
            
            // 通知用户网络问题
            this.store.commit('setNetworkStatus', {
                connected: false,
                error: error.message
            });
        } else {
            // 指数退避重试
            const retryDelay = Math.pow(2, this.retryCount) * 1000;
            console.log(`${retryDelay}ms后重试状态同步 (${this.retryCount}/${this.maxRetries})`);
            
            setTimeout(() => {
                this.syncStatus();
            }, retryDelay);
        }
    }

    /**
     * 手动触发同步
     */
    forcSync() {
        this.retryCount = 0;
        return this.syncStatus();
    }

    /**
     * 设置同步频率
     */
    setSyncFrequency(frequency) {
        this.syncFrequency = frequency;
        
        if (this.syncInterval) {
            this.stopSync();
            this.startSync(this.lotteryName);
        }
    }

    /**
     * 获取同步状态
     */
    getSyncStatus() {
        return {
            isRunning: !!this.syncInterval,
            lotteryName: this.lotteryName,
            lastSyncTime: this.lastSyncTime,
            retryCount: this.retryCount,
            syncFrequency: this.syncFrequency
        };
    }
}

// 全局状态同步实例
let globalStatusSync = null;

/**
 * 获取或创建状态同步实例
 */
export function getStatusSync(store, axios) {
    if (!globalStatusSync) {
        globalStatusSync = new StatusSync(store, axios);
    }
    return globalStatusSync;
}

/**
 * 销毁状态同步实例
 */
export function destroyStatusSync() {
    if (globalStatusSync) {
        globalStatusSync.stopSync();
        globalStatusSync = null;
    }
}

export default StatusSync;