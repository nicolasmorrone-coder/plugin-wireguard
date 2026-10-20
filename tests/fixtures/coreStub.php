<?php
class eqLogic {
    private $configuration = [];
    public function getId() { return 7; }
    public function setConfiguration($key, $value) { $this->configuration[$key] = $value; }
    public function getConfiguration($key, $default = '') { return isset($this->configuration[$key]) ? $this->configuration[$key] : $default; }
}
class cmd {}
