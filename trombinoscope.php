<!DOCTYPE html>
<html>
<head>
    <title>Simple Template Example</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css"
          integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <script type="text/javascript" src="js/d3.v3.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">BAMBA Mory</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.html">dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="trombinoscope.php">Trombinoscope</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="agenda/callback.php">aurhentification google</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="grid.html">grid</a>
            </li>
        </ul>
    </div>
</nav>
<center>
    <h1>Trombinoscope</h1>
    <form method="get" action="agenda/index.php">
        <div id="etuTrombi"/>
        <div id="etuData"/>
        <input type="hidden" name="q" id ="q" value="present">
        <br>
        <label>Description</label>
        <input type="text" id="desc" name="desc" value="">
        <br>

        <script>

            var eleve = [
                "hatim.elmabrouk@gmail.com",
                "mansour.ismail.pro@gmail.com",
                "wehbihazem@gmail.com",
                "fb.mory@gmail.com",
                "elmounjide.hamza@gmail.com",
                "saadhamdani1995@gmail.com",
                "derfoufiabdel@gmail.com",
                "berradayacin@gmail.com",
                "bouna.nadia.isamm@gmail.com",
                "rabiataleb5@gmail.com",
                "aoua.kahina@gmail.com",
                "lydia.lebaz@gmail.com",
                "soukaynamanessoub@gmail.com",
                "yassine.you159@gmail.com",
                "hassina.salmi90@gmail.com",
                "rabahmaakni@gmail.com",
                "cylia.oulebsir@gmail.com",
                "roza.arezki@outlook.fr",
                "nourhenyahyaoui@gmail.com",
                "saimilamine1@gmail.com ",
                "cylia.oulebsir@yahoo.fr",
                "amenibenmrad@gmail.com",
                "eilabchiriboujnah@gmail.com"
            ]
            $.getJSON("https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=79672885f8a018343cd8849f57e8a50a&photoset_id=72157686924962130&extras=original_format&format=json&jsoncallback=?",
                function (data) {
                    console.log(data);
                    // Debut de la boucle
                    $.each(data.photoset.photo, function (i, item) {
                        // Sockage de l'image dans une variable
                        var photo = 'http://farm' + item.farm + '.static.flickr.com/' + item.server + '/' + item.id + '_' + item.secret + '_s.jpg';

                        // Sockage de l'url dans une variable
                        var url = 'http://farm' + item.farm + '.static.flickr.com/' + item.server + '/' + item.id + '_' + item.secret + '_c.jpg';
                        // Affichage des images dans la balise ul#images avec le l'url dans la balise li


                        if (i % 5 == 0) {
                            $("<br>").appendTo("#etuTrombi")
                        }
                        $("<img/>").attr({
                            src: photo,
                            alt: item.title
                        }).appendTo("#etuTrombi").wrap("<td><a href=' " + url + "' title=' " + item.title + " ' ></a> <input type='checkbox' name='email[]' value='" + eleve[i] + "' ></td>");

                    }); //Fin de la boucle
                });
            // Fin appel JSON


        </script>
        <input type="submit" value="enregistrer présence"  class="btn btn-primary">
        <br>
    </form>

</center>
</body>
</html>