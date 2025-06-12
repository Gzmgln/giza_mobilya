<?php

require_once 'db_config.php';

$customers = [];
$employees = [];
$products = [];

try {
    $stmt = $pdo->query("SELECT musteri_id, CONCAT(musteri_ad, ' ', musteri_soyad) AS full_name FROM giza_musteriler");
    $customers = $stmt->fetchAll();
    $stmt->closeCursor();

    $stmt = $pdo->query("SELECT calisan_id, CONCAT(calisan_ad, ' ', calisan_soyad) AS full_name FROM giza_calisanlar");
    $employees = $stmt->fetchAll();
    $stmt->closeCursor();

    $stmt = $pdo->query("SELECT urun_id, urun_ad, urun_fiyat FROM giza_urunler");
    $products = $stmt->fetchAll();
    $stmt->closeCursor();
} catch (PDOException $e) {
    $error = "Dropdown verileri getirilirken hata oluştu: " . $e->getMessage();
}

if (isset($_POST['add_sale'])) {
    $satis_id = $_POST['satis_id'];
    $musteri_id = $_POST['musteri_id'];
    $calisan_id = $_POST['calisan_id'];
    $urun_id = $_POST['urun_id'];
    $satis_tarih = $_POST['satis_tarih'];
    $satis_fiyat = $_POST['satis_fiyat'];

    try {
        $stmt = $pdo->prepare("CALL giza_SatisEkle(?, ?, ?, ?, ?, ?)");
        $stmt->execute([$satis_id, $musteri_id, $calisan_id, $urun_id, $satis_tarih, $satis_fiyat]);
        $message = "Satış başarıyla eklendi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Satış eklenirken hata oluştu: " . $e->getMessage();
    }
}

if (isset($_POST['update_sale'])) {
    $satis_id = $_POST['satis_id'];
    $musteri_id = $_POST['musteri_id'];
    $calisan_id = $_POST['calisan_id'];
    $urun_id = $_POST['urun_id'];
    $satis_tarih = $_POST['satis_tarih'];
    $satis_fiyat = $_POST['satis_fiyat'];

    try {
        $stmt = $pdo->prepare("CALL giza_SatisGuncelle(?, ?, ?, ?, ?, ?)");
        $stmt->execute([$satis_id, $musteri_id, $calisan_id, $urun_id, $satis_tarih, $satis_fiyat]);
        $message = "Satış başarıyla güncellendi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Satış güncellenirken hata oluştu: " . $e->getMessage();
    }
}

if (isset($_GET['delete_id'])) {
    $satis_id = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("CALL giza_SatisSil(?)");
        $stmt->execute([$satis_id]);
        $message = "Satış başarıyla silindi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Satış silinirken hata oluştu: " . $e->getMessage();
    }
}

$satislar = [];
try {
    $stmt = $pdo->query("CALL giza_SatisDetay()");
    $satislar = $stmt->fetchAll();
    $stmt->closeCursor();
} catch (PDOException $e) {
    $error = "Satış detayları getirilirken hata oluştu: " . $e->getMessage();
}

