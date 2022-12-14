# AutoScreen

require lsg/auto-screen:1.2
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
实现了根据提交参数自动查询与更新