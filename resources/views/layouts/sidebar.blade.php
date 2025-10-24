<aside id="sidebar" class="fixed left-0 top-16 h-screen w-64 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-40">
    <nav class="p-4 space-y-1">
        <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-teal-50 text-teal-700 border-l-4 border-teal-600' : '' }}">
            <i class="fas fa-chart-line w-5"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="{{ route('colaboradores.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 {{ request()->routeIs('colaboradores.*') ? 'bg-teal-50 text-teal-700 border-l-4 border-teal-600' : '' }}">
            <i class="fas fa-users w-5"></i>
            <span>Colaboradores</span>
        </a>
        
        <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100">
            <i class="fas fa-clock w-5"></i>
            <span>Ponto</span>
        </a>
        
        <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100">
            <i class="fas fa-umbrella-beach w-5"></i>
            <span>Férias & Licenças</span>
        </a>
        
        <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100">
            <i class="fas fa-money-bill-wave w-5"></i>
            <span>Folha de Pagamento</span>
        </a>
        
        <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100">
            <i class="fas fa-gift w-5"></i>
            <span>Benefícios</span>
        </a>
        
        <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100">
            <i class="fas fa-folder w-5"></i>
            <span>Documentos</span>
        </a>
        
        <a href="#" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100">
            <i class="fas fa-chart-bar w-5"></i>
            <span>Relatórios</span>
        </a>
    </nav>
</aside>

<!-- Overlay para mobile -->
<div id="sidebar-overlay" onclick="closeSidebar()" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
}
</script>
