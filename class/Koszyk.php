<?php


class Koszyk implements Modul
{

    public function draw($a)
    {
        $doZaplaty = 0;
        $ileNieDostepnych = 0;
        echo "

        <div id='koszyk'>
        <h1 id='naglowek'>Kosz (".$_SESSION['ileWKoszu'].")</h1>   
         ";

        $rzeczy = DataBase::showToCart($_SESSION['koszyk']);

        foreach ($rzeczy as $karta)
        {
            $this->drawItem($a, $karta);
            $doZaplaty += $karta['ilosc'] * $karta['cena'];
            if(!$karta['dostepny'])
            {
                $ileNieDostepnych++;
            }
        }

        if($_SESSION['ileWKoszu'] > 0)
        {
           echo "
          <form id='zamow' action='".ADDRESS."zamow'>
          <p id='cena'>Łącznie <a>" . $doZaplaty . " zł</a></p>
          ";

             if($ileNieDostepnych == 0)
             {
                 echo "<input type='submit' name='zamow' value='Zamów'>";
             }

        }

        echo "
        </form>
        <div id='whitespace'></div>
        </div>
        ";
    }
            
    public function drawCSS($a)
    {
        echo "<link rel='stylesheet' href='".$a."css/Koszyk.css'>";
    }

    public function drawJS($a)
    {
    }

    public function PHP()
    {

       if(isset($_POST['usun']))
       {
         DataBase::deleteFromCart($_POST['id'], $_POST['rozmiar']);
       }
       else
       {
           if(isset($_POST['aktualizuj']))
           {
               if($_POST['ilosc'] == '' or $_POST['ilosc'] == ' ' or $_POST['ilosc'] == 0)
               {
                   DataBase::deleteFromCart($_POST['id'], $_POST['rozmiar']);
               }
               else
               {
                   if(is_numeric($_POST['ilosc']))
                   {
                       DataBase::updateCart($_POST['id'], $_POST['rozmiar'], $_POST['ilosc']);
                   }
               }
           }
       }
    }

    private function drawItem($a, $item)
    {
        if($item['dostepny'])
        {
            $stan = 'dostepny';
        }
        else
        {
            $stan = 'niedostepny';
        }

        echo "
        <div class='item'>
        <form action='' method='POST'>
        <input type='hidden' name='id' value='".$item['id']."'>
        <input type='hidden' name='rozmiar' value='".$item['rozmiar']."'>
           <a href='".ADDRESS."product/?id=".$item['id']."'><img href='".ADDRESS."product/?id=".$item['id']."' src='".$a."base/".$item['img']."'></a>
        <div class='left'>    
        <div class='top'>
          <div>
            <a href='".ADDRESS."product/?id=".$item['id']."'><h1 class='nazwa'>".$item['nazwa']."</h1></a>
            <a class='model'>".$item['model']."</a>
            <a class='rozmiar'>Rozmiar: ".$item['rozmiar']."</a>
          </div>
        </div>
        
        <div class='bot'>
          <fieldset>
            <legend>Ilość</legend>
            <input type='number' min='0' name='ilosc' value='".$item['ilosc']."'>
          </fieldset>
          <input type='submit' value='Aktualizuj' name='aktualizuj'>
          <svg><circle class='".$stan."'></circle></svg>  
          <a class='a".$stan."'>Produkt ".$stan."</a>       
        </div>
        </div> 
        <div class='right'>
        <p class='cena'>".$item['ilosc']*$item['cena']." zł</p>
         <center><input type='submit' name='usun' value='X Usuń produkt'></center>
        </div>
             </form>
        </div>
                <hr>

        ";
    }

}