<?php

class Zamow implements Modul
{

 private $doZap�aty = 0;
 private $dane;

 public function draw($a)
 {


   echo "
    
     <div id='zamow_mid'>

     <div id='zamow'>
     <h1>Podaj dane osobowe do zamówienia</h1>
     <form method='POST' action=''>
     ";
     
     $this->dane->draw();

   echo"
     <div id='bot'>
     <p class='left'>Suma: </p>
     <p class='right'>".$this->doZaplaty." zł</p>
     <div onmouseover='zablokuj()' id='przycisk'><center><input type='submit' name='submit' id='submit' value='ZAMAWIAM I PŁACĘ'></center></div>
     </div>
     </form>
     </div>

     </div>

   ";
 }

 public function drawCSS($a)
 {
        echo "<link rel='stylesheet' href='".$a."css/Zamow.css'>";
        $this->dane->drawCSS($a);
 }

 public function drawJS($a)
 {
       $this->dane->drawJS();
 }

 public function PHP()
 {

    $produkty = Array();

        $rzeczy = DataBase::showToCart($_SESSION['koszyk']);
        $ileNieDostepnych=0;
        foreach ($rzeczy as $karta)
        {
            $this->doZaplaty += $karta['ilosc'] * $karta['cena'];

            if(!$karta['dostepny'])
            {
                $ileNieDostepnych++;
            }
        }

       
     if(isset($_POST['submit']))
     {
       unset($_POST['submit']);
       foreach($_SESSION['koszyk'] as $zamowienie)
       {
        $produkty[] = explode(";", $zamowienie);
       }

       $int = 0;
        foreach ($rzeczy as $karta)
        {
            $this->doZaplaty += $karta['ilosc'] * $karta['cena'];
            $produkty[$int][3] = $karta['cena']; 
            $int++;
        }

       if(DataBase::addOrder($produkty, $_POST))
       {
              header("Location: ".ADDRESS);
       }
       else
       {
              header("Location: ".ADDRESS."koszyk");
       } 

     }

    if($_SESSION['ileWKoszu'] == 0 OR $ileNieDostepnych != 0)
     {
          header("Location: ".ADDRESS."koszyk");
     }

     // pojawienie sie danych uzytkownika jesli zalogowany
     $this->dane = new DaneOsobowe();
     $dane = DataBase::dowlandPersonal($_SESSION['id']);
            if($dane != false)
            {
                $this->dane->osobowe = $dane;
            } 
    

}

}