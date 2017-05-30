<?php
// Facebook SDK requires it
session_start();

// require direct to it's folder as it is easy to maintain
require_once '/usr/local/captiveportal/vendor/autoload.php';

try {
    
    $fb = new \Facebook\Facebook([
            'app_id' => 'YOUR-APP-ID',
            'app_secret' => 'YOUR-APP-SECRET',
            'default_graph_version' => 'v2.9',
            'persistent_data_handler'=>'session'
    ]);
    
    $helper = $fb->getRedirectLoginHelper();
    
    if (isset($_GET['state'])) {
        $helper->getPersistentDataHandler()->set('state', $_GET['state']);
    }
    
    $permissions = ['email']; // Optional permissions

    // your callback
    $loginUrl = $helper->getLoginUrl('your-url-callback', $permissions);
}
catch (\Exception $e)
{
}

$user = array ('email');

try {
    $accessToken = $helper->getAccessToken();

    // if we have accessToken means we are authenticated with Facebook already
    if (!empty($accessToken))
    {
        // get logged user info
        $response = $fb->get('/me?fields=id,name,email', $accessToken);
        $user = $response->getGraphUser();

        /*
         * For Radius users
         * */
        $dsn = 'mysql:host=host;dbname=dbname';
        $username = 'username';
        $password = 'password';
        $options = array();

        $connection = new PDO($dsn, $username, $password, $options);
        $stmt = $connection->prepare('SELECT * from radcheck WHERE username = ?');
        $stmt->execute(array ($user['email']));

        $result = $stmt->fetchAll();

        if (empty($result[0]))
        {
            $statement = $connection->prepare('INSERT INTO `radius`.`radcheck` (`id`,`username`,`attribute`,`op`,`value`) VALUES (NULL , :username, :attribute, :op, :value)');

            $statement->bindValue(':username', $user['email'], PDO::PARAM_STR);
            $statement->bindValue(':attribute', 'Cleartext-Password', PDO::PARAM_STR);
            $statement->bindValue(':op', ':=', PDO::PARAM_STR);
            $statement->bindValue(':value', md5($user['email']), PDO::PARAM_STR);
            
            $statement->execute();
            
            $statement = $connection->prepare("INSERT INTO userinfo(username, creationby) VALUES(:username, :creationby)");
            $statement->bindValue(':username', $user['email'], PDO::PARAM_STR);
            $statement->bindValue(':creationby', 'administrator', PDO::PARAM_STR);
            
            $statement->execute();
        }
    }

} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // dont show errors in production
    //echo 'Graph returned an error: ' . $e->getMessage();
    //exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // dont show errors in production
    //echo 'Facebook SDK returned an error: ' . $e->getMessage();
    //exit;
}
catch (Exception $e)
{
    // dont show errors in production
    //echo $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
    <body>
        <?php echo '<a href="' . htmlspecialchars($loginUrl) . '"><img src="captiveportal-facebook-login.png" /></a>'; ?>

        <form id="loginForm" name="loginForm" method="post" action="$PORTAL_ACTION$">
            <input name="auth_user" type="hidden" value="<?php echo $user['email'] ?>" />
            <input name="auth_pass" type="hidden" value="<?php echo md5($user['email']) ?>" />
            <input name="zone" type="hidden" value="$PORTAL_ZONE$" />
            <input name="redirurl" type="hidden" value="$PORTAL_REDIRURL$" />
            <input style="display: none" id="submitbtn" name="accept" type="submit" value="Continue" />
        </form>
        <script type="text/javascript">
        <?php if (!empty($accessToken)) : ?>
            document.getElementById("submitbtn").click();
        <?php endif; ?>
        </script>
    </body>
</html>
