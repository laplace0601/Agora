<?php

namespace App\Models;

use CodeIgniter\Model;

class ComunicadoModel extends Model
{
    protected $table            = 'noticias_comunicados';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'autor_id',
        'titulo',
        'contenido',
        'fecha_publicacion',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'autor_id'  => 'required|integer',
        'titulo'    => 'required|max_length[255]',
        'contenido' => 'required',
    ];

    /**
     * Lista todos los comunicados ordenados del más reciente al más antiguo.
     */
    public function listarTodos(): array
    {
        return $this->orderBy('fecha_publicacion', 'DESC')->findAll();
    }

    /**
     * Lista comunicados con el nombre del autor (JOIN a usuarios).
     */
    public function listarConAutor(): array
    {
        return $this->select('noticias_comunicados.*, usuarios.correo AS correo_autor')
                    ->join('usuarios', 'usuarios.id = noticias_comunicados.autor_id')
                    ->orderBy('noticias_comunicados.fecha_publicacion', 'DESC')
                    ->findAll();
    }
}
