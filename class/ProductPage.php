<?php

class ProductPage implements modul
{

    private $item;

    public function draw($a)
    {

        $this->item->drawCard();

        // jesli nacisniety przycisk 'dodaj do koszyka' to pokazuje okienka co ma zrobic dalej
        if (isset($_POST['send']) && isset($_POST['rozmiar']) && $_POST['rozmiar'] >= 38 && $_POST['rozmiar'] <= 43 ) {

            if(DataBase::addToCart($_GET['id'].";".$_POST['rozmiar'].";")) // TODO: sprawdzenie czy zostal pomyslnie dodany do kosza
            {
                echo "
        
        <div id='zadyma'>       
        <div id='powiadomienie'>
          <h1>Produkt został dodany do koszyka</h1>
          <input type='button' onclick='history.go(-1);' id='zakupy' value='Kontynuj zakupy'>
          <input type='button' onclick=\"location.href='" . ADDRESS . "koszyk'\" id='koszyk' value='Przejdź do koszyka'>
        </div>
        </div>
        ";
            }
            else
            {

                echo "
        <div id='zadyma'>       
        <div id='powiadomienie'>
          <h1 style='color: darkred'>Produkt nie został dodany do koszyka</h1>
          <input type='button' onclick='history.go(-1);' id='zakupy' value='Kontynuj zakupy'>
          <input type='button' onclick=\"history.go(-1);location.href='" . ADDRESS . "koszyk'\" id='koszyk' value='Przejdź do koszyka'>
        </div>
        </div>
            ";
            }
        }
  }

    public function drawCSS($a)
    {
        echo "<link rel='stylesheet' href='".$a."css/ProductPage.css'>";
    }

    public function drawJS($a)
    {

    }

    public function PHP()
    {
            if(isset($_GET['id']))
            {
                $this->item = DataBase::showOne($_GET['id']);
                if(!$this->item)
                {
                    header("Location: ".ADDRESS);
                }
            }
            else
            {
                header("Location: " . ADDRESS);
            }
    }

}