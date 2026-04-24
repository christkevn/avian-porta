<head>
    <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <style>
        body {
            background-color: #1C2127;
            margin: 0;
            overflow: hidden;
        }

        .message {
            font-family: 'Poppins', sans-serif;
            font-size: 30px;
            color: white;
            font-weight: 500;
            position: absolute;
            top: 230px;
            left: 40px;
        }

        .message2 {
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            color: white;
            font-weight: 300;
            width: 380px;
            position: absolute;
            top: 280px;
            left: 40px;
        }

        .back-btn {
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            color: #1C2127;
            background-color: #5BE0B3;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            position: absolute;
            top: 360px;
            left: 40px;
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(91, 224, 179, 0.5);
        }

        .back-btn:hover {
            background-color: #6EECC1;
            box-shadow: 0 0 20px rgba(91, 224, 179, 0.8);
            transform: translateY(-2px);
        }

        .container {
            position: absolute;
            right: 30px;
            top: 40px;
            text-align: center;
        }

        .neon {
            font-family: 'Varela Round', sans-serif;
            font-size: 90px;
            color: #5BE0B3;
            letter-spacing: 3px;
            text-shadow: 0 0 5px #6EECC1;
            animation: flux 2s linear infinite;
        }

        .lost-zone {
            margin-top: 40px;
            position: relative;
            width: 260px;
            height: 260px;
        }

        .planet {
            width: 180px;
            height: 180px;
            background: radial-gradient(circle at 30% 30%, #6EECC1, #5BE0B3, #2E3A44);
            border-radius: 50%;
            margin: 0 auto;
            box-shadow: 0 0 40px rgba(91, 224, 179, 0.5);
            animation: float 4s ease-in-out infinite;
        }

        .ring {
            position: absolute;
            width: 240px;
            height: 60px;
            border: 3px solid #5BE0B3;
            border-radius: 50%;
            top: 90px;
            left: 10px;
            transform: rotate(-15deg);
            opacity: 0.5;
        }

        .star {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #B9FFE8;
            border-radius: 50%;
            animation: twinkle 3s infinite alternate;
        }

        .star:nth-child(1) {
            top: 10px;
            left: 20px;
        }

        .star:nth-child(2) {
            top: 40px;
            left: 200px;
        }

        .star:nth-child(3) {
            top: 160px;
            left: 30px;
        }

        .star:nth-child(4) {
            top: 200px;
            left: 180px;
        }

        @keyframes float {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-12px);
            }

            100% {
                transform: translateY(0);
            }
        }

        @keyframes twinkle {
            from {
                opacity: 0.2;
                transform: scale(1);
            }

            to {
                opacity: 1;
                transform: scale(1.6);
            }
        }

        @keyframes flux {

            0%,
            100% {
                text-shadow: 0 0 5px #00FFC6, 0 0 15px #00FFC6, 0 0 50px #00FFC6, 0 0 50px #00FFC6, 0 0 2px #B9FFE8, 2px 2px 3px #12E29C;
                color: #4BFFEF;
            }

            50% {
                text-shadow: 0 0 3px #00B58D, 0 0 7px #00B58D, 0 0 25px #00B58D, 0 0 25px #00B58D, 0 0 2px #00B58D, 2px 2px 3px #006A60;
                color: #63D3AE;
            }
        }
    </style>
</head>

<body>
    <div class="message">Halaman tidak ditemukan.</div>
    <div class="message2">Kamu nyasar ke koordinat yang nggak ada di sistem. URL-nya mungkin salah atau halamannya sudah
        hilang.</div>
    <a href="{{ url('/dashboard') }}" class="back-btn">Kembali ke Home</a>

    <div class="container">
        <div class="neon">404</div>
        <div class="lost-zone">
            <div class="planet"></div>
            <div class="ring"></div>
            <div class="star"></div>
            <div class="star"></div>
            <div class="star"></div>
            <div class="star"></div>
        </div>
    </div>
</body>
