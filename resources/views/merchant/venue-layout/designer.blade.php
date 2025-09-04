@extends('layouts.merchant')

@section('title', 'مصمم المقاعد - ' . $venueLayout->name)

@section('content')
<div class="min-h-screen bg-gray-50 py-8" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">مصمم المقاعد</h1>
                    <p class="text-gray-600 mt-1">{{ $venueLayout->name }}</p>
                </div>
                <div class="flex space-x-3 space-x-reverse">
                    <a href="{{ route('merchant.venue-layout.preview', $venueLayout) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        معاينة
                    </a>
                    <button id="save-layout" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        حفظ التخطيط
                    </button>
                </div>
            </div>
        </div>

        <!-- Designer Interface -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Controls Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border p-6 sticky top-6">
                    <h3 class="text-lg font-semibold mb-6">أدوات التصميم</h3>
                    
                    <!-- Grid Settings -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">إعدادات الشبكة</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">عدد الصفوف</label>
                                <input type="number" id="rows-input" value="{{ $venueLayout->rows }}" 
                                       class="w-full rounded-md border-gray-300 text-sm" min="1" max="50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">عدد الأعمدة</label>
                                <input type="number" id="columns-input" value="{{ $venueLayout->columns }}" 
                                       class="w-full rounded-md border-gray-300 text-sm" min="1" max="50">
                            </div>
                        </div>
                    </div>

                    <!-- Tools -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">الأدوات</h4>
                        <div class="space-y-2">
                            <button type="button" data-tool="seat" 
                                    class="tool-btn w-full text-right px-3 py-2 rounded-md bg-blue-100 text-blue-700 border border-blue-300 hover:bg-blue-200 transition-colors">
                                🪑 إضافة مقعد
                            </button>
                            <button type="button" data-tool="table" 
                                    class="tool-btn w-full text-right px-3 py-2 rounded-md bg-gray-100 text-gray-700 border border-gray-300 hover:bg-gray-200 transition-colors">
                                🪑 إضافة طاولة
                            </button>
                            <button type="button" data-tool="vip" 
                                    class="tool-btn w-full text-right px-3 py-2 rounded-md bg-gray-100 text-gray-700 border border-gray-300 hover:bg-gray-200 transition-colors">
                                ⭐ مقعد VIP
                            </button>
                            <button type="button" data-tool="remove" 
                                    class="tool-btn w-full text-right px-3 py-2 rounded-md bg-gray-100 text-gray-700 border border-gray-300 hover:bg-gray-200 transition-colors">
                                🗑️ حذف
                            </button>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">إجراءات</h4>
                        <div class="space-y-2">
                            <button type="button" id="clear-all" 
                                    class="w-full text-right px-3 py-2 rounded-md bg-red-100 text-red-700 border border-red-300 hover:bg-red-200 transition-colors">
                                🧹 مسح الكل
                            </button>
                            <button type="button" id="auto-fill" 
                                    class="w-full text-right px-3 py-2 rounded-md bg-green-100 text-green-700 border border-green-300 hover:bg-green-200 transition-colors">
                                ⚡ ملء تلقائي
                            </button>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-3">الإحصائيات</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">إجمالي المقاعد:</span>
                                <span id="total-seats" class="font-medium">0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">مقاعد عادية:</span>
                                <span id="regular-seats" class="font-medium">0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">مقاعد VIP:</span>
                                <span id="vip-seats" class="font-medium">0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">طاولات:</span>
                                <span id="tables-count" class="font-medium">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Designer Canvas -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="mb-6">
                        <div class="text-center mb-4">
                            <div class="inline-block px-8 py-3 bg-gray-800 text-white rounded-lg font-medium">
                                🎭 المسرح / المنصة
                            </div>
                        </div>
                    </div>

                    <!-- Seat Grid Container -->
                    <div class="designer-canvas bg-gray-50 rounded-lg p-8 min-h-96 overflow-auto">
                        <div id="seat-grid" class="mx-auto" style="display: grid; gap: 8px;">
                            <!-- Grid will be generated by JavaScript -->
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="mt-6 flex justify-center">
                        <div class="flex items-center space-x-6 space-x-reverse text-sm">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <div class="w-6 h-6 bg-gray-200 border-2 border-gray-400 rounded"></div>
                                <span>فارغ</span>
                            </div>
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <div class="w-6 h-6 bg-blue-200 border-2 border-blue-400 rounded"></div>
                                <span>مقعد عادي</span>
                            </div>
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <div class="w-6 h-6 bg-yellow-200 border-2 border-yellow-400 rounded"></div>
                                <span>مقعد VIP</span>
                            </div>
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <div class="w-6 h-6 bg-green-200 border-2 border-green-400 rounded-sm"></div>
                                <span>طاولة</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p>جاري حفظ التخطيط...</p>
    </div>
