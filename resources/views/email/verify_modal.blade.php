<style>
    .modal-backdrop {
        display: none !important;
      }
      .modal-open .modal {
          width: 300px;
          margin: 0 auto;
      }
</style>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    
<div id="myModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-sm">
    <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Email Verification</h4>
        </div>
        <div class="modal-body">
        {{-- <p>Your Email is verified.</p> --}}
        <p>{{$message}}</p>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
    </div>
</div>

<script type="text/javascript">
    $(window).on("load", function() {
        $("#myModal").modal("show");
    });
</script>