<nav class="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-50">
    <div class="px-4 py-3 lg:px-6">
        <div class="flex items-center justify-between">
            <!-- Logo e Menu Toggle -->
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="lg:hidden">
                    <i class="fas fa-bars text-xl text-gray-600"></i>
                </button>
                <div class="flex items-center gap-2">
                    <div class="h-10 w-10 bg-primary rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-800">ATS DP</span>
                </div>
            </div>
            
            <!-- User Menu -->
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <div class="font-medium text-gray-800">{{ auth()->user()->name }}</div>
                    <div class="text-sm text-gray-500">
                        @if(auth()->user()->isSuperAdmin())
                            Super Admin
                        @elseif(auth()->user()->isAdminEmpresa())
                            Admin - {{ auth()->user()->empresa->nome }}
                        @else
                            Usuário DP - {{ auth()->user()->empresa->nome }}
                        @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-700">
                        <i class="fas fa-sign-out-alt text-xl"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
