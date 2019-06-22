<?php
/**
 * HttpNotFoundException
 * 例外
 */

class HttpNotFoundException extends Exception
{
}

/**
 * try { ... 
 * } catch (HogeException $e) {
 *  //HogeExceptionの例外をキャッチ
 * } catch (FugaException $e) {
 *  //FugaExceptionの例外をキャッチ
 * } catch (Exception $e) {
 *  //すべての例外をキャッチ
 * }
 */