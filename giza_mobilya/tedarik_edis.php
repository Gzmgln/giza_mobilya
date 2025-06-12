<?php

require_once 'db_config.php';

$suppliers = [];
$products = [];

try {
    $stmt_suppliers = $pdo->query("SELECT tedarik_id, sirket_ad FROM tedarikciler");
    $suppliers = $stmt_suppliers->fetchAll();
    $stmt_suppliers->closeCursor();

    $stmt_products = $pdo->query("SELECT urun_id, urun_ad FROM giza_urunler");
    $products = $stmt_products->fetchAll();
    $stmt_products->closeCursor();

} catch (PDOException $e) {
    $error = "Dropdown verileri getirilirken hata oluştu: " . $e->getMessage();
}

if (isset($_POST['add_supply'])) {
    $siparis_id = $_POST['siparis_id'];
    $tedarik_id = $_POST['tedarik_id'];
    $urun_id = $_POST['urun_id'];
    $siparis_tarih = $_POST['siparis_tarih'];
    $toplam_tutar = $_POST['toplam_tutar'];

    try {
        $stmt = $pdo->prepare("CALL giza_TedarikEtmeEkle(?, ?, ?, ?, ?)");
        $stmt->execute([$siparis_id, $tedarik_id, $urun_id, $siparis_tarih, $toplam_tutar]);
        $message = "Tedarik işlemi başarıyla eklendi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Tedarik işlemi eklenirken hata oluştu: " . $e->getMessage();
    }
}

if (isset($_POST['update_supply'])) {
    $siparis_id = $_POST['siparis_id'];
    $tedarik_id = $_POST['tedarik_id'];
    $urun_id = $_POST['urun_id'];
    $siparis_tarih = $_POST['siparis_tarih'];
    $toplam_tutar = $_POST['toplam_tutar'];

    try {
        $stmt = $pdo->prepare("CALL giza_TedarikEtmeGuncelle(?, ?, ?, ?, ?)");
        $stmt->execute([$siparis_id, $tedarik_id, $urun_id, $siparis_tarih, $toplam_tutar]);
        $message = "Tedarik işlemi başarıyla güncellendi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Tedarik işlemi güncellenirken hata oluştu: " . $e->getMessage();
    }
}

if (isset($_GET['delete_id'])) {
    $siparis_id = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("CALL giza_TedarikEtmeSil(?)");
        $stmt->execute([$siparis_id]);
        $message = "Tedarik işlemi başarıyla silindi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Tedarik işlemi silinirken hata oluştu: " . $e->getMessage();
    }
}

$tedarik_etme_records = [];
try {
    $stmt_records = $pdo->query("CALL giza_TedarikEtmeHepsi()");
    $tedarik_etme_records = $stmt_records->fetchAll();
    $stmt_records->closeCursor();
} catch (PDOException $e) {
    $error = "Tedarik etme kayıtları getirilirken hata oluştu: " . $e->getMessage();
}

