<!DOCTYPE html>
<?php

$site = file_get_contents('https://mcapi.us/server/query?ip=nicepe.pl&port=19132');

$json = json_decode($site, true);

$gracze = $json['players']['now'];

$online = $json['players']['list'];

$administracja = 0;

if($online !== null){
$administracja_nicki = ['AdiPol1359', 'xStrixU', 'NooKierek', 'IncognitoName', 'v0xeleq', 'Roxer4', 'itsquickflash'];

foreach($administracja_nicki as $nick){
	if(in_array($nick, $online)) $administracja++;
}
}
?>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title>NicePE.PL - ItemShop</title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="AdiPol1359">
	<meta http-equiv="X-Ua-Compatible" content="IE=edge">

	<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	<script src="js/bootstrap.min.js"></script>



	<link rel="stylesheet" href="style.css" />
</head>
<body>
	<nav class="navbar navbar-dark bg-gray navbar-expand-sm">
		<button class="navbar-toggler ml-auto" data-toggle="collapse" data-target="#menu">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="menu">
			<ul class="navbar-nav">
				<li class="nav-item"><a href="index.php" class="nav-link active">STRONA GŁÓWNA</a></li>
				<li class="nav-item"><a href="itemshop.php" class="nav-link">ITEMSHOP</a></li>
				<li class="nav-item"><a href="yt.php" class="nav-link">ODBIERZ RANGE YT</a></li>
				<li class="nav-item"><a href="https://discord.gg/K9QdzjP" target="_BLANK"class="nav-link">DISCORD</a></li>
				<li class="nav-item"><a href="https://discord.gg/mEEK2hb" target="_BLANK"class="nav-link">DISCORD JANPVP</a></li>
				<li class="nav-item"><a href="https://www.mediafire.com/file/2fk11ms3um0inzy/MCBE+1.11.1.2+Xbox+Apk+By+ItzToxicYT.apk" target="_blank" class="nav-link">LINK DO MCPE</a></li>
				<li class="nav-item ml-md-auto"><a class="nav-link disabled"><i class="fas fa-users"></i> PANEL KLIENTA</a></li>
			</ul>
		</div>
	</nav>

	<div class="titlebox">
		<div class="fix"></div>
		<div class="fix1"></div>
		<div class="title">
			<h2><b>Z NAMI ROZPOCZNIESZ SWOJA PRZYGODE!</b></h2>
			<h3><b>NICEPE.PL - MCPE 1.11</b></h3>
			<button onclick="window.location.href='itemshop.php'">ITEMSHOP</button>
		</div>
	</div>

	<div class="box">
		<div class="fix"></div>
		<div class="fix1"></div>
		<div class="firstbox">
			<div class="firsttitle">
				<h3>STATYSTYKI SERWERA</h3>
				<div class="border"></div>
			</div>

			<div class="row">
				<div class="col-md-4">
					<div class="iconsbox">
							<i class="fas fa-users"></i>
						<h3>AKTUALNIE ONLINE</h3>
						<div class="userscounter"><b><?php echo $gracze;?></b></div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="iconsbox">
							<i class="fas fa-user"></i>
						<h3>ADMINISTRACJA ONLINE</h3>
						<div class="userscounter"><b><?php echo $administracja?></b></div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="iconsbox">
							<i class="fas fa-home"></i>
						<h3>ZAŁOŻONE GILDIE</h3>
						<div class="userscounter"><b>0</b></div>
					</div>
				</div>

			</div>
		</div>

		<div class="secondbox">
			<div class="firsttitle">
				<h3>TOP 3 GRACZE</h3>
				<div class="border"></div>
			</div>
			<div class="row">
				<div class="col-md-4">
			<div class="topplayerbox">
				<img src="img/head.jpg" width="130" height="130"/>
				<h3><b>AdiPol1359</b></h3>
					<div class="topcounter">500pkt</div>
			</div>
		</div>

		<div class="col-md-4">
	<div class="topplayerbox">
		<img src="img/head.jpg" width="130" height="130"/>
		<h3><b>AdiPol1359</b></h3>
			<div class="topcounter">500pkt</div>
	</div>
</div>

<div class="col-md-4">
<div class="topplayerbox">
<img src="img/head.jpg" width="130" height="130"/>
<h3><b>AdiPol1359</b></h3>
	<div class="topcounter">500pkt</div>
</div>
</div>
			</div>
	</div>
		<div class="fix"></div>
		<div class="fix1"></div>
	</div>


</body>
</html>
