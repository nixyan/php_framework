<?php
/**
 * Controller
 * 画面を制御
 */

 abstract class Controller
 {
  protected $controller_name;
  protected $action_name;
  protected $application;
  protected $request;
  protected $response;
  protected $session;
  protected $db_manager;
  protected $auth_actions = [];

  //AppControllerのfindControllerアクションでApplication自身を引数にnewしている
  public function __construct($application)
  {
    //クラス名から"Controller"の10文字を抜い（て小文字にした）た値を格納
    $this->controller_name = strtolower(substr(get_class($this), 0 , -10));

    $this->application = $application;
    $this->request = $application->getRequest();
    $this->response = $application->getResponse();
    $this->session = $application->getSession();
    $this->db_manager = $application->getDbManager();
  }

  /**
   * アクションの実行
   */
  public function run($action, $params = [])
  {
    $this->action_name = $action;

    $action_method = $action . 'Action';
    if (!method_exists($this, $action_method)) {
      $this->forward404();
    }

    //ログインが必要だが未ログイン
    if ($this->needsAuthentication($action) && !$this->session->isAuthenticated()) {
      throw new UnauthorizedActionException();
    }

    //HogeAction()を実行
    $content = $this->action_method($params);

    return $content;
  }

  /**
   * 認証が必要なアクションかどうか
   */
  protected function needsAuthentication($action)
  {
    if ($this->auth_actions === true
    || (is_array($this->auth_actions) && in_array($action, $this->auth_actions))) {
      return true;
    }
    return false;
  }

  /**
   * ビューの呼び出し
   */
  public function render($variables = [], $template = null, $layout = 'layout')
  {
    $defaults = [
      'request' => $this->request,
      'base_url' => $this->request->getBaseUrl(),
      'session' => $this->session,
    ];

    $view = new View($this->application->getViewDir(), $defaults);

    if (is_null($template)) {
      $template = $this->action_name;
    }

    $path = $this->controller_name . '/' . $template;

    return $view->render($path, $variables, $layout);
  }

  protected function forward404()
  {
    throw new HttpNotFoundException('Forwarded 404 page from' . $this->controller_name . '/' . $this->action_name);
  }

  protected function redirect($url)
  {
    if (!preg_match('#https?://#', $url)) {
      $protocol = $this->request->isSsl() ? 'https://' : 'http://';
      $host = $this->request->getHost();
      $base_url = $this->request->getBaseUrl();

      $url = $protocol . $host . $base_url . $url;
    }

    $this->response->setStatusCode(302, 'Found');
    $this->response->setHttpHeader('Location', $url);
  }

  /**
   * クロスサイトリクエストフォージェリ対策
   */
  protected function generateCsrfToken($form_name)
  {
    $key = 'crsf_tokens/' . $form_name;
    $tokens = $this->session->get($key, []);

    //最大で10個トークンを保持、溢れたら古いものを削除
    if (count($tokens) >= 10) {
      array_shift($tokens);
    }

    //セッションIDと現在時刻をつなげたものをハッシュ化したものをトークンとする
    $token = sha1($form_name . session_id() . microtime());
    $tokens[] = $token;

    $this->session->set($key, $tokens);

    return $token;
  }

  /**
   * セッションチェック
   */
  protected function checkCsrfToken($form_name, $token)
  {
    $key = 'csrf_tokens/' . $form_name;
    $tokens = $this->session->get($key, []);

    //トークンが格納されていれば
    if (false !== ($pos = array_search($token, $tokens, true))) {
      unset($tokens[$pos]);
      $this->session->set($key, $tokens);

      return true;
    } 

    return false;
  }
 }