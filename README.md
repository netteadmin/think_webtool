# webtool

## 附近门店地图坐标

默认使用免费的天地图，也可以使用收费的腾讯地图

~~~
use app\webtool\classes\Map;
Map::$use = 'tx'; 
~~~

属性`$use`值  天地图 tiantitu 或 腾讯地图 tx

如果直接取坐标点，请使用 
天地图

~~~
$res = \helper_v3\Map::get_lat($address, $convert = 'wgs84_gcj02');
~~~

腾讯地址

~~~
$res = \lib\Map::tx($address);
~~~

返回`lat` 、`lng`

## 配置

在平台首页显示webtool接口调用次数

~~~
show_webtool = 1
~~~
