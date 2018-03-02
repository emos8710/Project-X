<?php
if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
	session_start();
}

$title = "UpStrain";
?>

<!DOCTYPE html>

<?php include 'top.php'; ?>


<body>
	<!-- Main content goes here -->
	<main>
		<h2 class="home">Welcome to Upstrain!</h2>
		<div class="home">
		Upstrain is the plasmid database for the Uppsala iGEM association, where information is stored about
		bacterial strains and their inserted plasmids. These plasmids subsuqently consist of certain backbones and
        specific insert sequence types, such as promotor or coding sequences. All this information about a certain
        strain and its plasmids can thus be found in this database, an efficient and easy solution to keep track of
        and reuse previous work.
		</div>
        <br>
        <br>
        <h3 class="home">About iGEM </h3>
		<div class="igem_text">
		iGEM stands for International Genetically Engineered Machine and it is an annual competition in
        synthetic biology where teams of students from all around the world work over the summer to
        design and build a biological system of their own choice, which is then implemented in living cells.
        <br>
        At the start of the competition all teams are given a tool kit of interchangable parts for their 
        project, so called "Biobricks". These parts consist of promoters, terminators, plasmid backbones etc., 
        and at the end of the summer the teams add their new BioBricks creations to the iGEM Parts Registry,
        and so the scientific community can build upon this added  number of BioBricks sets in the next year.
        <br>
        At the end of the competition, all teams meet in Boston for a scientific conference where the projects 
        are presented to one another and to a scientific jury. The judges then award medals and special prizes 
        in different categories to the teams, and then select a ‘Grand Prize Winner’ team as well as ‘Runner-Up’ teams.
        <br>
        The iGEM competition was first held in 2004  with 31 teams participating, and at that point it was mainly 
        aimed at undergraduate university students. But since then the competition has grown largely in size with ~300
        teams competing in both High School, Undergraduate and Overgraduate divisions.
        <br>
        The iGEM Uppsala team is an overgraduate team competing for Uppsala University in Sweden. The Uppsala team has 
        participated in iGEM since 2009 and have throughout the years created many interesting and practical biological systems.
        For example they have engineered bacteria to express different visible colors, to catalyze the formation of certain nutrients, 
        worked with the problem with antibiotic resistance and with the novel technologies CRISPR and microfluidics.
        </div>
        
		<h3 class="home">About Upstrain</h3>
        <div class="upstrain_text">
		The Upstrain database was created in 2018 as a data storage solution for the iGEM Uppsala team. Before the database was 
        created, all the information about parts and bacterial strains that were engineered during the course of the competition 
        were simply stored in an excel sheet made available for all the team members. This solution however was not an efficient
        way of storing information since there was no consistency regarding names and descriptions of the parts used, which 
        resulted in the entries created being unclear and ambiguous for those seeking to reuse previously created parts.
        Storing all the information in an excel sheet also meant that there was close to no searchablility for users interested in finding
        specific combinations of Biobrick parts, and finding the corresponding entry in iGEMs official Biobrick registry. 
        <br>
        And so as a solution to these problems, the official Uppsala iGEM association collaborated with a team of graduate students at 
        Uppsala University to create the Upstrain database, which served as a project for the grad students during the course 
        Information Management Systems. The aim of this project course was to create a data storage and management system
        (LIMS system) that could perform a certain set of tasks. The system needed to be able to perform database operations such as
        without any information loss, as well as incorporate security checks to protect the data from malignant outside attacks.
        <br>
        The basic functionality of the database is relatively straightforward and has a simple user interface, which hopefully will 
        be incorporated into the daily use of future iGEM teams at Uppsala university as an efficient and convenient solution for 
        data managment.
		</div>

			<?php 
				if(basename($_SERVER['PHP_SELF'])=="logout.php"){ ?>
				<h1 class="loginss">You have been logged out!</h1>
				<?php 
				} ?>
				
	</main>
	
	<?php include 'bottom.php'; ?>
	
</body>
</html>