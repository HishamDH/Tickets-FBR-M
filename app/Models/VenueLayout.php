<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $service_id
 * @property string $name
 * @property string|null $description
 * @property string $layout_type
 * @property int $total_capacity
 * @property array $layout_config
 * @property float $width
 * @property float $height
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Service $service
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Seat> $seats
 * @property-read int|null $seats_count
 */
class VenueLayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'description',
        'layout_type',
        'total_capacity',
        'layout_config',
        'width',
        'height',
        'is_active',
    ];

    protected $casts = [
        'layout_config' => 'array',
        'is_active' => 'boolean',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
    ];

    /**
     * Get the service that owns the venue layout.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get all seats in this venue layout.
     */
    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * Get available seats for a specific date/time.
     */
    public function getAvailableSeats(\DateTime $datetime = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->seats()->where('status', 'available');

        if ($datetime) {
            // Exclude seats that are reserved for the given datetime
            $query->whereDoesntHave('reservations', function ($q) use ($datetime) {
                $q->where('status', '!=', 'cancelled')
                  ->where(function ($timeQuery) use ($datetime) {
                      $timeQuery->whereNull('expires_at')
                               ->orWhere('expires_at', '>', $datetime);
                  });
            });
        }

        return $query->get();
    }

    /**
     * Get seat occupancy statistics.
     */
    public function getOccupancyStats(): array
    {
        $totalSeats = $this->seats()->count();
        $availableSeats = $this->seats()->where('status', 'available')->count();
        $reservedSeats = $this->seats()->where('status', 'reserved')->count();
        $occupiedSeats = $this->seats()->where('status', 'occupied')->count();
        $maintenanceSeats = $this->seats()->where('status', 'maintenance')->count();

        return [
            'total' => $totalSeats,
            'available' => $availableSeats,
            'reserved' => $reservedSeats,
            'occupied' => $occupiedSeats,
            'maintenance' => $maintenanceSeats,
            'occupancy_rate' => $totalSeats > 0 ? (($reservedSeats + $occupiedSeats) / $totalSeats * 100) : 0,
        ];
    }

    /**
     * Generate seat layout based on template.
     */
    public function generateSeatsFromTemplate(): void
    {
        switch ($this->layout_type) {
            case 'theater':
                $this->generateTheaterSeating();
                break;
            case 'banquet':
                $this->generateBanquetSeating();
                break;
            case 'classroom':
                $this->generateClassroomSeating();
                break;
            default:
                // Custom layouts are created manually
                break;
        }
    }

    /**
     * Generate theater-style seating.
     */
    private function generateTheaterSeating(): void
    {
        $config = $this->layout_config;
        $rows = $config['rows'] ?? 10;
        $seatsPerRow = $config['seats_per_row'] ?? 20;
        $seatPrice = $config['seat_price'] ?? 100;

        $seatNumber = 1;
        for ($row = 1; $row <= $rows; $row++) {
            for ($seat = 1; $seat <= $seatsPerRow; $seat++) {
                Seat::create([
                    'venue_layout_id' => $this->id,
                    'seat_number' => chr(64 + $row) . $seat, // A1, A2, B1, B2, etc.
                    'seat_type' => 'individual',
                    'capacity' => 1,
                    'price' => $seatPrice,
                    'x_position' => $seat * 2.0, // 2 units apart
                    'y_position' => $row * 2.5,  // 2.5 units apart for rows
                    'section' => $row <= 3 ? 'VIP' : 'General',
                ]);
                $seatNumber++;
            }
        }
    }

    /**
     * Generate banquet-style table seating.
     */
    private function generateBanquetSeating(): void
    {
        $config = $this->layout_config;
        $tables = $config['tables'] ?? 10;
        $seatsPerTable = $config['seats_per_table'] ?? 8;
        $tablePrice = $config['table_price'] ?? 800;

        for ($table = 1; $table <= $tables; $table++) {
            Seat::create([
                'venue_layout_id' => $this->id,
                'seat_number' => 'Table ' . $table,
                'seat_type' => 'table',
                'capacity' => $seatsPerTable,
                'price' => $tablePrice,
                'x_position' => ($table % 5) * 6.0, // 5 tables per row
                'y_position' => intval(($table - 1) / 5) * 6.0,
                'width' => 2.0,
                'height' => 2.0,
                'section' => 'Banquet',
            ]);
        }
    }

    /**
     * Generate classroom-style seating.
     */
    private function generateClassroomSeating(): void
    {
        $config = $this->layout_config;
        $rows = $config['rows'] ?? 8;
        $tablesPerRow = $config['tables_per_row'] ?? 6;
        $tablePrice = $config['table_price'] ?? 200;

        $tableNumber = 1;
        for ($row = 1; $row <= $rows; $row++) {
            for ($table = 1; $table <= $tablesPerRow; $table++) {
                Seat::create([
                    'venue_layout_id' => $this->id,
                    'seat_number' => 'T' . $tableNumber,
                    'seat_type' => 'table',
                    'capacity' => 4, // 4 people per classroom table
                    'price' => $tablePrice,
                    'x_position' => $table * 3.0,
                    'y_position' => $row * 3.0,
                    'width' => 1.5,
                    'height' => 1.0,
                    'section' => 'Classroom',
                ]);
                $tableNumber++;
            }
        }
    }

    /**
     * Get layout as JSON for frontend visualization.
     */
    public function toLayoutJson(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->layout_type,
            'width' => $this->width,
            'height' => $this->height,
            'capacity' => $this->total_capacity,
            'seats' => $this->seats->map(function ($seat) {
                return [
                    'id' => $seat->id,
                    'number' => $seat->seat_number,
                    'type' => $seat->seat_type,
                    'section' => $seat->section,
                    'x' => $seat->x_position,
                    'y' => $seat->y_position,
                    'width' => $seat->width,
                    'height' => $seat->height,
                    'rotation' => $seat->rotation,
                    'status' => $seat->status,
                    'price' => $seat->price,
                    'capacity' => $seat->capacity,
                    'accessible' => $seat->is_accessible,
                    'metadata' => $seat->metadata,
                ];
            })->toArray(),
        ];
    }
}