<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPeriksa extends Model
{
    protected $table = 'jadwal_periksa';

    protected $fillable = [
        'id_dokter',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function dokter()
    {
        return $this->belongsTo(User::class, 'id_dokter');
    }

    public function daftarPolis()
    {
        return $this->hasMany(DaftarPoli::class, 'id_jadwal');
    }

    /**
     * Apakah hari ini sama dengan hari jadwal?
     */
    public function hariIni(): bool
    {
        return $this->hari === self::namaHariIni();
    }

    /**
     * Apakah jadwal sedang berlangsung pada detik ini?
     */
    public function sedangBerlangsung(): bool
    {
        $sekarang = (int) now()->secondsSinceMidnight();

        return $this->hariIni()
            && $sekarang >= $this->detik($this->jam_mulai)
            && $sekarang <= $this->detik($this->jam_selesai);
    }

    /**
     * Nama hari ini mengikuti urutan enum kolom `hari`.
     */
    private static function namaHariIni(): string
    {
        return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][now()->isoWeekday() - 1];
    }

    private function detik($jam): int
    {
        return \Carbon\Carbon::parse($jam)->secondsSinceMidnight();
    }
}
