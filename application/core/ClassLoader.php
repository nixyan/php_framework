<?php
/**
 * ClassLoader.php
 * ファイルごとにクラスを分けると見通しがよくなるが、
 * require_once()などを用いて読み込む必要が出てくる。
 * 
 * オートローダを作ることで呼び出されるタイミングで自動的にrequireさせる。
 * 呼び出し処理を簡略化するために、クラス名とファイル名に規則性を持たせる
 */

class ClassLoader
{
  protected $dirs;

  public function register()
  {
    //__autoload()
    //インスタンス生成時に対象となるクラスが読み込まれていない時に呼ばれる関数
    //spl_autoload_register()は__autoload()が呼ばれた時に実行する関数を定義
    //以下の場合、__autoload()実行時にClassLoader->loadClass()も呼ばれる
    spl_autoload_register([$this, 'loadClass']);
  }

  public function registerDir($dir)
  {
    $this->dirs[] = $dir;
  }

  public function loadClass($class)
  {
    //__autoLoad()から$classの読み込み要求があった場合、
    //$this->dirディレクトリの中から対象のファイル読込を試行
    foreach ($this->dirs as $dir) {
      $file = $dir . '/' . $class . '.php';
      if (is_readable($file)) {
        require $file;

        return;
      }
    }
  }
}