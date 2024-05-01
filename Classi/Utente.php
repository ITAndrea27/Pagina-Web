<?php
class Utente
{
    public $codice;
    public $nome;
    public $cognome;
    public $username;
    public $password;
    public $email;
    public $admin;

    public function __construct($codice = false, $nome = false, $cognome = false, $username = false, $password, $email, $admin = false) {
        $this->codice = $codice;
        $this->nome = $nome;
        $this->cognome = $cognome;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->admin = $admin;
    }
}
?>