<?php
  $filename = './telegram-log.txt';
  if ($_GET['u']) {
    $user_id = preg_replace("/[^0-9\-]/","",$_GET['u']);
  } elseif ($_GET['t']) {
    $user_id = preg_replace("/[^0-9\-]/","",$_GET['t']);
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $raw_ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $raw_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $raw_ip = $_SERVER['REMOTE_ADDR'];
    }
    if (strpos($raw_ip,',') !== false) {
      $raw_ip = explode(',',$raw_ip)[0];
    }
    $ip = preg_replace("/[^0-9\.]/","",$raw_ip);
    $url = 'https://api.jsecoin.com/captcha/check/'.$ip.'/';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    if (strpos($response, '"pass":true') !== false) {
      $output = $user_id.",".$ip."\r\n";
      file_put_contents($filename,$output,FILE_APPEND);
    }
    exit;
  } elseif ($_GET['c']) {
    $user_id = preg_replace("/[^0-9\-]/","",$_GET['c']);
    $file = file_get_contents($filename);
    if (strpos($file, $user_id) !== false) {
      echo '1';
    } else {
      echo '0';
    }
    exit;
  } else {
    exit;
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>JSEcoin Telegram Captcha</title>
  <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> 
  <style>
    body { text-align: center; font-family: 'Nunito', sans-serif; }
    .small { font-size: 10px; }
    .box { border: 1px solid #CCC; padding: 10px; width: 320px; margin: 10% auto; text-align: center; }
    h1 { font-size: 17px; font-weight: normal; }
    #iframe-reloader { height: 1px; width: 1px; }
    #JSE-captcha { margin-top: 20px; margin-bottom: 20px; }
    #JSE-captcha-container { margin: 0px auto !important; }
  </style>
</head>
<body>
  <div class="box">
    <img src="https://jsecoin.com/img/2019_sml.png" style="max-width: 260px;" alt="JSEcoin" />
    <h1>Please complete the captcha</h1>

    <div id="JSE-captcha"></div>
    <script type="text/javascript" src="https://api.jsecoin.com/captcha/load/captcha.js"></script>

    <a href="javascript:void(0);" onclick="returnToTelegram(); return false;">Return to Telegram</a>

    <iframe id="iframe-reloader"></iframe>
  </div>
  <script>
    function returnToTelegram() {
      window.open('', '_self', '');
      window.close();
      window.location = 'https://web.telegram.org';
      return false;
    }

    document.addEventListener("JSECaptchaPass", function(e) {
      console.log('Captcha completed by '+e.ip);
      document.getElementById('iframe-reloader').src = 'https://jsecoin.com/telegram-captcha.php?t=<?php echo $user_id; ?>';
    }, false);
  </script>
</body>
</html>
