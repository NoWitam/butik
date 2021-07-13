<?php
require_once "DaneOsobowe.php";

class Profil implements Modul
{
    private $dane;

    function __construct()
    {
        $this->dane = new DaneOsobowe();
    }

    public function draw($a)
    {
        $czyZamowienie = false;
        $class1 = 'bar-check';
        $class2 = 'bar';
        if(isset($_GET['zamowienia']))
        {
            $czyZamowienie=true;
            $class1 = 'bar';
            $class2 = 'bar-check';
        }

        echo "
        <div id='profil_mid'>
        
        <div id='profil'>
          <div id='menu'>
           <form method='get' action=''>        
              <center><div><input type='submit' name='dane' value='Dane' class='".$class1."'></div></center>   
              <center><div><input type='submit' name='zamowienia' value='Zamówienia' class='".$class2."'></div></center> 
              <center><div><input type='submit' name='wyloguj' value='Wyloguj' class='zielony'></div></center>
          </form>
          </div>
          ";

        if($czyZamowienie)
        {
            $this->drawZamowienia();
        }
        else
        {
            $this->drawDane();
        }

        echo"
        </div>
        
        </div>
        ";
    }

    public function drawCSS($a)
    {
        echo "<link rel='stylesheet' href='".$a."css/Profil.css'>";
        $this->dane->drawCSS($a);
    }

    public function drawJS($a)
    {
        $this->dane->drawJS();
    }

    public function PHP()
    {
        // obsluga wylogowania sie

        if(isset($_GET['wyloguj']))
        {
            unset($_SESSION['id']);
            $_SESSION['isLogin'] = false;
            header("Location: ".ADDRESS."profil");
            unset($_SESSION['koszyk']);
            $_SESSION['koszyk'] = Array();
        }
          

        // obsluga danych osobowych   
        $this->dane = new DaneOsobowe();

        if(isset($_POST['submit'])) // jesli zostaly wyslane dane do wyslania
        {
            unset($_POST['submit']);

            $this->dane->walid($_POST);

            if($this->dane->czyPoprawnaWalidacja != 'nie')
            {
                $this->dane->czyPoprawnaWalidacja = 'tak';

                if(!DataBase::updatePersonal($_SESSION['id'], $_POST))
                {
                 //   $this->czyPoprawnaWalidacja = 'nie';
                }

            }

        }
        else
        {
            $dane = DataBase::dowlandPersonal($_SESSION['id']);
            if($dane != false)
            {
                $this->dane->osobowe = $dane;
            }
        }  
    }

    private function drawDane()
    {

       echo "<div id='right'>";
       echo "<div id='right_mid'><form action='' method='POST'>";


       $this->dane->draw();

       if($this->dane->czyPoprawnaWalidacja == 'tak')
       {
           echo "<a class='a_good'>Dane zapisane poprawnie</a>";
       }
        if($this->dane->czyPoprawnaWalidacja == 'nie')
       {
           echo "<a class='a_bad'>Dane wpisane niepoprawnie</a>";
       }

       echo "<center><div style='width: 50mm, height: 20mm'  onmouseover='zablokuj()'><input type='submit' id='submit' name='submit' class='zielony hover' value='Zapisz'></div></center>";


        echo "</form></div></div>";
    }

    private function drawZamowienia()
    {
         if(!isset($_GET['id']))
         {
         
            echo "
                 <div id='right'>      
                 ";
            $zamowienia = DataBase::showOrders();

            if($zamowienia != false)
              {
                 foreach($zamowienia as $id => $zam)
                        {
                          $date = explode("-", $zam['time']);
                          $date = $date[2][0].$date[2][1].".".$date[1].".".$date[0];
                           echo "
                                <div class='zamowienie'>           
                                  <a>Data: ".$date." </a>
                                  <a>Suma: ".$zam['cena']." zł </a>
                                  <div><a href='".ADDRESS."profil/?zamowienia&id=".$id."' class='szczegoly'> Pokaż szczegóły</a></div>
                                </div>
                                ";
                        }                 
                 echo "
                      </div>
                      ";
              }
         } 
         else
         {
              $a = DataBase::showOrderDetail($_GET['id']);
              $czas = $a['dane']['time'];
              $czas = explode(" ", $czas);
              $data = explode("-", $czas[0]);
              $godzina = explode(":", $czas[1]);
              $data = $data[2].".".$data[1].".".$data[0];
              $godzina = $godzina[0].":".$godzina[1];
              $kasa = 0;
              foreach($a['buty'] as $but)
              {
                    $kasa += $but['ilosc']*$but['cena'];
              }

          echo "
               <div id='zam'>
               <div id='zam_buty'>";

               echo "
                     <div id='info'>
                       <h1>Zamówiono ".$data."</h1>
                       <p id='p_left'> o godzinie ".$godzina."</p>
                       <p id='p_right'>za łączną sumę ".$kasa."zł</p>
                     </div> 
                    ";

      
           foreach($a['buty'] as $id => $but )
                  {
                        $this->drawItem($but);
                  }
          echo "</div><div id='zam_dane'><div id='right_mid'>";
          $this->dane->osobowe = $a['dane'];
          $this->dane->draw(true);
          echo "</div></div></div>";
         }
    }

    private function drawItem($item)
    {
         echo "
              <div class='item'>
              <div class='item_all'>
                  <a href='".ADDRESS."product/?id=".$item['id']."'><img href='".ADDRESS."product/?id=".$item['id']."' src='".$a.ADDRESS."base/".$item['img']."'></a>
                <div class='right'>   

                      <a href='".ADDRESS."product/?id=".$item['id']."'><h1 class='nazwa'>".$item['nazwa']."</h1></a>
                    <div class='informacje'>
                        <div class='blok1'>
                          <p>Model: ".$item['model']."</p>
                          <p>Ilość: ".$item['ilosc']."</p>
                        </div>
                        <div class='blok2'>
                          <p>Rozmiar: ".$item['rozmiar']."</p>
                          <p>Cena: ".$item['ilosc']*$item['cena']."zł</p>
                        </div>
                    </div>
   
                </div> 
              </div>
                     <hr>
              </div>
                                       

        ";//  
        /*
          <table>
                       <tr>
                         <td class='td_left'><a class='model'>Model: ".$item['model']."</a></td>
                         <td><a class='rozmiar'>Rozmiar: ".$item['rozmiar']."</a></td>
                       </tr>
                       <tr>
                         <td><a>Ilość: ".$item['ilosc']." </a></td>
                         <td><p>Cena: ".$item['ilosc']*$item['cena']." zł</p></td>
                       </tr>
                      </table>
                      */
    }

}