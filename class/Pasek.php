<?php

class Pasek implements Modul
{

    public function draw($a)
   {

    echo "

    <div id='pasek'> 
    
    <a href='".ADDRESS."profil'>
    <div class='ikona_div'>
       <img src='".$a."png/profil.png'
       alt = 'profil'
       class='ikona'>
       <p class='ikona_napis'
       >";

       if($_SESSION['isLogin'])
           echo 'PROFIL';
       else echo 'ZALOGUJ';

       echo "</p>
      </div>   </a>
   
      <a href='".ADDRESS."koszyk'>
     <div class='ikona_div'>
       <img src='".$a."png/koszyk.png'
       alt = 'koszyk'
       class='ikona'>
       <p class='ikona_napis'>
       KOSZYK</p>
       
       <div id='kosz_kropka'>
       <p id='kosz_kropka_napis'>";

       echo $_SESSION['ileWKoszu'];

       echo "</p>
       </div>   
     </div></a>
    
      <a href='".ADDRESS."'>   <div id='logo'>LOGO</div> </a>
    
       
     <div id='szukaj'>
      <input type='text' id='szukaj_input' name='query' >
      <input type='image' src='".$a."png/lupa.png' onclick='szukaj()' alt='szukaj' id='szukaj_ikona'>
     </div>
     
 </div>";
   }

    public function drawCSS($a)
    {
        echo "<link rel='stylesheet' href='".$a."css/Pasek.css'>";
    }

    public function drawJS($a)
    {
        echo"
        <script>
        function szukaj()
        {
        var url = window.location.href;
        
        if(url.indexOf('query=') != -1)
           {
                url = url.substr(0,url.indexOf('query=')-1);
           } 
                
        if(url.substr(-1) == '/')
            {
                url += '?query=';
            } 
            else
            {
                url += '&query=';   
            }
            var query = document.getElementById('szukaj_input').value;
            url += query;

       location.href= url;
        }
        
        function enter()
        {
           document.getElementById('szukaj_input').addEventListener('keydown', function (e){
              
            if (e.keyCode == 13)
            {
            szukaj();    
            }
              console.log('nie dziala');   
           });  
        }
        
        
        
        </script>
        ";
    }

    public function PHP()
    {
        $_SESSION['ileWKoszu'] = 0;

        if(count($_SESSION['koszyk']) > 0)
        {

            for($i=0;$i<count($_SESSION['koszyk']);$i++)
            {
                $exp = explode(";", $_SESSION['koszyk'][$i]);
                $_SESSION['ileWKoszu'] += $exp[2];
            }
        }
    }

}