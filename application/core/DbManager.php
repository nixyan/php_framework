<?php
/**
 * DbManager
 * 接続情報を管理
 * 
 * PDO(データベース抽象化ライブラリ)
 * 種々のDBに同じ記述方法で扱えるようにするライブラリ
 */
class DbManager
{
  //PDOインスタンスを保持するための配列
  protected $connections = [];

  //Repositoryクラスと接続名の対応
  protected $repository_connection_map = [];

  //Repositoryインスタンス格納
  protected $repositories = [];

  /**
   * DBと接続
   */
  public function connect($name, $params)
  {
    //キーの存在チェックを省くためにarray_merge
    $params = array_merge([
      'dsn' => null, //DBドライバ、DB名、ホスト名 'mysql:dbname=mydb:host=localhost'
      'user' => '', //ユーザ名
      'password' => '', //パスワード
      'options' => [],
    ], $params);

    //PDOインスタンスを作成
    $con = new PDO(
      $params['dsn'],
      $params['user'],
      $params['password'],
      $params['options']
    );

    //PDO内部エラーが起きた時に例外を発生させる(EXCEPTION)
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $this->connections[$name] = $con;
  }

  public function getConnection($name = null)
  {
    if (is_null($name)) {
      //配列の先頭の値（最初に作成したPDOインスタンス）
      return current($this->connections);
    }
    return $this->connections[$name];
  }
  
  public function setRepositoryConnectionMap($repository_name, $name)
  {
    $this->repository_connection_map[$repository_name] = $name;
  }

  /**
   * Repositoryクラスに対応する接続を取得
   */
  public function getConnectionForRepository($repository_name)
  {
    if (isset($this->repository_connection_map[$repository_name])) {
      //repository_connection_mapに設定されているものは接続名を指定
      $name = $this->repository_connection_map[$repository_name];
      $con = $this->getConnection($name);
    } else {
      $con = $this->getConnection();
    }

    return $con;
  }

  /**
   * レポジトリの格納
   */
  public function get($repository_name)
  {
    if (!isset($this->repositories[$repository_name])) {
      $repository_class = $repository.name . 'Repository';
      $con = $this->getConnectionForRepository($repository_name);

      $repository = new $repository_class($con);
      
      $this->repositories[$repository_name] = $repository;
    }

    return $this->repositories[$repository_name];
  }

  /**
   * レポジトリの解放
   */
  public function __distruct()
  {
    foreach ($this->repositories as $repository) {
      unset($repository);
    }

    foreach ($this->connections as $connection) {
      unset($connection);
    }
  }
}