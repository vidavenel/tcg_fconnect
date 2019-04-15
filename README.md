Package laravel pour une autentification via le service france-connect

# User renvoyé après autentification
* id : sub (identifiant unique france-connect)
* name : prenom et nom du user
* email
* user : ensemble des infos france connect
    * given_name : 	prénoms séparés par des espaces (standard OpenIDConnect)
    * family_name : le nom de famille de naissance (standard OpenIDConnect)
    * birthdate : la date de naissance (standard OpenIDConnect)
    * gender : hommes ou femmes (standard OpenIDConnect)
    * birthplace : le code INSEE du lieu de naissance
    * birthcountry : le code INSEE du pays de naissance
    * email : 
    * preferred_username : le nom d'usage de la personne sera retourné
    * address
    * phone
    
# Parametrage
Dans le fichier de configuration (config/service.php) :

    'fconnect' => [
        'client_id' => env('FCONNECT_CLIENTID', 'c1e6fcda982ae2d41c731df8c21571de91006fd37fedcb2d8aff8e1f1677ed72'),
        'client_secret' => env('FCONNECT_KEY', '57e089b178847b2a42d96bf584484c05b59b99c133bd05dce6388becbc0d8616'),
        'redirect' => env('FCONNECT_CALLBACK', 'http://localhost:8000/fconnect/callback')
    ]