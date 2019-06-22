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

    //HogeAction()を実行
    $content = $this->action_method($params);

    return $content;
  }
 }