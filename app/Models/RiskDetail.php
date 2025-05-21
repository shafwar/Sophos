<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskDetail extends Model
{
    use HasFactory;
    protected $fillable = ['risk_id', 'name', 'description', 'recommendation'];
    public function risk() { return $this->belongsTo(Risk::class); }
} 