<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Quicksand', sans-serif;
            background: linear-gradient(135deg, #ffffff, #fff7f0);
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }

        .card {
            background: #fffefc;
            border: 3px dashed #ffa726;
            padding: 3rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 20px rgba(255, 165, 0, 0.2);
            max-width: 500px;
        }

        h1 {
            color: #ff9800;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .btn {
            background-color: #ff9800;
            color: white;
            padding: 0.8rem 1.5rem;
            text-decoration: none;
            font-weight: bold;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #fb8c00;
        }

        .emoji {
            font-size: 2rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="emoji">🎉📝</div>
        <h1>Terima Kasih!</h1>
        <p>Telah mengisi form pendaftaran. 🧡<br>Tunggu proses selanjutnya dari tim kami ya... 😎</p>
        <a href="/" class="btn">🏠 Kembali ke Beranda</a>
    </div>
</body>
</html>
