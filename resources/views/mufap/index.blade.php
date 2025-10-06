<!DOCTYPE html>
<html lang="en">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>MUFAP Data</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
      <style>
            body {
                  background-color: #1e1e1e;
                  color: #e0e0e0;
            }

            .card {
                  background-color: #2a2a2a;
                  border: none;
                  border-radius: 12px;
            }

            .card-header {
                  background-color: #333 !important;
                  color: #f1f1f1 !important;
                  border-bottom: 1px solid #444;
            }

            .table {
                  color: #ddd;
            }

            .table thead {
                  background-color: #444;
                  color: #f1f1f1;
            }

            .table tbody tr:hover {
                  background-color: #2f2f2f;
            }

            .btn-dark-custom {
                  background-color: #444;
                  border: none;
                  color: #f1f1f1;
            }

            .btn-dark-custom:hover {
                  background-color: #555;
                  color: #fff;
            }

            .pagination .page-link {
                  background-color: #2a2a2a;
                  border: 1px solid #444;
                  color: #e0e0e0;
            }

            .pagination .page-item.active .page-link {
                  background-color: #555;
                  border-color: #666;
            }

            .form-label {
                  color: #ddd;
            }
      </style>
</head>

<body class="py-5">

      <div class="container">
            <h2 class="mb-4 text-center fw-bold text-light">📊 MUFAP Data Management</h2>

            {{-- Upload Form --}}
            <div class="card shadow-sm mb-4">
                  <div class="card-body">
                        <form action="{{ route('mufap.upload') }}" method="POST" enctype="multipart/form-data"
                              class="row g-2 align-items-center">
                              @csrf
                              <div class="col-md-6">
                                    <input type="file" name="csv_file"
                                          class="form-control bg-dark text-light border-0 @error('csv_file') is-invalid @enderror"
                                          accept=".csv">
                                    @error('csv_file')
                                    <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                              </div>
                              <div class="col-md-3">
                                    <button type="submit" class="btn btn-dark-custom w-100">
                                          <i class="bi bi-upload"></i> Upload CSV
                                    </button>
                              </div>
                        </form>
                  </div>
            </div>

            {{-- Success/Error Messages --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  {{ session('error') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            {{-- Filter Form --}}
            <div class="card shadow-sm mb-4">
                  <div class="card-body">
                        <form method="GET" action="{{ route('mufap.index') }}">
                              <div class="row g-3 align-items-end">

                                    {{-- AMC --}}
                                    <div class="col-md-2">
                                          <label class="form-label">AMC</label>
                                          <select name="amc" class="form-select">
                                                <option value="">All</option>
                                                @foreach($allFetchData['amcs'] as $amc)
                                                <option value="{{ $amc->id }}" {{ request('amc')==$amc->id ? 'selected'
                                                      : '' }}>
                                                      {{ $amc->name }}
                                                </option>
                                                @endforeach
                                          </select>
                                    </div>

                                    {{-- Sector --}}
                                    <div class="col-md-2">
                                          <label class="form-label">Sector</label>
                                          <select name="sector" class="form-select">
                                                <option value="">All</option>
                                                @foreach($allFetchData['sectors'] as $sector)
                                                <option value="{{ $sector->id }}" {{ request('sector')==$sector->id ?
                                                      'selected' :
                                                      '' }}>
                                                      {{ $sector->name }}
                                                </option>
                                                @endforeach
                                          </select>
                                    </div>

                                    {{-- Funds --}}
                                    <div class="col-md-2">
                                          <label class="form-label">Funds</label>
                                          <select name="funds" class="form-select">
                                                <option value="">All</option>
                                                @foreach($allFetchData['fundsList'] as $fund)
                                                <option value="{{ $fund->id }}" {{ request('funds')==$fund->id ?
                                                      'selected' : '' }}>
                                                      {{ $fund->name }}
                                                </option>
                                                @endforeach
                                          </select>
                                    </div>

                                    {{-- Category --}}
                                    <div class="col-md-2">
                                          <label class="form-label">Category</label>
                                          <select name="category" class="form-select">
                                                <option value="">All</option>
                                                @foreach($allFetchData['categories'] as $cat)
                                                <option value="{{ $cat->id }}" {{ request('category')==$cat->id ?
                                                      'selected' : ''
                                                      }}>
                                                      {{ $cat->name }}
                                                </option>
                                                @endforeach
                                          </select>
                                    </div>

                                    {{-- From Date --}}
                                    <div class="col-md-2">
                                          <label class="form-label">From</label>
                                          <input type="text" name="from_date" id="from_date" class="form-control"
                                                value="{{ request('from_date') }}" placeholder="YYYY-MM-DD">
                                    </div>

                                    {{-- To Date --}}
                                    <div class="col-md-2">
                                          <label class="form-label">To</label>
                                          <input type="text" name="to_date" id="to_date" class="form-control"
                                                value="{{ request('to_date') }}" placeholder="YYYY-MM-DD">
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="col-md-2">
                                          <button type="submit" class="btn btn-primary w-100 mb-2">
                                                <i class="fas fa-search"></i> Search
                                          </button>
                                          <a href="{{ route('mufap.index') }}" class="btn btn-outline-secondary w-100">
                                                Reset
                                          </a>
                                    </div>

                              </div>
                        </form>
                  </div>
            </div>

            {{-- Show Data --}}
            <div class="card shadow-sm">
                  <div class="card-header fw-bold">
                        Uploaded Funds Data
                  </div>
                  <div class="card-body p-0">
                        <div class="table-responsive">
                              <table class="table table-hover align-middle mb-0">
                                    <thead class="text-center">
                                          <tr>
                                                <th>Sector</th>
                                                <th>AMC</th>
                                                <th>Fund</th>
                                                <th>Category</th>
                                                <th>Inception Date</th>
                                                <th>Offer</th>
                                                <th>Repurchase</th>
                                                <th>NAV</th>
                                                <th>Validity Date</th>
                                                <th>Front-end</th>
                                                <th>Back-end</th>
                                                <th>Contingent</th>
                                                <th>Market</th>
                                                <th>Trustee</th>
                                          </tr>
                                    </thead>
                                    <tbody id="funds-table" class="text-center">
                                          <tr>
                                                <td colspan="14" class="text-center text-muted py-4">Loading data...
                                                </td>
                                          </tr>
                                    </tbody>
                              </table>
                        </div>
                  </div>

                  {{-- Pagination --}}
                  <div class="card-footer bg-dark text-light">
                        <div class="d-flex justify-content-center">
                              {{ $funds->links('pagination::bootstrap-5') }}
                        </div>
                  </div>
            </div>
      </div>
      {{-- Custom JS for handling data fetch and display --}}
      <script src="{{ asset('js/script.js') }}"></script>

      <!-- Bootstrap JS -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

      <!-- jQuery -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

      <!-- Moment.js -->
      <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

      <!-- DateRangePicker -->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
      </script>
      <script>
            // Datepicker Initialization
            $(function () {
            $('#from_date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
            });
            
            $('#to_date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
            });
            });
      </script>
</body>

</html>