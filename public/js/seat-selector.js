/**
 * Interactive Seat Selector Component
 * Handles seat selection, layout visualization, and booking integration
 */
class SeatSelector {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.options = {
            width: 800,
            height: 600,
            seatSize: 30,
            scale: 1,
            maxSelection: null,
            allowMultipleSelection: true,
            onSeatSelect: null,
            onSeatDeselect: null,
            onSelectionChange: null,
            showPrices: true,
            showSeatNumbers: true,
            language: 'ar',
            ...options
        };
        
        this.layout = null;
        this.selectedSeats = new Set();
        this.svg = null;
        this.seatElements = new Map();
        
        this.init();
    }

    init() {
        this.createSVG();
        this.setupEventListeners();
    }

    createSVG() {
        this.svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        this.svg.setAttribute('width', this.options.width);
        this.svg.setAttribute('height', this.options.height);
        this.svg.setAttribute('viewBox', `0 0 ${this.options.width} ${this.options.height}`);
        this.svg.classList.add('seat-selector-svg');
        
        // Add CSS styles
        const style = document.createElement('style');
        style.textContent = `
            .seat-selector-svg {
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                background: #f9fafb;
                user-select: none;
            }
            .seat {
                cursor: pointer;
                transition: all 0.2s ease;
                stroke-width: 2;
            }
            .seat:hover {
                stroke-width: 3;
                filter: brightness(1.1);
            }
            .seat.selected {
                stroke: #3b82f6;
                stroke-width: 3;
            }
            .seat.available {
                fill: #10b981;
                stroke: #059669;
            }
            .seat.reserved {
                fill: #f59e0b;
                stroke: #d97706;
            }
            .seat.occupied {
                fill: #ef4444;
                stroke: #dc2626;
                cursor: not-allowed;
            }
            .seat.maintenance {
                fill: #6b7280;
                stroke: #4b5563;
                cursor: not-allowed;
            }
            .seat-label {
                font-size: 10px;
                font-weight: bold;
                text-anchor: middle;
                dominant-baseline: middle;
                pointer-events: none;
                fill: white;
            }
            .seat-price {
                font-size: 8px;
                text-anchor: middle;
                dominant-baseline: middle;
                pointer-events: none;
                fill: white;
            }
            .stage {
                fill: #374151;
                stroke: #1f2937;
                stroke-width: 2;
            }
            .stage-label {
                font-size: 14px;
                font-weight: bold;
                text-anchor: middle;
                dominant-baseline: middle;
                fill: white;
            }
        `;
        document.head.appendChild(style);
        
        this.container.appendChild(this.svg);
    }

    setupEventListeners() {
        this.container.addEventListener('click', (e) => {
            if (e.target.classList.contains('seat') && !e.target.classList.contains('occupied') && !e.target.classList.contains('maintenance')) {
                this.handleSeatClick(e.target);
            }
        });
    }

    handleSeatClick(seatElement) {
        const seatId = parseInt(seatElement.dataset.seatId);
        const seat = this.layout.seats.find(s => s.id === seatId);
        
        if (!seat) return;

        if (this.selectedSeats.has(seatId)) {
            this.deselectSeat(seatId);
        } else {
            this.selectSeat(seatId);
        }
    }

    selectSeat(seatId) {
        const seat = this.layout.seats.find(s => s.id === seatId);
        if (!seat || seat.status !== 'available') return;

        // Check max selection limit
        if (this.options.maxSelection && this.selectedSeats.size >= this.options.maxSelection) {
            this.showMessage('تم الوصول إلى الحد الأقصى من المقاعد المحددة', 'warning');
            return;
        }

        this.selectedSeats.add(seatId);
        const seatElement = this.seatElements.get(seatId);
        if (seatElement) {
            seatElement.classList.add('selected');
        }

        if (this.options.onSeatSelect) {
            this.options.onSeatSelect(seat);
        }

        this.updateSelectionInfo();
    }

    deselectSeat(seatId) {
        const seat = this.layout.seats.find(s => s.id === seatId);
        if (!seat) return;

        this.selectedSeats.delete(seatId);
        const seatElement = this.seatElements.get(seatId);
        if (seatElement) {
            seatElement.classList.remove('selected');
        }

        if (this.options.onSeatDeselect) {
            this.options.onSeatDeselect(seat);
        }

        this.updateSelectionInfo();
    }

    updateSelectionInfo() {
        const selectedSeats = Array.from(this.selectedSeats).map(id => 
            this.layout.seats.find(s => s.id === id)
        );
        
        const totalPrice = selectedSeats.reduce((sum, seat) => sum + parseFloat(seat.price), 0);

        if (this.options.onSelectionChange) {
            this.options.onSelectionChange({
                seats: selectedSeats,
                count: selectedSeats.length,
                totalPrice: totalPrice
            });
        }

        // Update selection display
        this.updateSelectionDisplay(selectedSeats, totalPrice);
    }

    updateSelectionDisplay(seats, totalPrice) {
        const selectionInfo = document.getElementById('seat-selection-info');
        if (selectionInfo) {
            const seatNumbers = seats.map(s => s.number).join(', ');
            selectionInfo.innerHTML = `
                <div class="flex justify-between items-center">
                    <div>
                        <span class="font-medium">المقاعد المختارة:</span>
                        <span class="text-blue-600">${seatNumbers || 'لم يتم اختيار مقاعد'}</span>
                    </div>
                    <div class="font-bold text-lg">
                        ${totalPrice.toFixed(2)} ريال
                    </div>
                </div>
            `;
        }
    }

    loadLayout(layoutData) {
        this.layout = layoutData;
        this.renderLayout();
    }

    renderLayout() {
        if (!this.layout) return;

        // Clear existing content
        this.svg.innerHTML = '';
        this.seatElements.clear();

        // Calculate scale to fit layout
        const scale = Math.min(
            this.options.width / this.layout.width,
            this.options.height / this.layout.height
        ) * 0.8;

        // Render stage/screen if it's a theater layout
        if (this.layout.type === 'theater') {
            this.renderStage(scale);
        }

        // Render seats
        this.layout.seats.forEach(seat => {
            this.renderSeat(seat, scale);
        });

        // Render legend
        this.renderLegend();
    }

    renderStage(scale) {
        const stageWidth = this.layout.width * scale * 0.6;
        const stageHeight = 40;
        const stageX = (this.options.width - stageWidth) / 2;
        const stageY = 20;

        const stage = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        stage.setAttribute('x', stageX);
        stage.setAttribute('y', stageY);
        stage.setAttribute('width', stageWidth);
        stage.setAttribute('height', stageHeight);
        stage.setAttribute('rx', 5);
        stage.classList.add('stage');

        const stageLabel = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        stageLabel.setAttribute('x', stageX + stageWidth / 2);
        stageLabel.setAttribute('y', stageY + stageHeight / 2);
        stageLabel.classList.add('stage-label');
        stageLabel.textContent = 'المسرح';

        this.svg.appendChild(stage);
        this.svg.appendChild(stageLabel);
    }

    renderSeat(seat, scale) {
        const x = seat.x * scale + 50; // Add padding
        const y = seat.y * scale + 80; // Add padding for stage
        const seatWidth = seat.width * scale || this.options.seatSize;
        const seatHeight = seat.height * scale || this.options.seatSize;

        let seatElement;

        if (seat.type === 'table') {
            // Render table as rectangle
            seatElement = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
            seatElement.setAttribute('x', x);
            seatElement.setAttribute('y', y);
            seatElement.setAttribute('width', seatWidth);
            seatElement.setAttribute('height', seatHeight);
            seatElement.setAttribute('rx', 5);
        } else {
            // Render individual seat as circle
            seatElement = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            seatElement.setAttribute('cx', x + seatWidth / 2);
            seatElement.setAttribute('cy', y + seatHeight / 2);
            seatElement.setAttribute('r', Math.min(seatWidth, seatHeight) / 2);
        }

        seatElement.classList.add('seat', seat.status);
        seatElement.dataset.seatId = seat.id;
        seatElement.dataset.seatNumber = seat.number;
        seatElement.dataset.seatPrice = seat.price;

        // Add accessibility indicator
        if (seat.accessible) {
            seatElement.classList.add('accessible');
        }

        // Add seat number label
        if (this.options.showSeatNumbers) {
            const label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            label.setAttribute('x', x + seatWidth / 2);
            label.setAttribute('y', y + seatHeight / 2 - 5);
            label.classList.add('seat-label');
            label.textContent = seat.number;
            this.svg.appendChild(label);

            // Add price label if enabled
            if (this.options.showPrices && seat.status === 'available') {
                const priceLabel = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                priceLabel.setAttribute('x', x + seatWidth / 2);
                priceLabel.setAttribute('y', y + seatHeight / 2 + 8);
                priceLabel.classList.add('seat-price');
                priceLabel.textContent = `${seat.price}ر`;
                this.svg.appendChild(priceLabel);
            }
        }

        this.svg.appendChild(seatElement);
        this.seatElements.set(seat.id, seatElement);
    }

    renderLegend() {
        const legend = document.createElement('div');
        legend.className = 'seat-legend mt-4 flex flex-wrap gap-4 text-sm';
        legend.innerHTML = `
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded" style="background-color: #10b981;"></div>
                <span>متاح</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded" style="background-color: #f59e0b;"></div>
                <span>محجوز</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded" style="background-color: #ef4444;"></div>
                <span>مشغول</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded" style="background-color: #6b7280;"></div>
                <span>صيانة</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded border-2 border-blue-500" style="background-color: #10b981;"></div>
                <span>مُختار</span>
            </div>
        `;
        
        this.container.appendChild(legend);
    }

    showMessage(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'warning' ? 'bg-yellow-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    clearSelection() {
        this.selectedSeats.forEach(seatId => {
            const seatElement = this.seatElements.get(seatId);
            if (seatElement) {
                seatElement.classList.remove('selected');
            }
        });
        
        this.selectedSeats.clear();
        this.updateSelectionInfo();
    }

    getSelectedSeats() {
        return Array.from(this.selectedSeats).map(id => 
            this.layout.seats.find(s => s.id === id)
        );
    }

    getTotalPrice() {
        return this.getSelectedSeats().reduce((sum, seat) => sum + parseFloat(seat.price), 0);
    }

    // API methods
    async fetchLayout(serviceId) {
        try {
            const response = await fetch(`/api/services/${serviceId}/layout`);
            const data = await response.json();
            this.loadLayout(data);
        } catch (error) {
            console.error('Failed to fetch layout:', error);
            this.showMessage('فشل في تحميل خريطة المقاعد', 'error');
        }
    }

    async reserveSeats(bookingData) {
        try {
            const selectedSeats = this.getSelectedSeats();
            const response = await fetch('/api/seats/reserve', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    ...bookingData,
                    seat_ids: selectedSeats.map(s => s.id),
                    total_amount: this.getTotalPrice()
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showMessage('تم حجز المقاعد بنجاح', 'info');
                return result;
            } else {
                this.showMessage(result.message || 'فشل في حجز المقاعد', 'error');
                return null;
            }
        } catch (error) {
            console.error('Failed to reserve seats:', error);
            this.showMessage('فشل في حجز المقاعد', 'error');
            return null;
        }
    }
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SeatSelector;
}

// Make available globally
window.SeatSelector = SeatSelector;