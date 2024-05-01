<?php
class Noleggio {
    public $codice_noleggio;
    public $utente;
    public $veicolo;
    public $data;

    public function __construct($codice_noleggio, $utente, $veicolo, $data) {
        $this->codice_noleggio = $codice_noleggio;
        $this->utente = $utente;
        $this->veicolo = $veicolo;
        $this->data = $data;
    }
}

?>