</div>

@push('scripts')
<script>
class VenueDesigner {
    constructor() {
        this.rows = {{ $venueLayout->rows }};
        this.columns = {{ $venueLayout->columns }};
        this.currentTool = 'seat';
        this.grid = [];
        this.venueLayoutId = {{ $venueLayout->id }};
        
        this.init();
        this.loadExistingLayout();
    }

    init() {
        this.bindEvents();
        this.updateGrid();
    }

    bindEvents() {
        // Tool selection
        document.querySelectorAll('.tool-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.selectTool(e.target.dataset.tool);
            });
        });

        // Grid size changes
        document.getElementById('rows-input').addEventListener('change', (e) => {
            this.rows = parseInt(e.target.value);
            this.updateGrid();
        });

        document.getElementById('columns-input').addEventListener('change', (e) => {
            this.columns = parseInt(e.target.value);
            this.updateGrid();
        });

        // Actions
        document.getElementById('clear-all').addEventListener('click', () => {
            if (confirm('هل أنت متأكد من مسح جميع المقاعد؟')) {
                this.clearAll();
            }
        });

        document.getElementById('auto-fill').addEventListener('click', () => {
            this.autoFill();
        });

        document.getElementById('save-layout').addEventListener('click', () => {
            this.saveLayout();
        });
    }

    selectTool(tool) {
        this.currentTool = tool;
        
        // Update UI
        document.querySelectorAll('.tool-btn').forEach(btn => {
            btn.classList.remove('bg-blue-100', 'text-blue-700', 'border-blue-300');
            btn.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-300');
        });

        const selectedBtn = document.querySelector(`[data-tool="${tool}"]`);
        if (selectedBtn) {
            selectedBtn.classList.remove('bg-gray-100', 'text-gray-700', 'border-gray-300');
            selectedBtn.classList.add('bg-blue-100', 'text-blue-700', 'border-blue-300');
        }
    }

    updateGrid() {
        this.grid = [];
        for (let i = 0; i < this.rows * this.columns; i++) {
            this.grid.push({
                type: 'empty',
                number: null,
                row: Math.floor(i / this.columns) + 1,
                column: (i % this.columns) + 1
            });
        }
        this.renderGrid();
        this.updateStatistics();
    }

    renderGrid() {
        const gridContainer = document.getElementById('seat-grid');
        gridContainer.style.gridTemplateColumns = `repeat(${this.columns}, 1fr)`;
        gridContainer.innerHTML = '';

        this.grid.forEach((cell, index) => {
            const cellElement = document.createElement('div');
            cellElement.className = `seat-cell w-8 h-8 border-2 cursor-pointer transition-all duration-200 flex items-center justify-center text-xs font-medium`;
            cellElement.style.minWidth = '32px';
            cellElement.style.minHeight = '32px';
            
            this.updateCellAppearance(cellElement, cell);
            
            cellElement.addEventListener('click', () => {
                this.handleCellClick(index);
            });

            gridContainer.appendChild(cellElement);
        });
    }

    updateCellAppearance(element, cell) {
        // Reset classes
        element.className = 'seat-cell w-8 h-8 border-2 cursor-pointer transition-all duration-200 flex items-center justify-center text-xs font-medium';
        
        switch (cell.type) {
            case 'seat':
                element.classList.add('bg-blue-200', 'border-blue-400', 'hover:bg-blue-300', 'rounded');
                element.textContent = cell.number || '';
                break;
            case 'vip':
                element.classList.add('bg-yellow-200', 'border-yellow-400', 'hover:bg-yellow-300', 'rounded');
                element.textContent = cell.number || '';
                break;
            case 'table':
                element.classList.add('bg-green-200', 'border-green-400', 'hover:bg-green-300', 'rounded-sm');
                element.textContent = 'T' + (cell.number || '');
                break;
            default:
                element.classList.add('bg-gray-100', 'border-gray-300', 'hover:bg-gray-200', 'rounded');
                element.textContent = '';
        }
    }

    handleCellClick(index) {
        const cell = this.grid[index];
        
        if (this.currentTool === 'remove') {
            cell.type = 'empty';
            cell.number = null;
        } else if (this.currentTool === 'seat') {
            if (cell.type === 'empty') {
                cell.type = 'seat';
                cell.number = this.getNextNumber('seat');
            }
        } else if (this.currentTool === 'vip') {
            if (cell.type === 'empty') {
                cell.type = 'vip';
                cell.number = this.getNextNumber('vip');
            }
        } else if (this.currentTool === 'table') {
            if (cell.type === 'empty') {
                cell.type = 'table';
                cell.number = this.getNextNumber('table');
            }
        }
        
        this.renderGrid();
        this.updateStatistics();
    }

    getNextNumber(type) {
        const numbers = this.grid
            .filter(cell => cell.type === type)
            .map(cell => parseInt(cell.number))
            .filter(num => !isNaN(num));
        
        return numbers.length > 0 ? Math.max(...numbers) + 1 : 1;
    }

    clearAll() {
        this.grid.forEach(cell => {
            cell.type = 'empty';
            cell.number = null;
        });
        this.renderGrid();
        this.updateStatistics();
    }

    autoFill() {
        this.grid.forEach(cell => {
            if (cell.type === 'empty') {
                cell.type = 'seat';
                cell.number = this.getNextNumber('seat');
            }
        });
        this.renderGrid();
        this.updateStatistics();
    }

    updateStatistics() {
        const stats = {
            total: this.grid.filter(cell => cell.type !== 'empty').length,
            regular: this.grid.filter(cell => cell.type === 'seat').length,
            vip: this.grid.filter(cell => cell.type === 'vip').length,
            tables: this.grid.filter(cell => cell.type === 'table').length
        };

        document.getElementById('total-seats').textContent = stats.total;
        document.getElementById('regular-seats').textContent = stats.regular;
        document.getElementById('vip-seats').textContent = stats.vip;
        document.getElementById('tables-count').textContent = stats.tables;
    }

    async loadExistingLayout() {
        try {
            const response = await fetch(`/merchant/venue-layout/${this.venueLayoutId}/data`);
            const data = await response.json();
            
            if (data.success && data.data.seats.length > 0) {
                // Load existing seats into grid
                data.data.seats.forEach(seat => {
                    const index = (seat.row - 1) * this.columns + (seat.column - 1);
                    if (index < this.grid.length) {
                        this.grid[index] = {
                            type: seat.category === 'vip' ? 'vip' : 'seat',
                            number: seat.number,
                            row: seat.row,
                            column: seat.column
                        };
                    }
                });
                
                this.renderGrid();
                this.updateStatistics();
            }
        } catch (error) {
            console.error('Error loading layout:', error);
        }
    }

    async saveLayout() {
        const overlay = document.getElementById('loading-overlay');
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');

        try {
            const layoutData = {
                rows: this.rows,
                columns: this.columns,
                seats: this.grid.filter(cell => cell.type === 'seat' || cell.type === 'vip'),
                tables: this.grid.filter(cell => cell.type === 'table')
            };

            const response = await fetch(`/merchant/venue-layout/${this.venueLayoutId}/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    layout_data: JSON.stringify(layoutData),
                    rows: this.rows,
                    columns: this.columns
                })
            });

            const result = await response.json();

            if (result.success) {
                alert('تم حفظ التخطيط بنجاح!');
            } else {
                alert('خطأ في حفظ التخطيط: ' + result.message);
            }

        } catch (error) {
            alert('خطأ في الاتصال: ' + error.message);
        } finally {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', () => {
    new VenueDesigner();
});
</script>
@endpush
@endsection
