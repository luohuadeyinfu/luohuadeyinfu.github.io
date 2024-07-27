const messagesDiv = document.getElementById('messages');
const userInput = document.getElementById('user-input');
const sendButton = document.getElementById('send-button');

// 自适应高度
function autoResize(textarea) {
    textarea.style.height = 'auto'; // 清空高度以便重新计算
    const lineHeight = parseFloat(getComputedStyle(textarea).lineHeight);
    const maxHeight = lineHeight * 10; // 设置最大高度为 10 行的高度
    textarea.style.height = Math.min(textarea.scrollHeight, maxHeight) + 'px'; // 设置高度
}

// 发送消息的函数
const sendMessage = async () => {
    const userMessage = userInput.value.trim();
    if (!userMessage) return;

    // 显示用户消息
    messagesDiv.innerHTML += `<div class="message user-message"><strong>您:</strong> ${userMessage}</div>`;
    userInput.value = '';
    autoResize(userInput); // 发送后恢复高度

    // 调用新的聊天API
    const apiUrl = `https://api.lolimi.cn/API/AI/wx.php?msg=${encodeURIComponent(userMessage)}`;
    
    const settings = {
        method: 'GET',
        headers: {
            'Cookie': 'guard=423d88c1Brj177'
        }
    };

    try {
        const response = await fetch(apiUrl, settings);
        const data = await response.json();

        // 检查API返回的结果
        if (data.code === 200) {
            const aiMessage = data.data.output; // 提取 output 字段
            // 显示小集回复
            messagesDiv.innerHTML += `<div class="message ai-message"><strong>小集:</strong> ${aiMessage}</div>`;
        } else {
            messagesDiv.innerHTML += `<div class="message ai-message"><strong>小集:</strong> 出现错误，请重试。</div>`;
        }
    } catch (error) {
        console.error('请求失败:', error);
        messagesDiv.innerHTML += `<div class="message ai-message"><strong>小集:</strong> 网络请求失败，请重试。</div>`;
    }

    messagesDiv.scrollTop = messagesDiv.scrollHeight; // 滚动到底部
};

// 发送按钮点击事件
sendButton.addEventListener('click', sendMessage);

// 监听回车键
userInput.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
        sendMessage();
        event.preventDefault(); // 防止换行
    }
});

// 初始化输入框高度
userInput.addEventListener('input', () => autoResize(userInput));
