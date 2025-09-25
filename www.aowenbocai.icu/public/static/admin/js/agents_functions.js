// 代理后台管理系统 JavaScript 功能模块

// 全局变量
let chartInstances = {};

// 初始化页面
function initPage() {
    // 菜单切换事件
    $('.menu-item').click(function() {
        const index = $(this).data('index');
        switchMenuGroup(index);
    });
    
    // 左侧导航点击事件
    $('.link_item').click(function() {
        const tab = $(this).data('tab');
        switchTab(tab);
        $('.link_item').removeClass('link_active');
        $(this).addClass('link_active');
    });
    
    // 开通代理表单提交事件
    $('#submitCreateAgent').click(function() {
        submitCreateAgentForm();
    });
    
    // 调整额度表单提交事件
    $('#submitAdjustQuota').click(function() {
        submitAdjustQuotaForm();
    });
    
    // 编辑用户表单提交事件
    $('#submitEditUser').click(function() {
        submitEditUserForm();
    });
    
    // 表单验证事件
    bindFormValidation();
}

// 绑定表单验证事件
function bindFormValidation() {
    // 用户名验证
    $('#username').on('blur', function() {
        const username = $(this).val().trim();
        if (username && username.length >= 4) {
            checkUsernameExists(username);
        }
    });
    
    // 手机号验证
    $('#phone, #edit_phone').on('blur', function() {
        const phone = $(this).val().trim();
        const errorId = $(this).attr('id') + '-error';
        if (phone && !/^1[3-9]\d{9}$/.test(phone)) {
            $('#' + errorId).text('请输入正确的手机号码');
        } else {
            $('#' + errorId).text('');
        }
    });
    
    // 额度调整计算
    $('#adjust_amount, #adjust_type').on('change', function() {
        calculatePredictedQuota();
    });
}

// 菜单组切换
function switchMenuGroup(index) {
    $('.menu-item').removeClass('active');
    $('.menu-item[data-index="' + index + '"]').addClass('active');
    
    $('.nav-group').hide();
    $('#nav-group-' + index).show();
    
    // 激活第一个导航项
    const firstLink = $('#nav-group-' + index + ' .link_item').first();
    $('.link_item').removeClass('link_active');
    firstLink.addClass('link_active');
    
    const firstTab = firstLink.data('tab');
    if (firstTab) {
        switchTab(firstTab);
    }
}

// 标签页切换
function switchTab(tab) {
    $('.tab-content').removeClass('active').hide();
    $('#' + tab + '-content').addClass('active').show();
    
    // 根据标签页加载对应数据
    switch(tab) {
        case 'users':
            loadUsersData();
            break;
        case 'brokerage':
            loadBrokerageData();
            break;
        case 'reports':
            initCharts();
            break;
    }
}

// 加载用户数据
function loadUsersData() {
    $('#usersTable').html('<tr><td colspan="9" class="text-center"><i class="glyphicon glyphicon-refresh glyphicon-spin"></i> 加载中...</td></tr>');
    
    setTimeout(function() {
        const users = [
            {
                id: 1, username: 'agent001', nickname: '代理001', balance: '¥5,280.50',
                quota: '¥50,000.00', quota_value: 50000, agent_level: 1, agent_level_text: '一级代理',
                phone: '13800138001', create_time: '2024-01-15 10:30:25', status: '正常',
                status_class: 'text-success', remark: '优质代理'
            },
            {
                id: 2, username: 'agent002', nickname: '代理002', balance: '¥12,680.00',
                quota: '¥30,000.00', quota_value: 30000, agent_level: 2, agent_level_text: '二级代理',
                phone: '13800138002', create_time: '2024-01-14 15:20:10', status: '正常',
                status_class: 'text-success', remark: '表现良好'
            },
            {
                id: 3, username: 'agent003', nickname: '代理003', balance: '¥890.25',
                quota: '¥10,000.00', quota_value: 10000, agent_level: 3, agent_level_text: '三级代理',
                phone: '13800138003', create_time: '2024-01-13 09:15:30', status: '冻结',
                status_class: 'text-danger', remark: '违规操作'
            }
        ];
        
        let html = '';
        if (users.length > 0) {
            users.forEach(user => {
                html += `<tr>
                    <td>${user.id}</td>
                    <td>${user.username}</td>
                    <td>${user.nickname}</td>
                    <td>${user.balance}</td>
                    <td><span class="text-primary">${user.quota}</span></td>
                    <td><span class="label label-info">${user.agent_level_text}</span></td>
                    <td>${user.create_time}</td>
                    <td><span class="${user.status_class}">${user.status}</span></td>
                    <td>
                        <div class="btn-group-vertical btn-group-xs" role="group">
                            <button class="btn btn-info" onclick="viewUserDetail(${user.id})" title="查看详情">
                                <i class="glyphicon glyphicon-eye-open"></i>
                            </button>
                            <button class="btn btn-warning" onclick="showEditUser(${user.id})" title="编辑用户">
                                <i class="glyphicon glyphicon-edit"></i>
                            </button>
                            <button class="btn btn-primary" onclick="showAdjustQuota(${user.id})" title="调整额度">
                                <i class="glyphicon glyphicon-credit-card"></i>
                            </button>
                            ${user.status === '正常' ? 
                                `<button class="btn btn-danger" onclick="freezeUser(${user.id})" title="冻结用户">
                                    <i class="glyphicon glyphicon-ban-circle"></i>
                                </button>` :
                                `<button class="btn btn-success" onclick="unfreezeUser(${user.id})" title="解冻用户">
                                    <i class="glyphicon glyphicon-ok-circle"></i>
                                </button>`
                            }
                        </div>
                    </td>
                </tr>`;
            });
        } else {
            html = '<tr><td colspan="9" class="text-center text-muted">暂无用户数据</td></tr>';
        }
        
        $('#usersTable').html(html);
    }, 800);
}

