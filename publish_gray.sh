#!/bin/bash
#灰度标签k8s-5498-yuhuan-v
defaultKey="k8s-5498-yuhuan-v"

read -p "
------------------------------
输入基础标签,回车使用默认值或者输入
[默认值:$defaultKey]:
------------------------------
"  devKey
if [ ! -n "$devKey" ];then
    devKey=$defaultKey
fi

newVersion=$(git tag --sort=committerdate |grep "$defaultKey*"|tail -1)
version=$(git tag --sort=committerdate |grep "$defaultKey*"|tail -1|awk '{split($1,arr,"-v"); print arr[2]}')

#把.去掉
version=${version//./}
version=$(($version+1)) 

finialVersion=${version:0:1}.${version:1:1}.${version:2:1}${version:3:1}

read -p "
------------------------------
输入提交备注,回车使用默认值或者输入
------------------------------
[默认值:自动提交-测试]:"  desc
if [ ! -n "$desc" ];then
    desc="自动提交-测试"
fi

git pull

echo "拉取代码~"

read -p "
------------------------------
确认发布最终版本是否正确
回车使用默认值或者输入末尾版本号
最新版本为$newVersion
------------------------------
[默认值:$devKey$finialVersion]:"  VfinialVersion
if [ ! -n "$VfinialVersion" ];then
    VfinialVersion=$finialVersion
fi

git tag $devKey$VfinialVersion

git push git@codeup.aliyun.com:gupo/rd-backend/idc/idc-cancers-api.git $devKey$VfinialVersion
echo "------------------------------"
echo "发出成功啦~$devKey$VfinialVersion"
echo "------------------------------"
