<?php

namespace App\Livewire\TradingSummary;

use App\Models\TradingInfo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Stock Summary')]
class StockSummaryIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $queryString = [
        'search' => ['except' => ''],
        'date' => ['except' => ''],
        'sortField' => ['except' => 'kode_emiten'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public $search = '';
    public $date = '';
    public $sortField = 'kode_emiten';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Column visibility
    public $visibleColumns = [];

    // Available columns configuration
    public array $availableColumns = [
        'kode_emiten' => ['label' => 'Stock Code', 'sortable' => true, 'align' => 'left'],
        'company_name' => ['label' => 'Company Name', 'sortable' => true, 'align' => 'left'],
        'previous' => ['label' => 'Previous', 'sortable' => true, 'align' => 'right'],
        'open_price' => ['label' => 'Open', 'sortable' => true, 'align' => 'right'],
        'high' => ['label' => 'High', 'sortable' => true, 'align' => 'right'],
        'low' => ['label' => 'Low', 'sortable' => true, 'align' => 'right'],
        'close' => ['label' => 'Close', 'sortable' => true, 'align' => 'right'],
        'change' => ['label' => 'Change', 'sortable' => true, 'align' => 'right'],
        'volume' => ['label' => 'Volume', 'sortable' => true, 'align' => 'right'],
        'value' => ['label' => 'Value', 'sortable' => true, 'align' => 'right'],
        'frequency' => ['label' => 'Frequency', 'sortable' => true, 'align' => 'right'],
        'index_individual' => ['label' => 'Index Individual', 'sortable' => true, 'align' => 'right'],
        'offer' => ['label' => 'Offer', 'sortable' => true, 'align' => 'right'],
        'offer_volume' => ['label' => 'Offer Volume', 'sortable' => true, 'align' => 'right'],
        'bid' => ['label' => 'Bid', 'sortable' => true, 'align' => 'right'],
        'bid_volume' => ['label' => 'Bid Volume', 'sortable' => true, 'align' => 'right'],
        'listed_shares' => ['label' => 'Listed Shares', 'sortable' => true, 'align' => 'right'],
        'tradeble_shares' => ['label' => 'Tradeable Shares', 'sortable' => true, 'align' => 'right'],
        'foreign_sell' => ['label' => 'Foreign Sell', 'sortable' => true, 'align' => 'right'],
        'foreign_buy' => ['label' => 'Foreign Buy', 'sortable' => true, 'align' => 'right'],
        'non_regular_volume' => ['label' => 'Non-Reg Volume', 'sortable' => true, 'align' => 'right'],
        'non_regular_value' => ['label' => 'Non-Reg Value', 'sortable' => true, 'align' => 'right'],
        'non_regular_frequency' => ['label' => 'Non-Reg Frequency', 'sortable' => true, 'align' => 'right'],
    ];

    // Default visible columns
    public array $defaultColumns = [
        'kode_emiten',
        'company_name',
        'high',
        'low',
        'close',
        'change',
        'volume',
        'value',
        'frequency',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatedDate()
    {
        $this->resetPage();
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

    public function isColumnVisible($column)
    {
        return in_array($column, $this->visibleColumns);
    }

    public function getIsAllColumnsSelectedProperty()
    {
        return count($this->visibleColumns) === count($this->availableColumns);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function clearFilters()
    {
        $this->reset(['search']);
        $this->setDefaultDate();
        $this->resetPage();
    }

    public function mount()
    {
        // Set default date to latest trading date
        if (empty($this->date)) {
            $this->setDefaultDate();
        }

        // Set default visible columns
        if (empty($this->visibleColumns)) {
            $this->visibleColumns = $this->defaultColumns;
        }
    }

    /**
     * Set default date to latest trading date from database
     */
    private function setDefaultDate(): void
    {
        $latestDate = TradingInfo::max('date');
        $this->date = $latestDate ? \Carbon\Carbon::parse($latestDate)->format('Y-m-d') : now()->format('Y-m-d');
    }

    public function render()
    {
        $query = TradingInfo::with('stockCompany')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('trading_infos.kode_emiten', 'like', '%' . $this->search . '%')
                        ->orWhereHas('stockCompany', function ($q) {
                            $q->where('nama_emiten', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->date, fn($q) => $q->whereDate('trading_infos.date', $this->date));

        // Handle sorting for company_name (from relation)
        if ($this->sortField === 'company_name') {
            $query->leftJoin('stock_companies', 'trading_infos.kode_emiten', '=', 'stock_companies.kode_emiten')
                ->orderBy('stock_companies.nama_emiten', $this->sortDirection)
                ->select('trading_infos.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        // Handle "All" option for perPage
        if ($this->perPage === 'All') {
            $stocks = $query->get();
            $isAllData = true;
        } else {
            $stocks = $query->paginate((int) $this->perPage);
            $isAllData = false;
        }

        return view('livewire.trading-summary.stock-summary-index', [
            'stocks' => $stocks,
            'isAllData' => $isAllData,
        ]);
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 20, 30, 40, 50, 'All'];
    }

    public function getVisibleColumnCountProperty()
    {
        return count($this->visibleColumns);
    }

    public function selectStock($kodeEmiten)
    {
        // Update default_kode_emiten for current user
        auth()->user()->update(['default_kode_emiten' => $kodeEmiten]);

        // Redirect to dashboard
        return $this->redirect('/dashboard', true);
    }
}
