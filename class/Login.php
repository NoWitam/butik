<?php

class Login implements Modul
{

    private $blad;

    public function draw($a)
    {
		if(isset($_GET['page']) && $_GET['page'] == 'create' )
		{
			echo "
			
	     <div id='login_mid'>
        
           <div id='login'>
           <form action='' method='POST'>
            <h1>Stwórz konto</h1>
            <p>".$this->blad."</p>
            <fieldset><legend>Login</legend>
            <input type='text' name='login'>
            </fieldset>
            <fieldset><legend>Hasło</legend>
            <input type='password' name='haslo'>
            </fieldset>
			<fieldset><legend>Powtórz hasło</legend>
            <input type='password' name='p_haslo'>
            </fieldset>
            <input type='submit' name='stworz_konto' value='REJESTRACJA'>
		    <a href='".ADDRESS."profil' id='create'><p>Logowanie</p></a>
           </form>
           </div>
        
		</div>
			
			";
		}
		else
	    {
			
        echo "
        
        <div id='login_mid'>
        
        <div id='login'>
        <form action='' method='POST'>
         <h1>Logowanie</h1>
         <p>".$this->blad."</p>
         <fieldset><legend>Login</legend>
         <input type='text' name='login'>
         </fieldset>
         <fieldset><legend>Hasło</legend>
         <input type='password' name='haslo'>
         </fieldset>
         <input type='submit' name='logowanie' value='ZALOGUJ'>
		 <a href='".ADDRESS."profil/?page=create' id='create'><p>Stwórz konto</p></a>
         </form>
       </div>
        
        </div>
        
        ";
		}
    }

    public function drawCSS($a)
    {
        echo "<link rel='stylesheet' href='".$a."css/Login.css'>";
    }

    public function drawJS($a)
    {

    }

    public function PHP()
    {

         if(isset($_POST['logowanie']))
        {

            if(DataBase::login($_POST['login'], $_POST['haslo']))
            {
                header("Location: ".ADDRESS);
            }
            else
            {
                $this->blad = "Niepoprawny login lub haslo";
            }

        }
		 else 
		 {
			 if(isset($_POST['stworz_konto']))
             {
				 if($_POST['haslo'] != $_POST['p_haslo'])
				 {
					 $this->blad = "Hasła nie są identyczne";
				 }
				 if(strlen($_POST['haslo']) < 3 )
				 {
					 $this->blad = "Prosze wybrać dłuższe hasło";
				 }
				 if(strlen($_POST['login']) < 3 )
				 {
					 $this->blad = "Prosze wybrać dłuższy login";
				 }
                 if(empty($this->blad))
				 {
					  switch(DataBase::createAccount($_POST['login'], $_POST['haslo']))
					  {
						  case "good":
						    header("Location: ".ADDRESS."profil");
							break;
						  case "bad":
						    $this->blad = "Upss, coś poszło nie tak";
							break;
					      case "zajety":
						     $this->blad = "Taki użytkownik już istnieje";
							 break;
					  }
				 }	 
			 }
		 }
    }

}