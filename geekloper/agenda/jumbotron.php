<?php  
	$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/THYP_17-18/geekloper/agenda/callback.php';
?>


<div class="jumbotron">
  <h1 class="display-3">Absence management</h1>
  <p class="lead">This is a simple application to manage absences students using Google Agenda !</p>
  <hr class="my-4">
  <p>* You should have an Google account to access to this application</p>
  <p class="align-top">
    <a class="btn btn-primary btn-lg" href="<?php echo filter_var($redirect_uri, FILTER_SANITIZE_URL) ?>" role="button">Sign in with google</a>
  </p>
</div>