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

/*
// Pourrait servir en cas de création d'un ou plusieurs utilisateurs automatiquement.
// Le ou les groupes de l'utilisateur sont à ajouter dans : "$table_groupes" ("user_id","group_id")
// INSERT INTO "$table_groupes" ("user_id","group_id")
// VALUES (LAST_INSERT_ID(),'8');
*/

// Afficher les groupes existants pour chaque utilisateur.
// Groupe 2 = Enregistré. Groupe 8 = Super Admin.
// Définir et afficher la requête qui va afficher les groupes dans lesquels sont inscrits les utilisateurs :
$g = "SELECT user_id, group_id FROM $table_groupes";
echo "Afficher la requête de sélection des groupes utilisateur :<br/>$g<br/><br/>";

// Afficher le rendu de la requête :
echo "Afficher le rendu de la requête :<br/>";
$stmt = $pdo->prepare($g);
 
// Execution
$stmt->execute( );
 
// fetchAll() car PLUSIEURS LIGNES récupérées
$rowAll = $stmt->fetchAll(PDO::FETCH_BOTH);
 
foreach( $rowAll as $row )
{
echo 'Utilisateurs ID : '.$row['user_id'].' - Groupes ID : '.$row['group_id'].'<br />';
}



?>
