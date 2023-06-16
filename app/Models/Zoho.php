<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Zoho extends Model
{
    use HasFactory, HasApiTokens;
    public $timestamps = true;

    protected $fillable = ['access_token', 'scope', 'api_domain', 'token_type', 'expire_in', 'created_at', 'updated_at'];
}