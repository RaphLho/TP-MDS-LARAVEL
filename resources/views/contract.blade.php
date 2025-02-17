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
    </style>
</head>

<body class="bg-blue-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="neo-container rounded-2xl shadow-2xl p-8">
            <h1 class="text-4xl font-bold mb-8 text-center">
                <span class="text-blue-600">
                    Système de Gestion des Contrats
                </span>
            </h1>

            <form action="{{ route('contracts.store') }}" method="POST">
                @csrf
                <div class="mb-6 relative">
                    <label for="contractName" class="block text-sm font-medium mb-2 text-blue-600">Nom du modèle</label>
                    <input type="text" id="contractName" name="name"
                        class="neo-input w-full rounded-lg p-3 focus:outline-none"
                        placeholder="Entrez le nom du modèle..."
                        value="{{ $existingContract ? $existingContract->name : '' }}">
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
            const existingContent = @json($existingContract ? $existingContract->content : null);
            
            editor = new EditorJS({
                holder: 'editorjs',
                tools: editorTools,
                placeholder: 'Commencez à rédiger votre contrat ici...',
                data: existingContent || {
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
        });

        async function prepareSubmit(event) {
            event.preventDefault();
            const savedData = await editor.save();
            document.getElementById('editorContent').value = JSON.stringify(savedData);
            event.target.closest('form').submit();
        }
    </script>
</body>
</html>
