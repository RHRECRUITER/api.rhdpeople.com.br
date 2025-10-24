<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ATS DP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-primary { background-color: #009E9A; }
        .text-primary { color: #009E9A; }
        .hover\:bg-primary-dark:hover { background-color: #007B78; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo e Header -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-primary rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">ATS DP</h2>
                <p class="mt-2 text-sm text-gray-600">Sistema de Gestão de Departamento Pessoal</p>
            </div>

            <!-- Formulário de Login -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-1"></i> E-mail
                        </label>
                        <input id="email" name="email" type="email" required
                               value="{{ old('email') }}"
                               class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="seu@email.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1"></i> Senha
                        </label>
                        <input id="password" name="password" type="password" required
                               class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="********">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
                                   class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-900">Lembrar de mim</label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="text-primary hover:underline">Esqueceu a senha?</a>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 bg-primary text-white py-3 px-4 rounded-lg hover:bg-primary-dark transition">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Entrar</span>
                    </button>
                </form>

                <!-- Contas de demonstração -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-center text-xs text-gray-600 mb-2">
                        <i class="fas fa-info-circle"></i> Contas de demonstração:
                    </p>
                    <div class="space-y-2 text-xs">
                        <div class="bg-blue-50 p-2 rounded">
                            <strong>Super Admin:</strong> admin@atsdp.com / admin@2025
                        </div>
                        <div class="bg-green-50 p-2 rounded">
                            <strong>Admin Empresa:</strong> admin@empresademo.com / empresa@2025
                        </div>
                        <div class="bg-yellow-50 p-2 rounded">
                            <strong>Usuário DP:</strong> joao@empresademo.com / dp@2025
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
