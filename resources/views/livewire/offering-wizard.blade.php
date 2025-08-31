<div>
    <div class="mt-4">
        <label for="hasChairs" class="block text-sm font-medium text-gray-700">يحتوي على مقاعد</label>
        <select wire:model="hasChairs" id="hasChairs" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="0">لا</option>
            <option value="1">نعم</option>
        </select>
    </div>

    @if ($hasChairs)
        <div class="mt-4">
            <label for="chairsCount" class="block text-sm font-medium text-gray-700">عدد المقاعد</label>
            <input type="number" wire:model="chairsCount" id="chairsCount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
    @endif
</div>
