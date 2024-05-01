<?php
class Veicolo {
    public $codice;
    public $tipo;
    public $potenza;
    public $consumo;
    public $Co2;
    public $costo;

    public function __construct($codice, $tipo, $potenza, $consumo, $Co2, $costo) {
        $this->codice = $codice;
        $this->tipo = $tipo;
        $this->potenza = $potenza;
        $this->consumo = $consumo;
        $this->Co2 = $Co2;
        $this->costo = $costo;
    }
}
?>