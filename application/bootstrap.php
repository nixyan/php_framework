<?php
/**
 * bootstrap.php
 * アプリケーションを実行するにあたってまず行っておくべき設定
 */
//クラスローダーの読み込み
require_once 'core/ClassLoader.php';

$loader = new ClassLoader();

//オートロード対象ディレクトリを追加
$loader->registerDir(dirname(__FILE__).'/core');
$loader->registerDir(dirname(__FILE__).'/models');

//オートロード設定
$loader->register();

