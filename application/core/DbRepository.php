<?php
/**
 * DbRepository
 * データベースへのアクセス
 * 
 * テーブルごとにDbRepositoryクラスの子クラスを作成
 * SQL実行時に頻繁に出てくる処理をこの抽象クラスに格納
 */

 abstract class DbRepository{

   protected $con;

   public function __construct($con)
   {
     $this->setConnection($con);
   }

   /**
    * DbManagerクラスからPDOインスタンスを受け取り保持
    */
   public function setConnection($con)
   {
     $this->con = $con;
   }

   /**
    * プリペアードステートメントにパラメータを流し込み実行
    * @return $stmt //SQL
    */
   public function execute($sql, $params = [])
   {
     $stmt = $this->con->prepare($sql);
     $stmt->execute($params);

     return $stmt;
   }

   /**
    * プリペアードステートメントにパラメータを流し込み結果を1件取得
    */
   public function fetch($sql, $params = [])
   {
     return $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC 連想配列で受け取る
   }

   /**
    * プリペアードステートメントにパラメータを流し込み結果を全件取得
    */
   public function fetchAll($sql, $params = [])
   {
     return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
   }
 }