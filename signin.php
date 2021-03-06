<?php
/**
 * Sign-in page
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");

  $PAGETYPE = "public";
  require_once(RESOURCES_PATH . "/session.php");

  if (isSignedIn()) {
    header("Location: /index.php");
    exit();
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Sign in</title>
    <?php
    require_once(TEMPLATES_PATH . "/head.php");
    ?>
  </head>
  <body>
    <div class="container signin">
      <div class="signin-page">
        <?php
          if (isset($_SESSION["error"])) {
        ?>
        <div class="panel panel-danger signin-error-panel">
          <div class="panel-heading">
            <h3 class="panel-title"><strong>Oops!</strong></h3>
          </div>
          <div class="panel-body">
            <p>
            <?php
              switch ($_SESSION["error"]) {
                case "notregistered":     
                case "wrongpassword":
                  echo "Email and password do not match. Please, try again.";
                  break;
                case "missingdata":
                  echo "You should specify your email and password. Please, try again.";
                  break;
                case "unknownerror":
                default:
                  echo "Sorry, your request could not be processed. Please, try again later.";
                  break;
              }
            ?>
            </p>
          </div>
        </div>
        <?php
        }
        ?>

        <form class="form-signin <?= (isset($_SESSION["error"])) ? "form-signin-error" : "" ?>" data-toggle="validator" role="form" action="users/user_login.php" method="post">
          <div class="signin-title">
            <img src="img/pyramids-icon.png" alt="" />
            <div class="h3">Sign in</div>
          </div>
          <div class="form-group">
            <label for="email" class="sr-only control-label">Email address</label>
            <input type="text" pattern="^([^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+|root)$" name="email" id="email" class="form-control" placeholder="Email address" required="" autofocus="">
            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
            <div class="help-block with-errors">Type your email address</div>
          </div>
          <div class="form-group">
            <label for="password" class="sr-only control-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required="">
            <div class="help-block with-errors">Type your password</div>
          </div>
          <div class="form-group">
            <div class="row vertical-align">
              <div class="col-sm-6 col-xs-6">
                <a href="/signup.php" class="btn btn-lg btn-signup btn-link">Sign up</a>
              </div>
              <div class="col-sm-6 col-xs-6 text-right">
                <button class="btn btn-primary" type="submit">Sign in</button>
              </div>
            </div>
          </div>

          <?php if ($_SESSION["resetpassword"] == true) { ?>
          <div class="form-group">
            <span class="label label-success">
              <span class="glyphicon glyphicon-ok"></span> Done!
            </span>

            <p class="mt-3">
              From now on, you can sign in with your new password.
            </p>
          </div>
          <?php } ?>
        </form>
    </div>
    
    <footer>
      <div class="prompsit-gradient"></div>
      <div class="prompsit-info">
          <a target="_blank" href="http://www.prompsit.com" rel="noopener">
            <img src="/img/logotipo-prompsit.png" alt="Prompsit home" />
          </a>
      </div>
      <div class="pull-right">
        <a href="/forgot_password.php" class="btn btn-link" style="margin-top: 10px;">
          I forgot my password
        </a>
      </div>
    </footer>
  </div>
      
    <?php
    $_SESSION["error"] = null;
    $_SESSION["userinfo"] = null;
    $_SESSION["resetpassword"] = null;

    require_once(TEMPLATES_PATH . "/resources.php");
    ?>
  </body>
</html>
