# youtubeAPI_PHP
PHPからYoutubeのAPIにアクセスして.jsonファイルに書き出しを行います。  
  
# docker_php_mysql
PHPとMySQLの仮想サーバーを使って開発ができる。  
  
## 使い方
ターミナル上で該当のディレクトリに移動する  
docker-compose up -dで起動する。  
http://localhost:8080 に移動すると作業ページが開く  
http://localhost:8888 に移動するとphpmyadminが開く  
  
## Browser-Syncとの同時利用
1. `npm ci` でpackage.lock.jsonを基にnode_modules内の「browser-sync」をインストール
2. `docker-compose up -d` でnginx、php、mysqlのコンテナを立ち上げる
3. `npm run browsersync` でファイルを監視して、ブラウザを自動更新する。

## データベーススペース名の解決
1. phpmyadminにログインする
2. ログインパスワードはsecret
3. 上メニューの「データベース」→データベース名欄に作成したいデータベーススペース名を自由に入力。文字コードは「utf8 / utf8_general_ci」？


## MySQLへの接続
PHPファイルであるindex.phpからMySQLへの接続のため、`mysql_connect()`を使用していたが、現在は非推奨とのこと。  
https://sagara.ink/docker_compose-mysqli/  
`Fatal error: Uncaught Error: Call to undefined function mysql_connect() in /var/www/html/index.php:4 Stack trace: #0 {main} thrown in /var/www/html/index.php on line 4`  
上記のエラーが出るため、`mysqli_connect()`を追加する措置を取った。  
  
また、mysqli_connect()の引数に入れるURLについては、phpが動いているnginxのURLではなく、docker-compose.ymlの「php」で記載した`depends_on`の値（ここではdb）と「db」に書かれている`ports`の右側（コンテナにバインドした側？）のポート番号を記載する（`db:3306`）こと。

### 注意
- Browser-Syncでルートディレクトリを設定する必要はない。docker-compose.yml内でルートディレクトリを設定しているから  
https://qiita.com/ma_me/items/802059e8f1fadcb691eb  
https://orfool.com/programing/1505/  
https://www.browsersync.io/docs/command-line#start
