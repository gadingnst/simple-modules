<?php
  
/*
  # .htaccess configurtion 
  php_value auto_prepend_file "/rootdir/core/init.php" 
*/

define('SERVERHOST', '/');
date_default_timezone_set('Asia/Jakarta');

function redirect($url){
  if (!headers_sent()) {
    header('Location: '.$url);
  } else {
    echo '
      <script type="text/javascript">window.location.href="'.$url.'";</script>
      <noscript><meta http-equiv="refresh" content="0;url='.$url.'" /></noscript>
    ';
  }
  exit();
}

spl_autoload_register(function($module){
  $packagePath = __DIR__.'/packages/';
  $packages = array_diff(scandir($packagePath), ['.', '..']);
  foreach ($packages as $package) {
    $modulePath = $packagePath.$package;
    $file = is_dir($modulePath) ? "{$modulePath}/{$module}.php" : "{$packagePath}/{$module}.php";
    if (file_exists($file)) {
      require_once($file);
      break;
    }
  }
});
