---
name: log-skill
description: Create consistent releases and changelogs
license: MIT
compatibility: opencode
metadata:
    audience: maintainers
---

## What I do

- Tambahkan log yang sudah dikerjakan di file progress.txt

## When to use me

- Setelah selesai membuat file, refactor, menjalankan task, atau memperbaiki bug.
- Setiap kali ada perubahan kode.

## How I do it

1.  Read `progress.txt` untuk melihat log terakhir
2.  Append entry baru di bagian baawah file
3.  Grub berdasarkan tanggal, gunakan bullets points
4.  Format: aksi (`Modified`,`Created`,`Fixed`,`Deleted`,`Moved`,`Updated`,`Added`,`Implemented`,`Refactor`) + nama file + deskripsi singkat

## Log Format

```txt
## [YYYY-MM-DD]
- [aksi] nama file - deskripsi singkat
- [aksi] nama file - deskripsi singkat
```

Contoh:

```txt
## [2026-05-09]
- [Modified] resources/js/components/public/footer.tsx - deskripsi singkat
- [Created] resources/js/components/public/footer.tsx - deskripsi singkat
```

## Format Aksi

```txt
Modified
Created
Fixed
Deleted
Moved
Updated
Added
Implemented
Refactor
```

**PENTING:** Selalu append, jangan overwrite file yang sudah ada
