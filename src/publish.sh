#!/bin/bash
#灰度标签k8s-5498-yuhuan-v
if [[ -n $1 ]];then
    devKey=$1
else
    devKey="test-k8s-8888-yuhuan-v"
fi
version=$(git describe --tags --match $devKey* |awk '{split($1,arr,"-v"); print arr[2]}')

#把.去掉
version=${version//./}
version=$(($version+1)) 


finialVersion=${version:0:1}.${version:1:1}.${version:2:1}

git pull

echo "拉取代码~"

git add .

git commit -m "自动提交"

echo "提交了代码~"

git tag test-k8s-8888-yuhuan-v$finialVersion

git push git@codeup.aliyun.com:gupo/rd-backend/idc/idc-cancers-api.git $devKey$finialVersion

echo "发出成功啦~$devKey$finialVersion"