<?php

require_once 'db_config.php';

if (isset($_POST['add_employee'])) {
    $calisan_id = $_POST['calisan_id'];
    $calisan_ad = $_POST['calisan_ad'];
    $calisan_soyad = $_POST['calisan_soyad'];
    $calisan_tel = $_POST['calisan_tel'];
    $calisan_maas = $_POST['calisan_maas'];

    try {
        $stmt = $pdo->prepare("CALL giza_CalisanEkle(?, ?, ?, ?, ?)");
        $stmt->execute([$calisan_id, $calisan_ad, $calisan_soyad, $calisan_tel, $calisan_maas]);
        $message = "Çalışan başarıyla eklendi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Çalışan eklenirken hata oluştu: " . $e->getMessage();
    }
}

if (isset($_POST['update_employee'])) {
    $calisan_id = $_POST['calisan_id'];
    $calisan_ad = $_POST['calisan_ad'];
    $calisan_soyad = $_POST['calisan_soyad'];
    $calisan_tel = $_POST['calisan_tel'];
    $calisan_maas = $_POST['calisan_maas'];

    try {
        $stmt = $pdo->prepare("CALL giza_CalisanGuncelle(?, ?, ?, ?, ?)");
        $stmt->execute([$calisan_id, $calisan_ad, $calisan_soyad, $calisan_tel, $calisan_maas]);
        $message = "Çalışan başarıyla güncellendi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Çalışan güncellenirken hata oluştu: " . $e->getMessage();
    }
}

if (isset($_GET['delete_id'])) {
    $calisan_id = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("CALL giza_CalisanSil(?)");
        $stmt->execute([$calisan_id]);
        $message = "Çalışan başarıyla silindi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Çalışan silinirken hata oluştu: " . $e->getMessage();
    }
}

$calisanlar = [];
$search_query = '';
if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
    $search_query = $_GET['search_query'];
    try {
        $stmt = $pdo->prepare("CALL giza_CalisanBul(?)");
        $stmt->execute([$search_query]);
        $calisanlar = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Arama sırasında hata oluştu: " . $e->getMessage();
    }
} else {
    try {
        $stmt = $pdo->query("CALL giza_CalisanlarHepsi()");
        $calisanlar = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Çalışanlar getirilirken hata oluştu: " . $e->getMessage();
    }
}

$edit_employee = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM giza_calisanlar WHERE calisan_id = ?");
        $stmt->execute([$edit_id]);
        $edit_employee = $stmt->fetch();
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Düzenlenecek çalışan bilgileri getirilirken hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çalışanlar - Giza Mobilya Yönetim Sistemi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e8f5e9;
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
            background-color: #4CAF50;
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
            color: #4CAF50;
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
            background-color: #DCEDC8;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        .form-section h3 {
            color: #2E7D32;
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
        .form-group input[type="tel"],
        .form-group input[type="number"] {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }
        .form-group input[type="text"]:focus,
        .form-group input[type="tel"]:focus,
        .form-group input[type="number"]:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }
        .form-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .button {
            background-color: #4CAF50;
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
            background-color: #45A049;
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
            .search-section {
                flex-direction: column;
                align-items: stretch;
            }
            .search-section input[type="text"],
            .search-section .button {
                width: 100%;
                max-width: none;
            }
        }
    </style>
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
        <h2>Çalışan Yönetimi</h2>

        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h3><?php echo $edit_employee ? 'Çalışan Bilgilerini Güncelle' : 'Yeni Çalışan Ekle'; ?></h3>
            <form action="calisanlar.php" method="POST">
                <?php if ($edit_employee): ?>
                    <input type="hidden" name="calisan_id" value="<?php echo htmlspecialchars($edit_employee['calisan_id']); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="calisan_id">Çalışan ID:</label>
                    <input type="text" id="calisan_id" name="calisan_id"
                           value="<?php echo $edit_employee ? htmlspecialchars($edit_employee['calisan_id']) : ''; ?>"
                           <?php echo $edit_employee ? 'readonly' : 'required'; ?>>
                </div>
                <div class="form-group">
                    <label for="calisan_ad">Adı:</label>
                    <input type="text" id="calisan_ad" name="calisan_ad"
                           value="<?php echo $edit_employee ? htmlspecialchars($edit_employee['calisan_ad']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="calisan_soyad">Soyadı:</label>
                    <input type="text" id="calisan_soyad" name="calisan_soyad"
                           value="<?php echo $edit_employee ? htmlspecialchars($edit_employee['calisan_soyad']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="calisan_tel">Telefon:</label>
                    <input type="tel" id="calisan_tel" name="calisan_tel"
                           value="<?php echo $edit_employee ? htmlspecialchars($edit_employee['calisan_tel']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="calisan_maas">Maaş:</label>
                    <input type="number" id="calisan_maas" name="calisan_maas" step="0.01"
                           value="<?php echo $edit_employee ? htmlspecialchars($edit_employee['calisan_maas']) : ''; ?>" required>
                </div>
                <div class="form-buttons">
                    <?php if ($edit_employee): ?>
                        <button type="submit" name="update_employee" class="button">Güncelle</button>
                        <a href="calisanlar.php" class="button cancel">İptal</a>
                    <?php else: ?>
                        <button type="submit" name="add_employee" class="button">Ekle</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="search-section">
            <form action="calisanlar.php" method="GET" style="display: flex; width: 100%; gap: 10px;">
                <input type="text" name="search_query" placeholder="Çalışan ara (ID, Ad, Soyad, Telefon)" value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="button search">Ara</button>
                <a href="calisanlar.php" class="button cancel">Temizle</a>
            </form>
        </div>

        <div class="table-section">
            <h3>Mevcut Çalışanlar</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Adı</th>
                        <th>Soyadı</th>
                        <th>Telefon</th>
                        <th>Maaş</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($calisanlar) > 0): ?>
                        <?php foreach ($calisanlar as $calisan): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($calisan['calisan_id']); ?></td>
                                <td><?php echo htmlspecialchars($calisan['calisan_ad']); ?></td>
                                <td><?php echo htmlspecialchars($calisan['calisan_soyad']); ?></td>
                                <td><?php echo htmlspecialchars($calisan['calisan_tel']); ?></td>
                                <td><?php echo htmlspecialchars($calisan['calisan_maas']); ?></td>
                                <td class="action-buttons">
                                    <a href="calisanlar.php?edit_id=<?php echo htmlspecialchars($calisan['calisan_id']); ?>" class="button edit">Düzenle</a>
                                    <a href="calisanlar.php?delete_id=<?php echo htmlspecialchars($calisan['calisan_id']); ?>" class="button delete" onclick="return confirm('Bu çalışanı silmek istediğinizden emin misiniz?');">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Henüz çalışan bulunmamaktadır.</td>
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
