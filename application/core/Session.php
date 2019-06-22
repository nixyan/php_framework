<?php
/**
 * Session
 * セッションを管理
 * 
 */

class Session
{
  protected static $sessionStarted = false;
  protected static $sesionIdRegenerated = false;

  public function __construct()
  {
    //セッション情報がなければsession_start()
    if (!self::$sessionStarted) {
      session_start();

      self::$sessionStarted = true;
    }
  }

  /**
   * $_SESSIONに変数をセット
   */
  public function set($name, $value)
  {
    $_SESSION[$name] = $value;
  }

  /**
   * $_SESSIONの変数を取得
   */
  public function get($name, $defualt = null)
  {
    if (isset($_SESSION[$name])) {
      return $_SESSION[$name];
    }
    return $defualt;
  }

  /**
   * $_SESSIONの変数を消去
   */
  public function remove($name)
  {
    unset($_SESSION[$name]);
  }

  /**
   * $_SESSIONを解放
   */
  public function clear()
  {
    $_SESSION = [];
  }

  /**
   * セッションIDの再発行
   */
  public function regenerate($destroy = ture)
  {
    if (!self::$sessionIdRegenerated) {
      session_regenerate_id($destroy);

      self::$sessionIdRegenerated = true;
    }
  }

  /**
   * 認証状態のセット
   */
  public function setAuthenticated($bool)
  {
    $this->set('_authenticated', (bool)$bool);

    //セッション固定攻撃対策
    $this->regenerate();
  }

  /**
   * 認証状態の確認
   */
  public function isAuthenticated()
  {
    return $this->get('_authenticated', false);
  }
}