<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
    use HasFactory;
    protected $fillable = ['category'];
    public function details() { return $this->hasMany(RiskDetail::class); }
} 