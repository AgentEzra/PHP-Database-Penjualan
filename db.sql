CREATE TABLE tabel_kopi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_kopi VARCHAR(50) NOT NULL,
    harga INT NOT NULL,
    total_cup INT NOT NULL,
    total_harga INT NOT NULL,
    waktu_terjual TIMESTAMP
)