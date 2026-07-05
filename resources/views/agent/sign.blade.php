@extends('agent.layouts.app')
@section('title')
    @lang('trans.dashboard_agent')
@endsection
@section('contentheader')
    @lang('trans.dashboard_agent')
@endsection
@section('contentheaderlink')
    <a href="{{ route('agent') }}">
        @lang('trans.dashboard_agent') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_agent')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 order-lg-12">
                <div style="display: flex; justify-content: center;">
                    <div id="signature-pad" style="border: 1px solid #000; width: 1000; height: 400;"></div>

                    <button id="clear-signature">Clear</button>
                    <button id="save-signature">Save Signature</button>
                    <input type="hidden" id="signature-data" name="signature">
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
@endpush
@push('custom')
    <script>
        $(document).ready(function() {
            // Initialize signature pad
            const canvas = document.createElement('canvas');
            canvas.width = 1000;
            canvas.height = 400;
            canvas.style.border = '1px solid #000';
            $('#signature-pad').html(canvas);

            const ctx = canvas.getContext('2d');
            let isDrawing = false;
            let lastX = 0;
            let lastY = 0;

            // Handle pen tablet events
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseout', stopDrawing);

            // For touch devices (optional)
            canvas.addEventListener('touchstart', handleTouch);
            canvas.addEventListener('touchmove', handleTouch);
            canvas.addEventListener('touchend', stopDrawing);

            function startDrawing(e) {
                isDrawing = true;
                [lastX, lastY] = getPosition(e);
            }

            function draw(e) {
                if (!isDrawing) return;

                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                [lastX, lastY] = getPosition(e);
                ctx.lineTo(lastX, lastY);
                ctx.strokeStyle = '#000';
                ctx.lineWidth = 2;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.stroke();
            }

            function stopDrawing() {
                isDrawing = false;
            }

            function handleTouch(e) {
                e.preventDefault();
                const touch = e.touches[0];
                const mouseEvent = new MouseEvent(
                    e.type === 'touchstart' ? 'mousedown' : 'mousemove', {
                        clientX: touch.clientX,
                        clientY: touch.clientY
                    }
                );
                canvas.dispatchEvent(mouseEvent);
            }

            function getPosition(e) {
                const rect = canvas.getBoundingClientRect();
                return [
                    e.clientX - rect.left,
                    e.clientY - rect.top
                ];
            }

            // Clear signature
            $('#clear-signature').click(function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            });

            // Save signature
            $('#save-signature').click(function() {
                const dataURL = canvas.toDataURL('image/png');
                let demandeurId = @json($demandeur->id);
                $('#signature-data').val(dataURL);

                // Send to server
                $.ajax({
                    url: '/agent/save/' + demandeurId,
                    method: 'POST',
                    data: {
                        signature: dataURL,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert('Signature saved successfully!');
                    },
                    error: function(xhr) {
                        alert('Error saving signature');
                    }
                });
            });
        });
    </script>
@endpush