$edit_sale = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM giza_satislar WHERE satis_id = ?");
        $stmt->execute([$edit_id]);
        $edit_sale = $stmt->fetch();
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Düzenlenecek satış bilgileri getirilirken hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satışlar - Giza Mobilya Yönetim Sistemi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e8eaf6;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            align-items: center;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        header {
            background-color: #3f51b5;
            color: white;
            padding: 25px 0;
            text-align: center;
            border-radius: 12px 12px 0 0;
            width: 100%;
        }
        header h1 {
            margin: 0;
            font-size: 2.5em;
            letter-spacing: 1px;
        }
        nav {
            background-color: #333;
            padding: 15px 0;
            border-radius: 0 0 12px 12px;
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        nav ul li {
            margin: 0 15px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        nav ul li a:hover {
            background-color: #555;
            transform: translateY(-2px);
        }
        footer {
            margin-top: auto;
            padding: 0;
            background-color: transparent;
            color: transparent;
            text-align: center;
            width: 100%;
            border-radius: 0;
        }

        h2 {
            color: #3f51b5;
            text-align: center;
            margin-bottom: 25px;
            font-size: 2em;
        }
        .message {
            background-color: #D4EDDA;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #C3E6CB;
        }
        .error {
            background-color: #F8D7DA;
            color: #721C24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #F5C6CB;
        }

        .form-section {
            background-color: #c5cae9;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        .form-section h3 {
            color: #1a237e;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.5em;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="datetime-local"],
        .form-group select {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }
        .form-group input[type="text"]:focus,
        .form-group input[type="number"]:focus,
        .form-group input[type="datetime-local"]:focus,
        .form-group select:focus {
            border-color: #3f51b5;
            outline: none;
            box-shadow: 0 0 5px rgba(63, 81, 181, 0.3);
        }
        .form-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .button {
            background-color: #3f51b5;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .button:hover {
            background-color: #3949ab;
            transform: translateY(-2px);
        }
        .button.cancel {
            background-color: #f44336;
        }
        .button.cancel:hover {
            background-color: #da190b;
        }
        .button.search {
            background-color: #2196F3;
        }
        .button.search:hover {
            background-color: #0b7dda;
        }

        .table-section {
            overflow-x: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            vertical-align: middle;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: 600;
            color: #555;
            white-space: nowrap;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #e9e9e9;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: flex-start;
        }
        .action-buttons .button {
            padding: 8px 12px;
            font-size: 0.9em;
            margin: 0;
        }
        .button.edit {
            background-color: #ffc107;
            color: #333;
        }
        .button.edit:hover {
            background-color: #e0a800;
        }
        .button.delete {
            background-color: #f44336;
        }
        .button.delete:hover {
            background-color: #da190b;
        }

        @media (max-width: 768px) {
            nav ul {
                flex-direction: column;
            }
            nav ul li {
                margin: 5px 0;
                width: 90%;
            }
            nav ul li a {
                text-align: center;
            }
            .container {
                width: 95%;
                margin: 15px auto;
            }
        }
    </style>
    <script>
        function updateSalePrice() {
            var productSelect = document.getElementById('urun_id');
            var selectedOption = productSelect.options[productSelect.selectedIndex];
            var productPrice = selectedOption.getAttribute('data-price');
            document.getElementById('satis_fiyat').value = productPrice;
        }

        window.onload = function() {
            var now = new Date();
            var year = now.getFullYear();
            var month = (now.getMonth() + 1).toString().padStart(2, '0');
            var day = now.getDate().toString().padStart(2, '0');
            var hours = now.getHours().toString().padStart(2, '0');
            var minutes = now.getMinutes().toString().padStart(2, '0');
            var datetimeLocal = `${year}-${month}-${day}T${hours}:${minutes}`;
            
            var satisTarihInput = document.getElementById('satis_tarih');
            if (satisTarihInput && !satisTarihInput.value) {
                satisTarihInput.value = datetimeLocal;
            }

            if (document.getElementById('edit_mode_flag') && document.getElementById('urun_id')) {
                updateSalePrice();
            }
        };
    </script>
</head>
<body>
    <header>
        <h1>Giza Mobilya Yönetim Sistemi</h1>
    </header>
    <nav>
        <ul>
            <li><a href="index.php">Ana Sayfa</a></li>
            <li><a href="musteriler.php">Müşteriler</a></li>
            <li><a href="urunler.php">Ürünler</a></li>
            <li><a href="satislar.php">Satışlar</a></li>
            <li><a href="odemeler.php">Ödemeler</a></li>
            <li><a href="calisanlar.php">Çalışanlar</a></li>
            <li><a href="tedarikci.php">Tedarikçiler</a></li>
            <li><a href="tedarik_edis.php">Tedarik Etme</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Satış Yönetimi</h2>

        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h3><?php echo $edit_sale ? 'Satış Bilgilerini Güncelle' : 'Yeni Satış Ekle'; ?></h3>
            <form action="satislar.php" method="POST">
                <?php if ($edit_sale): ?>
                    <input type="hidden" name="satis_id" value="<?php echo htmlspecialchars($edit_sale['satis_id']); ?>">
                    <input type="hidden" id="edit_mode_flag" value="1">
                <?php endif; ?>

                <div class="form-group">
                    <label for="satis_id">Satış ID:</label>
                    <input type="text" id="satis_id" name="satis_id"
                           value="<?php echo $edit_sale ? htmlspecialchars($edit_sale['satis_id']) : ''; ?>"
                           <?php echo $edit_sale ? 'readonly' : 'required'; ?>>
                </div>
                <div class="form-group">
                    <label for="musteri_id">Müşteri:</label>
                    <select id="musteri_id" name="musteri_id" required>
                        <option value="">Müşteri Seçiniz</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo htmlspecialchars($customer['musteri_id']); ?>"
                                <?php echo ($edit_sale && $edit_sale['musteri_id'] == $customer['musteri_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($customer['full_name']); ?> (ID: <?php echo htmlspecialchars($customer['musteri_id']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="calisan_id">Çalışan:</label>
                    <select id="calisan_id" name="calisan_id" required>
                        <option value="">Çalışan Seçiniz</option>
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?php echo htmlspecialchars($employee['calisan_id']); ?>"
                                <?php echo ($edit_sale && $edit_sale['calisan_id'] == $employee['calisan_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($employee['full_name']); ?> (ID: <?php echo htmlspecialchars($employee['calisan_id']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="urun_id">Ürün:</label>
                    <select id="urun_id" name="urun_id" onchange="updateSalePrice()" required>
                        <option value="">Ürün Seçiniz</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo htmlspecialchars($product['urun_id']); ?>"
                                data-price="<?php echo htmlspecialchars($product['urun_fiyat']); ?>"
                                <?php echo ($edit_sale && $edit_sale['urun_id'] == $product['urun_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($product['urun_ad']); ?> (Fiyat: <?php echo htmlspecialchars($product['urun_fiyat']); ?> TL)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="satis_tarih">Satış Tarihi:</label>
                    <input type="datetime-local" id="satis_tarih" name="satis_tarih"
                           value="<?php echo $edit_sale ? date('Y-m-d\TH:i', strtotime($edit_sale['satis_tarih'])) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="satis_fiyat">Satış Fiyatı:</label>
                    <input type="number" id="satis_fiyat" name="satis_fiyat" step="0.01"
                           value="<?php echo $edit_sale ? htmlspecialchars($edit_sale['satis_fiyat']) : ''; ?>" required>
                </div>
                <div class="form-buttons">
                    <?php if ($edit_sale): ?>
                        <button type="submit" name="update_sale" class="button">Güncelle</button>
                        <a href="satislar.php" class="button cancel">İptal</a>
                    <?php else: ?>
                        <button type="submit" name="add_sale" class="button">Ekle</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="table-section">
            <h3>Mevcut Satışlar</h3>
            <table>
                <thead>
                    <tr>
                        <th>Satış ID</th>
                        <th>Müşteri Ad Soyad</th>
                        <th>Çalışan Ad Soyad</th>
                        <th>Ürün</th>
                        <th>Birim Fiyat</th>
                        <th>Satış Fiyatı</th>
                        <th>Satış Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($satislar) > 0): ?>
                        <?php foreach ($satislar as $satis): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($satis['satis_id']); ?></td>
                                <td><?php echo htmlspecialchars($satis['Müşteri Ad Soyad']); ?> (ID: <?php echo htmlspecialchars($satis['musteri_id']); ?>)</td>
                                <td><?php echo htmlspecialchars($satis['Çalışan Ad Soyad']); ?> (ID: <?php echo htmlspecialchars($satis['calisan_id']); ?>)</td>
                                <td><?php echo htmlspecialchars($satis['Ürün']); ?> (ID: <?php echo htmlspecialchars($satis['urun_id']); ?>)</td>
                                <td><?php echo htmlspecialchars($satis['Birim Fiyat']); ?></td>
                                <td><?php echo htmlspecialchars($satis['Satış Fiyatı']); ?></td>
                                <td><?php echo htmlspecialchars($satis['Satış Tarihi']); ?></td>
                                <td class="action-buttons">
                                    <a href="satislar.php?edit_id=<?php echo htmlspecialchars($satis['satis_id']); ?>" class="button edit">Düzenle</a>
                                    <a href="satislar.php?delete_id=<?php echo htmlspecialchars($satis['satis_id']); ?>" class="button delete" onclick="return confirm('Bu satışı silmek istediğinizden emin misiniz?');">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">Henüz satış bulunmamaktadır.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
    </footer>
</body>
</html>
