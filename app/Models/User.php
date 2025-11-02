<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'dni',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }


    public function role() : BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function persona()
    {
        return $this->hasOne(Persona::class, 'numero_dni', 'dni');
    }

    public function alumno()
    {
        return $this->hasOneThrough(
            Alumno::class,     // modelo final
            Persona::class,    // modelo intermedio
            'numero_dni',      // FK en Persona que “apunta” a users.dni
            'persona_id',      // FK en Alumno que apunta a personas.id
            'dni',             // clave local en User
            'id'               // clave local en Persona usada por Alumno
        );
    }

    public function isAdmin()
    {
        return in_array(strtoupper($this->role->nombre), ['ADMINISTRADOR', 'SUPER USUARIO']);
    }
}
