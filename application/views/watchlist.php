<style>
	
	span.star:before {
		cursor: pointer;
		text-align: center;
		font-family: 'Glyphicons Halflings'; 
		/*content:"\e007";	*/	
		content:"\e105";
	}

	span.starred:before {
		font-family: 'Glyphicons Halflings'; 		
		/*content:"\e006";*/
		content:"\e106";
		cursor: pointer;		
	}

	td.watch {text-align: center; font-size: 20px}

	div#nepse_company_length {
    margin-top: 10px;
    display: inline-block;
	}

	div.dataTables_length select {
    width: 75px;
    display: inline-block;
    margin-left: 15px;
	}

	div.dataTables_filter {
    display: table-cell;
	}

</style>

<div class="row">
<ul class="nav nav-tabs">  
  <li class="active"><a data-toggle="tab" href="#Watchlist">Watchlist</a></li>
  <li><a data-toggle="tab" href="#allCompanies">Companies</a></li>
</ul>
<div class="tab-content">	

	<div id="Watchlist" class="tab-pane active">
		<div class="table-responsive">
		<!-- <h4>My watchlist</h4> -->
		<div id="_err_msg"></div>
			<table id="nepse_watchlist" class="table table-responsive">
			<thead>
			<tr>
				<th rowspan="2">Symbol</th>
				<th rowspan="2">Company</th>
				<th rowspan="2">LTP</th>			
				<th colspan="2" class="text-center">Change</th>			
				<th rowspan="2">High/Low</th>
				<th rowspan="2">Updated</th>
				<!-- <th rowspan="2">Remarks</th> -->
				<th rowspan="2">Watch</th>	
			</tr>
			<tr>
				<td>point</td>
				<td>%</td>
			</tr>
			</thead>	
			</table>
		</div>

	</div>	<!-- watchlist -->

	<div  id="allCompanies" class="tab-pane fade">
		<div class="table-responsive">
<!-- 		<h4>NEPSE Companies</h4> -->
		<div id="_err_msg_company"></div>
		<table id="nepse_company" class="table table-striped table-hover">
			<thead>
				<tr>
					<th rowspan="2">Symbol</th>
					<th rowspan="2">Company</th>
					<th rowspan="2">LTP</th>	
					<th rowspan="2">LTV</th>			
					<th colspan="2" class="text-center">Change</th>
					<th rowspan="2">Open </th>
					<th rowspan="2">High</th>
					<th rowspan="2">Low</th>
					<th rowspan="2">Volume</th>			
					<th rowspan="2">Prev Closing</th>			
					<th rowspan="2">Updated</th>			
					<th rowspan="2">Watch</th>
				</tr>
				<tr>
					<td>point</td>
					<td>%</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<!-- <tfoot>
				<tr>
					<th>Symbol</th>
					<th>Company</th>
					<th>LTP</th>			
					<th>LTV</th>			
					<th>Change</th>
					<th>Change %</th>
					<th>Open </th>
					<th>High</th>
					<th>Low</th>
					<th>Volume</th>			
					<th>Prev closing</th>	
					<th>Updated</th>
					<th>Watch</th>
				</tr>	
			</tfoot> -->
		</table>
		</div>	<!-- table responsive -->
	</div>	<!-- tab pane -->

</div>	<!-- tab content -->

</div>	<!-- row -->

<script>
	
	$(document).ready(function(){
		
		fnWatchList();		
		//fnShowCompany();	

	});
	
</script>