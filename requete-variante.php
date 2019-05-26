<?php
// Définir les variables pour la connexion à votre base de données :
$user = ".............";
$passwd = ".............";
$host = ".............";
$bdd = ".............";

// Modifier la valeur du prefixe par celle utilisée pour votre installation :
// Exemple : jos
$prefixe = ".............";

// Déclaration du mail administrateur en cas d'erreur de connexion à la base de données :
$mail_admin = ".............";

// ######################################
// Ne pas modifier les lignes suivantes #
// ######################################

// Définir la table des utilisateurs et la table des groupes :
$_users = "_users";
$_user_usergroup_map = "_user_usergroup_map";
// Ajouter le préfixe devant les tables :
$table_utilisateurs = "$prefixe$_users";
$table_groupes = "$prefixe$_user_usergroup_map";

// ##########################
// Connexion MySQL avec PDO #
// ##########################

// Déclaration de la variable de connexion pour la requête de sélection :
$dsn = "mysql:host=$host;dbname=$bdd;port=3306;charset=utf8";
// Connexion au serveur MySQL pour la requête de sélection :
try {
$pdo = new PDO($dsn, "$user" , "$passwd");
}
catch (PDOException $exception) {
mail('$mail_admin', 'PDOException', $exception->getMessage());
exit('Erreur de connexion à la base de données.');
}

// Définir et afficher la requête qui va lister les utilisateurs de Joomla et le mail associé au compte :
$q = "SELECT username, email FROM $table_utilisateurs";
echo "La requête de sélection :<br/>$q<br/><br/>";

// Afficher le rendu de la requête de sélection :
echo "Le rendu de la requête :<br/>";
$stmt = $pdo->prepare($q);
// Execution de la requête
$stmt->execute( );
 
// Utiliser fetchAll() car plusieurs lignes sont récupérées :
$rowAll = $stmt->fetchAll(PDO::FETCH_BOTH);

// Faire une boucle pour afficher les résultats de la requête de sélection :
foreach( $rowAll as $row )
{
echo 'Utilisateur : '.$row['username'].' - Mail : '.$row['email'].'<br />';
}




// Facultatif - Exporter les résultats de la requête de sélection vers un fichier XML :
// UTF-8 --- ISO-8859-1
$xml = '<?xml version="1.0" encoding="UTF-8"?>'.'<selection>';
		foreach( $rowAll as $row ) {
			$xml .= '<personne>';
			$xml .= '<login>'.$row["username"].'</login>';
			$xml .= '<password>'.$row["password"].'</password>';
			$xml .= '<email>'.$row["email"].'</email>';
			$xml .= '</personne>';
		}
		$xml .= '</selection>';
		
		$fp = fopen("fichier-selection.xml", 'w+');
		fputs($fp, $xml);
		fclose($fp);
// Consulter le fichier qui a été créé :
echo 'Export XML effectue : <a href="fichier-selection.xml">Voir le fichier</a>';




// Déclarer la requête pour lister les ID utilisateurs et le login associé :
$p = "SELECT id, username FROM $table_utilisateurs";
echo "Afficher la requête pour connaître les ID utilisateurs :<br/>$p<br/><br/>";

// Afficher le rendu de la requête ID utilisateurs et le login associé :
echo "<br/><br/>Afficher le rendu de la requête :<br/>";
$stmt = $pdo->prepare($p);
 
// Execution de la requête :
$stmt->execute( );

// Utiliser fetchAll() car plusieurs lignes sont récupérées :
$rowAll = $stmt->fetchAll(PDO::FETCH_BOTH);

// Début de la boucle foreach.
// Le rendu de la requête sous forme de liste :
foreach( $rowAll as $row )
{
// Déclarer une variable pour stocker un nouveau mot de passe utilisateur basé sur un unique mot (secret) et l'identifiant utilisateur (exemple : secret191) :
$newpassword = "secret";
$newpassword .= $row['id'];

// Lister la proposition de changement de mot de passe pour tous les utilisateurs.
echo 'Utilisateurs ID : '.$row['id'].' - Login  : '.$row['username'].' - Nouveau mot de passe : '.$newpassword.'<br />';

// Affecter la valeur du login à la variable utilisateur.
$utilisateur = $row['username'];

// Déclarer la variable $id avec la valeur stockée dans la colonne id.
$id = $row['id'];

// Test conditionnel :
// Seul le mot de passe de l'identifiant 191 est modifié !

// Le test conditionnel est commenté. Tous les mots de passe sont remplaçés !
/*
if ($id == 191) {
*/

// Le mot de passe $newpassword est hashé avec BCrypt et placé dans la variable $lepassword.
$lepassword = password_hash("$newpassword", PASSWORD_DEFAULT);

// Se connecter à nouveau à la BDD pour permettre la mise à jour du mot de passe.
try {
$conn = new PDO("mysql:host=$host;dbname=$bdd", $user, $passwd);
// Gestion des erreurs :
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// La requête de mise à jour du mot de passe :
$sql = "UPDATE $table_utilisateurs SET password='$lepassword' WHERE id='$id'";

$stmt = $conn->prepare($sql);
$stmt->execute();

// Message pour informer que la mise à jour a été effectuée :
echo $stmt->rowCount() . "<br/> Mise à jour effectuée !";
}
catch(PDOException $e)
{
echo $sql . "<br>" . $e->getMessage();
}
$conn = null;

echo "<br/>L'utilisateur $utilisateur a un nouveau mot de passe ($newpassword).<br/>Le hash bcrypt est $lepassword<br/><br/>";

// Le test conditionnel est commenté. Tous les mots de passe sont remplaçés !
/*
} else {
echo "La condition id=191 ne correspond pas.<br/>Le mot de passe de l'utilisateur $utilisateur ID $id n'est pas modifié.<br/><br/>";
}
*/

// Fin de la boucle foreach.
}
?>
