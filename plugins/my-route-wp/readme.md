## My Route Wp
Contributors: 1rojin
Donate link: https://qiita.com/ichi-rojin
Tags: url, permalink
Requires at least: 5.8
Tested up to: 5.8
Requires PHP: 7.0
Stable tag: trunk
License: MIT
License URI: https://opensource.org/licenses/mit-license.php

The purpose of this plugin is to create URLs freely.
By including your own template, you can create a page without using the static page creation feature of WordPress.
Also, the created pages can use WordPress functions, and you can give parameters by setting your own rewrite rules.

## Description

This is the long description.  No limit, and you can use Markdown (as well as in the following sections).

For backwards compatibility, if this section is missing, the full length of the short description will be used, and
Markdown parsed.

## Installation

1. Upload `my-route-wp` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the following code in the theme you are using.

### The following example creates a page to check the current rewrite rules

```
$MyRouteWp = MyRouteWp::getInstance();
$MyRouteWp->addPage(
  'preview_rewrite_rules.html$',
  'preview_rewrite_rules',
  function ()
  {
    if( !current_user_can('administrator') ){
      global $wp_query;
      $wp_query->set_404();
      status_header(404);
      return;
    }

    header('Content-type: text/html; charset=UTF-8');
    global $wp_rewrite;
    echo '<pre>';
    var_dump($wp_rewrite);
    echo '</pre>';
    exit;
  }
);
```
If you want to give further URL parameters, you can add a rewrite rule by doing the following
```
$MyRouteWp->addRule(
  'preview_rewrite_rules/([0-9]{1,})/?$',
  'p=$matches[1]&preview_rewrite_rules=1'
);
```
4. Go to the permalink settings page (/wp-admin/options-permalink.php) and reset the rewrite rules.


## Frequently Asked Questions

### Is there a management screen?

No, please write the code in the theme you are using to set the URL. 