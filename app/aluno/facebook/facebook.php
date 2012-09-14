<?php
require_once('AppInfo.php');
require_once('utils.php');
//incluindo a classe de conexão com o facebook
require_once 'src/facebook.php';
 
if (substr(AppInfo::getUrl(), 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  exit();
}

$App_ID = '338115866266320';
$App_Secret = '3aeab59509c17ef4dfa32bf87a585211';
 
//Instanciando o Objeto da classe do facebook
$facebook = new Facebook(array(
        'appId'  => $App_ID ,
        'secret' => $App_Secret
));

$user_id = $facebook->getUser();
if ($user_id) {
  try {
    $basic = $facebook->api('/me/');
  } catch (FacebookApiException $e) {
    if (!$facebook->getUser()) {
      header('Location: '. AppInfo::getUrl($_SERVER['REQUEST_URI']));
      exit();
    }
  }
$app_using_friends = $facebook->api(array(
    'method' => 'fql.query',
    'query' => 'SELECT uid, name FROM user WHERE uid IN(SELECT uid2 FROM friend WHERE uid1 = me()) AND is_app_user = 1'
  ));
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="ISO8859-1" />
    <script type="text/javascript" src="/javascript/jquery-1.7.1.min.js"></script>

    <script type="text/javascript">
      function logResponse(response) {
        if (console && console.log) {
          console.log('The response was', response);
        }
      }
		//funções de compartilhamento;
      $(function(){
        $('#postToWall').click(function() {
          FB.ui(
            {
              method : 'feed',
              link   : $(this).attr('data-url')
            },
            function (response) {
              if (response != null) {
                logResponse(response);
              }
            }
          );
        });
        $('#sendToFriends').click(function() {
          FB.ui(
            {
              method : 'send',
              link   : $(this).attr('data-url')
            },
            function (response) {
              if (response != null) {
                logResponse(response);
              }
            }
          );
        });
        $('#sendRequest').click(function() {
          FB.ui(
            {
              method  : 'apprequests',
              message : $(this).attr('data-message')
            },
            function (response) {
              if (response != null) {
                logResponse(response);
              }
            }
          );
        });
      });
    </script>
  </head>
  <body>
  <script type="text/javascript">
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo $App_ID; ?>', // App ID
          channelUrl : '//<?php echo $_SERVER["HTTP_HOST"]; ?>/channel.html', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true // parse XFBML
        });
		//Login
        FB.Event.subscribe('auth.login', function(response) {
          window.location = window.location;
        });
        FB.Canvas.setAutoGrow();
      };
      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>
	<?php include("../includes/topoFace.htm"); ?>
	<?php if (isset($basic)) { ?>
	</table>
	<div style="padding: 12px;">
		<div align="left">
			<a href="#" id="postToWall" data-url="<?php echo AppInfo::getUrl(); ?>">Post-to-wall</a>&nbsp
            <a href="#" id="sendToFriends" data-url="<?php echo AppInfo::getUrl(); ?>">Send-to-friends</a>&nbsp
            <a href="#" id="sendRequest" data-message="Conheça o CIAWEB, portal eletrônico, do IFSP Campus Caraguatatuba!">Send-request</a>&nbsp
        </div>
        <div align="center">
       		<font color="#409B01"><h2>Bem-vindo ao CIAWEB, <?php echo "Victor Pugliese" /*he(idx($basic, 'name'));*/ ?></h2></font>		
		</div>
	<?php } else { ?>
    <div>
        <h1>Bem-vindo ao CIAWEB!</h1>
        <div class="fb-login-button" data-scope="user_likes,user_photos"></div>
    <?php } ?>
	</div>
	</body>
</html>