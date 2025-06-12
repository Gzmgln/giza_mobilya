<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giza Mobilya Yönetim Sistemi</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
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
        }
        header {
            background-color: #2c3e50;
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
            background-color: #34495e;
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
            background-color: #4a657e;
            transform: translateY(-2px);
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        .content h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.8em;
        }
        .content p {
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .card {
            background-color: #e6f0f5;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            text-align: left;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }
        .card h3 {
            color: #34495e;
            margin-top: 0;
            font-size: 1.4em;
            border-bottom: 2px solid #a9b9c9;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .card p {
            font-size: 0.95em;
            color: #555;
        }
        footer {
            margin-top: auto;
            padding: 20px;
            background-color: #34495e;
            color: white;
            text-align: center;
            width: 100%;
            border-radius: 0 0 12px 12px;
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
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
        <div class="content">
            <h2>Hoş Geldiniz!</h2>
            <p>Bu sistem, Giza Mobilya mağazasının müşteri, ürün, satış, ödeme, çalışan, tedarikçi ve tedarik etme işlemlerini düzenli bir şekilde yönetmek için tasarlanmıştır.</p>
            <p>Yan menüden ilgili bölümlere erişerek kayıtları görüntüleyebilir, ekleyebilir, güncelleyebilir ve silebilirsiniz.</p>

            <div class="info-cards">
                <div class="card">
                    <h3>Verimli Yönetim</h3>
                    <p>Tüm mağaza operasyonlarınızı tek bir yerden kolayca yönetin. Stok takibinden satış raporlarına kadar her şey kontrol altında.</p>
                </div>
                <div class="card">
                    <h3>Detaylı Raporlar</h3>
                    <p>Müşteri harcamaları, ürün stokları ve genel satış performansı hakkında anında detaylı raporlar alın.</p>
                </div>
                <div class="card">
                    <h3>Kullanıcı Dostu Arayüz</h3>
                    <p>Minimalist ve temiz tasarımı sayesinde verilere hızlıca erişin ve işlemlerinizi sorunsuz bir şekilde gerçekleştirin.</p>
                </div>
            </div>
        </div>
    </div>

    <footer>
    </footer>
</body>
</html>
