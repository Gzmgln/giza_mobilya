<?php

require_once 'db_config.php';

if (isset($_POST['add_supplier'])) {
    $tedarik_id = $_POST['tedarik_id'];
    $sirket_ad = $_POST['sirket_ad'];
    $tedarik_tel = $_POST['tedarik_tel'];
    $tedarik_adres = $_POST['tedarik_adres'];
    $tedarik_sozlesme = $_POST['tedarik_sozlesme'];

    try {
        $stmt = $pdo->prepare("CALL giza_TedarikciEkle(?, ?, ?, ?, ?)");
        $stmt->execute([$tedarik_id, $sirket_ad, $tedarik_tel, $tedarik_adres, $tedarik_sozlesme]);
        $message = "Tedarikçi başarıyla eklendi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Tedarikçi eklenirken hata oluştu: " . $e->getMessage();
    }
}

if (isset($_POST['update_supplier'])) {
    $tedarik_id = $_POST['tedarik_id'];
    $sirket_ad = $_POST['sirket_ad'];
    $tedarik_tel = $_POST['tedarik_tel'];
    $tedarik_adres = $_POST['tedarik_adres'];
    $tedarik_sozlesme = $_POST['tedarik_sozlesme'];

    try {
        $stmt = $pdo->prepare("CALL giza_TedarikciGuncelle(?, ?, ?, ?, ?)");
        $stmt->execute([$tedarik_id, $sirket_ad, $tedarik_tel, $tedarik_adres, $tedarik_sozlesme]);
        $message = "Tedarikçi başarıyla güncellendi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Tedarikçi güncellenirken hata oluştu: " . $e->getMessage();
    }
}

if (isset($_GET['delete_id'])) {
    $tedarik_id = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("CALL giza_TedarikciSil(?)");
        $stmt->execute([$tedarik_id]);
        $message = "Tedarikçi başarıyla silindi!";
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Tedarikçi silinirken hata oluştu: " . $e->getMessage();
    }
}

$tedarikciler = [];
$search_query = '';
if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
    $search_query = $_GET['search_query'];
    try {
        $stmt = $pdo->prepare("CALL giza_TedarikciBul(?)");
        $stmt->execute([$search_query]);
        $tedarikciler = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Arama sırasında hata oluştu: " . $e->getMessage();
    }
} else {
    try {
        $stmt = $pdo->query("CALL giza_TedarikcilerHepsi()");
        $tedarikciler = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Tedarikçiler getirilirken hata oluştu: " . $e->getMessage();
    }
}

$edit_supplier = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM tedarikciler WHERE tedarik_id = ?");
        $stmt->execute([$edit_id]);
        $edit_supplier = $stmt->fetch();
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $error = "Düzenlenecek tedarikçi bilgileri getirilirken hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tedarikçiler - Giza Mobilya Yönetim Sistemi</title>
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
        .form-group input[type="tel"],
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
        .form-group input[type="tel"]:focus,
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
        <h2>Tedarikçi Yönetimi</h2>

        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h3><?php echo $edit_supplier ? 'Tedarikçi Bilgilerini Güncelle' : 'Yeni Tedarikçi Ekle'; ?></h3>
            <form action="tedarikci.php" method="POST">
                <?php if ($edit_supplier): ?>
                    <input type="hidden" name="tedarik_id" value="<?php echo htmlspecialchars($edit_supplier['tedarik_id']); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="tedarik_id">Tedarikçi ID:</label>
                    <input type="text" id="tedarik_id" name="tedarik_id"
                           value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['tedarik_id']) : ''; ?>"
                           <?php echo $edit_supplier ? 'readonly' : 'required'; ?>>
                </div>
                <div class="form-group">
                    <label for="sirket_ad">Şirket Adı:</label>
                    <input type="text" id="sirket_ad" name="sirket_ad"
                           value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['sirket_ad']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="tedarik_tel">Telefon:</label>
                    <input type="tel" id="tedarik_tel" name="tedarik_tel"
                           value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['tedarik_tel']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="tedarik_adres">Adres:</label>
                    <input type="text" id="tedarik_adres" name="tedarik_adres"
                           value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['tedarik_adres']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="tedarik_sozlesme">Sözleşme:</label>
                    <textarea id="tedarik_sozlesme" name="tedarik_sozlesme" rows="4" required><?php echo $edit_supplier ? htmlspecialchars($edit_supplier['tedarik_sozlesme']) : ''; ?></textarea>
                </div>
                <div class="form-buttons">
                    <?php if ($edit_supplier): ?>
                        <button type="submit" name="update_supplier" class="button">Güncelle</button>
                        <a href="tedarikci.php" class="button cancel">İptal</a>
                    <?php else: ?>
                        <button type="submit" name="add_supplier" class="button">Ekle</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="search-section">
            <form action="tedarikci.php" method="GET" style="display: flex; width: 100%; gap: 10px;">
                <input type="text" name="search_query" placeholder="Tedarikçi ara (ID, Şirket Adı, Telefon, Adres)" value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="button search">Ara</button>
                <a href="tedarikci.php" class="button cancel">Temizle</a>
            </form>
        </div>

        <div class="table-section">
            <h3>Mevcut Tedarikçiler</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Şirket Adı</th>
                        <th>Telefon</th>
                        <th>Adres</th>
                        <th>Sözleşme</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($tedarikciler) > 0): ?>
                        <?php foreach ($tedarikciler as $tedarikci): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($tedarikci['tedarik_id']); ?></td>
                                <td><?php echo htmlspecialchars($tedarikci['sirket_ad']); ?></td>
                                <td><?php echo htmlspecialchars($tedarikci['tedarik_tel']); ?></td>
                                <td><?php echo htmlspecialchars($tedarikci['tedarik_adres']); ?></td>
                                <td><?php echo htmlspecialchars($tedarikci['tedarik_sozlesme']); ?></td>
                                <td class="action-buttons">
                                    <a href="tedarikci.php?edit_id=<?php echo htmlspecialchars($tedarikci['tedarik_id']); ?>" class="button edit">Düzenle</a>
                                    <a href="tedarikci.php?delete_id=<?php echo htmlspecialchars($tedarikci['tedarik_id']); ?>" class="button delete" onclick="return confirm('Bu tedarikçiyi silmek istediğinizden emin misiniz?');">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Henüz tedarikçi bulunmamaktadır.</td>
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
