<?php
 
namespace App\Models;
 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
 
class Usuario extends Authenticatable
{
    use HasFactory;
 
    protected $table = 'usuario2';                // Nombre real de la tabla
    protected $primaryKey = 'ID_Usuario';         // Llave primaria
    public $timestamps = true;                    // Tu tabla SÍ tiene timestamps
 
    protected $fillable = [
        'Cedula',
        'Nombre',
        'Apellido',
        'Telefono',
        'Correo',
        'Contraseña',
        'Fecha',
        'FK_ID_Municipio',
        'FK_ID_Rol'
    ];
 
    protected $hidden = ['Contraseña'];
 
    /**
     * Devuelve el campo donde está guardada la contraseña.
     */
    public function getAuthPassword()
    {
        return $this->Contraseña;
    }
 
    /**
     * Laravel usa por defecto el campo "email".
     * Aquí indicamos que debe usar "Correo".
     */
    public function username()
    {
        return 'Correo';
    }
 
    /**
     * Relación con proyectos (si existe tu tabla pivote).
     */
    public function proyectos()
    {
        return $this->belongsToMany(
            Proyecto::class,
            'proyecto_usuario',
            'FK_ID_Usuario',
            'FK_ID_Proyecto'
        );
    }
}