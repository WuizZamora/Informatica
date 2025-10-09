<?php
class PersonalController
{
    public function index($rol)
    {
        include './src/Views/Personal/personal.php';
    }

    public function NoAdeudos($rol)
    {
        include './src/Views/Personal/NoAdeudos.php';
    }
}
