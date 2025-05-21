@extends('layouts.app')
@section('content')
<div class="row">
    @foreach(['low' => 'Low Risk', 'medium' => 'Medium Risk'] as $cat => $label)
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ $label }}</span>
                <div>
                    <a href="{{ route('risk.export', $cat) }}" class="btn btn-success btn-sm">Export Excel</a>
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $cat }}">Detail</button>
                </div>
            </div>
            <div class="card-body">
                <h2>{{ $risks[$cat]->details->count() ?? 0 }}</h2>
                <p>Jumlah data {{ $label }}</p>
            </div>
        </div>
        <!-- Modal Detail -->
        <div class="modal fade" id="detailModal-{{ $cat }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header"><h5>Detail {{ $label }}</h5></div>
                    <div class="modal-body">
                        <table class="table">
                            <thead><tr><th>Name</th><th>Description</th><th>Recommendation</th></tr></thead>
                            <tbody>
                                @foreach($risks[$cat]->details as $detail)
                                <tr>
                                    <td>{{ $detail->name }}</td>
                                    <td>{{ $detail->description }}</td>
                                    <td>{{ $detail->recommendation }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection 