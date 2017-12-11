<?php include 'header.php'; 
session_start();
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) 
{ 

?>
<nav class="navbar navbar-toggleable-md navbar-inverse bg-primary">
  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="#">Geekloper</a>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item active">
        <a class="nav-link" href="index.php?out=1">Lougout<span class="sr-only">(current)</span></a>
      </li>
    </ul>
  </div>
</nav>

<div class="container">
<div class="row">

<?php $csvFile = file('https://docs.google.com/spreadsheets/d/e/2PACX-1vQxmWDytc5hSTaF-V-96gefaJxHJWnLGS7xudeNJChpgpvqWdskujnlt03TkiWRHtW5uoTV8sYAH3HZ/pub?gid=642939185&single=true&output=csv');

$rows = array_map('str_getcsv', $csvFile);

$header = array_shift($rows);
$csv    = array();
foreach($rows as $row) {
    $csv[] = array_combine($header, $row);
}

foreach ($csv as $key => $value) {

	$link='img/'. $value['lien vers la photo'];

    
	if (!is_dir($link) && file_exists($link)) {
	    $picture_link = $link;
	} else {
	    $picture_link =  "https://s-media-cache-ak0.pinimg.com/originals/ac/90/e9/ac90e95d1e6816448a5cb06e5b3b80fa.jpg";
	}

?>
	<div class="col-sm-3 mb-4">
	  <div class="card">
			<img class="card-img img-fluid" src="<?php echo $picture_link; ?>" alt="Card image">
			<p class="card-text"><?php echo $value['Prénom']; ?> <br> <?php echo $value['Nom']; ?> <br>
			<small class="text-muted"><?php echo $value['E-mail']; ?></small>
			</p>
	  </div>
	</div>

<?php } ?>
</div>
	<div class="row justify-content-md-center">
		<div class="pt-2 pb-5">
			<button id="confirm" type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" disabled>Send present students</button>
		</div>
	</div>
</div>

<?php

}
else{

	$redirect_uri = "http://" . $_SERVER['HTTP_HOST'] . "/THYP_17-18/geekloper/agenda/index.php";
	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));

} 


include 'footer.php'; 

?>
