+<?php


class DaneOsobowe
{
    public $osobowe = Array();
    public $czyPoprawnaWalidacja;
    private $firstIncorrect;

    public function draw($disabled=false)
    {
        echo "<div id='dane'>";

        echo $this->drawField('Email', $disabled);
        echo $this->drawField('Imie', $disabled);
        echo $this->drawField('Nazwisko', $disabled);
        echo $this->drawField('Numer telefonu', $disabled);
        echo "<div>";
        echo $this->drawField('Ulica', $disabled);
        echo $this->drawField('Nr', $disabled);
        echo "</div>";
        echo "<div>";
        echo $this->drawField('Kod pocztowy', $disabled);
        echo $this->drawField('Miasto', $disabled);
        echo "</div>";

        echo "</div>";
    }

    public function drawCSS($a)
    {
        echo "<link rel='stylesheet' href='".$a."css/DaneOsobowe.css'>";
    }

    public function drawJS() // sprawdzenie poprawnosci pól w js
    {
        echo "
        
        <script>
        
        function blad()
        {
            let i = '".$this->firstIncorrect."';
            if(i != '')
                {
                    sprawdz('brak');
                }
        }
        
        function zablokuj()
        {
            let przycisk = document.getElementById('submit');
            console.log(sprawdz('brak'));
            if(sprawdz('brak') > 0)
                {
                    przycisk.style.cursor = 'default';
                    przycisk.className = 'zielony no_hover';
                    przycisk.disabled = true;
                }
            else 
                {
                    przycisk.style.cursor = 'pointer';
                    przycisk.className = 'zielony hover';
                    przycisk.disabled = false;
                }
        }
  
        function sprawdz(aktualny)
        {
            let pola = ['Email', 'Imie', 'Nazwisko', 'Numer_telefonu', 'Ulica', 'Nr', 'Kod_pocztowy', 'Miasto'];    
            if(aktualny != 'brak')
            {                
            let found = pola.findIndex(element => element == aktualny);
            pola.splice(found, 1);
            }
            
            let zle=0;

            for(let i of pola)
                {
                   let klasa = check(i);            
                    document.getElementById(i).className = klasa;
                    
                    if(klasa == 'good')
                        {
                            document.getElementById('a_'+i).innerHTML = '&#10003';
                        }
                    if(klasa == 'bad')
                        {
                            document.getElementById('a_'+i).innerHTML = 'X';
                            zle++;
                        }
                    if(klasa == '')
                        {
                           document.getElementById('a_'+i).innerHTML = '';
                           zle++;
                        }
                    
                }
            return zle;
        }
        
        function check(pole)
        {
            let poleInput = 'input_'+pole;
            let value = document.getElementById(poleInput).value.trim();
            if(value == \"\")
            {
                return '';
            }
            else 
                {
                    let reg;
                    if(pole == 'Email')
                        {
                            reg = /^[a-z\d]+[\w\d.-]*@(?:[a-z\d]+[a-z\d-]+\.){1,5}[a-z]{2,6}$/i;
                            if(reg.test(value)) { return 'good'} else {return 'bad'};
                        }
                    if(pole == 'Imie' || pole == 'Nazwisko' || pole == 'Ulica' || pole == 'Miasto')
                        {
                            reg = /^[a-z]{3,}$/i;                       
                            if(reg.test(value)) { 
                                value = value.toLowerCase(); 
                                value = value[0].toUpperCase() + value.substr(1,value.length-1);
                                document.getElementById(poleInput).value = value;
                                return 'good'} else {return 'bad'};
                        }
                    if(pole == 'Numer_telefonu')
                        {
                            reg = /^[0-9]{9}$/;
                            if(reg.test(value)) { return 'good'} else {return 'bad'};
                        }
                    if(pole == 'Nr')
                        {
                            reg = /^[0-9]{1,3}[a-z]?([/][0-9]{1,3})?$/i;
                            if(reg.test(value)) { return 'good'} else {return 'bad'};
                        }
                    if(pole == 'Kod_pocztowy')
                        {
                            reg = /^[0-9]{2}[-][0-9]{3}$/;
                            if(reg.test(value)) { return 'good'} else {return 'bad'};
                        }
                    
                    return '';
                }
                      
        }
        
        </script>
        
        ";
    }

    private function drawField($name, $disabled)
    { // fajka - &#10003
        $nameBezSpacji = str_replace(' ', '_', $name); // zamienia spacje na podloge _
        $html = "
        <fieldset id='".$nameBezSpacji."'>
        <legend>".$name."</legend>
        <input id='input_".$nameBezSpacji."' onclick=\"sprawdz('".$nameBezSpacji."')\" type='text' name='".$nameBezSpacji."' value='".$this->osobowe[$nameBezSpacji]."'";

        if($disabled){ $html .= " disabled"; } 

        $html .= "><a id='a_".$nameBezSpacji."'></a>
        </fieldset>";

        return $html;
    }

    public function walid($dane)
    {
    
            foreach ($dane as $pole => $data) // utworzenie tablicy z danymi osobowymi
            {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                $this->osobowe[$pole] = $data;
            }

            foreach ($dane as $pole => $value) // sprawdzenie danych osobowych
            {
                if($pole == 'Email')
                {
                    if(preg_match('/^[a-z\d]+[\w\d.-]*@(?:[a-z\d]+[a-z\d-]+\.){1,5}[a-z]{2,6}$/i', $value)){}
                    else
                    {
                        $this->firstIncorrect = $pole;
                        $this->czyPoprawnaWalidacja = 'nie';
                        break;
                    }
                }

                if($pole == 'Imie' or $pole == 'Nazwisko' or $pole == 'Ulica' or $pole == 'Miasto')
                {
                    if(preg_match('/^[a-z]{3,}$/i', $value)){}
                    else
                    {
                        $this->firstIncorrect = $pole;
                        $this->czyPoprawnaWalidacja = 'nie';
                        break;
                    }
                }

                if($pole == 'Numer_telefonu')
                {
                    if(preg_match('/^[0-9]{9}$/', $value)){}
                    else
                    {
                        $this->firstIncorrect = $pole;
                        $this->czyPoprawnaWalidacja = 'nie';
                        break;
                    }
                }

                if($pole == 'Nr')
                {
                    if(preg_match('/^[0-9]{1,3}[a-z]?([\/][0-9]{1,3})?$/i', $value)){}
                    else
                    {
                        $this->firstIncorrect = $pole;
                        $this->czyPoprawnaWalidacja = 'nie';
                        break;
                    }
                }

                if($pole == 'Kod_pocztowy')
                {
                    if(preg_match('/^[0-9]{2}[-][0-9]{3}$/', $value)){}
                    else
                    {
                        $this->firstIncorrect = $pole;
                        $this->czyPoprawnaWalidacja = 'nie';
                        break;
                    }
                }

            }
    }

}