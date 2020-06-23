</div>

<!-- Jquery JS-->
<!--<script src="vendor/jquery-3.2.1.min.js"></script>-->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<!-- Bootstrap JS-->
<script src="vendor/bootstrap-4.1/popper.min.js"></script>
<script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
<!-- Vendor JS       -->


<?php if(preg_match("/admin.php/", $_SERVER['REQUEST_URI'])){
    echo '<script src="vendor/slick/slick.min.js">
</script>
<script src="vendor/wow/wow.min.js"></script>
<script src="vendor/animsition/animsition.min.js"></script>
<script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
</script>
<script src="vendor/counter-up/jquery.waypoints.min.js"></script>
<script src="vendor/counter-up/jquery.counterup.min.js">
</script>
<script src="vendor/circle-progress/circle-progress.min.js"></script>
<script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="vendor/chartjs/Chart.bundle.min.js"></script>
<script src="vendor/select2/select2.min.js"></script>

<script src="js/main.js"></script>';
} ?>

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
    /* Custom filtering function which will filter data in column four between two values */

    /* Custom filtering function which will search data in column four between two values */
    $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            var min = parseFloat($('#min').val() );
            var max = parseFloat($('#max').val() );
            var age = parseFloat( data[4] ) || 0; // use data for the age column

            if ( ( isNaN( min ) && isNaN( max ) ) ||
                ( isNaN( min ) && age <= max ) ||
                ( min <= age   && isNaN( max ) ) ||
                ( min <= age   && age <= max ) )
            {
                return true;
            }
            return false;
        }
    );

    $(document).ready(function() {
        var table = $('#instagram').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Russian.json"
            },
            lengthMenu: [
                [ 25, 50, 10, -1 ],
                [ '25', '50', '10', 'Показать все' ]
            ],
            searchPanes:{
                viewTotal: true,
            },



        });

        // Event listener to the two range filtering inputs to redraw on input
        $('#min, #max').keyup( function() {
            table.draw();
        } );
    } );
</script>

</body>

</html>
<!-- end document-->
