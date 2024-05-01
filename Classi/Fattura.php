<?php
class Fattura {
    public $codice;
    public $data;
    public $codice_utente;
    public $codice_veicolo;

    public function __construct($codice, $data, $codice_utente, $codice_veicolo) {
        $this->codice = $codice;
        $this->data = $data;
        $this->codice_utente = $codice_utente;
        $this->codice_veicolo = $codice_veicolo;
    }
}

?>