<?php

class MainPage implements Modul
{

   private $produkty = Array();
   private $where = Array();

    public function draw($a)
    {
      echo "
 
        <div id='produkty'>
        ";

      for($i=0;$i<count($this->produkty);$i++)
      {
          $this->produkty[$i]->drawMiniCard();
      }

      echo"
        </div>

      ";
    }

    public function drawCSS($a)
    {
        echo "<link rel='stylesheet' href='".$a."css/MainPage.css'>";
    }

    public function drawJS($a)
    {

    }

    public function PHP()
    {
        //czy byly filtrowane produkty
        if(isset($_GET['submit']))
        {
            //typ
            if($this->checkGET('typ') == 'array')
            {
            $this->where['typ'] = $_GET['typ'];
            }

            //rozmiar
            if($this->checkGET('rozmiar') == 'array')
            {
               for($i=0; $i < count($_GET['rozmiar']); $i++)
               {
                   $this->where['rozmiar'][] = "ilosc".$_GET['rozmiar'][$i];
               }
            }

            //producent
            if($this->checkGET('producent') == 'array')
            {
                $this->where['producent'] = $_GET['producent'];
            }

            //cena
            //              sprawdzenie czy jest tylko min lub tylko max                                 lub    istnieje to i to ale max jest wieksze/rowne min
            if( ($this->checkGET('cena_min') == 'int' xor $this->checkGET('cena_max') == 'int') or ( ($this->checkGET('cena_min') == 'int' and $this->checkGET('cena_max') )
                    && $_GET['cena_max'] >= $_GET['cena_min'] ))
            {
                if($this->checkGET('cena_min') == 'int')
                {
                    $this->where['cena']['min'] = $_GET['cena_min'];
                }
                if($this->checkGET('cena_max') == 'int')
                {
                    $this->where['cena']['max'] = $_GET['cena_max'];
                }
            }

            if(isset($_GET['query']))
            {
                $this->where['query'] = $_GET['query'];
            }

            $this->produkty = DataBase::showSome($this->where);

        }
        else
        {
            if(isset($_GET['query']))
            {
                $this->where['query'] = $_GET['query'];
                $this->produkty = DataBase::showSome($this->where);
            }
            else
            {
                $this->produkty = DataBase::showAll();
            }
        }
    }

    private function checkGET($get)
    {
        if(isset($_GET[$get]))
        {
            //tablica
            if(is_array($_GET[$get]) == 'array' and count($_GET[$get]) > 0)
            {
                return 'array';
            }
            if(is_numeric($_GET[$get]))
            {
                if(floor($_GET[$get]) == $_GET[$get])
                {
                    return 'int';
                }
            }
            if(is_string($_GET[$get]))
            {
                if($_GET[$get] != "" && $_GET[$get] != " ")
                {
                    return 'string';
                }
                return false;
            }
            return false;

        }
        return false;
    }

}