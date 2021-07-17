<?php

abstract class SessionControler
{

    public static function start($a)
    {
        session_start();

        require_once $a."config.php";
        require_once $a."class/modul.php";
        require_once $a."class/DataBase.php";
        require_once $a."class/Pasek.php";
        require_once $a."class/MainPage.php";
        require_once $a."class/Filtr.php";
        require_once $a."class/KartaProduktu.php";
        require_once $a."class/ProductPage.php";
        require_once $a."class/Koszyk.php";
        require_once $a."class/Login.php";
        require_once $a."class/Profil.php";
        require_once $a."class/Zamow.php";

        $_SESSION['modules']['filtr'] = new Filtr();
        $_SESSION['modules']['mainpage'] = new MainPage();
        $_SESSION['modules']['pasek'] = new Pasek();
        $_SESSION['modules']['productpage'] = new ProductPage();
        $_SESSION['modules']['koszyk'] = new Koszyk();
        $_SESSION['modules']['login'] = new Login();
        $_SESSION['modules']['profil'] = new Profil();
        $_SESSION['modules']['zamow'] = new Zamow();


      if(!isset($_SESSION['isLogin']))
      {
          $_SESSION['isLogin'] = false;
          $_SESSION['koszyk'] = Array();
      }

    }

    public static function makePage($a, $modules)
    {
        echo "
       <!DOCTYPE html>
             <html lang='pl'>
             <head>
             <meta charset='UTF-8'>
             <title>Sklep z butami</title>
             <link rel='stylesheet' href='".$a."style.css'>
      ";

           for($i=0;$i<count($modules);$i++)
           {
               $_SESSION['modules'][$modules[$i]]->PHP();
           }

           for($i=0;$i<count($modules);$i++)
           {
               $_SESSION['modules'][$modules[$i]]->drawCSS($a);
           }

           for($i=0;$i<count($modules);$i++)
           {
               $_SESSION['modules'][$modules[$i]]->drawJS($a);
           }

           echo "</head><body onload='enter(), blad()'>";

           for($i=0;$i<count($modules);$i++)
           {
               $_SESSION['modules'][$modules[$i]]->draw($a);
           }

        echo "</body></html>";

    }

}