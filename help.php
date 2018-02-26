<?php
if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
	session_start();
}
?>

<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Help</title>
	<link href="css/upstrain.css" rel="stylesheet">
</head>

<?php
//Set display for the content div
if (isset($_GET['content'])) {
	$current_content = $_GET['content'];
} else {
	$current_content = '';
}
?>

<body>
	
	<?php include 'top.php'; ?>
	
	<!-- Main content goes here -->
	<main>
		<div class="innertube">
                    
                    <div class="control_panel_menu">
                        <h2>Help page</h2>
                        <p>
                            If you are new to the site this help page can be used 
                            as a guideline for how to use the different features,
                            such as the search function, the insert new entry function and
                            your own personal user page.
                            Most of these features are only available for registered users,
                            so we do advise you to register an account if you have not already done so.
                            
                        </p>
                        <br>
                        <br>
				<h3>Navigation</h3>
					<ul class="help_page_nav">
						<li><a href="?content=search_entries">Search for entries</a></li>
						<li><a href="?content=insert_entries">Insert new entry </a></li>
						<li><a href="?content=edit_profile">Edit my profile</a></li>
                                                <li><a href="?content=edit_entries">Edit my entries</a></li>
					</ul>
				</div>
                    
                    <!-- Desired content is displayed here -->
				<div class="help_page_show">
				
					<?php if ($current_content == "search_entries") {
                                        
						?>
                                                <br>
						<h3>How to search the database for entries</h3>
                                                <p>
                                                    If you wish to serach for entries in the database, click on the "Search" link in the
                                                    navigation bar. This will bring you to the Search page, where there are a number of
                                                    fields that can be filled in to specify the type of entries you which to find. So if you already
                                                    know the specific upstrain ID of the entry of interest, you can simply fill in the upstrain ID
                                                    search field to get that entry. However if you for example want to search for all entries containing
                                                    a certain type of backbone and insert, these fields can be filled out together to get all entries matching
                                                    these specific criteria. 
                                                    <br>
                                                    If you want more information about the entry, click on the corresponding upstrain ID to access the entry page.
                                                    You can also access the user page of the entry's creator by clicking on the creators name, which will redirect
                                                    you to their user page where contact information is available.
                                                    <br>
                                                    <br>
                                                    Good luck!
                                                </p>
                                                
                                                <?php
                                            } else if ($current_content == "insert_entries") {
                                                
                                                ?>
                                                <br>
                                                <h3> How to insert new entries into database </h3>
                                                <p>
                                                    One of the key features of Upstrain is the ability to add new entries
                                                    into the database, which can only be done if you have a registered account and if
                                                    you are logged into said account. If you are not already registered, 
                                                    click on the "Log in" link in the top right corner of the page to register.
                                                    If you are logged into your account, there should be a "New entry" link visible in the 
                                                    navigation bar. This will bring you to the New entry page, which allows you to add
                                                    new entries by filling in information such as bacteria strain, plasmid backbone, inserts,
                                                    insert types, biobrick registry id and so forth. Some of the input fields are required to be 
                                                    filled out (marked with a "*"), while others are optional. You can also add a sequence file in
                                                    fasta format to your entry, add comments about the entry, or choose to make the entry
                                                    private. Making the entry private means that non-registered users would not be able to view the entry.
                                                    <br>
                                                    <br>
                                                    Good luck!
                                                </p>
                                                
                                                <?php
                                            } else if ($current_content == "edit_profile") {
                                                
                                                ?>
                                                <br>
                                                <h3> How to edit your user profile </h3>
                                                <p>
                                                    To edit your user information, click on "My profile" in the navigation bar
                                                    to access your personal user profile. Your contact information is displayed
                                                    under the header "Contact information", and below this list there is a
                                                    "Edit user information" button. 
                                                    Click on this button to access and edit your user information.
                                                    <br>
                                                    <br>
                                                    Good luck!
                                                </p>
                                                
                                                <?php
                                            } else if ($current_content == "edit_entries") {
                                                
                                                ?>
                                                <br>
                                                <h3> How to edit your previous entries </h3>
                                                <p>
                                                    If you want to edit your previously inserted entries,
                                                    click on "My profile" in the navigation bar to access your personal user
                                                    profile. All of your previous entries are displayed in a table under 
                                                    the header "User entries", with a edit button available for each entry.
                                                    Scroll through the table until you find the entry you want to edit and click
                                                    on the corresponding entry button. This will forward you to a page where the 
                                                    entry information can be edited as you please.
                                                    <br>
                                                    <br>
                                                    Good luck!
                                                </p>
                                                                                                
                                                
                                                <?php
                                            } else {
                                                echo "";
                                            }
                                            ?>
                                </div>	 
                    
		</div>
	</main>
	
	<?php include 'bottom.php'; ?>
	
</body>

</html>