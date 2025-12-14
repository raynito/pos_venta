<?php
class Controller
{
    // El ? indica que puede ser null
    protected $views;
    protected ?object $model = null;

    public function __construct()
    {
        $this->views = new Views();
        $this->cargarModel();
    }
    
    public function cargarModel()
    {
        $model = get_class($this)."Model";
        $ruta = "Models/".$model.".php";
        if (file_exists($ruta)) {
            require_once $ruta;
            $this->model = new $model();
        }
    }
}
?>