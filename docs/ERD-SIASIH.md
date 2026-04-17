# ERD (Entity Relationship Diagram) SI-ASIH

Dokumen ini menjelaskan struktur database SI-ASIH dengan memenuhi komponen ERD standar: **Entitas**, **Atribut**, **Primary Key**, **Foreign Key**, **Relasi**, dan **Kardinalitas**.

---

## 1. Entitas (Entities)

| No | Entitas     | Tabel DB    | Deskripsi |
|----|-------------|-------------|-----------|
| 1  | **Users**   | `users`     | Pengguna sistem: pengunjung, admin per wisata, pengelola BUMDes |
| 2  | **Wisata**  | `wisata`    | Tempat wisata yang dikelola (Curug, Puncak, Bukit) |
| 3  | **Tiket**   | `tiket`     | Pemesanan tiket wisata (menghubungkan pengunjung dan wisata) |
| 4  | **Produk Khas** | `produk_khas` | Produk unggulan Desa Cibeusi (standalone) |

---

## 2. Atribut (Attributes)

### Entitas Users
| Atribut         | Tipe       | Constraint    | Keterangan |
|-----------------|------------|---------------|------------|
| id              | BIGINT     | PK, AUTO_INCREMENT | Primary Key |
| name            | VARCHAR(255) | NOT NULL  | Nama lengkap |
| email           | VARCHAR(255) | NOT NULL, UNIQUE | Email login |
| email_verified_at | TIMESTAMP | NULL | Verifikasi email |
| password        | VARCHAR(255) | NOT NULL | Password terenkripsi |
| remember_token  | VARCHAR(100) | NULL | Token remember me |
| role            | VARCHAR(20) | NOT NULL, DEFAULT 'pengunjung' | pengunjung \| admin \| pengelola_bumdes |
| wisata_id       | BIGINT     | NULL, FK → wisata.id | Hanya untuk admin |
| google_id       | VARCHAR(255) | NULL, UNIQUE | ID dari Google OAuth |
| avatar          | VARCHAR(255) | NULL | URL avatar |
| created_at      | TIMESTAMP  | | Waktu dibuat |
| updated_at      | TIMESTAMP  | | Waktu diubah |

### Entitas Wisata
| Atribut     | Tipe         | Constraint    | Keterangan |
|-------------|--------------|---------------|------------|
| id          | BIGINT       | PK, AUTO_INCREMENT | Primary Key |
| nama        | VARCHAR(255) | NOT NULL      | Nama wisata |
| slug        | VARCHAR(255) | NOT NULL, UNIQUE | Slug URL |
| harga_tiket | DECIMAL(12,0) | NOT NULL    | Harga per tiket |
| deskripsi   | TEXT         | NULL          | Deskripsi wisata |
| gambar      | VARCHAR(255) | NULL          | Path/URL gambar |
| created_at  | TIMESTAMP    | | Waktu dibuat |
| updated_at  | TIMESTAMP    | | Waktu diubah |

### Entitas Tiket
| Atribut              | Tipe         | Constraint    | Keterangan |
|----------------------|--------------|---------------|------------|
| id                   | BIGINT       | PK, AUTO_INCREMENT | Primary Key |
| user_id              | BIGINT       | NOT NULL, FK → users.id | Pemesan |
| wisata_id            | BIGINT       | NOT NULL, FK → wisata.id | Wisata yang dipesan |
| kode_tiket           | VARCHAR(20)  | NOT NULL, UNIQUE | Kode unik tiket |
| jumlah               | INT          | NOT NULL, DEFAULT 1 | Jumlah tiket |
| total_harga          | DECIMAL(12,0) | NOT NULL | Total pembayaran |
| status               | VARCHAR(20)  | NOT NULL, DEFAULT 'pending' | pending \| paid \| used \| expired \| cancelled |
| midtrans_order_id    | VARCHAR(255) | NULL | Order ID Midtrans |
| midtrans_transaction_id | VARCHAR(255) | NULL | Transaction ID Midtrans |
| tanggal_berkunjung   | DATE         | NOT NULL | Tanggal kunjungan |
| camping              | VARCHAR(20)  | NULL | Ya/Tidak (khusus Curug) |
| parkir_tipe          | VARCHAR(30)  | NULL | Tipe parkir |
| parkir_harga         | DECIMAL(12,0) | DEFAULT 0 | Harga parkir |
| used_at              | TIMESTAMP    | NULL | Waktu validasi |
| created_at           | TIMESTAMP    | | Waktu dibuat |
| updated_at           | TIMESTAMP    | | Waktu diubah |

