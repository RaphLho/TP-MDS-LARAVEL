<!DOCTYPE html>
<html lang="fr" class="antialiased">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RaphCorp - Gestion de locations de box</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                    animation: {
                        'bounce-slow': 'bounce 3s infinite',
                    }
                },
            },
        }
    </script>
    <style>
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        .float-animation:hover {
            animation-play-state: paused;
        }

        .toggle-checkbox:checked {
            right: 0;
            border-color: #68D391;
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #68D391;
        }

        .toggle-checkbox:not(:checked)+.toggle-label {
            background-color: #F56565;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: rgb(224, 224, 224);
            margin: 15% auto;
            padding: 20px;
            width: 50%;
            border-radius: 8px;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-2xl font-bold text-blue-600">RaphCorp</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="ml-3 relative" x-data="{ open: false }">
                        <div>
                            <button @click="open = !open"
                                class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                id="user-menu" aria-haspopup="true">
                                <span class="mr-2">{{ Auth::user()->name }}</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div x-show="open" @click.away="open = false"
                            class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 animate-slideDown"
                            role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
                            <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                role="menuitem">Votre Profil</a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none"
                                    role="menuitem">
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Modal pour ajouter une box -->
    <div id="addBoxModal" class="modal">
        <div class="modal-content">
            <h2 class="text-2xl font-bold mb-4">Ajouter une nouvelle box</h2>
            <form id="addBoxForm" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Adresse</label>
                    <input type="text" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prix</label>
                    <input type="number" name="price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded-md">Annuler</button>
                    <button type="submit" onclick="event.preventDefault(); submitForm();" class="px-4 py-2 bg-blue-600 text-white rounded-md">Ajouter</button>
                </div>
            </form>
        </div>
    </div>

    <main class="flex-grow">
        <div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="flex justify-end mb-6">
                    <button onclick="openModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Ajouter une box
                    </button>
                </div>
                <h2
                    class="text-3xl font-extrabold text-center mb-8 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300 animate-bounce-slow">
                    Gestion de locations de box
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($boxes as $box)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105 hover:shadow-lg float-animation"
                            x-data="{
                                status: {{ $box->status ? 1 : 0 }},
                                async toggleStatus() {
                                    try {
                                        const response = await fetch('/web/boxes/{{ $box->id }}/toggle-status', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                            }
                                        });
                                        if (response.ok) {
                                            const data = await response.json();
                                            this.status = data.status;
                                        } else {
                                            console.error('Failed to update status');
                                        }
                                    } catch (error) {
                                        console.error('Error:', error);
                                    }
                                }
                            }">
                            <img src="https://picsum.photos/800/40{{ rand(0, 9) }}" alt="Image de la box"
                                class="w-full h-48 object-cover">
                            <div class="p-6">
                                <h3
                                    class="text-xl font-semibold mb-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300">
                                    {{ $box->name }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-300 mb-4">
                                    {{ $box->description }}
                                </p>
                                <p class="text-gray-900 dark:text-gray-100 mb-4">
                                    Adresse: {{ $box->address }}
                                </p>
                                <p class="text-gray-900 dark:text-gray-100 mb-4">
                                    Prix: {{ $box->price }} €
                                </p>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-gray-900 dark:text-gray-100">Statut:</span>
                                    <div
                                        class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                        <input type="checkbox" name="toggle" id="toggle-{{ $box->id }}"
                                            class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                                            :checked="status === 1" @click="toggleStatus">
                                        <label for="toggle-{{ $box->id }}"
                                            class="toggle-label block overflow-hidden h-6 rounded-full cursor-pointer bg-gray-300 dark:bg-gray-600"
                                            :class="{ 'bg-green-500': status === 1, 'bg-gray-300': status === 0 }"></label>
                                    </div>
                                    <span x-text="status === 0 ? 'Disponible' : 'Loué'"
                                        :class="{ 'text-green-600': status === 0, 'text-red-600': status === 1 }">
                                    </span>
                                </div>
                                <button @click="deleteBox({{ $box->id }})"
                                    class="text-red-600 dark:text-red-400 hover:underline transition-all duration-300 ease-in-out hover:text-red-800 dark:hover:text-red-200 cursor-pointer">
                                    Supprimer
                                </button>
                                <button onclick="openEditModal({{ $box->id }})"
                                    class="m-8 text-blue-600 dark:text-blue-400 hover:underline transition-all duration-300 ease-in-out hover:text-blue-800 dark:hover:text-blue-200 cursor-pointer">
                                    Modifier
                                </button>

                                <!-- Edit Box Modal -->
                                <div id="editBoxModal-{{ $box->id }}" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden overflow-y-auto">
                                    <div class="flex items-center justify-center min-h-screen p-4">
                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 w-full max-h-screen overflow-y-auto mx-4">
                                            <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Modifier la box</h2>
                                            <form id="editBoxForm-{{ $box->id }}" class="space-y-4">
                                                <div>
                                                    <label class="block text-gray-700 dark:text-gray-300 mb-2">Nom</label>
                                                    <input type="text" name="name" value="{{ $box->name }}" 
                                                        class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label class="block text-gray-700 dark:text-gray-300 mb-2">Description</label>
                                                    <textarea name="description" 
                                                        class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ $box->description }}</textarea>
                                                </div>
                                                <div>
                                                    <label class="block text-gray-700 dark:text-gray-300 mb-2">Adresse</label>
                                                    <input type="text" name="address" value="{{ $box->address }}"
                                                        class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label class="block text-gray-700 dark:text-gray-300 mb-2">Prix</label>
                                                    <input type="number" name="price" value="{{ $box->price }}"
                                                        class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                </div>
                                                <div class="flex justify-end space-x-4">
                                                    <button type="button" onclick="closeEditModal({{ $box->id }})"
                                                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                                        Annuler
                                                    </button>
                                                    <button type="button" onclick="submitEditForm({{ $box->id }})"
                                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                                        Sauvegarder
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $boxes->links() }}
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script>
        // Remove or comment out the Three.js code
        // const scene = new THREE.Scene();
        // ...

        // Keep only the modal functions
        function openModal() {
            document.getElementById('addBoxModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addBoxModal').style.display = 'none';
        }

        function openEditModal(boxId) {
            document.getElementById(`editBoxModal-${boxId}`).style.display = 'block';
        }

        function closeEditModal(boxId) {
            document.getElementById(`editBoxModal-${boxId}`).style.display = 'none';
        }

        // Add the missing submitForm function
        async function submitForm() {
            const form = document.getElementById('addBoxForm');
            const formData = new FormData(form);

            try {
                const response = await fetch('/web/boxes', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData
                });

                if (response.ok) {
                    closeModal();
                    window.location.reload(); // Refresh the page to show the new box
                } else {
                    console.error('Failed to add box');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('addBoxModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Add this function to handle box deletion
        async function deleteBox(boxId) {
            try {
                const response = await fetch(`/web/boxes/${boxId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                });
                if (response.ok) {
                    window.location.reload();
                } else {
                    console.error('Failed to delete box');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function submitEditForm(boxId) {
            const form = document.getElementById(`editBoxForm-${boxId}`);
            const formData = new FormData(form);
            
            try {
                const response = await fetch(`/web/boxes/${boxId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });

                if (response.ok) {
                    closeEditModal(boxId);
                    window.location.reload();
                } else {
                    const error = await response.json();
                    console.error('Failed to update box:', error);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    </script>
</body>

</html>
