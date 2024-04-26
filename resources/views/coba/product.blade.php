!DOCTYPE html>
<html>

<head>
  <title>Laravel 9 Table Inline Edit Using JQuery Editable - Mywebtuts.com</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/jquery-editable/css/jquery-editable.css"
    rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet" />
  <script
    src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/jquery-editable/js/jquery-editable-poshytip.min.js">
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>
<style type="text/css">
  .container h2,
  .panel-info {
    margin-top: 80px;
    line-height: 50px;
  }
</style>

<body>
  <div class="container">
    <div class="col-md-8 col-md-offset-2">
      <h2 class="text-center">Laravel 9 Table Inline Edit Using JQuery Editable - Mywebtuts.com</h2>
      <div class="panel panel-info">
        <div class="panel-heading">Laravel 9 Table Inline Edit Using jQuery editable</div>
        <div class="panel-body">
          <table class="table table-bordered data-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Name</th>
                <th>Detail</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($product as $product)
              <tr>
                <td>{{ $product->id }}</td>
                <td>
                  <a href="" class="update" data-name="name" data-type="text" data-pk="{{ $product->id }}"
                    data-title="Enter name">{{ $product->name }}</a>
                </td>
                <td>
                  <a href="" class="update" data-name="detail" data-type="text" data-pk="{{ $product->id }}"
                    data-title="Enter Detail">{{ $product->detail }}</a>
                </td>
                <td>
                  <a class="deleteProduct btn btn-xs btn-danger"
                    onclick="return confirm('apakah anda akan menghapus data ini?')"
                    data-id="{{ $product->id }}">Delete</a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    $.fn.editable.defaults.mode = 'inline';

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        $('.update').editable({
            url: "{{ route('product.update') }}",
            type: 'text',
            pk: 1,
            name: 'name',
            title: 'Enter name'
        });

        $(".deleteProduct").click(function(){
            $(this).parents('tr').hide();
            var id = $(this).data("id");
            var token = '{{ csrf_token() }}';
            $.ajax(
            {
                method:'POST',
                url: "product/delete/"+id,
                data: {_token: token},
                success: function(data)
                {
                    toastr.success('Successfully!','Delete');
                }
            });
        });
  </script>
</body>

</html>