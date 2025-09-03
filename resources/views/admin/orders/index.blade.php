@extends('template.layout')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <h4>Daftar Pesanan</h4>
        <form id="filter-form" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="table_number_filter" class="form-label">Filter Nomor Meja</label>
                <input type="number" class="form-control" id="table_number_filter" name="table_number_filter" placeholder="Masukkan nomor meja">
            </div>
            <div class="col-md-3">
                <label for="orderer_name_filter" class="form-label">Filter Nama Pemesan</label>
                <input type="text" class="form-control" id="orderer_name_filter" name="orderer_name_filter" placeholder="Masukkan nama pemesan">
            </div>
            <div class="col-md-3">
                <label for="status_filter" class="form-label">Filter Status</label>
                <select class="form-select" id="status_filter" name="status_filter">
                    <option value="">Semua Status</option>
                    <option value="0">Pending</option>
                    <option value="1">Sedang Dibuat</option>
                    <option value="2">Sudah Disajikan</option>
                    <option value="3">Selesai</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>

    {{-- Container untuk menampilkan daftar pesanan --}}
    <div id="orders-container" class="row col-12"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

fetchOrders();
function fetchOrders() {
    let tableNumberFilter = $('#table_number_filter').val();
    let statusFilter = $('#status_filter').val();
    let ordererNameFilter = $('#orderer_name_filter').val();

    $.ajax({
        url: "{{ route('admin.orders.getData') }}",
        method: 'GET',
        data: {
            table_number_filter: tableNumberFilter,
            orderer_name_filter: ordererNameFilter,
            status_filter: statusFilter
        },
        beforeSend: function() {
            $('#orders-container').html('<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Memuat pesanan...</p></div>');
        },
        success: function (orders) {
            $('#orders-container').empty();

            if (orders.length === 0) {
                $('#orders-container').append('<div class="col-12"><div class="alert alert-info" role="alert">Tidak ada pesanan yang ditemukan dengan filter ini.</div></div>');
                return;
            }

            renderOrders(orders);

        },
        error: function (xhr, status, error) {
            console.error("Error fetching orders:", error);
            $('#orders-container').html('<div class="col-12"><div class="alert alert-danger" role="alert">Terjadi kesalahan saat memuat pesanan. Silakan coba lagi.</div></div>');
        }
    });
}

function renderOrders(orders) {
    $.each(orders, function (index, order) {
        let orderStatusOptions = `
            <option value="0" ${order.status == 0 ? 'selected' : ''}>Pending</option>
            <option value="1" ${order.status == 1 ? 'selected' : ''}>Sedang Dibuat</option>
            <option value="2" ${order.status == 2 ? 'selected' : ''}>Sudah Disajikan</option>
            <option value="3" ${order.status == 3 ? 'selected' : ''}>Selesai</option>
        `;

        let orderItemsHtml = '';
        let totalOrderPrice = 0;

        $.each(order.items, function(itemIndex, item) {
            let menuName = item.menu ? item.menu.name : '-';
            if (item.type === 'member') {
                orderItemsHtml += `
                    <tr>
                        <td>${menuName}</td>
                        <td>${item.qty}</td>
                        <td colspan="2">Member</td>
                    </tr>
                `;
            }else {
                let menuPrice = item.menu ? item.menu.price : 0;
                let subtotal = item.qty * menuPrice;
                totalOrderPrice += subtotal;

                orderItemsHtml += `
                    <tr>
                        <td>${menuName}</td>
                        <td>${item.qty}</td>
                        <td>Rp ${new Intl.NumberFormat('id-ID').format(menuPrice)}</td>
                        <td>Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</td>
                    </tr>
                `;
            }

        });

        let cardHtml = `
            <div class="col-md-4">
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5><b>Pesanan:</b> ${order.uuid}</h5>
                        <p><b>Nama Pemesan:</b> ${order.name}</p>
                        <p><b>No HP:</b> ${order.phone || '-'}</p>
                        <p><b>Nomor Meja:</b> ${order.table ? order.table.number : '-'}</p>

                        <p>
                            <b>Status:</b>
                            <select class="form-select status-select w-50" data-id="${order.id}">
                                ${orderStatusOptions}
                            </select>
                        </p>

                        <h6><b>Detail Pesanan:</b></h6>
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${orderItemsHtml}
                                <tr>
                                    <td colspan="3" class="text-end"><b>Total</b></td>
                                    <td><b>Rp ${new Intl.NumberFormat('id-ID').format(totalOrderPrice)}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>`;
        $('#orders-container').append(cardHtml);
    });

}

$('#filter-form').on('submit', function (e) {
    e.preventDefault();
    fetchOrders();
});

// Update status (delegasi event karena elemen dibuat secara dinamis)
$(document).on('change', '.status-select', function () {
    let orderId = $(this).data('id');
    let status = $(this).val();

    $.ajax({
        url: "{{ route('admin.orders.updateStatus', ['order' => ':orderId']) }}".replace(':orderId', orderId),
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            status: status
        },
        success: function (res) {
            if (res.success) {
                fetchOrders();
            }
        },
        error: function (xhr, status, error) {
            console.error("Error updating status:", error);
        }
    });
});
</script>
@endsection
