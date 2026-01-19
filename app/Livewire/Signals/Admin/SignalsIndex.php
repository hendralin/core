<?php

namespace App\Livewire\Signals\Admin;

use Livewire\Component;
use App\Models\StockSignal;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class SignalsIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $signalType = '';
    public $status = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;

    // Column visibility
    public $visibleColumns = [];

    // Available columns configuration
    public array $availableColumns = [
        // Basic Info
        'no' => ['label' => 'No.', 'sortable' => false, 'align' => 'center'],
        // 'id' => ['label' => 'ID', 'sortable' => true, 'align' => 'center'],
        'kode_emiten' => ['label' => 'Kode Emiten', 'sortable' => true, 'align' => 'left'],
        'signal_type' => ['label' => 'Tipe Sinyal', 'sortable' => true, 'align' => 'left'],

        // Financial Ratios
        'market_cap' => ['label' => 'Market Cap', 'sortable' => false, 'align' => 'right'],
        'pbv' => ['label' => 'PBV', 'sortable' => false, 'align' => 'right'],
        'per' => ['label' => 'PER', 'sortable' => false, 'align' => 'right'],

        // Before Data (H-1)
        'before_date' => ['label' => 'Before Date', 'sortable' => false, 'align' => 'center'],
        'before_close' => ['label' => 'Before Close', 'sortable' => false, 'align' => 'right'],
        'before_volume' => ['label' => 'Before Volume', 'sortable' => false, 'align' => 'right'],
        'before_value' => ['label' => 'Before Value', 'sortable' => false, 'align' => 'right'],

        // Hit Data (H)
        'hit_date' => ['label' => 'Hit Date', 'sortable' => false, 'align' => 'center'],
        'hit_close' => ['label' => 'Hit Close', 'sortable' => false, 'align' => 'right'],
        'hit_volume' => ['label' => 'Hit Volume', 'sortable' => false, 'align' => 'right'],
        'hit_value' => ['label' => 'Hit Value', 'sortable' => false, 'align' => 'right'],

        // After Data (H+1)
        'after_date' => ['label' => 'After Date', 'sortable' => false, 'align' => 'center'],
        'after_close' => ['label' => 'After Close', 'sortable' => false, 'align' => 'right'],
        'after_volume' => ['label' => 'After Volume', 'sortable' => false, 'align' => 'right'],
        'after_value' => ['label' => 'After Value', 'sortable' => false, 'align' => 'right'],

        // Management
        'status' => ['label' => 'Status', 'sortable' => true, 'align' => 'center'],
        'published_at' => ['label' => 'Published At', 'sortable' => true, 'align' => 'center'],
        // 'notes' => ['label' => 'Notes', 'sortable' => false, 'align' => 'left'],
        // 'recommendation' => ['label' => 'Recommendation', 'sortable' => false, 'align' => 'left'],
        // 'user_id' => ['label' => 'User ID', 'sortable' => true, 'align' => 'center'],
        // 'created_by' => ['label' => 'Dibuat Oleh', 'sortable' => false, 'align' => 'left'],
        'created_at' => ['label' => 'Dibuat', 'sortable' => true, 'align' => 'center'],
        'updated_at' => ['label' => 'Updated', 'sortable' => true, 'align' => 'center'],

        // Actions
        'actions' => ['label' => 'Aksi', 'sortable' => false, 'align' => 'left'],
    ];

    // Default visible columns
    public array $defaultColumns = [
        'no',
        'kode_emiten',
        'signal_type',
        'market_cap',
        'pbv',
        'per',
        'hit_date',
        'hit_close',
        'hit_value',
        'hit_volume',
        'status',
        'created_at',
        'actions',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'signalType' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        // Set default visible columns
        if (empty($this->visibleColumns)) {
            $this->visibleColumns = $this->defaultColumns;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSignalType()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }


    public function publish($signalId)
    {
        $signal = StockSignal::findOrFail($signalId);
        $signal->publish();

        session()->flash('success', 'Sinyal berhasil dipublikasikan.');
    }

    public function cancel($signalId)
    {
        $signal = StockSignal::findOrFail($signalId);
        $signal->cancel();

        session()->flash('success', 'Sinyal berhasil dibatalkan.');
    }

    public function delete($signalId)
    {
        $signal = StockSignal::findOrFail($signalId);

        // Check if signal is auto-generated
        if ($signal->signal_type === 'value_breakthrough' && is_null($signal->user_id)) {
            session()->flash('error', 'Sinyal yang dihasilkan otomatis tidak dapat dihapus.');
            return;
        }

        $signal->delete();
        session()->flash('success', 'Sinyal berhasil dihapus.');
    }

    public function toggleColumn($column)
    {
        if (in_array($column, $this->visibleColumns)) {
            $this->visibleColumns = array_values(array_diff($this->visibleColumns, [$column]));
        } else {
            $this->visibleColumns[] = $column;
        }
    }

    public function resetColumns()
    {
        $this->visibleColumns = $this->defaultColumns;
    }

    public function selectAllColumns()
    {
        $this->visibleColumns = array_keys($this->availableColumns);
    }

    public function deselectAllColumns()
    {
        // Reset to default columns
        $this->visibleColumns = $this->defaultColumns;
    }

    public function getIsAllColumnsSelectedProperty()
    {
        return count($this->visibleColumns) === count($this->availableColumns);
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 15, 25, 50, 'All'];
    }

    public function getVisibleColumnCountProperty()
    {
        return count($this->visibleColumns);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->signalType = '';
        $this->status = '';
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function getSignals()
    {
        return StockSignal::query()
            ->when($this->search, function ($query) {
                $query->where('kode_emiten', 'like', '%' . $this->search . '%')
                    ->orWhere('recommendation', 'like', '%' . $this->search . '%');
            })
            ->when($this->signalType, function ($query) {
                $query->where('signal_type', $this->signalType);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->with(['user', 'stockCompany'])
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        // Handle "All" option for perPage
        if ($this->perPage === 'All') {
            $signals = $this->getSignals()->get();
            $isAllData = true;
        } else {
            $signals = $this->getSignals()->paginate((int) $this->perPage);
            $isAllData = false;
        }

        return view('livewire.signals.admin.signals-index', [
            'signals' => $signals,
            'isAllData' => $isAllData,
            'signalTypes' => StockSignal::distinct('signal_type')->pluck('signal_type'),
        ]);
    }
}
