<!DOCTYPE html>
<html >
<head>
	<meta charset="UTF-8">
	<title>Pickmeamovie</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
	<link rel='stylesheet prefetch' href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900|RobotoDraft:400,100,300,500,700,900'>
	<link rel='stylesheet prefetch' href='http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
	<link rel="stylesheet" href="../css/login.css">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1">
</head>

<body>
  
<!-- Mixins-->
<!-- Pen Title-->
<div class="pen-title"></div>
<div class="container">
  <div class="card"></div>
  <div class="card">
    <h1 class="title">Login</h1>
    <form>
      <div class="input-container">
        <input type="#{type}" id="#{label}" required="required"/>
        <label for="#{label}">Username</label>
        <div class="bar"></div>
      </div>
      <div class="input-container">
        <input type="password" id="#{label}" required="required"/>
        <label for="#{label}">Password</label>
        <div class="bar"></div>
      </div>
      <div class="button-container">
          <button class="action-button"><span>Go</span></button>
      </div>
        <div class="button-container">
            <a href="#" class="action-button login-with-facebook">
                <span class="facebook-icon">
                    <i class="fa fa-facebook"></i>
                </span>
                <span class="login-with-facebook-span-color">Login with facebook</span>
            </a>
        </div>
		 <div class="button-container">
            <a href="#" class="action-button login-with-google-plus">
                <span class="google-plus-icon">
                    <i class="fa fa-google-plus"></i>
                </span>
                <span class="login-with-google-plus-span-color">Login with google</span>
            </a>
        </div>
      <div class="footer"><a href="#">Forgot your password?</a></div>
    </form>
  </div>
  <div class="card alt">
    <div class="toggle"></div>
    <h1 class="title">Register
      <div class="close"></div>
    </h1>
    <form>
      <div class="input-container">
        <input type="#{type}" id="#{label}" required="required"/>
        <label for="#{label}">Username</label>
        <div class="bar"></div>
      </div>
      <div class="input-container">
        <input type="password" id="#{label}" required="required"/>
        <label for="#{label}">Password</label>
        <div class="bar"></div>
      </div>
      <div class="input-container">
        <input type="password" id="#{label}" required="required"/>
        <label for="#{label}">Repeat Password</label>
        <div class="bar"></div>
      </div>
      <div class="button-container">
        <button class="action-button"><span>Next</span></button>
      </div>
    </form>
  </div>
</div>

  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

    <script src="../js/login.js"></script>

</body>
</html>