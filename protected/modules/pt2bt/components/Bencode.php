<?php
/**
 * https://raw.github.com/mjijackson/bencode/master/lib/Bencode.php
 * Contains a pair of recursive functions that implement the bencode data
 * encoding format.
 *
 * @author  Michael J. I. Jackson <mjijackson@gmail.com>
 */

class BencodeException extends Exception {

}

class Bencode {

  /**
   * The version of the library.
   *
   * @var string
   */
  const VERSION = "1.0";

  /**
   * Bencodes the given data structure.
   *
   * @param   mixed
   * @return  string
   * @throws  BencodeException
   */
  public static function encode($value) {
    if (is_null($value)) {
      return "0:";
    }
    if (is_string($value)) {
      return strlen($value) . ":" . $value;
    }
    if (is_numeric($value)) {
      return "i" . sprintf('%0.0f', $value) . "e";
    }
    if (is_array($value)) {
      if (self::isAssoc($value)) {
        ksort($value, SORT_STRING);
        $buffer = "d";
        foreach ($value as $key => $v) {
          $buffer .= self::encode(strval($key));
          $buffer .= self::encode($v);
        }
        $buffer .= "e";
      }
      else {
        ksort($value, SORT_NUMERIC);
        $buffer = "l";
        foreach ($value as $v) {
          $buffer .= self::encode($v);
        }
        $buffer .= "e";
      }
      return $buffer;
    }

    throw new BencodeException("Unable to encode data type: " . gettype($value));
  }

  /**
   * Tells whether an array is associative or not. In order to be non-associative,
   * each of the array's key numbers must correspond exactly to it's position
   * in the array.
   *
   * @param   array
   * @return  bool
   */
  public static function isAssoc($array) {
    return count($array) !== array_reduce(array_keys($array), array("Bencode", "isAssocCallback"), 0);
  }

  /**
   * Decodes the given string. The second parameter is only used in recursion.
   *
   * @param   string
   * @param   int
   * @return  mixed
   * @throws  BencodeException
   */
  public static function decode($tokens, &$i = 0) {
    if (is_string($tokens)) {
      $tokens = str_split($tokens);
    }


    switch ($tokens[$i]) {
      case "d":
        $dict = array();
        while (isset($tokens[++$i])) {
          if ($tokens[$i] == "e") {
            return $dict;
          }
          else {
            $key = self::decode($tokens, $i);
            if (isset($tokens[++$i])) {
              $dict[$key] = self::decode($tokens, $i);
            }
            else {
              throw new BencodeException("Dictionary key ($key) without a value at index $i");
            }
          }
        }
        throw new BencodeException("Unterminated dictionary at index $i");

      case "l":
        $list = array();
        while (isset($tokens[++$i])) {
          if ($tokens[$i] == "e") {
            return $list;
          }
          else {
            $list[] = self::decode($tokens, $i);
          }
        }
        throw new BencodeException("Unterminated list at index $i");

      case "i":
        $buffer = '';
        $T = FALSE;
        while (isset($tokens[++$i])) {
          if ($tokens[$i] == "e") {
            return round(floatval($buffer)); //FIXME
          }
          elseif (ctype_digit($tokens[$i])) {
            $buffer .= $tokens[$i];
          }
          elseif ($tokens[$i] == "-" && !$T) { //FIXME
            $buffer .= $tokens[$i];
            $T = TRUE;
          }
          else {
            throw new BencodeException("Unexpected token while parsing integer at index $i: {$tokens[$i]}");
          }
        }
        throw new BencodeException("Unterminated integer at index $i");

      case ctype_digit($tokens[$i]):
        $length = $tokens[$i];
        while (isset($tokens[++$i])) {
          if ($tokens[$i] == ":") {
            break;
          }
          elseif (ctype_digit($tokens[$i])) {
            $length .= $tokens[$i];
          }
          else {
            throw new BencodeException("Unexpected token while parsing string length at index $i: {$tokens[$i]}");
          }
        }
        $end = $i + round(floatval($length)); //FIXME
        $buffer = '';
        while (isset($tokens[++$i])) {
          if ($i <= $end) {
            $buffer .= $tokens[$i];
            if ($i == $end) {
              return $buffer;
            }
          }
        }
        throw new BencodeException("Unterminated string at index $i");

      case '0':
        while (isset($tokens[++$i])) {
          if ($tokens[$i] == ":") {
            return NULL;
          }
          else {
            throw new BencodeException("Unexpected token while parsing string length at index $i: {$tokens[$i]}");
          }
        }
    }
    throw new BencodeException("Unexpected token at index $i: {$tokens[$i]}");
  }

  /**
   * A callback function used by {@link isAssoc()}.
   *
   * @return  int
   */
  protected static function isAssocCallback($a, $b) {
    return $a === $b ? $a + 1 : 0;
  }

}

/**
 * Shorthand for {@link Bencode::encode()}.
 *
 * @param   mixed
 * @return  string
 * @throws  BencodeException
 * @see     Bencode::encode()
 */
//function bencode($value) {
//  return Bencode::encode($value);
//}

/**
 * Shorthand for {@link Bencode::decode()}.
 *
 * @param   string
 * @return  mixed
 * @throws  BencodeException
 * @see     Bencode::decode()
 */
//function bdecode($value) {
//  return Bencode::decode($value);
//}