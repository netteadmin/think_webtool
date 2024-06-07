
# PDF 依赖 
~~~ 
yum install ImageMagick ImageMagick-devel ghostscript pdftk  poppler-utils  perl-Image-ExifTool.noarch 
~~~

# 安装 libreoffice
doc xls ppt转pdf
~~~ 
yum install libreoffice  
~~~

# 图片去背景 
 
~~~ 
pip3 install backgroundremover 
~~~ 

# GX 
~~~
yum install python3
如果提示requests模块不存在
pip install --upgrade pip
pip install requests
如果提示secrets模块不存在
pip install python-secrets -i https://pypi.tuna.tsinghua.edu.cn/simple
如果上面的secrets不能安装可尝试
pip install virtualenv
virtualenv myenv
source myenv/bin/activate 
后再执行 
如果报urllib3错误，说明 urllib3与chardet版本不一致，使用以下方式解决
pip uninstall urllib3 chardet 
pip install --upgrade requests
~~~