$edit_supply = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    try {
        $stmt_edit = $pdo->prepare("SELECT * FROM tedarik_etme WHERE siparis_id = ?");
        $stmt_edit->execute([$edit_id]);
        $edit_supply = $stmt_edit->fetch();
        $stmt_edit->closeCursor();
    } catch (PDOException $e) {
        $error = "Düzenlenecek tedarik etme bilgileri getirilirken hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tedarik Etme - Giza Mobilya Yönetim Sistemi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5dc;
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
            background-color: #795548;
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
            color: #795548;
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
            background-color: #d7ccc8;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        .form-section h3 {
            color: #5d4037;
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
            border-color: #795548;
            outline: none;
            box-shadow: 0 0 5px rgba(121, 85, 72, 0.3);
        }
        .form-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .button {
            background-color: #795548;
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
            background-color: #6d4c41;
            transform: translateY(-2px);
        }
        .button.cancel {
            background-color: #f44336;
        }
        .button.cancel:hover {
            background-color: #da190b;
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
        window.onload = function() {
            var now = new Date();
            var year = now.getFullYear();
            var month = (now.getMonth() + 1).toString().padStart(2, '0');
            var day = now.getDate().toString().padStart(2, '0');
            var hours = now.getHours().toString().padStart(2, '0');
            var minutes = now.getMinutes().toString().padStart(2, '0');
            var datetimeLocal = `${year}-${month}-${day}T${hours}:${minutes}`;
            
            var siparisTarihInput = document.getElementById('siparis_tarih');
            if (siparisTarihInput && !siparisTarihInput.value) {
                siparisTarihInput.value = datetimeLocal;
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
        <h2>Tedarik Etme Yönetimi</h2>

        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h3><?php echo $edit_supply ? 'Tedarik İşlemi Bilgilerini Güncelle' : 'Yeni Tedarik İşlemi Ekle'; ?></h3>
            <form action="tedarik_edis.php" method="POST">
                <?php if ($edit_supply): ?>
                    <input type="hidden" name="siparis_id" value="<?php echo htmlspecialchars($edit_supply['siparis_id']); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="siparis_id">Sipariş ID:</label>
                    <input type="text" id="siparis_id" name="siparis_id"
                           value="<?php echo $edit_supply ? htmlspecialchars($edit_supply['siparis_id']) : ''; ?>"
                           <?php echo $edit_supply ? 'readonly' : 'required'; ?>>
                </div>
                <div class="form-group">
                    <label for="tedarik_id">Tedarikçi:</label>
                    <select id="tedarik_id" name="tedarik_id" required>
                        <option value="">Tedarikçi Seçiniz</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?php echo htmlspecialchars($supplier['tedarik_id']); ?>"
                                <?php echo ($edit_supply && $edit_supply['tedarik_id'] == $supplier['tedarik_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($supplier['sirket_ad']); ?> (ID: <?php echo htmlspecialchars($supplier['tedarik_id']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="urun_id">Ürün:</label>
                    <select id="urun_id" name="urun_id" required>
                        <option value="">Ürün Seçiniz</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo htmlspecialchars($product['urun_id']); ?>"
                                <?php echo ($edit_supply && $edit_supply['urun_id'] == $product['urun_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($product['urun_ad']); ?> (ID: <?php echo htmlspecialchars($product['urun_id']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="siparis_tarih">Sipariş Tarihi:</label>
                    <input type="datetime-local" id="siparis_tarih" name="siparis_tarih"
                           value="<?php echo $edit_supply ? date('Y-m-d\TH:i', strtotime($edit_supply['siparis_tarih'])) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="toplam_tutar">Toplam Tutar:</label>
                    <input type="number" id="toplam_tutar" name="toplam_tutar" step="0.01"
                           value="<?php echo $edit_supply ? htmlspecialchars($edit_supply['toplam_tutar']) : ''; ?>" required>
                </div>
                <div class="form-buttons">
                    <?php if ($edit_supply): ?>
                        <button type="submit" name="update_supply" class="button">Güncelle</button>
                        <a href="tedarik_edis.php" class="button cancel">İptal</a>
                    <?php else: ?>
                        <button type="submit" name="add_supply" class="button">Ekle</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="table-section">
            <h3>Mevcut Tedarik İşlemleri</h3>
            <table>
                <thead>
                    <tr>
                        <th>Sipariş ID</th>
                        <th>Tedarikçi ID</th>
                        <th>Ürün ID</th>
                        <th>Sipariş Tarihi</th>
                        <th>Toplam Tutar</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($tedarik_etme_records) > 0): ?>
                        <?php foreach ($tedarik_etme_records as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['siparis_id']); ?></td>
                                <td><?php echo htmlspecialchars($record['tedarik_id']); ?></td>
                                <td><?php echo htmlspecialchars($record['urun_id']); ?></td>
                                <td><?php echo htmlspecialchars($record['siparis_tarih']); ?></td>
                                <td><?php echo htmlspecialchars($record['toplam_tutar']); ?></td>
                                <td class="action-buttons">
                                    <a href="tedarik_edis.php?edit_id=<?php echo htmlspecialchars($record['siparis_id']); ?>" class="button edit">Düzenle</a>
                                    <a href="tedarik_edis.php?delete_id=<?php echo htmlspecialchars($record['siparis_id']); ?>" class="button delete" onclick="return confirm('Bu tedarik işlemini silmek istediğinizden emin misiniz?');">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Henüz tedarik işlemi bulunmamaktadır.</td>
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
