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
    
# Routes
3 routes à creer
* login/fconnect
* fconnect/callback
* logout/fconnect **desactiver la vérification CSRF**

# Controller
* login :


    public function redirectToProvider()
    {
        return Socialite::driver('fconnect')->redirect();
    }
* redirect après auth franceconnect


    public function handleProviderCallback()
    {
        /** @var \Laravel\Socialite\Two\User $fc_user */
        $fc_user = Socialite::driver('fconnect')->user();
        session()->put('fc_token', $fc_user->user['id_token']);

        /** @var User $user */
        $user = User::firstOrCreate(['fc_sub' => $fc_user->id], [
            'name' => $fc_user->name,
            'email' => $fc_user->email
        ]);
        \Auth::login($user);

        return redirect($this->redirectTo);
        // $user->token;
    }
* redirect apres logout


    public function fc_logout_redirect(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }
* completer la methode logout
    
    
    if (session()->has('fc_token')) {
        return \Redirect::away(url('https://fcp.integ01.dev-franceconnect.fr/api/v1/logout') .
            '?id_token_hint=' . session()->get('fc_token') .
            '&state=' . sha1(mt_rand(0,mt_getrandmax())) .
            '&post_logout_redirect_uri=' . url('logout/fconnect')
        );
    }   