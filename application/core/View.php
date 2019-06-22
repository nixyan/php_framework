<?php
/**
 * View
 */
 class View
 {
   protected $base_dir;
   protected $defaults;
   protected $layout_variables = [];

   public function __construct($base_dir, $defaults = [])
   {
     $this->base_dir = $base_dir;
     $this->defaults = $defaults;
   }

   //レイアウトに渡す用の変数をセット
   //例えばタイトルやメタ要素など
   public function setLayoutVar($name, $value)
   {
     $this->layout_variables[$name] = $value;
   }

   /**
    * viewファイル(.php)の読み込み
    * @param $_path
    * @param $_variables ビューに渡す変数
    * @param $_layout レイアウトファイル名
    * 変数名にアンスコをつけているのは、$_variablesの変数と衝突させないため
    */
   public function render($_path, $_variables = [], $_layout = [])
   {
    $_file = $this->base_dir . '/' . $_path . '.php';

    //extractは連想配列のキーを変数名に、値を変数の値にする
    extract(array_merge($this->defaults, $_variables));

    //アウトプットバッファリング
    ob_start(); //バッファ出力のスタート
    ob_implicit_flush(0); //自動フラッシュをオフに

    require $_file;

    $content = ob_get_clean(); //フラッシュしてバッファを消去

    //$_layoutが指定されていたらそれを含めて再度renderする
    if ($_layout) {
      $content = $this->render($_layout,
        array_merge($this->layout_variables, [
          '_content' => $content
          ]
        ));
    }
    return $content;
   }

   //htmlspecialcharsが長いのでメソッド化
   public function escape($string)
   {
     return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
   }
 }