# BileMo


# Installation du projet :  
  
    ● Cloner le projet : git clone https://github.com/Abdessamad-Bannouf/SnowTricks.git
    
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
        
    
    
