<?php

require_once '../class/SessionControler.php';


SessionControler::start('../');

if($_SESSION['isLogin'])
{
    SessionControler::makePage('../', ['profil', 'pasek']);
}
else
{
    SessionControler::makePage('../', ['login', 'pasek']);
}

