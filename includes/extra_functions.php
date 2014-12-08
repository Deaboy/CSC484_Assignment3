<?php



class iimysqli_result
{
  public $stmt, $nCols, $colNames;
  function free() {}
}



/**
 * This function is a replacement for SOMEone *coughRODGERcough* not
 * installing the necessary plugin necessary to use full mysqli
 * functionality.
 *
 * I don't honestly know why we can't use the default stuff, just
 * that the stuff's not there. So we can use these instead.
 *
 * Not written by us.
 * Written by someone anonymous online.
 * Taken from the PHP manual: http://php.net/manual/en/mysqli-stmt.get-result.php
 *
 * Adapted to use associative arrays
 */
function iimysqli_stmt_get_result($stmt)
{
  /**    EXPLANATION:
   * We are creating a fake "result" structure to enable us to have
   * source-level equivalent syntax to a query executed via
   * mysqli_query().
   *
   *    $stmt = mysqli_prepare($conn, "");
   *    mysqli_bind_param($stmt, "types", ...);
   *
   *    $param1 = 0;
   *    $param2 = 'foo';
   *    $param3 = 'bar';
   *    mysqli_execute($stmt);
   *    $result _mysqli_stmt_get_result($stmt);
   *        [ $arr = _mysqli_result_fetch_array($result);
   *            || $assoc = _mysqli_result_fetch_assoc($result); ]
   *    mysqli_stmt_close($stmt);
   *    mysqli_close($conn);
   *
   * At the source level, there is no difference between this and mysqlnd.
   **/
  $metadata = mysqli_stmt_result_metadata($stmt);
  $ret = new iimysqli_result;
  if (!$ret) return NULL;

  $ret->nCols = mysqli_num_fields($metadata);
  $ret->colNames = mysqli_fetch_fields($metadata);
  $ret->stmt = $stmt;

  mysqli_free_result($metadata);
  return $ret;
}


/**
 * This function is a replacement for SOMEone *coughRODGERcough* not
 * installing the necessary plugin necessary to use full mysqli
 * functionality.
 *
 * I don't honestly know why we can't use the default stuff, just
 * that the stuff's not there. So we can use these instead.
 *
 * Not written by us.
 * Written by someone anonymous online.
 * Taken from the PHP manual: http://php.net/manual/en/mysqli-stmt.get-result.php
 *
 * Adapted to use associative arrays
 */
function iimysqli_result_fetch_array(&$result)
{
  $ret = array();
  $code = "return mysqli_stmt_bind_result(\$result->stmt ";

  for ($i=0; $i<$result->nCols; $i++)
  {
    $ret[$result->colNames[$i]->name] = NULL;
    $code .= ", \$ret['". $result->colNames[$i]->name ."']";
  };

  $code .= ");";
  if (!eval($code)) { return NULL; };

  // This should advance the "$stmt" cursor.
  if (!mysqli_stmt_fetch($result->stmt)) { return NULL; };

  // Return the array we built.
  return $ret;
}


?>
