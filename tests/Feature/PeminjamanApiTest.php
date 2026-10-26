<?php

namespace Tests\Feature;

use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeminjamanApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_manage_peminjaman_through_the_api(): void
    {
        $payload = [
            'nama_peminjam' => 'Ayu',
            'judul_buku' => 'Laskar Pelangi',
            'tanggal_pinjam' => '2026-10-26',
            'tanggal_kembali' => '2026-11-09',
        ];

        $createResponse = $this->postJson('/api/peminjaman', $payload);
        $createResponse->assertCreated()->assertJsonPath('status', 'dipinjam');

        $peminjaman = Peminjaman::firstOrFail();

        $this->getJson('/api/peminjaman')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $peminjaman->id);

        $this->getJson("/api/peminjaman/{$peminjaman->id}")
            ->assertOk()
            ->assertJsonPath('nama_peminjam', 'Ayu');

        $this->putJson("/api/peminjaman/{$peminjaman->id}", [
            'status' => 'dikembalikan',
        ])->assertOk()->assertJsonPath('status', 'dikembalikan');

        $this->deleteJson("/api/peminjaman/{$peminjaman->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('peminjaman', ['id' => $peminjaman->id]);
    }

    public function test_it_rejects_an_invalid_status(): void
    {
        $peminjaman = Peminjaman::create([
            'nama_peminjam' => 'Budi',
            'judul_buku' => 'Filosofi Teras',
            'tanggal_pinjam' => '2026-10-26',
            'tanggal_kembali' => '2026-11-02',
        ]);

        $this->putJson("/api/peminjaman/{$peminjaman->id}", [
            'status' => 'hilang',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('status');
    }
}
