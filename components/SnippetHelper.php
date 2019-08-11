<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\components;
use app\modules\catalog\widgets\SnippetWidget;

class SnippetHelper
{
  /**
   * 
   * @param string $code
   * @return string
   */
  public static function generateSnippetCode($code)
  {
    return "[[snippet:{$code}]]";
  }
  
  /**
   * 
   * @param string $content
   * @return string
   */
  public static function replaceSnippet($content)
  {
    $matches = [];
    if(!preg_match_all('/\[\[snippet:\w+\]\]/iu', $content, $matches)){
      return $content;
    }
    $replaces = [];
    foreach($matches[0] as $snippetCode) {
      $code = str_replace(['[[snippet:', ']]'], ['', ''], $snippetCode);
      $replaces[$snippetCode] = SnippetWidget::widget(['code' => $code]);
    }
    return str_replace(array_keys($replaces), array_values($replaces), $content);
  }
}