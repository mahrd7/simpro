<!DOCTYPE html>
<html>
    <head>
        <title>Schedule Kerja</title>
        <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>/assets/css/chart.css">

        <script type="text/javascript" src="<?php echo base_url();?>/assets/js/jquery-1.9.1.min.js"></script>

        <script type="text/javascript">
        $(document).ready(function(){

            jQuery('#container').scroll(function() {
                var left = $(this).scrollLeft();
            
                // console.log(top);
                //$('.box-label').css('margin-top','-'+top+'px');
                $('#month-week-title').css('margin-left','-'+left+'px');
            });
        });

        </script>
    </head>
    <body>  
          
    <?php echo $chart; ?>
    
    </body>
</html>