<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'ip_address', 'port', 'username', 'ssh_credentials', 'webhook_token'
    ];

    protected $casts = [
        'ssh_credentials' => 'encrypted', // Make password encrypted
    ];

    public function sites()
    {
        return $this->hasMany(Site::class);
    }
}