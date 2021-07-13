<?php


abstract class DataBase
{

    public static function showAll() // zwraca wszystkie dostepne buty
    {
        $baza=mysqli_connect(DB_SERVER,DB_USERNAME,DB_PW,DB_DB);

        if (mysqli_connect_errno())
        {
        return false;
        }

        $wynik = mysqli_query($baza,"SELECT * FROM buty WHERE ilosc38>0 OR ilosc39>0 OR ilosc40>0 OR ilosc41>0 OR ilosc42>0 OR ilosc43>0");

        if(!$wynik)
        {
            return false;
        }


        $produkty = Array();

        while($row = mysqli_fetch_array($wynik))
        {
            $produkty[] = new KartaProduktu($row);
        }


        mysqli_close($baza);

        return $produkty;
    }

    public static function showSome($where) // zwraca wszystkie dostepne buty ale po przefiltrowaniu wyników
    {
        if(count($where) > 0)
        {
            $baza = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PW, DB_DB);

            if (mysqli_connect_errno())
            {
                return false;
            }

            $query = "SELECT * FROM buty WHERE ";

            // budowanie warunku WHERE
            if (isset($where['typ']))
            {
                $query .= "typ IN (";
                for ($i = 0; $i < count($where['typ']); $i++)
                {
                  $query .= $where['typ'][$i].", ";
                }
                $query = substr($query, 0, -2);
                $query .=") AND ";
            }

            if(isset($where['rozmiar']))
            {
                $query .= "( ";
                for ($i = 0; $i < count($where['rozmiar']); $i++)
                {
                    $query .= $where['rozmiar'][$i].">0 OR ";
                }
                $query = substr($query, 0, -3);
                $query .=") AND ";
            } else //zeby pokazlo dostepne
            {
                $query .= "(ilosc38>0 OR ilosc39>0 OR ilosc40>0 OR ilosc41>0 OR ilosc42>0 OR ilosc43>0) AND ";
            }

            if(isset($where['producent']))
            {
                $query .= "producent IN (";
                for ($i = 0; $i < count($where['producent']); $i++)
                {
                    $query .= "'".$where['producent'][$i]."', ";
                }
                $query = substr($query, 0, -2);
                $query .=") AND ";
            }

            if(isset($where['cena']))
            {
                if(count($where['cena']) == 2)
                {
                    $query .= "cena BETWEEN ".$where['cena']['min']." AND ".$where['cena']['max']." AND ";
                }
                else
                {
                    if(isset($where['cena']['min']))
                    {
                        $query .= "cena > ".$where['cena']['min']." AND ";
                    }
                    else
                    {
                        $query .= "cena < ".$where['cena']['max']." AND ";
                    }
                }
            }

            if(isset($where['query']))
            {
                $query .= "( ";
                $dowyrzucenia = ['/\./', '/,/', '/-/'];
                $tekst = preg_replace($dowyrzucenia, ' ', $where['query']); //zastopienie przerywnikow na spacje
                $tekst = preg_replace('/\s\s+/', ' ', $tekst); //usuniecie nadmiaru spacji
                $wyrazy = explode(" ", $tekst);
                $kolumny = ['nazwa', 'model', 'producent', 'kolor', 'opis'];

                for($i=0;$i<count($kolumny);$i++)
                {
                    for($j=0;$j<count($wyrazy);$j++)
                    {
                        $query .= $kolumny[$i]." LIKE '%".$wyrazy[$j]."%' OR ";
                    }
                }
                $query = substr($query, 0, -3); //usuniecie ostatniego OR
                $query .= ") AND ";
            }

            $query = substr($query, 0, -4); // usuniecie ostatniego AND
            echo $query;
            //////////////////////////

             $wynik = mysqli_query($baza,$query);

            if(!$wynik)
            {
                return false;
            }

            $produkty = Array();

             while($row = mysqli_fetch_array($wynik))
             {
                 $produkty[] = new KartaProduktu($row);
             }

            mysqli_close($baza);


            return $produkty;
        }
        else
        {
            return self::showAll();
        }
    }

    public static function showOrderItmes($id) // zwraca informacje o butach z zamowienia o podanym id nawet jesli nie dostepne
    {
     $baza=mysqli_connect(DB_SERVER,DB_USERNAME,DB_PW,DB_DB);

        if (mysqli_connect_errno())
        {
        return false;
        }

        $query ="SELECT idBut, rozmiar, ilosc, cena FROM orderproducts WHERE idOrder=".$id;

        $wynik = mysqli_query($baza, $query);

        if(!$wynik)
        {
            return false;
        }

        $produkty = Array();

        while($row = mysqli_fetch_array($wynik))
        {
            $produkty[$row['idBut']] = $row;
            unset($produkty[$row['idBut']]['idBut']);
        }

        $query = "SELECT id, nazwa, model, img FROM buty WHERE id LIKE(";

        foreach($produkty as $id => $produkt)
        {
            $query .= $id.", ";
        }

        $query = substr($query, 0, -2);

        $query .= ")";

        $wynik = mysqli_query($baza, $query);

        if(!$wynik)
        {
            return false;
        }

        while($row = mysqli_fetch_array($wynik))
        {
            foreach($row as $tag => $value)
            {
                $produkty[$row['id']][$tag] = $value;
            }          
            $produkty[$row['id']]['img'] .= ".jpg";
            unset($produkty[$row['id']]['id']);
        }

        mysqli_close($baza);

        return $produkty;
    }

    public static function showOne($id) //zwaraca konkretny but nawet jesli niedostepny 
    {
        $baza = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PW, DB_DB);

        if (mysqli_connect_errno())
        {
            return false;
        }

        $query = "SELECT * FROM buty WHERE id = ".$id;

        $wynik = mysqli_query($baza,$query);

        if(!$wynik)
        {
            return false;
        }


        $wynik = mysqli_fetch_array($wynik);

        if(!isset($wynik['id']))
        {
            return false;
        }

        mysqli_close($baza);

        return new KartaProduktu($wynik);
    }

    public static function addToCart($but) // dodaje but do koszyka
    {
        if($_SESSION['isLogin'])
        {
            $baza = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PW, DB_DB);

            if (mysqli_connect_errno())
            {
                return false;
            }


            $tab = explode(";", $but);

            $wynik = mysqli_query($baza,"INSERT INTO `koszyk` (`id_uzytkownika`, `id_buta`, `rozmiar`, `ilosc`) VALUES ('".$_SESSION['id']."', '".$tab[0]."', '".$tab[1]."', '1')");

            if(!$wynik)
            {
                return false;
            }

            mysqli_close($baza);
        }


            for($i=0;$i<count($_SESSION['koszyk']);$i++)
            {
               if(preg_match("/".$but."/", $_SESSION['koszyk'][$i]))
               {
                   $tab = explode(";", $_SESSION['koszyk'][$i]);
                   $tab[2] = (int)$tab[2] + 1;
                   $_SESSION['koszyk'][$i] = $tab[0] . ";" . $tab[1] . ";" . $tab[2];
                   return true;
               }

            }
            $_SESSION['koszyk'][] = $but."1";
            return true;
    }

    public static function showToCart($buty) // pobiera informacje dla koszyka
    {
        $baza=mysqli_connect(DB_SERVER,DB_USERNAME,DB_PW,DB_DB);

        if (mysqli_connect_errno())
        {
            return false;
        }

        $rzeczy = Array();

        for($i=0; $i<count($buty); $i++)
        {
            $but = explode(";", $buty[$i]);
            $wynik = mysqli_query($baza,"SELECT nazwa, model, cena, img, ilosc".$but[1]." >= ".$but[2]." AS dostepny FROM buty WHERE id=".$but[0]);
            $wynik = mysqli_fetch_array($wynik);
            $wynik['img'] .='.jpg';
            $wynik['id'] = $but[0];
            $wynik['rozmiar'] = $but[1];
            $wynik['ilosc'] = $but[2];
            $rzeczy[] = $wynik;
        }

        mysqli_close($baza);

        return $rzeczy;
    }

    public static function deleteFromCart($id, $rozmiar)  // usuwa przedmiot z koszyka
    {
        if($_SESSION['isLogin'])
        {
            $baza = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PW, DB_DB);

            if (mysqli_connect_errno())
            {
                return false;
            }

            $query = "DELETE FROM koszyk WHERE id_buta=".$id." AND rozmiar=".$rozmiar;

            $wynik = mysqli_query($baza,$query);

            if(!$wynik)
            {
                return false;
            }

            mysqli_close($baza);
        }

        for($i=0;$i<count($_SESSION['koszyk']);$i++)
        {
            $tab = explode(";", $_SESSION['koszyk'][$i]);
                if($tab[0] == $id && $tab[1] == $rozmiar)
                {
                    unset($_SESSION['koszyk'][$i]);
                    $_SESSION['koszyk'] = array_values($_SESSION['koszyk']);
                    return true;
                }
        }
    }

    public static function updateCart($id, $rozmiar, $ilosc) // edytuje ilosc przedmiotu w koszyku
    {
        if($_SESSION['isLogin'])
        {
            $baza = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PW, DB_DB);

            if (mysqli_connect_errno())
            {
                return false;
            }

            $query = "UPDATE koszyk SET ilosc=".$ilosc." WHERE id_buta=".$id." AND rozmiar=".$rozmiar;

            $wynik = mysqli_query($baza,$query);

            if(!$wynik)
            {
                return false;
            }

            mysqli_close($baza);
        }

        for($i=0;$i<count($_SESSION['koszyk']);$i++)
        {
            $tab = explode(";", $_SESSION['koszyk'][$i]);
            if($tab[0] == $id && $tab[1] == $rozmiar)
            {
                $_SESSION['koszyk'][$i] = $tab[0].";".$tab[1].";".$ilosc;
                return true;
            }
        }
    }

    public static function login($login, $haslo) // logowanie sie, jesli poprawne dane logowania to zwraca true i wrzuca id uzytkownika do sesji, inaczej zwraca false
    {

        $baza = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PW, DB_DB);

        if (mysqli_connect_errno())
        {
            return false;
        }

        $query = "SELECT id, haslo FROM uzytkownicy WHERE login = '".$login."'";
		//password_hash($haslo, PASSWORD_DEFAULT)

        $wynik = mysqli_query($baza,$query);

        $wynik = mysqli_fetch_array($wynik);

        if(!isset($wynik['id']))
        {
            return false;
        }
        else
        {
			if(!password_verify($haslo, $wynik['haslo']))
			{
				return false;
			}
            $_SESSION['isLogin'] = true;
            $_SESSION['id'] = $wynik['id'];
            unset($_SESSION['koszyk']);
            $_SESSION['koszyk'] = Array();

            $query = "SELECT * FROM koszyk WHERE id_uzytkownika = ".$_SESSION['id'];

            $wynik = mysqli_query($baza,$query);

            while($row = mysqli_fetch_array($wynik))
            {
                $_SESSION['koszyk'][] = $row['id_buta'].";".$row['rozmiar'].";".$row['ilosc'];
            }


            mysqli_close($baza);

            return true;
        }
    }

    public static function updatePersonal($id, $tab) // zmienia dane osobowe uzytkoniwka
    {

        $baza = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PW, DB_DB);

        $query = "UPDATE uzytkownicy SET ";

        $_SESSION['info'] = $query;

        if (mysqli_connect_errno())
        {
            return false;
        }

        foreach ($tab as $pole => $value)
        {
            $query .= $pole." = '".$value."', ";
            $_SESSION['info'] = $query;
        }

        $query = substr($query, 0, -2);

        $_SESSION['info'] = $query;

        $query .= " WHERE id = ".$id;


        $_SESSION['info'] = $query;

        $wynik = mysqli_query($baza,$query);

        if(!$wynik)
        {
            return false;
        }

        mysqli_close($baza);

        $_SESSION['info'] = $query;

        return true;
    }
    
    public static function dowlandPersonal($id) // zwaraca dane osobowe uzytkownika
    {
        $baza = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PW, DB_DB);

        if (mysqli_connect_errno())
        {
            return false;
        }

        $query = "SELECT Email, Imie, Nazwisko, Numer_telefonu, Ulica, Nr, Kod_pocztowy, Miasto FROM uzytkownicy WHERE id = ".$id;

        $wynik = mysqli_query($baza,$query);

        if(!$wynik)
        {
            return false;
        }


        $wynik = mysqli_fetch_array($wynik);

        if(!isset($wynik['Email']))
        {
            return false;
        }

        mysqli_close($baza);

        return $wynik;
    }

    public static function addOrder($products, $dane) // tabela products (0-id, 1-rozmiar, 2-ilosc, cena - cena)
    {
      $baza = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PW, DB_DB);

        if (mysqli_connect_errno())
        {
            return false;
        }
 
        //sprawdzanie dostepnosci

        foreach ($products as $product)
        {
          $query =  "SELECT IF(ilosc".$product[1].">=".$product[2].", 'YES', 'NO') AS czyDostepny FROM buty WHERE id = ".$product[0];
          $wynik = mysqli_query($baza,$query);
          $wynik = mysqli_fetch_array($wynik);
          if($wynik['czyDostepny'] != 'YES')
          {
             mysqli_fetch_array($wynik);
             return false;
          }
        }

        //dodanie informacji zamowienia




           if($_SESSION['isLogin'])
          {
            $query = "
            INSERT INTO `orderdata` (`idUser`, `Email`, `Imie`, `Nazwisko`, `Numer_telefonu`, `Ulica`, `Nr`, `Kod_pocztowy`, `Miasto`)
            VALUES ('".$_SESSION['id']."', '".$dane['Email']."', '".$dane['Imie']."', '".$dane['Nazwisko']."', '".$dane['Numer_telefonu']."', '".$dane['Ulica']."', '".$dane['Nr']."', '".$dane['Kod_pocztowy']."', '".$dane['Miasto']."');
            ";         
          }
          else
          {
            $query = "
            INSERT INTO `orderdata` (`idUser`) VALUES ('NULL');";
          }

          $wynik = mysqli_query($baza,$query);          
    
          //dodanie produktow do zamowienia

          if($wynik)
          {       
             $id = mysqli_insert_id($baza); // id zamowienia

              foreach($products as $product)
              {
                $query = "INSERT INTO `orderproducts` (`idOrder`, `idBut`, `rozmiar`, `ilosc`, `cena`) VALUES ('".$id."', '".$product[0]."', '".$product[1]."', '".$product[2]."', ".$product[3].")";
                $wynik = mysqli_query($baza,$query);                 
              }
          }
          else 
          {
             mysqli_close($baza);
             return false;
          }       

          //zamiana stanu zamowienia

          $query = "";

          foreach($products as $product)
          {
               $query .= "UPDATE buty SET ilosc".$product[1]." = ilosc".$product[1]."-".$product[2]." WHERE id=".$product[1].";";
          }

          $wynik = mysqli_query($baza,$query);

            //wyczyszczenia koszyka

            if($_SESSION['isLogin'])
            {
            $query = "DELETE FROM koszyk WHERE id_uzytkownika=".$_SESSION['id'];
            $wynik = mysqli_query($baza,$query);
            }

            unset($_SESSION['koszyk']);
            $_SESSION['koszyk'] = Array();
   
        return true;      
    }
    
    public static function showOrders() // zwraca tablice z danymi dotyczacy zamowienien (id, data, laczna suma)
    {

      if(!isset($_SESSION['id']))
      {
        return false;
      }

       $baza = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PW, DB_DB);

        if (mysqli_connect_errno())
        {
            return false;
        }

        $query = "SELECT id, time FROM orderdata WHERE idUser = ".$_SESSION['id'];

        $wynik = mysqli_query($baza,$query);

        if(!$wynik)
        {
            return false;
        }

         while($row = mysqli_fetch_array($wynik))
             {
                 $zamowienia[$row['id']]['time'] = $row['time'];             
             }    
          
          foreach($zamowienia as $id => $zamowienie)
          {
       
                 $query = "SELECT ilosc, cena FROM orderproducts WHERE idOrder = ".$id;

                 $wynik = mysqli_query($baza,$query);
     
                if(!$wynik)
                {
                   return false;
                }
                
                $zamowienie['cena'] = 0;

                while($row = mysqli_fetch_array($wynik))
                     { 
                        $zamowienia[$id]['cena'] += $row['cena']*$row['ilosc'];
                     }
          }
      
        return $zamowienia;
        mysqli_close($baza);
    }

    public static function showOrderDetail($id) // zwaraca tablice z danymi zamowienia (danymi osobowymi i informacje o zamowionych butach)
    {
      $baza = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PW, DB_DB);

        if (mysqli_connect_errno())
        {
            return false;
        }

        $data = Array();

        $query = "SELECT * FROM orderdata WHERE id=".$id." AND idUser=".$_SESSION['id'];
        $wynik = mysqli_query($baza,$query);

        if(!$wynik)
        {
            return false;
        }

        while($row = mysqli_fetch_array($wynik))
             {
                  $data['dane'] = $row;
             }
             if(isset($data['dane']['idUser']) && $data['dane']['idUser'] == $_SESSION['id'] )
               {
                      unset($data['dane']['idUser']);
                      unset($data['dane']['id']);
               }
               else 
               {
                      return false;
               }

        $query = "SELECT idBut AS id, rozmiar, ilosc, cena FROM orderproducts WHERE idOrder = ".$id;

        $wynik = mysqli_query($baza,$query);

        if(!$wynik)
        {
            return false;
        }

        $query = "SELECT id, nazwa, model, img FROM buty WHERE id IN(";

        while($row = mysqli_fetch_array($wynik))
             {
               $data['buty'][$row['id']] = $row;
               unset($data['buty'][$row['id']]['id']);
               $query .= $row['id'].", ";
             }

        $query = substr($query, 0, -2);
        $query .= ")";

        $wynik = mysqli_query($baza,$query);
        
         if(!$wynik)
           {
             return false;
           }

        while($row = mysqli_fetch_array($wynik))
             {
               $data['buty'][$row['id']] += $row;
               $data['buty'][$row['id']]['img'] .= ".jpg";
               unset($data['buty'][$row['id']]['id']);
             }
       
        mysqli_close($baza);

        return $data;
    }
	
	public static function createAccount($login ,$haslo)
	{
		$baza = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PW, DB_DB);

        if (mysqli_connect_errno())
        {
            return "bad";
        }


        $query = "SELECT login FROM `uzytkownicy` WHERE login='".$login."'";
        $wynik = mysqli_query($baza,$query);

        if(!$wynik)
        {
            return "bad";
        }

        $row = mysqli_fetch_array($wynik);
        if(isset($row['login']))
		{
			return "zajety";
		}
		else 
		{
			$query = "INSERT INTO `uzytkownicy` (`login`, `haslo`) VALUES ('".$login."', '".password_hash($haslo, PASSWORD_DEFAULT)."')";
			$wynik = mysqli_query($baza,$query);
		}
			 
	    mysqli_close($baza);
        return "good";		
	}

}