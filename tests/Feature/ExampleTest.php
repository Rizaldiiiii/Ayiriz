<?php

test('halaman beranda katalog publik berhasil dibuka tanpa login', function () {
    $response = $this->get('/');

    $response->assertStatus(200)
        ->assertSee('Ayiriz');
});
