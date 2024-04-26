

<!-- jQuery -->
<script src="lte/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="lte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="lte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="lte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="lte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="lte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="lte/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="lte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="lte/plugins/jszip/jszip.min.js"></script>
<script src="lte/plugins/pdfmake/pdfmake.min.js"></script>
<script src="lte/plugins/pdfmake/vfs_fonts.js"></script>
<script src="lte/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="lte/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="lte/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="lte/dist/js/adminlte.min.js"></script>

<!-- AdminLTE for demo purposes -->
<script src="lte/dist/js/demo.js"></script>

@livewireScripts

<!-- SweetAlert2 -->
<script src="lte/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="lte/plugins/toastr/toastr.min.js"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}


{{-- <script>
    $(document).ready(function(){
        toastr.options={
            "progressBar":true,
            "positionClass":"toast-bottom-right",
        }
    });
            window.addEventListener('success',event=>{
                toastr.success(event.detail.message);
            });
            window.addEventListener('warning',event=>{
                toastr.warning(event.detail.message);
            });
            window.addEventListener('error',event=>{
                toastr.error(event.detail.message);
            });
</script> --}}



<script>
    $(document).ready(function () {
        // Initialize Toastr options
        toastr.options = {
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": 2000
        };

        // Show flash messages using Toastr
        @if (session('success'))
            toastr.success('{{ session('success') }}');
        @endif

        @if (session('error'))
            toastr.error('{{ session('error') }}');
        @endif

        @if (session('warning'))
            toastr.warning('{{ session('warning') }}');
        @endif

        window.addEventListener('success',event=>{
                toastr.success("Data Berhasil Diinput");
            });
            window.addEventListener('warning',event=>{
                toastr.warning('Data Berhasil Update');
            });
            window.addEventListener('error',event=>{
                toastr.error('Data Berhasil Dihapus');
            });
            window.addEventListener('info',event=>{
                toastr.info('Operasi atau Perintah Berhasil');
            });
    });
</script>


{{-- <script>
    $(document).ready(function() {
        // Configure Toastr options
        toastr.options = {
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": 6000
        };

        // Listen for Livewire events and trigger Toastr notifications
        document.addEventListener('livewire:load', function() {
            Livewire.on('notification', function(type, message) {
                if (type === 'success') {
                    toastr.success(message);
                } else if (type === 'error') {
                    toastr.error(message);
                } else if (type === 'warning') {
                    toastr.warning(message);
                }
            });
        });
    });
</script> --}}









<!-- Page specific script -->
<script>
  $(function () {
  $("#example1").DataTable({
    "scrollX": true,
    "responsive": true, "lengthChange": false, "autoWidth": false,
    "buttons": ["pageLength", "csv", "excel", "pdf", "print", "colvis"]
  }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  $('#example2').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,

  });
});
</script>


