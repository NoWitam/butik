<?php

class Filtr implements Modul
{

    private $typ1 = "";
    private $typ0 = "";
    private $ilosc38 = "";
    private $ilosc39 = "";
    private $ilosc40 = "";
    private $ilosc41 = "";
    private $ilosc42 = "";
    private $ilosc43 = "";
    private $f = "";
    private $lasocki = "";
    private $nb = "";
    private $adidas = "";
    private $min = "";
    private $max = "";


    function __construct()
    {

        if(isset($_GET['typ']))
        {
            if(in_array('0',$_GET['typ']))
            {
                $this->typ0 = "checked";
            }
            if(in_array('1',$_GET['typ']))
            {
                $this->typ1 = "checked";
            }
        }

        if(isset($_GET['rozmiar']))
        {
            if(in_array('38',$_GET['rozmiar']))
            {
                $this->ilosc38 = "checked";
            }
            if(in_array('39',$_GET['rozmiar']))
            {
                $this->ilosc39 = "checked";
            }
            if(in_array('40',$_GET['rozmiar']))
            {
                $this->ilosc40 = "checked";
            }
            if(in_array('41',$_GET['rozmiar']))
            {
                $this->ilosc41 = "checked";
            }
            if(in_array('42',$_GET['rozmiar']))
            {
                $this->ilosc42 = "checked";
            }
            if(in_array('43',$_GET['rozmiar']))
            {
                $this->ilosc43 = "checked";
            }
        }

        if(isset($_GET['producent']))
        {
            if(in_array('4F',$_GET['producent']))
            {
                $this->f = "checked";
            }
            if(in_array('addidas',$_GET['producent']))
            {
                $this->adidas = "checked";
            }
            if(in_array('LASOCKI',$_GET['producent']))
            {
                $this->lasocki = "checked";
            }
            if(in_array('New Balance',$_GET['producent']))
            {
                $this->nb = "checked";
            }
        }

        if(isset($_GET['cena_min']))
        {
            $this->min = $_GET['cena_min'];
        }

        if(isset($_GET['cena_max']))
        {
            $this->max = $_GET['cena_max'];
        }

    }

    public function draw($a)
    {
     echo "
     
     <div id='filtr_mid'>
     <div id='filtr'>
     <form method='GET'>
      
      <h1 class='form_napis'>Dla</h1>
      
      <label for='form_dlaniej'> <input type='checkbox' value='1' name='typ[]' id='form_dlaniej' ".$this->typ1."> niej </label> <br>
      <label for='form_dlaniego'> <input type='checkbox'  value='0' name='typ[]' id='form_dlaniego' ".$this->typ0.">  niego </label>
      
     <h1 class='form_napis'>Rozmiar</h1>
      <input type='checkbox' id='roz38' value='38' name='rozmiar[]' class='hidden' ".$this->ilosc38.">
      <input type='checkbox' id='roz39' value='39' name='rozmiar[]' class='hidden' ".$this->ilosc39.">
      <input type='checkbox' id='roz40' value='40' name='rozmiar[]' class='hidden' ".$this->ilosc40.">
      <input type='checkbox' id='roz41' value='41' name='rozmiar[]' class='hidden' ".$this->ilosc41.">
      <input type='checkbox' id='roz42' value='42' name='rozmiar[]' class='hidden' ".$this->ilosc42.">
      <input type='checkbox' id='roz43' value='43' name='rozmiar[]' class='hidden' ".$this->ilosc43.">
      <label for='roz38' class='roz' id='for38'>38</label>
      <label for='roz39' class='roz' id='for39'>39</label>
      <label for='roz40' class='roz' id='for40'>40</label> <br> <br> 
      <label for='roz41' class='roz' id='for41'>41</label>
      <label for='roz42' class='roz' id='for42'>42</label>
      <label for='roz43' class='roz' id='for43'>43</label>
      
    <h1 class='form_napis'>Producent</h1>
     <label for='prod_4f'> <input type='checkbox' id='prod_4f' value='4F' name='producent[]' ".$this->f."> 4F </label> <br>
     <label for='prod_addidas'> <input type='checkbox' id='prod_addidas' value='addidas' name='producent[]' ".$this->adidas."> adidas </label> <br>
     <label for='prod_NB'> <input type='checkbox' id='prod_NB' value='New Balance' name='producent[]' ".$this->nb."> New Balance </label> <br>
     <label for='prod_LASOCKI'> <input type='checkbox' id='prod_LASOCKI' value='LASOCKI' name='producent[]' ".$this->lasocki."> Lasocki </label> <br>
    
    <h1 class='form_napis'>Cena</h1>
     <fieldset>
      <legend>Od</legend>
      <input type='number' name='cena_min' min='0' value='".$this->min."'>
     </fieldset>
     <fieldset>
      <legend>Do</legend>
      <input name='cena_max' type='number' min='0' value='".$this->max."'>
     </fieldset>
     <br>
     <input type='submit' value='Filtruj' name='submit'>
     
    </form>   
    </div>
    </div>
   ";
    }

    public function drawJS($a)
    {
        echo
            "<script>
       function start()
       {          
       var pasekHeight = document.getElementById('pasek').offsetHeight;
       var Height = window.innerHeight;
       var top = window.innerHeight;
       
       document.getElementById('filtr').style.top = (Height-pasekHeight)/2 + pasekHeight + 'px'; 
       }
       
       </script>";
    }

    public function drawCSS($a)
    {
        echo "<link rel='stylesheet' href='".$a."css/Filtr.css'>";
    }

    public function PHP()
    {

    }

}