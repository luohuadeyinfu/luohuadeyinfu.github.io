# 使用官方的 Python 镜像
FROM python:3

# 设置工作目录
WORKDIR /app

# 复制当前目录的内容到容器中的 /app 目录
COPY . /app

# 暴露端口
EXPOSE 8888

# 运行 Python HTTP 服务器
CMD ["python3", "-m", "http.server", "8888"]