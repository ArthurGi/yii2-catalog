<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\components;
use app\modules\catalog\widgets\InteriorWidget;

class InteriorHelper
{
  /**
   * 
   * @param string $code
   * @return string
   */
  public static function generateSnippetCode($code)
  {
    return "[[interior:{$code}]]";
  }
  
  /**
   * 
   * @param string $content
   * @return string
   */
  public static function replaceSnippet($content)
  {
    $matches = [];
    if(!preg_match_all('/\[\[interior:\w+\]\]/iu', $content, $matches)){
      return $content;
    }
    $replaces = [];
    foreach($matches[0] as $snippetCode) {
      $code = str_replace(['[[interior:', ']]'], ['', ''], $snippetCode);
      $replaces[$snippetCode] = InteriorWidget::widget(['code' => $code]);
    }
    return str_replace(array_keys($replaces), array_values($replaces), $content);
  }
}