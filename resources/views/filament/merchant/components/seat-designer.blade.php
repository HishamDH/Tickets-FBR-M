<div x-data="seatDesigner()" x-init="init()" class="w-full">
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-4">مصمم المقاعد التفاعلي</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">عدد الصفوف</label>
                <input type="number" x-model.number="rows" @change="updateGrid()" 
                       class="w-full rounded-md border-gray-300" min="1" max="20">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">عدد الأعمدة</label>
                <input type="number" x-model.number="columns" @change="updateGrid()" 
                       class="w-full rounded-md border-gray-300" min="1" max="20">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">إجمالي المقاعد</label>
                <input type="text" :value="totalSeats" disabled 
                       class="w-full rounded-md border-gray-300 bg-gray-100">
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mb-4">
            <button type="button" @click="setTool('seat')" 
                    :class="tool === 'seat' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                    class="px-3 py-2 rounded-md">
                إضافة مقعد
            </button>
            <button type="button" @click="setTool('table')" 
                    :class="tool === 'table' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                    class="px-3 py-2 rounded-md">
                إضافة طاولة
            </button>
            <button type="button" @click="setTool('remove')" 
                    :class="tool === 'remove' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700'"
                    class="px-3 py-2 rounded-md">
                حذف
            </button>
            <button type="button" @click="clearAll()" 
                    class="px-3 py-2 bg-orange-600 text-white rounded-md">
                مسح الكل
            </button>
        </div>
    </div>

    <!-- Seat Grid -->
    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 bg-white">
        <div class="mb-4 text-center">
            <div class="inline-block px-6 py-2 bg-gray-800 text-white rounded-md">
                المسرح / المنصة
            </div>
        </div>
        
        <div class="seat-grid mx-auto" 
             :style="`display: grid; grid-template-columns: repeat(${columns}, 1fr); gap: 8px; max-width: ${columns * 40}px;`">
            <template x-for="(cell, index) in grid" :key="index">
                <div @click="handleCellClick(index)" 
                     :class="getCellClass(cell)"
                     class="w-8 h-8 border-2 cursor-pointer transition-all duration-200 flex items-center justify-center text-xs">
                    <span x-text="cell.number || ''"></span>
                </div>
            </template>
        </div>
        
        <div class="mt-4 flex justify-center">
            <div class="flex items-center gap-4 text-sm">
                <div class="flex items-center gap-1">
                    <div class="w-4 h-4 bg-gray-200 border-2 border-gray-400 rounded"></div>
                    <span>متاح</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-4 h-4 bg-blue-200 border-2 border-blue-400 rounded"></div>
                    <span>مقعد</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-4 h-4 bg-green-200 border-2 border-green-400 rounded-sm"></div>
                    <span>طاولة</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden input to store layout data -->
    <input type="hidden" name="layout_data" :value="JSON.stringify(layoutData)">
</div>

<script>
function seatDesigner() {
    return {
        rows: 10,
        columns: 15,
        grid: [],
        tool: 'seat',
        layoutData: {
            seats: [],
            tables: []
        },

        init() {
            this.updateGrid();
        },

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
            this.updateLayoutData();
        },

        setTool(toolType) {
            this.tool = toolType;
        },

        handleCellClick(index) {
            const cell = this.grid[index];
            
            if (this.tool === 'remove') {
                cell.type = 'empty';
                cell.number = null;
            } else if (this.tool === 'seat') {
                if (cell.type === 'empty') {
                    cell.type = 'seat';
                    cell.number = this.getNextSeatNumber();
                }
            } else if (this.tool === 'table') {
                if (cell.type === 'empty') {
                    cell.type = 'table';
                    cell.number = this.getNextTableNumber();
                }
            }
            
            this.updateLayoutData();
        },

        getCellClass(cell) {
            const baseClass = 'border rounded';
            
            switch (cell.type) {
                case 'seat':
                    return `${baseClass} bg-blue-200 border-blue-400 hover:bg-blue-300`;
                case 'table':
                    return `${baseClass} bg-green-200 border-green-400 hover:bg-green-300`;
                default:
                    return `${baseClass} bg-gray-100 border-gray-300 hover:bg-gray-200`;
            }
        },

        getNextSeatNumber() {
            const seatNumbers = this.grid
                .filter(cell => cell.type === 'seat')
                .map(cell => parseInt(cell.number))
                .filter(num => !isNaN(num));
            
            return seatNumbers.length > 0 ? Math.max(...seatNumbers) + 1 : 1;
        },

        getNextTableNumber() {
            const tableNumbers = this.grid
                .filter(cell => cell.type === 'table')
                .map(cell => parseInt(cell.number))
                .filter(num => !isNaN(num));
            
            return tableNumbers.length > 0 ? Math.max(...tableNumbers) + 1 : 1;
        },

        clearAll() {
            this.grid.forEach(cell => {
                cell.type = 'empty';
                cell.number = null;
            });
            this.updateLayoutData();
        },

        updateLayoutData() {
            this.layoutData = {
                rows: this.rows,
                columns: this.columns,
                seats: this.grid.filter(cell => cell.type === 'seat'),
                tables: this.grid.filter(cell => cell.type === 'table')
            };
        },

        get totalSeats() {
            return this.grid.filter(cell => cell.type === 'seat').length;
        }
    }
}
</script>
