<?php

require_once 'db_config.php';

if (isset($_POST['add_customer'])) {
    $musteri_id = $_POST['musteri_id'];
    $musteri_ad = $_POST['musteri_ad'];
    $musteri_soyad = $_POST['musteri_soyad'];
    $musteri_adres = $_POST['musteri_adres'];
    $musteri_tel = $_POST['musteri_tel'];

    try {
        $stmt = $pdo->prepare("CALL giza_MusteriEkle(?, ?, ?, ?, ?)");
        $stmt->execute([$musteri_id, $musteri_ad, $musteri_soyad, $musteri_adres, $musteri_tel]);
        $message = "Müşteri başarıyla eklendi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Müşteri eklenirken hata oluştu: " . $e->getMessage();
    }
}

if (isset($_POST['update_customer'])) {
    $musteri_id = $_POST['musteri_id'];
    $musteri_ad = $_POST['musteri_ad'];
    $musteri_soyad = $_POST['musteri_soyad'];
    $musteri_adres = $_POST['musteri_adres'];
    $musteri_tel = $_POST['musteri_tel'];

    try {
        $stmt = $pdo->prepare("CALL giza_MusteriGuncelle(?, ?, ?, ?, ?)");
        $stmt->execute([$musteri_id, $musteri_ad, $musteri_soyad, $musteri_adres, $musteri_tel]);
        $message = "Müşteri başarıyla güncellendi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Müşteri güncellenirken hata oluştu: " . $e->getMessage();
    }
}

if (isset($_GET['delete_id'])) {
    $musteri_id = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("CALL giza_MusteriSil(?)");
        $stmt->execute([$musteri_id]);
        $message = "Müşteri başarıyla silindi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Müşteri silinirken hata oluştu: " . $e->getMessage();
    }
}

$musteriler = [];
$search_query = '';
if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
    $search_query = $_GET['search_query'];
    try {
        $stmt = $pdo->prepare("CALL giza_MusteriBul(?)");
        $stmt->execute([$search_query]);
        $musteriler = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Arama sırasında hata oluştu: " . $e->getMessage();
    }
} else {
    try {
        $stmt = $pdo->query("CALL giza_MusterilerHepsi()");
        $musteriler = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Müşteriler getirilirken hata oluştu: " . $e->getMessage();
    }
}

$edit_customer = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM giza_musteriler WHERE musteri_id = ?");
        $stmt->execute([$edit_id]);
        $edit_customer = $stmt->fetch();
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Düzenlenecek müşteri bilgileri getirilirken hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteriler - Giza Mobilya Yönetim Sistemi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f8f8;
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
            background-color: #008080;
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
            color: #008080;
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
            background-color: #E0FFFF;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        .form-section h3 {
            color: #005A5A;
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
        .form-group input[type="tel"] {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }
        .form-group input[type="text"]:focus,
        .form-group input[type="tel"]:focus {
            border-color: #008080;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 128, 128, 0.3);
        }
        .form-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .button {
            background-color: #008080;
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
            background-color: #006666;
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

        .search-section {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: center;
        }
        .search-section input[type="text"] {
            flex-grow: 1;
            max-width: 400px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
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
        <h2>Müşteri Yönetimi</h2>

        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h3><?php echo $edit_customer ? 'Müşteri Bilgilerini Güncelle' : 'Yeni Müşteri Ekle'; ?></h3>
            <form action="musteriler.php" method="POST">
                <?php if ($edit_customer): ?>
                    <input type="hidden" name="musteri_id" value="<?php echo htmlspecialchars($edit_customer['musteri_id']); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="musteri_id">Müşteri ID:</label>
                    <input type="text" id="musteri_id" name="musteri_id"
                           value="<?php echo $edit_customer ? htmlspecialchars($edit_customer['musteri_id']) : ''; ?>"
                           <?php echo $edit_customer ? 'readonly' : 'required'; ?>>
                </div>
                <div class="form-group">
                    <label for="musteri_ad">Adı:</label>
                    <input type="text" id="musteri_ad" name="musteri_ad"
                           value="<?php echo $edit_customer ? htmlspecialchars($edit_customer['musteri_ad']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="musteri_soyad">Soyadı:</label>
                    <input type="text" id="musteri_soyad" name="musteri_soyad"
                           value="<?php echo $edit_customer ? htmlspecialchars($edit_customer['musteri_soyad']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="musteri_adres">Adres:</label>
                    <input type="text" id="musteri_adres" name="musteri_adres"
                           value="<?php echo $edit_customer ? htmlspecialchars($edit_customer['musteri_adres']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="musteri_tel">Telefon:</label>
                    <input type="tel" id="musteri_tel" name="musteri_tel"
                           value="<?php echo $edit_customer ? htmlspecialchars($edit_customer['musteri_tel']) : ''; ?>" required>
                </div>
                <div class="form-buttons">
                    <?php if ($edit_customer): ?>
                        <button type="submit" name="update_customer" class="button">Güncelle</button>
                        <a href="musteriler.php" class="button cancel">İptal</a>
                    <?php else: ?>
                        <button type="submit" name="add_customer" class="button">Ekle</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="search-section">
            <form action="musteriler.php" method="GET" style="display: flex; width: 100%; gap: 10px;">
                <input type="text" name="search_query" placeholder="Müşteri ara (ID, Ad, Soyad, Adres, Telefon)" value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="button search">Ara</button>
                <a href="musteriler.php" class="button cancel">Temizle</a>
            </form>
        </div>

        <div class="table-section">
            <h3>Mevcut Müşteriler</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Adı</th>
                        <th>Soyadı</th>
                        <th>Adres</th>
                        <th>Telefon</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($musteriler) > 0): ?>
                        <?php foreach ($musteriler as $musteri): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($musteri['ID'] ?? $musteri['musteri_id']); ?></td>
                                <td><?php echo htmlspecialchars($musteri['Adı'] ?? $musteri['musteri_ad']); ?></td>
                                <td><?php echo htmlspecialchars($musteri['Soyadı'] ?? $musteri['musteri_soyad']); ?></td>
                                <td><?php echo htmlspecialchars($musteri['Adres'] ?? $musteri['musteri_adres']); ?></td>
                                <td><?php echo htmlspecialchars($musteri['Telefon'] ?? $musteri['musteri_tel']); ?></td>
                                <td class="action-buttons">
                                    <a href="musteriler.php?edit_id=<?php echo htmlspecialchars($musteri['ID'] ?? $musteri['musteri_id']); ?>" class="button edit">Düzenle</a>
                                    <a href="musteriler.php?delete_id=<?php echo htmlspecialchars($musteri['ID'] ?? $musteri['musteri_id']); ?>" class="button delete" onclick="return confirm('Bu müşteriyi silmek istediğinizden emin misiniz?');">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Henüz müşteri bulunmamaktadır.</td>
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
