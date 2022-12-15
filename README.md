[English](README.md) | 简体中文

# GUPO AUTOMAKE SDK for PHP
实现了根据表结构与提交参数自动查询与更新
## 安装

### Composer

```bash
composer require lsg/auto-screen
```


## 使用说明
```bash
php artisan vendor:publish --provider="Lsg\AutoScreen\AutoScreenServiceProvider"
使用方法
config/app.php
增加 
//自动查询脚本
Lsg\AutoScreen\AutoScreenServiceProvider::class
php代码

use \Lsg\AutoScreen\AutoMake;
use App\Models\Admin;

$query = new Admin();
$res = AutoMake::getQuery($query)->makeAutoQuery();
dd($res->get()->toArray());


$query = new Admin();
$res = AutoMake::getQuery($query)->doAutoUpdate();
dd($res);


$query = new Admin();
$res = AutoMake::getQuery($query)->makeAutoPageList();
dd($res);


$res = Admin::autoMake($query);
dd($res);

```
## 发行说明

每个版本的详细更改记录在[发行说明](./ChangeLog.txt)中。

## 相关

* [最新源码](https://github.com/linsonggao/AutoScreen)

## 许可证


Copyright (c) 2009-present, linsonggao All rights reserved