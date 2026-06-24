<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ComunicadoModel
 *
 * Tabla: noticias_comunicados
 * Gestiona los comunicados/anuncios que el admin publica en la cartelera.
 */
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
        'estado',                  // 'publicado' | 'borrado'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'autor_id'  => 'required|integer',
        'titulo'    => 'required|max_length[255]',
        'contenido' => 'required',
    ];

    // ---------------------------------------------------------------
    // Métodos de negocio
    // ---------------------------------------------------------------

    /**
     * Lista solo los comunicados en estado 'publicado', del más reciente al más antiguo.
     */
    public function listarActivos(): array
    {
        return $this->where('estado', 'publicado')
                    ->orderBy('fecha_publicacion', 'DESC')
                    ->findAll();
    }

    /**
     * Lista todos los comunicados (activos e historial) del más reciente al más antiguo.
     */
    public function listarTodos(): array
    {
        return $this->orderBy('fecha_publicacion', 'DESC')->findAll();
    }

    /**
     * Lista comunicados con el nombre del autor (JOIN a usuarios).
     * Usado por el endpoint del ComunidadController.
     */
    public function listarConAutor(): array
    {
        return $this->select('noticias_comunicados.*, usuarios.correo AS correo_autor')
                    ->join('usuarios', 'usuarios.id = noticias_comunicados.autor_id')
                    ->orderBy('noticias_comunicados.fecha_publicacion', 'DESC')
                    ->findAll();
    }

    /**
     * Lista comunicados activos con el nombre del autor para la cartelera de residentes.
     */
    public function listarActivosConAutor(): array
    {
        return $this->select('noticias_comunicados.*, usuarios.correo AS correo_autor')
                    ->join('usuarios', 'usuarios.id = noticias_comunicados.autor_id')
                    ->where('noticias_comunicados.estado', 'publicado')
                    ->orderBy('noticias_comunicados.fecha_publicacion', 'DESC')
                    ->findAll();
    }
}
