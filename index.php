<?php
include "ExplorerHelperStat.php";
?>
<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css"
        crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<?php
    include "styles.php";
?>

</head>
<body id="ftp_tool">

<div class="overlay d-none"></div>
<div id="top">
	<div id="breadcrumb">&nbsp;</div>
</div>

<div class="actions">
	<div class="btn-group" role="group">

		<button type="button" class="btn btn-default" id="refresh_button" data-toggle="tooltip"
				data-placement="bottom" title="" data-original-title="Report spam">
				<i class="fa fa-refresh" aria-hidden="true"></i> Refresh
		</button>

		<?php if($allow_upload): ?>
			<button type="button" class="btn btn-default" id="upload_file"  data-toggle="tooltip"
				data-placement="bottom" title="" data-original-title="Report spam">
				<i class="fa fa-upload" aria-hidden="true"></i>
					<input type="file"  multiple id="upload" hidden/> <label for="upload" id="upload_label">Upload file</label>
			</button>
		<?php endif; ?>

		<button type="button" class="btn btn-default" id="create_new_folder" data-toggle="tooltip"
			data-placement="bottom" title="" data-original-title="Report spam"><i
				class="fa fa-folder-open-o"></i> New Folder</button>

		<div class="form-popup d-none" id="myForm">
			<form action="?" method="post" id="mkdir" class="form-container">
			    <span class="close_icon"><i class="fa fa-times" aria-hidden="true"></i></span>
				<h4 class="mt-4">Create New Folder</h4>
				<input type="text" placeholder="Enter folder name" maxlength="50" id="dirname" value="" name="name" required>
				<p class="error_messages"></p>
				<button type="button" class="btn cancel close_folder_creation_form">Cancel</button>
				<input type="submit" value="Create" class="btn create_btn"/>
			</form>
		</div>

		<div class="form-popup d-none" id="folder_rename_form">
			<form action="?" method="post" id="folder_rename" class="form-container">
			    <span class="close_icon_rename_form"><i class="fa fa-times" aria-hidden="true"></i></span>
				<h4 class="mt-4">Folder Rename</h4>
				<input type="text" placeholder="Enter new folder name" maxlength="50" id="new_folder_name" value="" name="new_folder_name" required>
				<p class="error_messages"></p>
				<button type="button" class="btn cancel close_folder_rename_form">Cancel</button>
				<input type="submit" value="Submit" class="btn rename_btn"/>
			</form>
		</div>
	</div>
	<div class="search_section">
		<div class="spinner">
			<div class="lds-roller d-none" id="loading_roller">
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
			</div>
		</div>
		<div id="search_scope" class="mr-3">
			<label for="search_scope_select">Search scope:</label>
			<select id="search_scope_select">
				<option value="1">Current folder</option>
				<option value="2">All folders</option>
			</select>
		</div>

		<div id="search_input">
			<label for="search">Search by file or folder name: </label>
			<input type="text" id="search" class="search_input" placeholder="">
		</div>
	</div>
</div>

<div id="upload_progress"></div>
<table id="table">
	<thead>
		<tr>
			<th>Name</th>
			<th>Size</th>
			<th>Modified</th>
			<!-- <th>Actions</th> -->
		</tr>
	</thead>
	<tbody id="list"></tbody>
<div id="item_popup">
	<p><a href="" class="download" id="download_item">Download</a></p>
	<p><a href="javascript:;" class="rename" id="rename_item">
		<i class="fa fa-bars" aria-hidden="true"></i>&nbsp; Rename</a></p>
	<p><a href="" class="delete" id="delete_item">&nbsp;Delete</a></p>
</div>
</table>
</body>

<?php include "scripts.php"; ?>
</html>
