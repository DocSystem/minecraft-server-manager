<?php
session_start();

if (isset($_POST["ip"])) {
  $ip = $_POST["ip"];
  $qport = $_POST["qport"];
  $rport = $_POST["rport"];
  $rpass = $_POST["rpass"];
  $mdp = $_POST["mdp"];
  $db = new SQLite3('database/minecraft.db');
  $db->exec("CREATE TABLE IF NOT EXISTS `server` ( `ip` VARCHAR(255) NOT NULL , `qport` INT(10) NOT NULL , `rport` INT(10) NOT NULL , `rpass` VARCHAR(255) NOT NULL , PRIMARY KEY (`ip`))");
  $db->exec("CREATE TABLE IF NOT EXISTS `login` ( `mdp` VARCHAR(255) NOT NULL , PRIMARY KEY (`mdp`))");
  $db->exec("INSERT INTO `server` (`ip`, `qport`, `rport`, `rpass`) VALUES ('$ip', '$qport', '$rport', '$rpass') ");
  $db->exec("INSERT INTO `login` (`mdp`) VALUES ('$mdp') ");
  echo "<script type='text/javascript'>document.location.href='/'</script>";
}

elseif (isset($_GET["reset"])) {
  $db = new SQLite3('database/minecraft.db');
  $db->exec("DROP TABLE `server`");
  $db->exec("DROP TABLE `login`");
  echo "<script type='text/javascript'>document.location.href='/setup.php'</script>";
}

else {
  ?><!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Setup - Minecraft Server Manager</title>
    <link rel="stylesheet" href="/css/setup.css">
  </head>
  <body>
    <div class="master">
      <div class="title">
        <h1>Setup - Minecraft Server Manager</h1>
      </div>
      <form action="setup.php" method="post">
        <div class="varinput">
          <p>Adresse IP :</p><br>
          <input type="text" id="ip" name="ip" placeholder="127.0.0.1" value="127.0.0.1" onclick="document.getElementById('ip').value=''">
        </div>
        <div class="varinput">
          <p>QUERY Port :</p><br>
          <input type="text" id="qport" name="qport" placeholder="25565" value="25565" onclick="document.getElementById('qport').value=''">
        </div>
        <div class="varinput">
          <p>RCON Port :</p><br>
          <input type="text" id="rport" name="rport" placeholder="25575" value="25575" onclick="document.getElementById('rport').value=''">
        </div>
        <div class="varinput">
          <p>RCON Password :</p><br>
          <input type="password" id="rpass" name="rpass" placeholder="Mot de passe" value="">
        </div>
        <div class="varinput">
          <p>Mot de passe du site :</p><br>
          <input type="password" id="mdp" name="mdp" placeholder="Mot de passe" value="">
        </div>
        <input type="submit" value="Terminer la configuration">
      </form>
    </div>
  </body>
</html><?php } ?>
