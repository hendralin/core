<?php

namespace App\Console\Commands;

use App\Models\StockSignal;
use Illuminate\Console\Command;

class ListStockSignals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:list-signals
                            {--status= : Filter by status (draft, active, published, expired, cancelled)}
                            {--limit=10 : Limit number of results}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List saved stock signals from database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('📊 Stock Signals List');
        $this->newLine();

        $query = StockSignal::with('user')->latest();

        if ($status = $this->option('status')) {
            $query->where('status', $status);
        }

        $signals = $query->limit((int) $this->option('limit'))->get();

        if ($signals->isEmpty()) {
            $this->warn('No signals found.');
            return Command::SUCCESS;
        }

        $this->displaySignals($signals);

        $this->newLine();
        $this->info("Total signals shown: {$signals->count()}");

        return Command::SUCCESS;
    }

    /**
     * Display signals in a table format
     */
    private function displaySignals($signals)
    {
        $tableData = $signals->map(function ($signal) {
            return [
                'ID' => $signal->id,
                'Kode' => $signal->kode_emiten,
                'Type' => ucfirst(str_replace('_', ' ', $signal->signal_type)),
                'Status' => $signal->status_label,
                'Market Cap' => $signal->formatted_market_cap,
                'PBV' => $signal->formatted_pbv,
                'PER' => $signal->formatted_per,
                'Hit Date' => $signal->hit_date->format('Y-m-d'),
                'Published' => $signal->published_at?->format('Y-m-d H:i') ?? '-',
                'Created By' => $signal->user?->name ?? 'System',
            ];
        });

        $headers = ['ID', 'Kode', 'Type', 'Status', 'Market Cap', 'PBV', 'PER', 'Hit Date', 'Published', 'Created By'];

        $this->table($headers, $tableData->toArray());
    }
}
