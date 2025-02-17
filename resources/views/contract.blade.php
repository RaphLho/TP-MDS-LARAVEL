<!DOCTYPE html>
<html lang="fr" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RaphCorp - Gestion des contrats</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Editor.js and plugins -->
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/paragraph@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/list@1.8.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/image@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/table@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/link@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/checklist@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/marker@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/warning@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/code@latest"></script>

    <style>
        .neo-container {
            background: linear-gradient(145deg, #ffffff, #e6f3ff);
            color: #0066cc;
            font-family: 'Orbitron', sans-serif;
        }
        .neo-input {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #0066cc;
            color: #0066cc;
            transition: all 0.3s ease;
        }
        .neo-input:focus {
            box-shadow: 0 0 15px #0066cc;
        }
        .neo-button {
            background: linear-gradient(45deg, #0066cc, #3399ff);
            color: white;
            transition: all 0.3s ease;
        }
        .neo-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(0, 102, 204, 0.5);
        }
        .delete-button {
            background: linear-gradient(45deg, #cc0000, #ff3333);
            color: white;
            transition: all 0.3s ease;
        }
        .delete-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(204, 0, 0, 0.5);
        }
        .editor-container {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #0066cc;
            box-shadow: 0 0 10px rgba(0, 102, 204, 0.2);
        }
        @keyframes glow {
            0% { box-shadow: 0 0 5px #0066cc; }
            50% { box-shadow: 0 0 20px #0066cc; }
            100% { box-shadow: 0 0 5px #0066cc; }
        }
        @keyframes success-animation {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1); opacity: 0; }
        }
        .success-animation {
            animation: success-animation 1s ease-out forwards;
        }
        @keyframes delete-animation {
            0% { transform: scale(1) rotate(0deg); opacity: 1; }
            50% { transform: scale(0.5) rotate(180deg); opacity: 0.5; }
            100% { transform: scale(0) rotate(360deg); opacity: 0; }
        }
        .delete-animation {
            animation: delete-animation 0.8s ease-out forwards;
        }
    </style>
</head>

<body class="bg-blue-50 min-h-screen">
    <div id="success-overlay" class="fixed inset-0 flex items-center justify-center bg-green-500 bg-opacity-50 hidden">
        <div class="bg-white p-8 rounded-lg shadow-xl text-center">
            <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <p class="text-xl font-semibold text-gray-800">Contrat sauvegardé avec succès!</p>
        </div>
    </div>

    <div id="delete-overlay" class="fixed inset-0 flex items-center justify-center bg-red-500 bg-opacity-50 hidden">
        <div class="bg-white p-8 rounded-lg shadow-xl text-center">
            <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            <p class="text-xl font-semibold text-gray-800">Contrat supprimé avec succès!</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="neo-container rounded-2xl shadow-2xl p-8">
            <h1 class="text-4xl font-bold mb-8 text-center">
                <span class="text-blue-600">
                    Système de Gestion des Contrats
                </span>
            </h1>

            @if($contracts->count() > 0)
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2 text-blue-600">Sélectionner un modèle existant</label>
                    <div class="flex gap-4">
                        <select id="templateSelect" class="neo-input w-full rounded-lg p-3 focus:outline-none">
                            <option value="">Sélectionnez un modèle...</option>
                            @foreach($contracts as $contract)
                                <option value="{{ $contract->id }}" data-content='{{ json_encode($contract->content) }}'>
                                    {{ $contract->name }}
                                </option>
                            @endforeach
                        </select>
                        <button id="deleteButton" 
                            class="delete-button font-bold py-3 px-8 rounded-lg transform hover:scale-105 transition-all duration-300 hidden">
                            Supprimer
                        </button>
                    </div>
                </div>
            @endif

            <form action="{{ route('contracts.store') }}" method="POST" id="contractForm">
                @csrf
                <input type="hidden" name="contract_id" id="contract_id">
                <div class="mb-6 relative">
                    <label for="contractName" class="block text-sm font-medium mb-2 text-blue-600">Nom du modèle</label>
                    <input type="text" id="contractName" name="name"
                        class="neo-input w-full rounded-lg p-3 focus:outline-none"
                        placeholder="Entrez le nom du modèle...">
                </div>

                <div id="editorjs" class="editor-container rounded-lg min-h-[400px] p-6 mb-6">
                    
                </div>

                <input type="hidden" name="content" id="editorContent">

                <div class="flex justify-end">
                    <button type="submit" onclick="prepareSubmit(event)" 
                        class="neo-button font-bold py-3 px-8 rounded-lg transform hover:scale-105 transition-all duration-300">
                        Sauvegarder le modèle
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const editorTools = {
            header: {
                class: Header,
                inlineToolbar: ['link']
            },
            paragraph: {
                class: Paragraph,
                inlineToolbar: true
            },
            list: {
                class: List,
                inlineToolbar: true
            },
            image: {
                class: Image,
                inlineToolbar: true
            },
            table: {
                class: Table,
                inlineToolbar: true
            },
            linkTool: {
                class: LinkTool,
                inlineToolbar: true
            },
            embed: {
                class: Embed,
                inlineToolbar: true
            },
            checklist: {
                class: Checklist,
                inlineToolbar: true
            },
            quote: {
                class: Quote,
                inlineToolbar: true
            },
            marker: {
                class: Marker,
                inlineToolbar: true
            },
            warning: {
                class: Warning,
                inlineToolbar: true
            },
            code: {
                class: CodeTool,
                inlineToolbar: true
            }
        };

        let editor;
        
        document.addEventListener('DOMContentLoaded', () => {
            editor = new EditorJS({
                holder: 'editorjs',
                tools: editorTools,
                placeholder: 'Commencez à rédiger votre contrat ici...',
                data: {
                    blocks: [
                        {
                            type: "header",
                            data: {
                                text: "Contrat de Location",
                                level: 2
                            }
                        },
                        {
                            type: "paragraph",
                            data: {
                                text: "Entre les soussignés :"
                            }
                        },
                        {
                            type: "paragraph",
                            data: {
                                text: "Le propriétaire : [Nom du propriétaire]"
                            }
                        },
                        {
                            type: "paragraph",
                            data: {
                                text: "Le locataire : [Nom du locataire]"
                            }
                        },
                        {
                            type: "header",
                            data: {
                                text: "Article 1 - Objet du contrat",
                                level: 3
                            }
                        },
                        {
                            type: "paragraph",
                            data: {
                                text: "Le présent contrat a pour objet la location d'un box de stockage..."
                            }
                        }
                    ]
                }
            });

            // Load template when select changes
            const templateSelect = document.getElementById('templateSelect');
            const templatePreview = document.getElementById('templatePreview');
            const templateContent = document.getElementById('templateContent');
            const deleteButton = document.getElementById('deleteButton');

            if (templateSelect) {
                templateSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (this.value) {
                        const content = JSON.parse(selectedOption.dataset.content);
                        editor.render(content);
                        document.getElementById('contractName').value = selectedOption.text;
                        document.getElementById('contract_id').value = this.value;
                        deleteButton.classList.remove('hidden');
                        
                        // Afficher le contenu du template
                        templatePreview.classList.remove('hidden');
                        templateContent.innerHTML = ''; // Clear previous content
                        
                        content.blocks.forEach(block => {
                            let element;
                            switch(block.type) {
                                case 'header':
                                    element = document.createElement(`h${block.data.level}`);
                                    element.textContent = block.data.text;
                                    break;
                                case 'paragraph':
                                    element = document.createElement('p');
                                    element.textContent = block.data.text;
                                    element.className = 'mb-4';
                                    break;
                                case 'list':
                                    element = document.createElement(block.data.style === 'ordered' ? 'ol' : 'ul');
                                    block.data.items.forEach(item => {
                                        const li = document.createElement('li');
                                        li.textContent = item;
                                        element.appendChild(li);
                                    });
                                    break;
                                // Add more cases for other block types as needed
                            }
                            if (element) {
                                templateContent.appendChild(element);
                            }
                        });
                    } else {
                        templatePreview.classList.add('hidden');
                        deleteButton.classList.add('hidden');
                        editor.clear();
                        document.getElementById('contractName').value = '';
                        document.getElementById('contract_id').value = '';
                    }
                });
            }

            if (deleteButton) {
                deleteButton.addEventListener('click', async function(event) {
                    event.preventDefault();
                    const contractId = document.getElementById('contract_id').value;
                    
                    if (!contractId) return;

                    if (!confirm('Êtes-vous sûr de vouloir supprimer ce modèle de contrat ?')) return;

                    try {
                        const response = await fetch(`/contracts/${contractId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (response.ok) {
                            // Show delete animation
                            const overlay = document.getElementById('delete-overlay');
                            overlay.classList.remove('hidden');
                            overlay.classList.add('delete-animation');

                            // Redirect after animation
                            setTimeout(() => {
                                window.location.href = '/contract';
                            }, 800);
                        } else {
                            console.error('Deletion failed');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                });
            }
        });

        async function prepareSubmit(event) {
            event.preventDefault();
            const savedData = await editor.save();
            document.getElementById('editorContent').value = JSON.stringify(savedData);
            
            const form = event.target.closest('form');
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    // Show success animation
                    const overlay = document.getElementById('success-overlay');
                    overlay.classList.remove('hidden');
                    overlay.classList.add('success-animation');

                    // Redirect after animation
                    setTimeout(() => {
                        window.location.href = '/contract';
                    }, 1000);
                } else {
                    console.error('Submission failed');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    </script>
</body>
</html>
