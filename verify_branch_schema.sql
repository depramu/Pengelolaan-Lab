-- Verify database schema matches branch structure

-- Check Peminjaman_Ruangan schema
SELECT 'Peminjaman_Ruangan' as TableName, COLUMN_NAME, DATA_TYPE, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'Peminjaman_Ruangan'
ORDER BY COLUMN_NAME;

-- Check Peminjaman_Barang schema
SELECT 'Peminjaman_Barang' as TableName, COLUMN_NAME, DATA_TYPE, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'Peminjaman_Barang'
ORDER BY COLUMN_NAME;

-- Check status values
SELECT DISTINCT statusPeminjaman, 'Peminjaman_Ruangan' as TableName
FROM Peminjaman_Ruangan
UNION
SELECT DISTINCT statusPeminjaman, 'Peminjaman_Barang' as TableName
FROM Peminjaman_Barang;

-- Check Penolakan table
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'Penolakan'
ORDER BY COLUMN_NAME;

-- Check other tables
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME IN ('Pengembalian_Barang', 'Pengembalian_Ruangan', 'Ruangan', 'Barang', 'Mahasiswa', 'Karyawan')
ORDER BY TABLE_NAME, COLUMN_NAME;
