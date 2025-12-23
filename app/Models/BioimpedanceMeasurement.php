<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BioimpedanceMeasurement extends Model
{
    protected $fillable = [
        'user_id',
        'measured_by',
        'weight',
        'height',
        'bmi',
        'body_fat_percentage',
        'muscle_mass',
        'bone_mass',
        'water_percentage',
        'visceral_fat',
        'basal_metabolic_rate',
        'protein_percentage',
        'chest',
        'waist',
        'hip',
        'arm',
        'thigh',
        'notes',
        'measurement_date'
    ];

    protected $casts = [
        'measurement_date' => 'date',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'bmi' => 'decimal:2',
        'body_fat_percentage' => 'decimal:2',
        'muscle_mass' => 'decimal:2',
        'bone_mass' => 'decimal:2',
        'water_percentage' => 'decimal:2',
        'visceral_fat' => 'decimal:1',
        'protein_percentage' => 'decimal:2',
        'chest' => 'decimal:2',
        'waist' => 'decimal:2',
        'hip' => 'decimal:2',
        'arm' => 'decimal:2',
        'thigh' => 'decimal:2',
    ];

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function measuredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'measured_by');
    }

    // Métodos de apoio
    public function calculateBMI(): float
    {
        if ($this->height && $this->weight) {
            $heightInMeters = $this->height / 100;
            return round($this->weight / ($heightInMeters * $heightInMeters), 2);
        }
        return 0;
    }

    public function getBMICategory(): string
    {
        $bmi = $this->bmi ?? $this->calculateBMI();
        
        if ($bmi < 18.5) return 'Abaixo do peso';
        if ($bmi < 25) return 'Peso normal';
        if ($bmi < 30) return 'Sobrepeso';
        if ($bmi < 35) return 'Obesidade grau I';
        if ($bmi < 40) return 'Obesidade grau II';
        return 'Obesidade grau III';
    }

    public function getBodyFatCategory(): string
    {
        if (!$this->body_fat_percentage) return 'Não informado';
        
        $bodyFat = $this->body_fat_percentage;
        
        // Categorias gerais (pode ser ajustado por gênero se necessário)
        if ($bodyFat < 10) return 'Muito baixo';
        if ($bodyFat < 16) return 'Baixo';
        if ($bodyFat < 21) return 'Normal';
        if ($bodyFat < 25) return 'Acima do normal';
        return 'Alto';
    }
}
