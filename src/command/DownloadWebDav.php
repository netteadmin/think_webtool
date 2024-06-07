<?php
declare (strict_types = 1);

namespace app\webtool\command;
/*
php think webtool:webdav --ansi
*/
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

use app\webtool\classes\WebDAV;

class DownloadWebDav extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('webtool_download_webdav')
            ->setDescription('下载webdav云数据');
    }

    protected function execute(Input $input, Output $output)
    {
        echo "开始下载\n";
        $webdav = new WebDAV;
        $webdav->read_all(true);
        // 指令输出
        $output->info("操作完成");
    }
}
