<?php
/**
 * Application
 * Request, Response, Sessionなどのオブジェクト管理
 * ルーティング定義、コントローラ実行、レスポンス送信
 * ディレクトリへのパスの管理、デバッグモード
 */

abstract class Application
{
  protected $debug = false;
  protected $request; //Requestクラスのインスタンスを格納
  protected $response; //Responseクラスのインスタンスを格納
  protected $session; //Sessionクラスのインスタンスを格納
  protected $db_manager; //DbManagerクラスのインスタンスを格納

  public function __construct($debug = false)
  {
    $this->setDebugMode($debug);
    $this->initialize();
    $this->configure();
  }

  /**
   * デバッグモード（エラーの表示）
   */
  protected function setDebugMode($debug)
  {
    if ($debug) {
      $this->debug = ture;
      ini_set('display_errors', 1);
      error_reporting(-1);
    } else {
      $this->debug = false;
      ini_set('display_errors', 0);
    }
  }

  /**
   * クラスの初期化
   */
  protected function initialize()
  {
    $this->request = new Request();
    $this->response = new Response();
    $this->session = new Session();
    $this->db_manager = new DbManager();
    $this->router = new Router($this->registerRoutes());
  }

  /**
   * アプリケーションごとの設定
   */
  protected function configure()
  {
  }

  abstract public function getRootDir();

  //抽象メソッド
  abstract protected function registerRoutes();

  public function isDebugMode()
  {
    return $this->debug;
  }

  public function getRequest()
  {
    return $this->request;
  }

  public function getResponse()
  {
    return $this->response;
  }

  public function getSession()
  {
    return $this->session;
  }

  public function getDbManager()
  {
    return $this->db_manager;
  }

  public function getControllerDir()
  {
    return $this->getRootDir() . '/controllers';
  }

  public function getViewDir()
  {
    return $this->getRootDir() . '/views';
  }

  public function getModelDir()
  {
    return $this->getRootDir() . '/models';
  }

  public function getWebDir()
  {
    return $this->gerRootDir() . '/web';
  }

  /**
   * パラメータを元にアクションを呼び出し
   */
  public function run()
  {
    try {
      //Routerに取得したパラメータを渡す
      $params = $this->router->resolve($this->request->getPathInfo());
      if ($params === false) {
        throw new HttpNotFoundException('No route found for ' . $this->request->getPathInfo());
      }

      $controller = $params['controller'];
      $action = $params['action'];

      $this->runAction($controller, $action, $params);

    //404エラーページ
    } catch (HttpNotFoundException $e) {
      $this->render404Page($e);
    }
  
    $this->response->send();
  }

  /**
   * 各コントローラのアクションを実行
   */
  public function runAction($controller_name, $action, $params = [])
  {
    $controller_class = ucfirst($controller_name). 'Controller';

    $controller = $this->findController($controller_class);
    if ($controller === false) {
      throw new HttpNotFoundException($controller_class . ' controller id not found.');
    }
    //各コントローラのrunメソッド実行
    $content = $controller->run($action, $params);

    $this->response->setContent($content);
  }

  /**
   * コントローラ名に対応するクラスファイルを読み込みコントローラをnewする
   */
  protected function findController($controller_class)
  {
    if (!class_exists($controller_class)) {
      $controller_file = $this->getControllerDir() . '/' . $controller_class . '.php';

      if (!is_readable($controller_file)) {
        return false;
      } else {
        require_once $controller_file;

        if (!class_exists($controller_class)) {
          return false;
        }
      }
    }

    return new $controller_class($this);
  }

  protected function render404Page($e)
  {
    $this->response->setStatusCode(404, 'Not Found');
    $message = $this->isDebugMode() ? $e->Message() : 'Page not found.';
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    $this->response->setContent(<<<EOF
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>404</title>
</head>
<body>
  {$message}
</body>
</html>
EOF
    );
  }
}