### Entitas Produk Khas
| Atribut    | Tipe         | Constraint    | Keterangan |
|------------|--------------|---------------|------------|
| id         | BIGINT       | PK, AUTO_INCREMENT | Primary Key |
| nama       | VARCHAR(255) | NOT NULL      | Nama produk |
| keterangan | TEXT         | NULL          | Deskripsi produk |
| gambar     | VARCHAR(255) | NULL          | Path/URL gambar |
| urutan     | INT          | DEFAULT 0     | Urutan tampilan |
| created_at | TIMESTAMP    | | Waktu dibuat |
| updated_at | TIMESTAMP    | | Waktu diubah |

---

## 3. Primary Key & Foreign Key

### Primary Key (PK)
| Tabel        | Kolom PK | Tipe   |
|--------------|----------|--------|
| users        | id       | BIGINT |
| wisata       | id       | BIGINT |
| tiket        | id       | BIGINT |
| produk_khas  | id       | BIGINT |

### Foreign Key (FK)
| Tabel  | Kolom FK  | Referensi    | ON DELETE |
|--------|-----------|--------------|-----------|
| users  | wisata_id | wisata.id    | SET NULL  |
| tiket  | user_id   | users.id     | CASCADE   |
| tiket  | wisata_id | wisata.id    | CASCADE   |

### Unique & Index
| Tabel  | Kolom/Kombinasi     | Tipe   |
|--------|---------------------|--------|
| users  | email               | UNIQUE |
| users  | google_id           | UNIQUE |
| wisata | slug                | UNIQUE |
| tiket  | kode_tiket          | UNIQUE |
| tiket  | (wisata_id, status) | INDEX  |
| tiket  | (user_id, status)   | INDEX  |
| tiket  | tanggal_berkunjung  | INDEX  |

---

## 4. Relasi (Relationships)

| Relasi      | Entitas A | Entitas B | Jenis      | Deskripsi |
|-------------|-----------|-----------|------------|-----------|
| R1          | Users     | Wisata    | Many-to-One| Admin terhubung ke satu wisata |
| R2          | Tiket     | Users     | Many-to-One| Tiket dimiliki satu pengunjung |
| R3          | Tiket     | Wisata    | Many-to-One| Tiket untuk satu wisata |

---

## 5. Kardinalitas (Cardinality)

```
                    (0,1)
    [WISATA] ◄────────────── [USERS]
        ▲                        │
        │                        │
        │ (1,N)                  │ (1,N)
        │                        │
        │                        ▼
    [TIKET] ◄──────────────── [USERS]
        │
        │ (1,N)
        │
        └─────────────────────► [WISATA]
```

| Relasi | Entitas A | Kard. A→B | Entitas B | Kard. B→A | Notasi |
|--------|-----------|-----------|-----------|-----------|--------|
| R1     | Users     | 0 atau 1  | Wisata    | 0..N      | (0,1) : (0,N) |
| R2     | Tiket     | N         | Users     | 1         | (1,N) : (1,1) |
| R3     | Tiket     | N         | Wisata    | 1         | (1,N) : (1,1) |

**Penjelasan:**
- **R1:** Satu wisata dapat memiliki banyak admin (0..N users). Satu user (admin) terhubung ke 0 atau 1 wisata (pengunjung tidak punya wisata_id).
- **R2:** Satu user (pengunjung) dapat memiliki banyak tiket (1:N). Satu tiket dimiliki oleh tepat satu user (N:1).
- **R3:** Satu wisata dapat memiliki banyak tiket (1:N). Satu tiket untuk tepat satu wisata (N:1).

---

## Diagram Visual

Gunakan file `docs/siasih-dbdiagram.dbml` di [dbdiagram.io](https://dbdiagram.io/d) untuk mendapatkan diagram visual ERD.
