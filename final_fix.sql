-- Final fixes to standardize status values across both tables

-- Update Peminjaman_Barang to match Peminjaman_Ruangan status values
UPDATE Peminjaman_Barang
SET statusPeminjaman = 'Ditolak'
WHERE statusPeminjaman = 'Sedang dipinjam';

-- Verify status values are now consistent
SELECT DISTINCT statusPeminjaman, 'Peminjaman_Ruangan' as TableName
FROM Peminjaman_Ruangan
UNION
SELECT DISTINCT statusPeminjaman, 'Peminjaman_Barang' as TableName
FROM Peminjaman_Barang;

-- Verify column nullability
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'Peminjaman_Barang'
ORDER BY COLUMN_NAME;
