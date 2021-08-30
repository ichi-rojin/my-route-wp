<?php
/**
 * Class StackTest
 *
 * @package My_Route_Wp
 */

/**
 * Stack test case.
 */
final class StackTest extends WP_UnitTestCase
{
  public function test_ページ追加ができる()
  {
    $case = [
      'url' => 'test_page.html$',
      'name' => 'test_page',
    ];

    $MyRouteWp = MyRouteWp::getInstance();
    $MyRouteWp->addPage(
      $case['url'],
      $case['name'],
      function ()
      {
        header('Content-type: text/html; charset=UTF-8');
        echo '<div>テスト1</div>';
        exit;
      }
    );
    $rules = $MyRouteWp->getRules();
    $rule = $rules[$case['url']];
    $this->assertSame($rule['query'], $case['name'].'=1');
  }
  
  public function test_ルールが追加ができる()
  {
    $case = [
      'url' => 'single/([0-9]{1,})/?$',
      'rule' => 'p=$matches[1]',
    ];
    $MyRouteWp = MyRouteWp::getInstance();
    $MyRouteWp->addRule(
      $case['url'],
      $case['rule']
    );
    $rules = $MyRouteWp->getRules();
    $rule = $rules[$case['url']];
    $this->assertSame($rule['query'], $case['rule']);
  }
}