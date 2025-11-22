<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_id', 'domain_name', 'port', 
        'container_name', 'container_id',
        'db_name', 'db_user', 'db_password',
        'status'
    ];

    protected $casts = [
        'db_password' => 'encrypted', // Make password encrypted
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}