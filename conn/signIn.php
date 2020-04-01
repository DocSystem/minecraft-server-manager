<?php
session_start();

if (isset($_POST['mdp'])) {
  // Si le formulaire est vérifié :
  $password = $_POST['mdp'];
  $requete = "SELECT * FROM `login` where `mdp` = '".$password."'";
  $db = new SQLite3('../database/minecraft.db');
  $res = $db->query($requete);
  while ($r = $res->fetchArray()) {
    $_SESSION["mdp"] = $r["mdp"];
  }
}
echo "<script>document.location.href='/'</script>";
?>
