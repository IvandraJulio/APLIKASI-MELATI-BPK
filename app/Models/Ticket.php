<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'pengirimId',
        'pengirimName',
        'jenis',
        'layananKategori',
        'layananSub',
        'layanan',
        'detail',
        'bisa_remote',
        'tanggal',
        'tanggalUpdate',
        'tanggalSelesai',
        'kasubbagId',
        'kasubbagName',
        'solverId',
        'solverName',
        'solver2Id',
        'solver2Name',
        'status',
        'alasanTolak',
        'catatanKasubbag'
    ];

    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengirimId');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'ticketId');
    }
}
