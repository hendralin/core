<div>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Buat Sinyal Saham Baru</h1>
                <p class="text-muted">Tambahkan sinyal saham secara manual</p>
            </div>
            <div>
                <a href="{{ route('admin.signals.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        <!-- Create Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informasi Sinyal</h6>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="save">
                            <!-- Signal Type & Kode Emiten -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="signal_type">Tipe Sinyal <span class="text-danger">*</span></label>
                                        <select class="form-control @error('signal_type') is-invalid @enderror" id="signal_type" wire:model="signal_type">
                                            <option value="manual">Manual</option>
                                            <option value="technical">Technical</option>
                                            <option value="fundamental">Fundamental</option>
                                            <option value="momentum">Momentum</option>
                                            <option value="value_breakthrough">Value Breakthrough</option>
                                        </select>
                                        @error('signal_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @error
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kode_emiten">Kode Emiten <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('kode_emiten') is-invalid @enderror"
                                               id="kode_emiten" wire:model.live="kode_emiten" placeholder="Contoh: BBCA" maxlength="10">
                                        @error('kode_emiten')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @error
                                        <small class="form-text text-muted">Masukkan kode emiten (maksimal 10 karakter)</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Ratios -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="market_cap">Market Cap</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="number" class="form-control @error('market_cap') is-invalid @enderror"
                                                   id="market_cap" wire:model="market_cap" step="0.01" placeholder="1000000000000">
                                            @error('market_cap')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @error
                                        </div>
                                        <small class="form-text text-muted">Dalam Rupiah (contoh: 1000000000000 untuk 1T)</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pbv">PBV</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control @error('pbv') is-invalid @enderror"
                                                   id="pbv" wire:model="pbv" step="0.01" placeholder="1.25">
                                            <div class="input-group-append">
                                                <span class="input-group-text">x</span>
                                            </div>
                                        </div>
                                        @error('pbv')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @error
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="per">PER</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control @error('per') is-invalid @enderror"
                                                   id="per" wire:model="per" step="0.01" placeholder="15.5">
                                            <div class="input-group-append">
                                                <span class="input-group-text">x</span>
                                            </div>
                                        </div>
                                        @error('per')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @error
                                    </div>
                                </div>
                            </div>

                            <!-- Hit Data (H) -->
                            <div class="card border-primary mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="m-0"><i class="fas fa-bullseye"></i> Data Breakthrough (H)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="hit_date">Tanggal Hit <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control @error('hit_date') is-invalid @enderror"
                                                       id="hit_date" wire:model="hit_date">
                                                @error('hit_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @error
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="hit_value">Nilai <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="number" class="form-control @error('hit_value') is-invalid @enderror"
                                                           id="hit_value" wire:model="hit_value" step="0.01">
                                                </div>
                                                @error('hit_value')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @error
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="hit_close">Harga Close <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="number" class="form-control @error('hit_close') is-invalid @enderror"
                                                           id="hit_close" wire:model="hit_close" step="0.01">
                                                </div>
                                                @error('hit_close')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @error
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="hit_volume">Volume <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control @error('hit_volume') is-invalid @enderror"
                                                       id="hit_volume" wire:model="hit_volume" min="0">
                                                @error('hit_volume')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @error
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Before Data (H-1) -->
                            <div class="card border-info mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="m-0"><i class="fas fa-arrow-left"></i> Data Sebelum (H-1)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="before_date">Tanggal</label>
                                                <input type="date" class="form-control @error('before_date') is-invalid @enderror"
                                                       id="before_date" wire:model="before_date">
                                                @error('before_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @error
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="before_value">Nilai</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="number" class="form-control @error('before_value') is-invalid @enderror"
                                                           id="before_value" wire:model="before_value" step="0.01">
                                                </div>
                                                @error('before_value')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @error
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="before_close">Harga Close</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="number" class="form-control @error('before_close') is-invalid @enderror"
                                                           id="before_close" wire:model="before_close" step="0.01">
                                                </div>
                                                @error('before_close')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @error
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="before_volume">Volume</label>
                                                <input type="number" class="form-control @error('before_volume') is-invalid @enderror"
                                                       id="before_volume" wire:model="before_volume" min="0">
                                                @error('before_volume')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @error
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- After Data (H+1) -->
                            <div class="card border-success mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="m-0"><i class="fas fa-arrow-right"></i> Data Sesudah (H+1)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="after_date">Tanggal</label>
                                                <input type="date" class="form-control @error('after_date') is-invalid @enderror"
                                                       id="after_date" wire:model="after_date">
                                                @error('after_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @error
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="after_value">Nilai</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="number" class="form-control @error('after_value') is-invalid @enderror"
                                                           id="after_value" wire:model="after_value" step="0.01">
                                                </div>
                                                @error('after_value')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @error
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="after_close">Harga Close</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="number" class="form-control @error('after_close') is-invalid @enderror"
                                                           id="after_close" wire:model="after_close" step="0.01">
                                                </div>
                                                @error('after_close')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @error
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="after_volume">Volume</label>
                                                <input type="number" class="form-control @error('after_volume') is-invalid @enderror"
                                                       id="after_volume" wire:model="after_volume" min="0">
                                                @error('after_volume')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @error
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status & Notes -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="status" wire:model="status">
                                            <option value="draft">Draft</option>
                                            <option value="active">Active</option>
                                            <option value="published">Published</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @error
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="notes">Catatan</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes"
                                                  wire:model="notes" rows="2" maxlength="1000"
                                                  placeholder="Catatan tambahan tentang sinyal ini..."></textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @error
                                        <small class="form-text text-muted">Maksimal 1000 karakter</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Recommendation -->
                            <div class="form-group">
                                <label for="recommendation">Rekomendasi <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('recommendation') is-invalid @enderror" id="recommendation"
                                          wire:model="recommendation" rows="4" maxlength="2000" required
                                          placeholder="Jelaskan rekomendasi investasi untuk saham ini..."></textarea>
                                @error('recommendation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @error
                                <small class="form-text text-muted">Maksimal 2000 karakter</small>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <span wire:loading.remove><i class="fas fa-save"></i> Simpan Sinyal</span>
                                    <span wire:loading><i class="fas fa-spinner fa-spin"></i> Menyimpan...</span>
                                </button>
                                <a href="{{ route('admin.signals.index') }}" class="btn btn-secondary ml-2">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Info Panel -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Panduan</h6>
                    </div>
                    <div class="card-body">
                        <h6>Tentang Data Sinyal:</h6>
                        <ul class="small">
                            <li><strong>H (Breakthrough):</strong> Data saat saham mencapai kriteria breakthrough</li>
                            <li><strong>H-1 (Sebelum):</strong> Data satu hari sebelum breakthrough</li>
                            <li><strong>H+1 (Sesudah):</strong> Data satu hari setelah breakthrough</li>
                        </ul>

                        <hr>

                        <h6>Tipe Sinyal:</h6>
                        <ul class="small">
                            <li><strong>Manual:</strong> Sinyal yang dibuat secara manual</li>
                            <li><strong>Technical:</strong> Berdasarkan analisis teknikal</li>
                            <li><strong>Fundamental:</strong> Berdasarkan analisis fundamental</li>
                            <li><strong>Momentum:</strong> Berdasarkan momentum pasar</li>
                            <li><strong>Value Breakthrough:</strong> Otomatis dari command analisis</li>
                        </ul>

                        <hr>

                        <h6>Status:</h6>
                        <ul class="small">
                            <li><strong>Draft:</strong> Belum siap dipublikasikan</li>
                            <li><strong>Active:</strong> Siap untuk distribusi</li>
                            <li><strong>Published:</strong> Sudah dipublikasikan ke investor</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
