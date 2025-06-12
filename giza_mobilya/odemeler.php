<?php

require_once 'db_config.php';

$customers = [];
try {
    $stmt = $pdo->query("SELECT musteri_id, CONCAT(musteri_ad, ' ', musteri_soyad) AS full_name FROM giza_musteriler");
    $customers = $stmt->fetchAll();
    $stmt->closeCursor();
} catch (PDOException $e) {
    $error = "Müşteri verileri getirilirken hata oluştu: " . $e->getMessage();
}

if (isset($_POST['add_payment'])) {
    $odeme_id = $_POST['odeme_id'];
    $musteri_id = $_POST['musteri_id'];
    $odeme_tarih = $_POST['odeme_tarih'];
    $odeme_tutar = $_POST['odeme_tutar'];
    $odeme_tur = $_POST['odeme_tur'];
    $odeme_sekil = $_POST['odeme_sekil'];
    $odeme_aciklama = $_POST['odeme_aciklama'];

    try {
        $stmt = $pdo->prepare("CALL giza_OdemeEkle(?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$odeme_id, $musteri_id, $odeme_tarih, $odeme_tutar, $odeme_tur, $odeme_sekil, $odeme_aciklama]);
        $message = "Ödeme başarıyla eklendi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Ödeme eklenirken hata oluştu: " . $e->getMessage();
    }
}

if (isset($_POST['update_payment'])) {
    $odeme_id = $_POST['odeme_id'];
    $musteri_id = $_POST['musteri_id'];
    $odeme_tarih = $_POST['odeme_tarih'];
    $odeme_tutar = $_POST['odeme_tutar'];
    $odeme_tur = $_POST['odeme_tur'];
    $odeme_sekil = $_POST['odeme_sekil'];
    $odeme_aciklama = $_POST['odeme_aciklama'];

    try {
        $stmt = $pdo->prepare("CALL giza_OdemeGuncelle(?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$odeme_id, $musteri_id, $odeme_tarih, $odeme_tutar, $odeme_tur, $odeme_sekil, $odeme_aciklama]);
        $message = "Ödeme başarıyla güncellendi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Ödeme güncellenirken hata oluştu: " . $e->getMessage();
    }
}

if (isset($_GET['delete_id'])) {
    $odeme_id = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("CALL giza_OdemeSil(?)");
        $stmt->execute([$odeme_id]);
        $message = "Ödeme başarıyla silindi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Ödeme silinirken hata oluştu: " . $e->getMessage();
    }
}

$odemeler = [];
try {
    $stmt = $pdo->query("CALL giza_OdemeDetay()");
    $odemeler = $stmt->fetchAll();
    $stmt->closeCursor();
} catch (PDOException $e) {
    $error = "Ödeme detayları getirilirken hata oluştu: " . $e->getMessage();
}

$edit_payment = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM giza_odemeler WHERE odeme_id = ?");
        $stmt->execute([$edit_id]);
        $edit_payment = $stmt->fetch();
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Düzenlenecek ödeme bilgileri getirilirken hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödemeler - Giza Mobilya Yönetim Sistemi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3e5f5;
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
            background-color: #673ab7;
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
            color: #673ab7;
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
            background-color: #ede7f6;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        .form-section h3 {
            color: #4527a0;
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
        .form-group select,
        .form-group textarea {
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
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #673ab7;
            outline: none;
            box-shadow: 0 0 5px rgba(103, 58, 183, 0.3);
        }
        .form-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .button {
            background-color: #673ab7;
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
            background-color: #5e35b1;
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
            
            var odemeTarihInput = document.getElementById('odeme_tarih');
            if (odemeTarihInput && !odemeTarihInput.value) {
                odemeTarihInput.value = datetimeLocal;
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
        <h2>Ödeme Yönetimi</h2>

        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h3><?php echo $edit_payment ? 'Ödeme Bilgilerini Güncelle' : 'Yeni Ödeme Ekle'; ?></h3>
            <form action="odemeler.php" method="POST">
                <?php if ($edit_payment): ?>
                    <input type="hidden" name="odeme_id" value="<?php echo htmlspecialchars($edit_payment['odeme_id']); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="odeme_id">Ödeme ID:</label>
                    <input type="text" id="odeme_id" name="odeme_id"
                           value="<?php echo $edit_payment ? htmlspecialchars($edit_payment['odeme_id']) : ''; ?>"
                           <?php echo $edit_payment ? 'readonly' : 'required'; ?>>
                </div>
                <div class="form-group">
                    <label for="musteri_id">Müşteri:</label>
                    <select id="musteri_id" name="musteri_id" required>
                        <option value="">Müşteri Seçiniz</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo htmlspecialchars($customer['musteri_id']); ?>"
                                <?php echo ($edit_payment && $edit_payment['musteri_id'] == $customer['musteri_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($customer['full_name']); ?> (ID: <?php echo htmlspecialchars($customer['musteri_id']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="odeme_tarih">Ödeme Tarihi:</label>
                    <input type="datetime-local" id="odeme_tarih" name="odeme_tarih"
                           value="<?php echo $edit_payment ? date('Y-m-d\TH:i', strtotime($edit_payment['odeme_tarih'])) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="odeme_tutar">Ödeme Tutarı:</label>
                    <input type="number" id="odeme_tutar" name="odeme_tutar" step="0.01"
                           value="<?php echo $edit_payment ? htmlspecialchars($edit_payment['odeme_tutar']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="odeme_tur">Ödeme Türü:</label>
                    <select id="odeme_tur" name="odeme_tur" required>
                        <option value="">Seçiniz</option>
                        <option value="Nakit" <?php echo ($edit_payment && $edit_payment['odeme_tur'] == 'Nakit') ? 'selected' : ''; ?>>Nakit</option>
                        <option value="Kredi Kartı" <?php echo ($edit_payment && $edit_payment['odeme_tur'] == 'Kredi Kartı') ? 'selected' : ''; ?>>Kredi Kartı</option>
                        <option value="Banka Ödemesi" <?php echo ($edit_payment && $edit_payment['odeme_tur'] == 'Banka Ödemesi') ? 'selected' : ''; ?>>Banka Ödemesi</option>
                        <option value="Çek" <?php echo ($edit_payment && $edit_payment['odeme_tur'] == 'Çek') ? 'selected' : ''; ?>>Çek</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="odeme_sekil">Ödeme Şekli:</label>
                    <select id="odeme_sekil" name="odeme_sekil" required>
                        <option value="">Seçiniz</option>
                        <option value="Peşin" <?php echo ($edit_payment && $edit_payment['odeme_sekil'] == 'Peşin') ? 'selected' : ''; ?>>Peşin</option>
                        <option value="Taksit" <?php echo ($edit_payment && $edit_payment['odeme_sekil'] == 'Taksit') ? 'selected' : ''; ?>>Taksit</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="odeme_aciklama">Açıklama:</label>
                    <textarea id="odeme_aciklama" name="odeme_aciklama" rows="3" required><?php echo $edit_payment ? htmlspecialchars($edit_payment['odeme_aciklama']) : ''; ?></textarea>
                </div>
                <div class="form-buttons">
                    <?php if ($edit_payment): ?>
                        <button type="submit" name="update_payment" class="button">Güncelle</button>
                        <a href="odemeler.php" class="button cancel">İptal</a>
                    <?php else: ?>
                        <button type="submit" name="add_payment" class="button">Ekle</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="table-section">
            <h3>Mevcut Ödemeler</h3>
            <table>
                <thead>
                    <tr>
                        <th>Ödeme ID</th>
                        <th>Müşteri Ad Soyad</th>
                        <th>Ödeme Tarihi</th>
                        <th>Ödeme Tutarı</th>
                        <th>Ödeme Türü</th>
                        <th>Ödeme Şekli</th>
                        <th>Açıklama</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($odemeler) > 0): ?>
                        <?php foreach ($odemeler as $odeme): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($odeme['odeme_id']); ?></td>
                                <td><?php echo htmlspecialchars($odeme['Müşteri Ad Soyad']); ?> (ID: <?php echo htmlspecialchars($odeme['musteri_id']); ?>)</td>
                                <td><?php echo htmlspecialchars($odeme['Ödeme Tarihi']); ?></td>
                                <td><?php echo htmlspecialchars($odeme['Ödeme Tutarı']); ?></td>
                                <td><?php echo htmlspecialchars($odeme['Ödeme Türü']); ?></td>
                                <td><?php echo htmlspecialchars($odeme['Ödeme Şekli']); ?></td>
                                <td><?php echo htmlspecialchars($odeme['Açıklama']); ?></td>
                                <td class="action-buttons">
                                    <a href="odemeler.php?edit_id=<?php echo htmlspecialchars($odeme['odeme_id']); ?>" class="button edit">Düzenle</a>
                                    <a href="odemeler.php?delete_id=<?php echo htmlspecialchars($odeme['odeme_id']); ?>" class="button delete" onclick="return confirm('Bu ödemeyi silmek istediğinizden emin misiniz?');">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">Henüz ödeme bulunmamaktadır.</td>
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