// 显示开通代理弹窗
function showCreateAgent() {
    resetCreateAgentForm();
    $('#createAgentModal').modal('show');
}

// 重置开通代理表单
function resetCreateAgentForm() {
    $('#createAgentForm')[0].reset();
    $('.help-block.text-danger').text('');
    $('#submitCreateAgent .btn-text').show();
    $('#submitCreateAgent .btn-loading').hide();
    $('#submitCreateAgent').prop('disabled', false);
}

// 验证开通代理表单
function validateCreateAgentForm() {
    let isValid = true;
    $('.help-block.text-danger').text('');
    
    const username = $('#username').val().trim();
    if (!username) {
        $('#username-error').text('用户名不能为空');
        isValid = false;
    } else if (username.length < 4 || username.length > 20) {
        $('#username-error').text('用户名长度应为4-20个字符');
        isValid = false;
    }
    
    const password = $('#password').val();
    if (!password) {
        $('#password-error').text('密码不能为空');
        isValid = false;
    } else if (password.length < 6 || password.length > 20) {
        $('#password-error').text('密码长度应为6-20个字符');
        isValid = false;
    }
    
    const nickname = $('#nickname').val().trim();
    if (!nickname) {
        $('#nickname-error').text('昵称不能为空');
        isValid = false;
    }
    
    const agentLevel = $('#agent_level').val();
    if (!agentLevel) {
        $('#agent_level-error').text('请选择代理等级');
        isValid = false;
    }
    
    return isValid;
}

// 提交开通代理表单
function submitCreateAgentForm() {
    if (!validateCreateAgentForm()) {
        return;
    }
    
    $('#submitCreateAgent .btn-text').hide();
    $('#submitCreateAgent .btn-loading').show();
    $('#submitCreateAgent').prop('disabled', true);
    
    const formData = {
        username: $('#username').val().trim(),
        password: $('#password').val(),
        nickname: $('#nickname').val().trim(),
        agent_level: $('#agent_level').val(),
        phone: $('#phone').val().trim(),
        initial_quota: $('#initial_quota').val() || 0
    };
    
    $.ajax({
        url: '/index/agents/createAgent',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.code === 1) {
                showMessage('success', response.msg || '代理开通成功！');
                $('#createAgentModal').modal('hide');
                loadUsersData();
            } else {
                showMessage('error', response.msg || '代理开通失败，请重试');
            }
        },
        error: function() {
            showMessage('error', '网络错误，请检查网络连接后重试');
        },
        complete: function() {
            $('#submitCreateAgent .btn-text').show();
            $('#submitCreateAgent .btn-loading').hide();
            $('#submitCreateAgent').prop('disabled', false);
        }
    });
}

// 检查用户名是否存在
function checkUsernameExists(username) {
    $.ajax({
        url: '/index/agents/checkUsername',
        type: 'POST',
        data: { username: username },
        dataType: 'json',
        success: function(response) {
            if (response.code === 0) {
                $('#username-error').text('用户名已存在，请更换');
            } else {
                $('#username-error').text('');
            }
        },
        error: function() {
            // 静默处理错误
        }
    });
}

