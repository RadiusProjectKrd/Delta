<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добро пожаловать!</title>
    <style>
        :root {
            --neon-color: #0fff83;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            min-height: 100%;
            overflow-x: hidden;
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #f5f5f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header, footer {
            width: 100%;
        }

        header {
            background-color: #1e1e1e;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 10;
            border-radius: 50px;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 50px;
            width: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 1rem;
        }

        .brand-name {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ffffff;
            display: flex;
            align-items: center;
        }

        nav {
            position: relative;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 1rem;
            margin-left: auto;
            transition: max-height 0.3s ease;
        }

        nav ul li a {
            text-decoration: none;
            color: #ffffff;
            transition: color 0.3s, box-shadow 0.3s, border-color 0.3s;
            padding: 0.5rem 1rem;
            border: 3px solid var(--neon-color);
            border-radius: 50px;
            display: inline-block;
            text-align: center;
            box-shadow: 0 0 10px var(--neon-color);
        }

        nav ul li a:hover {
            color: var(--neon-color);
            box-shadow: 0 0 15px var(--neon-color);
        }

        .menu-toggle {
            display: none;
            cursor: pointer;
        }

        .menu-toggle img {
            height: 30px;
            width: 30px;
        }

        main {
            flex: 1;
            width: 100%;
            display: grid;
            place-items: center;
            padding: 2rem;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            width: 100%;
        }

        @media (min-width: 1200px) {
            .cards-container {
                max-width: 1200px;
                margin: 0 auto;
            }
        }

        .card {
            background-color: #1f1f1f;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 0 15px var(--neon-color);
            border: 2px solid var(--neon-color);
            transition: transform 0.3s, background-color 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            background-color: #292929;
            box-shadow: 0 0 25px var(--neon-color);
        }

        .card h3 {
            margin-bottom: 0.5rem;
            font-size: 1.3rem;
        }

        .card p {
            font-size: 1rem;
            line-height: 1.4;
            color: #cccccc;
        }

        footer {
            background-color: #1e1e1e;
            text-align: center;
            padding: 1rem;
            font-size: 0.9rem;
            color: #aaaaaa;
            border-radius: 50px;
        }

        @media (max-width: 600px) {
            .menu-toggle {
                display: block;
            }

            nav ul {
                flex-direction: column;
                position: absolute;
                top: 100%;
                right: 0;
                background-color: #1e1e1e;
                width: 200px;
                max-height: 0;
                overflow: hidden;
                border-radius: 10px;
            }

            nav ul.show {
                max-height: 500px;
            }

            nav ul li a {
                width: 100%;
                display: block;
            }

            .logo img {
                height: 40px;
                width: 40px;
                margin-right: 0.5rem;
            }

            .brand-name {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <img src="{{ asset('storage/logo.png') }}" alt="MyBrand Logo">
        <div class="brand-name">《 Radius Project 》</div>
    </div>
    <nav>
        <div class="menu-toggle">
            <img src="{{ asset('storage/menu.svg') }}" alt="Menu">
        </div>
        <ul>
            <li><a href="#">Главная</a></li>
            <li><a href="https://github.com/RadiusProjectKrd">Контакты</a></li>
            <li><a href="/status">Статус</a></li>
        </ul>
    </nav>
</header>

<script>
    const toggle = document.querySelector('.menu-toggle');
    const menu = document.querySelector('nav ul');
    toggle.addEventListener('click', () => {
        menu.classList.toggle('show');
    });
</script>

<main>
    <div class="cards-container">
        <div class="card">
            <h3>Технологии</h3>
            <p>Разработка ведется на стеке: Laravel v12, PostgreSQL v16, Docker, React TS, Python 3.10+</p>
        </div>
        <div class="card">
            <h3>Оборудование</h3>
            <p>В нашем распоряжении находится 3 сервера с постоянным аптаймом: 1 виртуальный и 2 выделенных сервера.</p>
        </div>
        <div class="card">
            <h3>Деятельность</h3>
            <p>Наша комманда работает над творческими проектами в сфере игр и технологий коммуникации: Приложения для коммуникации, боты для соц сетей для любых задач, разработка интеграций и поддержка игровых серверов.</p>
        </div>
    </div>
</main>

<footer>
    © Radius Project from Krasnodar/RU by Ult1mateXPHP.
</footer>
</body>
</html>
