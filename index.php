<?php
  $context = [
      "ssl"=>[
          "verify_peer"=>false,
          "verify_peer_name"=>false,
      ]
  ];
  libxml_set_streams_context(stream_context_create($context));
  $url = 'https://valisertl.com/rss';
  $startListText = '<ol>';
  $endListText = '</ol>';
  $startTitleText = '<h2>';
  $endTitleText = '</h2>';
  $xml = simplexml_load_file($url);
?>
<!doctype html>
<html lang="fr">
	<head>
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"/>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		<meta name="robots" content="noindex, nofollow">
		<title>Valise RTL</title>
        <link rel="shortcut icon" href="valise.png">
	</head>
	<body>
		<div class="container">
            <?php
                $found = false;
                foreach ($xml->channel->item as $item){
                    $content = file_get_contents($item->link, null, stream_context_create($context));
                    if (strpos($content,$startListText) !== false)  {
                        $found = true;
                    } else {
                        continue;
                    }
                    $date = date("d/m/Y à h:i:s", strtotime($item->pubDate));
                    $startTitle = strpos($content,$startTitleText);
                    $endTitle = strpos($content,$endTitleText);
                    $title = strip_tags(substr($content, $startTitle, ($endTitle - $startTitle + strlen($endTitleText))));
                    $startList = strpos($content,$startListText);
                    $endList = strpos($content,$endListText);
                    $list = substr($content, $startList, ($endList - $startList + strlen($endListText)));
                    $listArray = explode('</li>',str_replace(['<ol>','</ol>','<li>'],'',$list));
                    $price = str_replace([' ',',','.','€','&euro;'],'',$listArray[0]);
                    if (!is_numeric($price)) {
                        $found = false;
                    }
                    if ($found) {
                        ?>
                            <h2 class="text-center"><?= $title ?><br><small><?= $date ?></small></h2>
                            <hr>
                            <ul class="list-group">
                            <?php
                                foreach ($listArray as $item) {
                                    if ($item != '') {
                                        ?>
                                        <li class="list-group-item"><?= $item ?></li>
                                        <?php
                                    }
                                }
                            ?>
                            </ul>
                        <?php
                        break;
                    }
                }
            ?>
            <hr>
            <div class="text-center">
                <a href="https://valisertl.com" target="_blank" class="btn btn-info">Sources : https://valisertl.com</a>
            </div>
		</div>
	</body>
</html>
