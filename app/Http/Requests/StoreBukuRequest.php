<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_id'  => ['required', 'exists:kategoris,id'],
            'judul'        => ['required', 'string', 'min:3', 'max:150'],
            'penulis'      => ['required', 'string', 'min:3', 'max:100'],
            'tahun_terbit' => ['required', 'integer', 'between:1900,' . date('Y')],
        ];
    }

    public function messages(): array
    {
        return [
            'required'             => ':attribute wajib diisi.',
            'judul.string'         => 'Judul buku harus berupa teks.',
            'judul.min'            => 'Judul buku minimal :min karakter.',
            'judul.max'            => 'Judul buku maksimal :max karakter.',
            'penulis.string'       => 'Penulis harus berupa teks.',
            'penulis.min'          => 'Nama penulis minimal :min karakter.',
            'penulis.max'          => 'Nama penulis maksimal :max karakter.',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa angka bulat.',
            'tahun_terbit.between' => 'Tahun terbit harus antara :min sampai :max.',
            'kategori_id.exists'   => 'Kategori yang dipilih tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'kategori_id'  => 'kategori',
            'judul'        => 'judul',
            'penulis'      => 'penulis',
            'tahun_terbit' => 'tahun terbit',
        ];
    }
}