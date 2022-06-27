<?php
	require_once('Rcon.php');
	$nick = isset($_POST['nick']) ? $_POST['nick'] : "NULL";
?>
<?php
	use thedudeguy\Rcon;
	$host = "nicepe.pl";
	$port = "19132";
	$password = "";
	$timeout = 3;

	$rcon = new Rcon($host, $port, $password, $timeout);

	if ($rcon->connect())
	{
		//$rcon->send_command("say Hello World");
	}
?>
<?php
if($nick == "NULL"){
	header('Location: itemshop.php');
}
else{

}
?>
<?php
$usluga = $_POST['usluga'];
?>

<?php
$id = 45558;
$code = $_POST['kod'];
if($usluga == 'vip'){
	$number = 72068;
	$desc = "ZAKUP RANGI VIP, KOD: $code | NICK: $nick";
}
elseif($usluga == 'svip'){
	$number = 74068;
	$desc = "ZAKUP RANGI SVIP, KOD: $code | NICK: $nick";
}
elseif($usluga == 'sponsor'){
	$number = 91758;
	$desc = "ZAKUP RANGI SPONSOR, KOD: $code | NICK: $nick";
}
elseif($usluga == 'pc16'){
	$number = 74068;
	$desc = "ZAKUP PREMIUMCASE (x16), KOD: $code | NICK: $nick";
}
elseif($usluga == 'pc32'){
	$number = 76068;
	$desc = "ZAKUP PREMIUMCASE (x32), KOD: $code | NICK: $nick";
}
elseif($usluga == 'pc64'){
	$number = 91058;
	$desc = "ZAKUP PREMIUMCASE (x64), KOD: $code | NICK: $nick";
}
elseif($usluga == 'pc128'){
	$number = 91758;
	$desc = "ZAKUP PREMIUMCASE (x128), KOD: $code | NICK: $nick";
}
elseif($usluga == 'unban'){
	$number = 76068;
	$desc = "ZAKUP UNBANA, KOD: $code | NICK: $nick";
}
else{
	$number == 'null';
	$desc = "null";
}

$site=file_get_contents("https://lvlup.pro/api/checksms?id=".$id."&code=".$code."&number=".$number."&desc=".$desc);

$json = json_decode($site);
?>
<?php
if($usluga == 'vip'){
	$komenda = "daj vip $nick";
}
elseif($usluga == 'svip'){
	$komenda = "daj svip $nick";
}
elseif($usluga == 'sponsor'){
	$komenda = "daj sponsor $nick";
}
elseif($usluga == 'pc16'){
	$komenda = "daj pc16 $nick";
}
elseif($usluga == 'pc32'){
	$komenda = "daj pc32 $nick";
}
elseif($usluga == 'pc64'){
	$komenda = "daj pc64 $nick";
}
elseif($usluga == 'pc128'){
	$komenda = "daj pc128 $nick";
}elseif($usluga == 'sponsor'){
	$komenda = "daj sponsor $nick";
}
elseif($usluga == 'unban'){
	$komenda = "daj unban $nick";
}
else{
	$komenda = "null";
}

?>

<!DOCTYPE html>
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
				<li class="nav-item"><a href="index.php" class="nav-link">STRONA GŁÓWNA</a></li>
				<li class="nav-item"><a href="itemshop.php" class="nav-link active">ITEMSHOP</a></li>
				<li class="nav-item"><a href="yt.php" class="nav-link">ODBIERZ RANGE YT</a></li>
				<li class="nav-item"><a href="https://discord.gg/K9QdzjP" target="_BLANK"class="nav-link">DISCORD</a></li>
				<li class="nav-item"><a href="https://discord.gg/mEEK2hb" target="_BLANK"class="nav-link">DISCORD JANPVP</a></li>
				<li class="nav-item"><a href="https://www.mediafire.com/file/2fk11ms3um0inzy/MCBE+1.11.1.2+Xbox+Apk+By+ItzToxicYT.apk" target="_blank" class="nav-link">LINK DO MCPE</a></li>
				<li class="nav-item ml-md-auto"><a class="nav-link disabled"><i class="fas fa-users"></i> PANEL KLIENTA</a></li>
			</ul>
		</div>
	</nav>

	<div class="navigatebar">
		<a href="index.php">STRONA GŁÓWNA</a> / <a href="itemshop.php">ITEMSHOP</a>
	</div>

<div class="shopbox">
<?php
	if ($json->valid)
	{
		echo '<div class="fix"></div>';
		echo '<div class="fix1"></div>';
		echo '<div class="truecode">Twoja usługa została aktywowana pomyślnie! Dziękujemy!</div>'.PHP_EOL;
		echo '<div class="fix"></div>';
		echo '<div class="fix1"></div>';
		$rcon->send_command("$komenda");
	}

	else
	{
		echo '<div class="fix"></div>';
		echo '<div class="fix1"></div>';
		echo '<div class="falsecode">Błąd! Podany kod jest nieprawidłowy!</div>'.PHP_EOL;
		echo '<div class="fix"></div>';
		echo '<div class="fix1"></div>';
	}
?>

</div>
</body>
</html>
