<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Now | Lumina University</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>

<body> 

<header id="main-header">
    <div class="header-container">
        <img src="images/logo.png" alt="Lumina University Logo" class="logo">

        <div class="title-group">
            <h1>Lumina University</h1>
            <h2>Department of Digital Learning &amp; Research</h2>
            <p><em>"Illuminating the Future of Education through Digital Innovation."</em></p>
        </div>
    </div>
</header>

<nav>
    <ul class="nav-menu">
        <li><a href="index.php">Home</a></li>
        <li><a href="jobs.php">Job Opportunities</a></li>
        <li><a href="apply.php" class="active" aria-current="page">Apply Now</a></li>
        <li><a href="about.php">About Our Team</a></li>
    </ul>
</nav>

<main>

<section id="hero-section">
    <h2>Apply for a Position</h2>
    <p>Please complete the application form below. Fields marked with an asterisk (*) are required.</p>
</section>

<form action="process_eoi.php"
      method="post"
      enctype="multipart/form-data"
      class="application-form"
      novalidate>

<h2>Job Application Form</h2>

<div class="form-grid">

<p class="form-section-title">Position</p>

<div class="full-width">
<label for="jobref" class="required">Job Reference Number</label>
<select id="jobref" name="jobref" required aria-describedby="jobref-hint">
<option value="">Select a position</option>
<option value="DLR01">Digital Learning Officer — DLR01</option>
<option value="WEB02">Web Content Coordinator — WEB02</option>
</select>
<span class="hint" id="jobref-hint">Choose the position you're applying for.</span>
</div>

<p class="form-section-title">Personal Details</p>

<div>
<label for="fname" class="required">First Name</label>
<input type="text" id="fname" name="fname" maxlength="20" pattern="[A-Za-z]+" required autocomplete="given-name">
</div>

<div>
<label for="lname" class="required">Last Name</label>
<input type="text" id="lname" name="lname" maxlength="20" pattern="[A-Za-z]+" required autocomplete="family-name">
</div>

<div>
<label for="dob" class="required">Date of Birth</label>
<input type="date" id="dob" name="dob" required autocomplete="bday">
</div>

<div class="full-width">
<fieldset>
<legend>Gender</legend>

<label><input type="radio" name="gender" value="Female" required> Female</label>
<label><input type="radio" name="gender" value="Male"> Male</label>

</fieldset>
</div>

<p class="form-section-title">Contact &amp; Address</p>

<div class="full-width">
<label for="address" class="required">Street Address</label>
<input type="text" id="address" name="address" maxlength="40" required autocomplete="address-line1">
</div>


<div>
<label for="state" class="required">State</label>

<select id="state" name="state" required autocomplete="address-level1">

<option value="">Select State</option>

<option value="VIC">Victoria</option>
<option value="NSW">New South Wales</option>
<option value="QLD">Queensland</option>
<option value="WA">Western Australia</option>
<option value="SA">South Australia</option>
<option value="TAS">Tasmania</option>

</select>

</div>

<div>
<label for="postcode" class="required">Postcode</label>
<input type="text" id="postcode" name="postcode" pattern="[0-9]{4}" maxlength="4" required
       aria-describedby="postcode-hint" autocomplete="postal-code" inputmode="numeric">
<span class="hint" id="postcode-hint">4 digits, e.g. 3000</span>
</div>

<div>
<label for="email" class="required">Email</label>
<input type="email" id="email" name="email" required autocomplete="email">
</div>

<div>
<label for="phone" class="required">Phone Number</label>
<input type="tel" id="phone" name="phone" pattern="[0-9]{8,12}" required
       aria-describedby="phone-hint" autocomplete="tel" inputmode="numeric">
<span class="hint" id="phone-hint">8 to 12 digits, no spaces or dashes.</span>
</div>

<p class="form-section-title">Skills &amp; Experience</p>

<div class="full-width">

<fieldset>

<legend>Skills</legend>

<label><input type="checkbox" name="skills[]" value="HTML"> HTML</label>
<label><input type="checkbox" name="skills[]" value="CSS"> CSS</label>
<label><input type="checkbox" name="skills[]" value="JavaScript"> JavaScript</label>
<label><input type="checkbox" name="skills[]" value="PHP"> PHP</label>
<label><input type="checkbox" name="skills[]" value="Other"> Other</label>

</fieldset>

</div>

<div class="full-width">
<label for="otherskills">Other Skills</label>
<textarea id="otherskills" name="otherskills" aria-describedby="otherskills-hint"></textarea>
<span class="hint" id="otherskills-hint">Optional — list any additional skills relevant to this role.</span>
</div>

<div class="full-width">
<label for="resume">Resume / CV</label>
<div class="file-upload">
<input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" aria-describedby="resume-hint">
</div>
<span class="hint" id="resume-hint">Optional — PDF or Word document, up to 5MB.</span>
</div>

<div class="full-width declaration">
<input type="checkbox" id="declaration" name="declaration" required aria-describedby="declaration-hint">
<label for="declaration">I confirm that the information provided in this application is true and accurate to the best of my knowledge. *</label>
<span class="hint" id="declaration-hint">This confirmation is required to submit your application.</span>
</div>

</div>

<div class="form-buttons"> 

<input type="submit" value="Apply">

<input type="reset" value="Reset">

</div>

</form>

</main>

<footer>
    <div class="footer-content">
        <p>&copy; 2026 Lumina University. All rights reserved.</p>
        <ul class="footer-links">
            <li><a href="mailto:info@luminauniversity.com">Contact Us: info@luminauniversity.com</a></li>
            <li><a href="https://nahanparvinnavas.atlassian.net/jira/software/projects/PT1/summary" target="_blank" rel="noopener noreferrer">Project Jira Board</a></li>
            <li><a href="https://github.com/106385897/Project-Part-1.git" target="_blank" rel="noopener noreferrer">GitHub Repository</a></li>
            <li><a href="https://106385897.github.io/Project-Part-1/" target="_blank" rel="noopener noreferrer">Project Website</a></li>
        </ul>
    </div>
</footer>

</body>

</html>