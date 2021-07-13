<?php

class KartaProduktu
{
    private $id;
    private $nazwa;
    private $cena;
    private $rozmiary = Array();
    private $img;
    private $producent;
    private $kolor;
    private $opis;
    private $typ;
    private $model;

    function __construct($tab)
    {
        $this->id = $tab['id'];
        $this->nazwa = $tab['nazwa'];
        $this->cena = $tab['cena'];
        $this->rozmiary = [$tab['ilosc38'], $tab['ilosc39'], $tab['ilosc40'], $tab['ilosc41'], $tab['ilosc42'], $tab['ilosc43']];
        $this->img = $tab['img'].".jpg";
        $this->producent = $tab['producent'];
        $this->kolor = $tab['kolor'];
        $this->opis = $tab['opis'];
        $this->model = $tab['model'];
        $this->typ = $tab['typ'];  // 1 to kobieta, 0 to facet

        //cena pokazuje grosze mimo ze jest liczba calokowita
        if($this->cena - floor($this->cena) == 0)
        {
            $this->cena .=".00";
        }
    }

    public function drawMiniCard()
    {
       echo
       "  
       <a href='".ADDRESS."product/?id=$this->id'>
       <div class='produkt'>
        <img src='base/$this->img' alt='$this->nazwa'>
        <p class='p_nazwa'> $this->nazwa </p>  
        <p class='p_cena'> $this->cena zł</p>   
       </div></a>
       ";
    }

    public function drawCard()
    {
        echo "
        
        <div id='productpage'>
        
         <h1> $this->nazwa </h1>
        
         <div id='mid'>
          <div><img src='../base/$this->img' alt='$this->nazwa'></div> 
          <div id='asd'>
         <form method='POST'>
          <fieldset>
          <h2>$this->producent</h2>
          <center><a  href='".ADDRESS."?producent%5B%5D=".$this->producent."&submit=Filtruj' id='inne'>Zobacz inne produkty</a></center>
          <hr />
          ";

          if(array_sum($this->rozmiary) > 0)
          {
              echo  "<p class='text'>Wybierz rozmiar: <select name='rozmiar'>";
              for($i=38;$i<38+count($this->rozmiary);$i++)
              {
                  if($this->rozmiary[$i-38] > 0)
                  {
                      echo "<option value='".$i."'>".$i."</option>                                                  ";

                  }
              }
              echo "
               </select> </p>
               <hr />
                               <p class='text'>Cena: <a>$this->cena zł</a></p>
                               <center><input type='submit' id='abled' name='send' value='DODAJ DO KOSZYKA'></center>
                               </fieldset>
                               </form>
               ";
          }
          else
          {
              echo "<center><p class='text'>Produkt niedostępny</p></center>
                               <hr />
                               <p class='text'>Cena: <a>$this->cena zł</a></p>
                               <center><input type='submit' id='disabled' name='send' value='DODAJ DO KOSZYKA' disabled></center>
                               </fieldset>
                               </form>
              ";
          }



            echo"                      
         </div>
        </div>
        
        <div id='details'>
        <div id='details-text'>
          <a class='detal'>Szczegóły produktu</a> <br><br>
          <div class='left'><a>Producent:</a></div> <div class='right'><a>$this->producent</a></div>        
          <hr />
          <div class='left'><a>Model:</a></div> <div class='right'><a>$this->model</a></div>        
          <hr />
          <div class='left'><a>Kolor:</a></div> <div class='right'><a>$this->kolor</a></div>        
          <hr />
        </div>
        <div id='details-opis'> 
        <a class='detal'> Opis</a>  <br><br>
        <a>$this->opis</a>  
        </div>
        </div>
        
        </div>
        ";
    }

}