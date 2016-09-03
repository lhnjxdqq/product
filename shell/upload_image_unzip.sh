#!/bin/bash

##################
#打标签-产品列表-上传图片.zip包解压脚本
#@author	sansan 2016.03.10
#@desc 		需要root权限执行此脚本
#			修改配置文件存储路径后，也要修改对应php脚本配置文件配置的读取路径
#			配置文件：config/application/upload_image_unzip.inc.sh
#			php脚本文件：shell/upload_image_unzip.php
#			执行例子：
#				sudo /bin/bash ./upload_image_unzip.sh '/home/sansan/project/select.kuandd.com/'
##################

#执行shell的此次批次标记
actionDate=`date +'%Y-%m-%d %H-%M-%S'`
echo "################################ SHELL BATCH ${actionDate} START ################################"
echo ' '

#项目部署路径，通过执行脚本时，传参接收
projectPath=$1
#shell配置文件路径
configFile='config/application/upload_image_unzip.inc.sh'
#获取配置完整路径
configPath="${projectPath}${configFile}"

#包含配置
. $configPath

[ ! -d ${rootPath} ] && `mkdir -p ${rootPath}`
[ ! -d ${savePath} ] && `mkdir -p ${savePath}`
[ ! -d ${errorPath} ] && `mkdir -p ${errorPath}`

#获取当前年月日时分秒
nowDate=''
#获取文件名前缀部分
fileName=''
#获取文件名后缀部分
fileExt=''
#剪切存储解压失败的路径
mvToPath=''

#查找指定目录下的文件
for i in `find ${rootPath} -name "*.${suffixName}"` 
do
	nowDate=`date +'%Y-%m-%d-%H-%M-%S'`
	fileName=$(basename ${i} .${suffixName})
	fileExt=${i##*.}
	
	#解压获取到的zip包到指定的存储目录
	`unzip -qo ${i} -d ${savePath}/${fileName}_${nowDate}/`
	#如果解压zip包成功，则删除解压成功的zip包;解压zip包失败，输出错误信息，剪切存储解压失败的zip包	
	if [ $? -eq 0  ]; then
		`rm -f ${i}`
		echo "${i}:unzip success"
	else
		mvToPath="${errorPath}/${fileName}_${nowDate}.${fileExt}"
		`mv ${i} ${mvToPath}`
		echo "${i}:unzip error...is mv to:${mvToPath}"
	fi
done

echo 'upload_image_unzip sh action end!'



#执行php脚本处理zip包内容
${phpBin} ${projectPath}shell/upload_image_unzip.php



echo ' '
echo "################################ SHELL BATCH ${actionDate} END   ################################"
