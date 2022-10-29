# BileMo


# Contexte

BileMo est une entreprise offrant toute une sélection de téléphones mobiles haut de gamme.

Vous êtes en charge du développement de la vitrine de téléphones mobiles de l’entreprise BileMo. Le business modèle de BileMo n’est pas de vendre directement ses produits sur le site web, mais de fournir à toutes les plateformes qui le souhaitent l’accès au catalogue via une API (Application Programming Interface). Il s’agit donc de vente exclusivement en B2B (business to business).

Il va falloir que vous exposiez un certain nombre d’API pour que les applications des autres plateformes web puissent effectuer des opérations.


# Besoin client

Le premier client a enfin signé un contrat de partenariat avec BileMo ! C’est le branle-bas de combat pour répondre aux besoins de ce premier client qui va permettre de mettre en place l’ensemble des API et de les éprouver tout de suite.

 Après une réunion dense avec le client, il a été identifié un certain nombre d’informations. Il doit être possible de :

    ● consulter la liste des produits BileMo ;
    ● consulter les détails d’un produit BileMo ;
    ● consulter la liste des utilisateurs inscrits liés à un client sur le site web ;
    ● consulter le détail d’un utilisateur inscrit lié à un client ;
    ● ajouter un nouvel utilisateur lié à un client ;
    ● supprimer un utilisateur ajouté par un client.



# Installation du projet :  
  
    ● Cloner le projet : git clone https://github.com/Abdessamad-Bannouf/BileMo.git
    
    ● Installer le gestionnaire de dépendance : composer  
        
    ● Lancer la commande : php bin/console doctrine:database:create  
      
    ● Lancer la commande : php bin/console make:migration  

    ● Lancer la commande : php bin/console doctrine:migrations:migrate  

    ● Lancer la commande : php bin/console doctrine:fixtures:load
    
    ● Lancer Postman :
    
    ● Allez sur la route :  http://localhost:8000/api/login_check
    
      ● dans l'onglet raw de body mettez votre username et password sous forme JSON :

      exemple {
      "username": "your username",
      "password": "your password"
      }
      
      ● Ensuite vous pourrez tester les différentes api's : 

      - Route[GET] Affiche une liste de smartphones => https://localhost:8000/api/smartphones
      - Route[GET] Affiche un smartphone => https://localhost:8000/api/smartphones/{id}
      - Route[GET] Affiche la liste d'utilisateurs lié à un client => https://localhost:8000/api/api/customers/{customer_id}/users
      - Route[GET] Affiche un utilisateur spécifique par rapport à un client => https://localhost:8000/api/customers/{customer_id}/users/{id}
      - Route[POST] Ajoute un utilisateur lié à un client  => https://localhost:8000/api/customers/{id}/users
      - Route[DELETE] Supprime un utilisateur => https://localhost:8000/api/users/{id}
      - Route[GET] Liste de tous les utilisateurs => https://localhost:8000/api/users
      - Route[GET] Liste tous les utilisateurs liés à un client => https://localhost:8000/api/customers/{id}/users
