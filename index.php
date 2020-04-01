<?php

// On importe les fonctions et on ouvre la session
session_start();

if (!isset($_SESSION["mdp"])) {
	$db = new SQLite3('database/minecraft.db');
	$res = $db->query('SELECT * FROM `login`');
	if ($res != false) {
		while ($r = $res->fetchArray()) {
			if (!isset($r["mdp"])) {
				echo "<script type='text/javascript'>document.location.href='setup.php?reset'</script>";
			}
		}
	}
	?><!DOCTYPE html>
<html lang="fr" dir="ltr">
	<head>
		<meta charset="utf-8">
		<title>Connexion - Minecraft Server Manager</title>
		<link rel="stylesheet" href="/css/setup.css">
	</head>
	<body>
		<div class="master">
			<div class="title">
				<h1>Connexion - Minecraft Server Manager</h1>
			</div>
			<form action="conn/signIn.php" method="post">
				<div class="varinput">
          <p>Mot de passe :</p><br>
          <input type="password" name="mdp" placeholder="Mot de passe" value="">
        </div>
				<input type="submit" value="Se connecter">
			</form>
		</div>
	</body>
</html>
	<?php
}
else{
// Le SQL
$db = new SQLite3('database/minecraft.db');
$res = $db->query('SELECT * FROM `server`');
if ($res != false) {
	while ($r = $res->fetchArray()) {
		$ip = $r['ip'];
		$qport = $r['qport'];
		$rport = $r['rport'];
		$rpass = $r['rpass'];
	}
}
else {
	echo "<script type='text/javascript'>document.location.href='setup.php'</script>";
}

	//--------------------------------------------------------------------------
	//			CONNEXION AU RCON POUR ENVOYER DES COMMANDES AU SERVEUR
	//--------------------------------------------------------------------------
	require_once('Rcon.class.php');

	$r = new rcon($ip,$rport,$rpass); // Remplacer l'ip, le port et le mot de passe par les votres

	if(isset($_POST['submit'])){

		$command = $_POST['command'];

			if($r->Auth())
			{
			$r->rconCommand($command);
			}
	}

	define( 'MQ_SERVER_ADDR', $ip ); // Remplacer l'ip par la votre
	define( 'MQ_SERVER_PORT', $qport ); // Remplacer le port par le votre
	define( 'MQ_TIMEOUT', 1 );

	Error_Reporting( E_ALL | E_STRICT );
	Ini_Set( 'display_errors', true );

	require __DIR__ . '/MinecraftQuery.class.php';

	$Timer = MicroTime( true );
	$Query = new MinecraftQuery( );

	try
	{
		$Query->Connect( MQ_SERVER_ADDR, MQ_SERVER_PORT, MQ_TIMEOUT );
	}
	catch( MinecraftQueryException $e )
	{
		$Error = $e->getMessage( );
	}
?><!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>Minecraft Server Manager</title>
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css">
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="/css/master.css">
	</head>
	<body onLoad="gonow()">
    <div class="container">
			<div class="jumbotron">
      	<h2>Minecraft Server Manager</h2>
        <p>Bienvenue sur le Minecraft Server Manager, ce projet est une initiative de <a href="https://twitter.com/DocSystemMC">DocSystem</a>. J'espère que cela vous plaira !<br />
        Merci d'utiliser Minecraft Server Manager ! La seule chose que vous devez faire, c'est activer RCON et QUERY dans le fichier server.properties<br />
				Si vous le souhaitez, vous pouvez <a href="setup.php?reset">reconfigurer</a> ou vous <a href="conn/logOut.php">déconnecter</a> de l'application.</p>
			</div>
			<?php if( isset( $Exception ) ): ?>
			<div class="panel panel-primary">
				<div class="panel-heading"><?php echo htmlspecialchars( $Exception->getMessage( ) ); ?></div>
				<p><?php echo nl2br( $e->getTraceAsString(), false ); ?></p>
			</div>
			<?php else: ?>
			<div class="row">
				<div class="col-sm-6">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th colspan="2">Information serveur <em>(queried in <?php echo $Timer; ?>s)</em></th>
							</tr>
						</thead>
						<tbody>
							<?php if( ( $Info = $Query->GetInfo( ) ) !== false ): ?>
							<?php foreach( $Info as $InfoKey => $InfoValue ): ?>
							<tr>
								<td><?php echo htmlspecialchars( $InfoKey ); ?></td>
								<td><?php
									if(Is_Array($InfoValue)) {
										echo "<pre>";
										print_r( $InfoValue );
										echo "</pre>";
									}
									else {
										echo htmlspecialchars( $InfoValue );
									}
									?>
								</td>
							</tr>
							<?php endforeach; ?>
							<?php else: ?>
							<tr>
								<td colspan="2">Pas d'informations reçues ! Vérifiez le port QUERY</td>
							</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
				<div class="col-sm-6">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Joueurs</th>
							</tr>
						</thead>
						<tbody>
							<?php if( ( $Players = $Query->GetPlayers( ) ) !== false ): ?>
							<?php foreach( $Players as $Player ): ?>
							<tr>
								<td><?php echo htmlspecialchars( $Player ); ?></td>
							</tr>
							<?php endforeach; ?>
							<?php else: ?>
							<tr>
								<td>Aucun joueur en ligne</td>
							</tr>
							<?php endif; ?>
						</tbody>
					</table>
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Console</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<script>
									function gonow() {
										document.getElementById('logs').contentWindow.scrollTo(10,100000);
									}
									</script>
									<iframe id="logs" src="server/logs/latest.log"></iframe>
								</td>
							</tr>
							<tr>
								<td>
									<form method="post" role="form">
										<div class="input-group">
											<span class="input-group-addon"><span class="glyphicon glyphicon-chevron-right"></span></span>
											<input type="text" name="command" class="form-control" placeholder="Entrez votre commande">
											<span class="input-group-btn">
												<input type="submit" name="submit" class="btn btn-default" value="Envoyer" />
											</span>
										</div>
									</form>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</body>
</html><?php } ?>
