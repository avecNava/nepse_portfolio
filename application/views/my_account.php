<style>
	.form-group button {margin-top: 25px}
	div#_err_msg{ margin: 15px;}
	div#_err_msg > div { display: list-item;}
	td > span.glyphicon {cursor: pointer; padding:2px}
	div#wrap {margin-left: -15px }	
	#Tax input,#Tax select {margin:5px 0px }
	#tblTax tbody tr:hover {cursor: pointer; background-color: #dcdcdc }		
	ul.nav-inline li.nav-item {float:left; margin:2px;}
	.selected { background-color: #e2d9d9; border-radius: 5px	}
	/*.nav>li>a:hover {
	    text-decoration: none;
	    background-color: #e2d9d9;
	}	*/
</style>
<div id="wrap">
<h4><mark>Control Panel</mark></h4>	

<ul class="nav nav-inline" id="account">
  	<li class="nav-item">
		<a class="nav-link selected" href="#Shareholder">Shareholder</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="#ShareGroup">Share Group</a>
	</li>

	<?php 
  	if($this->session->privilege) {			//show user management for privilege group only
  	?>

	<li class="nav-item">
		<a class="nav-link" href="#Admin">Admin</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="#User">Users</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="#Tax">Rates</a>
	</li>
	<li class="nav-item">
		<a class="nav-link disabled" href="#Company">New company</a>
	</li>	
<?php
	  }
?>
</ul>

<div id="_err_msg"></div>

<div class="tab-content">	
	
	<div id="Tax" class="tab-pane">
		<div class="col-md-4">
			<form id="Tax">
		  		<input type="hidden" value="<?=uniqid('TAX')?>" id="_tax_id" name="tax_id" />	 
				<label for="action" class="control-label col-sm-5">Action</label>
				<div class="col-sm-7">
				<select id="action" name="action" class="form-control">
					<option selected value="BUY">Buy</option>
					<option value="SALE">Sale</option>
				</select>
				</div>
			
				<label for="stock_type_id" class="control-label col-sm-5">Share type</label>
				<div class="col-sm-7">
				<select id="stock_type_id" name="stock_type_id" class="form-control"></select>
				</div>
			
				<label for="offr_code" class="control-label col-sm-5">Offering type</label>
				<div class="col-sm-7">
				<select id="offr_code" name="offr_code" class="form-control"></select>
				</div>
			
				<label for="low_range" class="control-label col-sm-5">Low range</label>
				<div class="col-sm-7">
				<input type="number" name="low_range" class="form-control"></input>
				</div>
			
				<label for="high_range" class="control-label col-sm-5">High range</label>
				<div class="col-sm-7">
				<input type="number" name="high_range" class="form-control"></input>
				</div>
			
				<label for="tax_per" class="control-label col-sm-5">Tax %</label>
				<div class="col-sm-7">
				<input type="number" name="tax_per" class="form-control"></input>
				</div>
			
				<div class="col-sm-5"></div>
				<div class="col-sm-7">
	    		<button type="button" id="btnSaveTax" class="btn btn-default">Save</button>
				</div>
			</form>
		</div>		<!--col-md-4-->			
		<div class="col-md-8">
			<div class="table-responsive">					
				<table class="table table-border table-striped" id="tblTax">
					<thead>
						<tr>
							<th>id</th>
							<th>Action</th>
							<th>Stock Type</th>
							<th>Offering</th>
							<th>Low range</th>
							<th>High range</th>
							<th>Tax %</th>
							<th>Last updated on</th>
							<th></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div> 	<!--col-md-8-->
	</div>		<!-- Tax -->	

	
	<div id="Shareholder" class="tab-pane active">
		
		<div class="form-group col-sm-3">
			<label for="txtShareholderName" class="control-label">Shareholder Name</label>
			<input type="text" name="txtShareholderName" class="form-control" />
		</div>
		<div class="form-group col-sm-2">
			<button class='btn btn-default' type="Submit" class="form-control" id="btnSaveShareholder">Submit</button>   
		</div>
	
		<div class="col-md-7 table-responsive">	
			<table class="table table-border table-striped" id="tblShareholder">
				<thead>
					<tr>
						<th width="34">ID</th>
						<th>Names</th>
						<th width="34" text-align="center"></th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>

	</div>	<!-- Shareholder -->


	<div id="ShareGroup" class="tab-pane ">
		<div class="form-group col-sm-3">
			<label for="txtGroupName" class="control-label">Group Name</label>			
			<input type="text" name="txtGroupName" class="form-control" />
		</div>  
		<div class="form-group col-sm-2">
			<button class='btn btn-default' type="Submit" class="form-control" id="btnSaveShareGroup">Submit</button>   
		</div>
	
		<div class="col-md-7 table-responsive">
				<table class="table table-border table-striped" id="tblShareGroup">
					<thead>
						<tr>
							<th width="34">ID</th>
							<th>Group</th>
							<th width="34" text-align="center"></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
		</div>
	</div>	<!-- ShareGroup -->


	<div id="User" class="tab-pane ">
		<div class="col-md-12 table-responsive">
			<table class="table table-border table-striped" id="tblUserInfo">
				<thead>
					<tr>
						<th width="34">ID</th>
						<th>Full Name</th>
						<th>Email</th>									
						<th>Last Login</th>
						<th>Date registered</th>
						<th>Privilege</th>
						<th>Active</th>									
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>	<!-- User -->



	<div id="Admin" class="tab-pane ">		
		<div class="col-md-12 table-responsive">
			<table class="table table-border table-striped" id="tblAdmin">
				<thead>
					<tr>
						<th width="34">ID</th>
						<th>Full Name</th>
						<th>Email</th>
						<th>Privilege</th>
						<th>Active</th>									
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>	<!-- Admin -->
	

	<div id="Company" class="tab-pane ">		
		<div class="col-md-12 table-responsive">
			<h5>Companies newly traded or whose symbol updated today are reflected here</h5>
			<table class="table table-border table-striped" id="tblCompany">
				<thead>
					<tr>									
						<th>Symbol</th>
						<th>Company Name</th>
						<th>Company Name</th>
						<th>Date</th>
						<th>Action</th>									
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>	<!-- Admin -->			
</div>	<!-- tab-content -->	
</div>
<script type="text/javascript">
	$(document).ready(function(){
		fnLoadShareholder();	
	})
	
</script>