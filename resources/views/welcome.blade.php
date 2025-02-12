<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Accueil - RaphCorp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                },
            },
        }
    </script>
</head>

<body class="h-full bg-gray-100 flex flex-col justify-center items-center">
    <div class="text-center">
        <h1 class="text-4xl font-bold mb-8 text-gray-800 hover:text-blue-600 transition-colors duration-300">
            RaphCorp
        </h1>
        <div class="space-y-4">
            <a href="{{ route('register') }}"
                class="block w-64 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                Inscription
            </a>
            <a href="{{ route('login') }}"
                class="block w-64 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-300">
                Connexion
            </a>
        </div>
    </div>
</body>

</html>
