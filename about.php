<!DOCTYPE html>
<html>
<head>
    <title>About - LogicLab Inc.</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 50px;
        }
        .team-members {
            list-style: none;
            padding-left: 0;
        }
        .team-member {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>

    <div class="container">
        <h1>About</h1>
        <p>This website has been developed by year three students doing Bachelor of Science in Data Science at Mzuzu University as a group project.</p>
        
        <h2>Team Members</h2>
        <ul class="team-members">
            <li class="team-member">
                <h3>Samson Mhango</h3>
                <p>Data Science Student</p>
            </li>
            <li class="team-member">
                <h3>Bernard Musewu</h3>
                <p>Data Science Student</p>
            </li>
            <li class="team-member">
                <h3>Harrison Chakadza</h3>
                <p>Data Science Student</p>
            </li>
            <li class="team-member">
                <h3>Glory Phiri</h3>
                <p>Data Science Student</p>
            </li>
        </ul>
    </div>

    <footer class="mt-5">
    <div class="container">
        <?php include('footer.php');?>
    </div>
</footer>


    <script src="js/bootstrap.min.js"></script>
</body>
</html>
