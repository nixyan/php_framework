<?php

class Router
{
  protected $routes;

  public function __construct($definitions)
  {
    $this->routes = $this->compileRoutes($definitions);
  }

  /**
   * ルーティング配列の動的パラメータを正規表現でマッチングするように変換
   */
  public function compileRoutes($definitions)
  {
    $routes = [];
    foreach ($definitions as $url => $params) {
      $tokens = explode('/', ltrim($url, '/'));
      foreach ($tokens as $i => $token) {
        //コロンで始まるパラメータを格納
        if (0 === strpos($token, ':')) {
          $name = substr($token, 1);
          $token = '(?P<' . $name . '>[^/]+';
        }
        $tokens[$i] = $token;
      }
      $pattern = '/' .implode('/', $tokens);
      $routes[$pattern] = $params;
    }
  }

  /**
   * パラメータを生成
   */
  public function resolve($path_info)
  {
    if ('/' !== substr($path_info, 0, 1)) {
      $path_info = '/'. $path_info;
    }

    foreach ($this->routes as $pattern => $params) {
      if (preg_match('#^' . $pattern . '$#', $path_info, $matches)) {
        $params = array_merge($params, $matches);

        return $params;
      }
    }
    return false;
  }
}