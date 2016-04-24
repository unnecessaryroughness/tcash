<h2><?php htmlout($reportDisplay->screenTitle); ?></h2>
<br>

<p><a href="<?php htmlout($reportDisplay->returnURL); ?>">&lt;&lt;Back</a></p>

<table>
<!--Results table headers-->
<tr>
    <?php foreach($reportDisplay->screenFields as $field): ?>
        <th class="<?php htmlout($field["class"]); ?>"><?php htmlout($field["caption"]); ?></th>
    <?php endforeach; ?>
</tr>

<!--Results table rows-->
<?php foreach($reportDisplay->screenData as $result): ?>
    <tr>
        <?php foreach($reportDisplay->screenFields as $field): 
                $reportDisplay->incrementTotal($field["field"], $result[$field["field"]]); ?>
            <td class="<?php htmlout($field["class"]); ?>">
                <?php htmlout($result[$field["field"]]); ?>
            </td>
        <?php endforeach; ?>
    </tr>
<?php endforeach; ?>

<!--Results table totals-->
<?php foreach($reportDisplay->screenFields as $field): ?>
        <td class="report-total <?php htmlout($field["class"]); ?>">
            <?php htmlout($reportDisplay->getTotalVal($field["field"])); ?>
        </td>
<?php endforeach; ?>

</table>

<!--Div that will hold the chart-->
<div id="chart_div" style="width:100%; height:<?php htmlout(sizeof($reportDisplay->screenData)*2);?>em;"></div>


<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});
     
      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);


      // Callback that creates and populates a data table, 
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

      // Create the data table.
      var data = new google.visualization.DataTable();

      <?php echo($reportDisplay->getChartFieldHeadingsJS()); ?>   
        
      data.addRows(<?php echo($reportDisplay->getChartFieldsJS()); ?>);
          
      // Set chart options
      var options = {'title':'<?php htmlout($reportDisplay->screenTitle); ?>',
                    'chartArea': {'top': 10, 'bottom': 10, 'width': '60%', 'height': '100%'} };

      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.<?php htmlout(ucfirst($reportDisplay->chart["type"])); ?>Chart(document.getElementById('chart_div'));
      chart.draw(data, options);
    }
</script>

