環境
===

* Windows7 Professional SP1 64bit
* PHP 5.3.19
* Composer 34df394
* PEAR 1.9.4
* PHPUnit 3.7.10
* Symfony 2.1.6

手順
===

### 1) 必要なBundleをインストール
[Composer] (http://getcomposer.org/)を使います。

    php composer.phar install

※実行前にgit.exeに対してパスを通しておいてください。


### 2) サーバ設定
[Apache Http Server] (http://httpd.apache.org/)を使用する場合は、httpd.confに以下の設定を追加し、
Apacheを再起動してください。

<pre><code>
Listen XXX
NameVirtualHost *:XXX
\<VirtualHost *:XXX\>
  ServerName localhost
  DocumentRoot "path\nameco\web"
  DirectoryIndex index.html, index.php
  \<Directory "path\nameco\web"\>
    AllowOverride All
    Allow from All
  \</Directory\>
\</VirtualHost\>
</pre></code>

※XXXには適宜ポート番号を設定してください。


### 3) parameters.yml.distの変更
app/config/parameters.yml.distをapp/config/parameters.ymlに変更
DBの設定を環境に合わせて変更しておいてください。