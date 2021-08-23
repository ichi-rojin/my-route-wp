<?php
/**
 * Plugin Name:     My Route Wp
 * Plugin URI:      https://github.com/ichi-rojin/my-route-wp
 * Description:     This plugin allows you to customize WordPress URLs freely
 * Author:          ichi-rojin
 * Author URI:      https://qiita.com/ichi-rojin
 * Text Domain:     my-route-wp
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         My_Route_Wp
 */


// プラグイン有効化時にリライトルールをクリアする。
register_activation_hook(__FILE__, 'flush_rewrite_rules');

class MyRouteWp
{
	private static $instance = [];
	private $struct = array();
	private $rules = array();
	private $vars = array();

	private function __construct()
	{
		add_filter('query_vars', array(&$this, 'queryVars'), 9999);
		add_action('wp', array(&$this, 'wp'), 9999);
		add_action('pre_get_posts', array(&$this, 'overwriteRewriteStruct'), 9999);
		$this->run();
	}

	public static function getInstance()
	{
		$class = get_called_class();
		if (!isset(self::$instance[$class]))
			self::$instance[$class] = new $class;

		return self::$instance[$class];
	}
	public final function __clone()
	{
		throw new \Exception('Clone is not allowed against' . get_class($this));
	}

	public function addStruct($name, $struct, $args = false)
	{
		$this->struct[$name] = array(
			'struct' => $struct,
			'args' => $args
		);
	}

	// Getter
	public function getRules()
	{
		return $this->rules;
	}

	// リライトルールを追加する
	public function addRule($rule, $query, $prior = true)
	{
		$this->rules[$rule] = array(
			'query' => $query,
			'prior' => $prior
		);
	}

	private function run(){
		add_action(
			'generate_rewrite_rules',
			array(&$this, 'generateRewriteRules'),
			9999
		);
	}

	public function generateRewriteRules($wp_rewrite)
	{
		foreach( $this->rules as $rule => $value ){
			$new_rules[$rule] = $wp_rewrite->index . '?' . $value['query'];
			if( $value['prior'] === true ){
				$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
			} else {
				$wp_rewrite->rules = $wp_rewrite->rules + $new_rules;
			}
		}
	}

	public function queryVars($vars) {
		foreach( $this->vars as $var ){
			$vars[] = $var['query'];
		}
		return $vars;
	}

	// 静的ページを追加する
	public function addPage($rule, $query, $callback, $prior = true)
	{
		$this->vars[] = array(
			'query' => $query,
			'callback' => $callback,
		);
		$this->rules[$rule] = array(
			'query' => $query . '=1',
			'prior' => $prior
		);
	}

	private function parseQuery($query)
	{
		$query = explode('&', $query);
		$query = explode(
			'=',
			is_array($query) && isset($query[0]) ? $query[0] : $query
		);
		return (is_array($query) && isset($query[0]) ? $query[0] : $query);
	}

	public function wp()
	{
		foreach( $this->vars as $var ){
			if (get_query_var($this->parseQuery($var['query']))) {
				if( !is_null($var['callback']) ){
					call_user_func($var['callback']);
				}
			}
		}
	}

	public function overwriteRewriteStruct()
	{
    global $wp_rewrite;
		foreach( $this->struct as $key => $val ){
      $wp_rewrite->add_permastruct( $key, $val['struct'], $val['args'] );
		}
	}
}