// 显示消息提示
function showMessage(type, message) {
    let alertClass, iconClass;
    
    switch(type) {
        case 'success':
            alertClass = 'alert-success';
            iconClass = 'glyphicon-ok-circle';
            break;
        case 'error':
            alertClass = 'alert-danger';
            iconClass = 'glyphicon-exclamation-sign';
            break;
        case 'info':
            alertClass = 'alert-info';
            iconClass = 'glyphicon-info-sign';
            break;
        default:
            alertClass = 'alert-info';
            iconClass = 'glyphicon-info-sign';
    }
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible" role="alert" style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="glyphicon ${iconClass}"></i> ${message}
        </div>
    `;
    
    $('body').append(alertHtml);
    
    setTimeout(function() {
        $('.alert').fadeOut(500, function() {
            $(this).remove();
        });
    }, 3000);
}

// 用户操作函数
function viewUserDetail(userId) {
    showMessage('info', `查看用户详情：${userId}`);
}

// 显示调整额度模态框
function showAdjustQuota(userId) {
    const userInfo = getUserInfo(userId);
    if (!userInfo) {
        showMessage('error', '用户信息不存在');
        return;
    }

    $('#adjust_user_id').val(userId);
    $('#adjust_user_info').text(`${userInfo.username} (${userInfo.nickname})`);
    $('#current_quota').text(userInfo.quota);

    resetAdjustQuotaForm();
    $('#adjustQuotaModal').modal('show');
}

// 显示编辑用户模态框
function showEditUser(userId) {
    const userInfo = getUserInfo(userId);
    if (!userInfo) {
        showMessage('error', '用户信息不存在');
        return;
    }

    $('#edit_user_id').val(userId);
    $('#edit_username').text(userInfo.username);
    $('#edit_nickname').val(userInfo.nickname);
    $('#edit_phone').val(userInfo.phone);
    $('#edit_agent_level').val(userInfo.agent_level);
    $('#edit_remark').val(userInfo.remark);

    $('.help-block.text-danger').text('');
    $('#editUserModal').modal('show');
}

// 获取用户信息
function getUserInfo(userId) {
    const users = {
        1: {
            username: 'agent001', nickname: '代理001', quota: '¥50,000.00', quota_value: 50000,
            agent_level: 1, phone: '13800138001', remark: '优质代理'
        },
        2: {
            username: 'agent002', nickname: '代理002', quota: '¥30,000.00', quota_value: 30000,
            agent_level: 2, phone: '13800138002', remark: '表现良好'
        },
        3: {
            username: 'agent003', nickname: '代理003', quota: '¥10,000.00', quota_value: 10000,
            agent_level: 3, phone: '13800138003', remark: '违规操作'
        }
    };
    return users[userId] || null;
}

// 重置调整额度表单
function resetAdjustQuotaForm() {
    $('#adjustQuotaForm')[0].reset();
    $('.help-block.text-danger').text('');
    $('#predicted_quota').text('¥0.00');
    $('#submitAdjustQuota .btn-text').show();
    $('#submitAdjustQuota .btn-loading').hide();
    $('#submitAdjustQuota').prop('disabled', false);
}

// 计算预计额度
function calculatePredictedQuota() {
    const userId = $('#adjust_user_id').val();
    const userInfo = getUserInfo(userId);
    const adjustType = $('#adjust_type').val();
    const adjustAmount = parseFloat($('#adjust_amount').val()) || 0;

    if (!userInfo || !adjustType || adjustAmount <= 0) {
        $('#predicted_quota').text('¥0.00');
        return;
    }

    let predictedValue = userInfo.quota_value;
    if (adjustType === 'increase') {
        predictedValue += adjustAmount;
    } else if (adjustType === 'decrease') {
        predictedValue -= adjustAmount;
        if (predictedValue < 0) predictedValue = 0;
    }

    $('#predicted_quota').text('¥' + predictedValue.toLocaleString('zh-CN', {minimumFractionDigits: 2}));
}

// 验证调整额度表单
function validateAdjustQuotaForm() {
    let isValid = true;
    $('.help-block.text-danger').text('');

    const adjustType = $('#adjust_type').val();
    if (!adjustType) {
        $('#adjust_type-error').text('请选择调整类型');
        isValid = false;
    }

    const adjustAmount = parseFloat($('#adjust_amount').val());
    if (!adjustAmount || adjustAmount <= 0) {
        $('#adjust_amount-error').text('请输入有效的调整金额');
        isValid = false;
    }

    const adjustReason = $('#adjust_reason').val().trim();
    if (!adjustReason) {
        $('#adjust_reason-error').text('请输入调整原因');
        isValid = false;
    } else if (adjustReason.length < 5) {
        $('#adjust_reason-error').text('调整原因至少5个字符');
        isValid = false;
    }

    return isValid;
}

// 提交调整额度表单
function submitAdjustQuotaForm() {
    if (!validateAdjustQuotaForm()) {
        return;
    }

    $('#submitAdjustQuota .btn-text').hide();
    $('#submitAdjustQuota .btn-loading').show();
    $('#submitAdjustQuota').prop('disabled', true);

    const formData = {
        user_id: $('#adjust_user_id').val(),
        adjust_type: $('#adjust_type').val(),
        adjust_amount: $('#adjust_amount').val(),
        adjust_reason: $('#adjust_reason').val().trim()
    };

    $.ajax({
        url: '/index/agents/adjustQuota',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.code === 1) {
                showMessage('success', response.msg || '额度调整成功！');
                $('#adjustQuotaModal').modal('hide');
                loadUsersData();
            } else {
                showMessage('error', response.msg || '额度调整失败，请重试');
            }
        },
        error: function() {
            showMessage('error', '网络错误，请检查网络连接后重试');
        },
        complete: function() {
            $('#submitAdjustQuota .btn-text').show();
            $('#submitAdjustQuota .btn-loading').hide();
            $('#submitAdjustQuota').prop('disabled', false);
        }
    });
}

// 验证编辑用户表单
function validateEditUserForm() {
    let isValid = true;
    $('.help-block.text-danger').text('');

    const nickname = $('#edit_nickname').val().trim();
    if (!nickname) {
        $('#edit_nickname-error').text('昵称不能为空');
        isValid = false;
    } else if (nickname.length < 2 || nickname.length > 20) {
        $('#edit_nickname-error').text('昵称长度应为2-20个字符');
        isValid = false;
    }

    const phone = $('#edit_phone').val().trim();
    if (phone && !/^1[3-9]\d{9}$/.test(phone)) {
        $('#edit_phone-error').text('请输入正确的手机号码');
        isValid = false;
    }

    return isValid;
}

// 提交编辑用户表单
function submitEditUserForm() {
    if (!validateEditUserForm()) {
        return;
    }

    $('#submitEditUser .btn-text').hide();
    $('#submitEditUser .btn-loading').show();
    $('#submitEditUser').prop('disabled', true);

    const formData = {
        user_id: $('#edit_user_id').val(),
        nickname: $('#edit_nickname').val().trim(),
        phone: $('#edit_phone').val().trim(),
        agent_level: $('#edit_agent_level').val(),
        remark: $('#edit_remark').val().trim()
    };

    $.ajax({
        url: '/index/agents/editUser',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.code === 1) {
                showMessage('success', response.msg || '用户信息修改成功！');
                $('#editUserModal').modal('hide');
                loadUsersData();
            } else {
                showMessage('error', response.msg || '修改失败，请重试');
            }
        },
        error: function() {
            showMessage('error', '网络错误，请检查网络连接后重试');
        },
        complete: function() {
            $('#submitEditUser .btn-text').show();
            $('#submitEditUser .btn-loading').hide();
            $('#submitEditUser').prop('disabled', false);
        }
    });
}

function freezeUser(userId) {
    if (confirm('确定要冻结该用户吗？冻结后用户将无法正常使用系统功能。')) {
        $.ajax({
            url: '/index/agents/changeUserStatus',
            type: 'POST',
            data: {
                user_id: userId,
                status: 0,
                remark: '管理员手动冻结'
            },
            dataType: 'json',
            success: function(response) {
                if (response.code === 1) {
                    showMessage('success', response.msg || '用户已冻结');
                    loadUsersData();
                } else {
                    showMessage('error', response.msg || '冻结失败');
                }
            },
            error: function() {
                showMessage('error', '网络错误，请重试');
            }
        });
    }
}

function unfreezeUser(userId) {
    if (confirm('确定要解冻该用户吗？解冻后用户将恢复正常使用权限。')) {
        $.ajax({
            url: '/index/agents/changeUserStatus',
            type: 'POST',
            data: {
                user_id: userId,
                status: 1,
                remark: '管理员手动解冻'
            },
            dataType: 'json',
            success: function(response) {
                if (response.code === 1) {
                    showMessage('success', response.msg || '用户已解冻');
                    loadUsersData();
                } else {
                    showMessage('error', response.msg || '解冻失败');
                }
            },
            error: function() {
                showMessage('error', '网络错误，请重试');
            }
        });
    }
}

// 搜索用户
function searchUsers() {
    const keyword = $('#searchInput').val().trim();
    if (keyword) {
        showMessage('info', `搜索关键词：${keyword}`);
    }
    loadUsersData();
}

// 加载佣金数据
function loadBrokerageData() {
    $('#brokerageTable').html('<tr><td colspan="5" class="text-center">暂无佣金记录</td></tr>');
}

// 初始化图表
function initCharts() {
    // 简单的图表初始化
    setTimeout(function() {
        showMessage('info', '图表功能开发中');
    }, 500);
}
