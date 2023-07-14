<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php include 'db_connect.php' ?>
    <style>
        .chart-container {
            display: flex;
            flex-wrap: wrap;
        }

        .chart-container .chart-item {
            flex: 1 1 50%;
            padding: 10px;
        }

        @media (max-width: 768px) {
            .chart-container .chart-item {
                flex: 1 1 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

            </div>
        </div>

        <div class="row mt-3 ml-3 mr-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <?php echo "Welcome back " . $_SESSION['login_name'] . "!"; ?>
                    </div>
                    <hr>
                    <div class="row ml-2 mr-2">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="mr-3">
                                            <div class="text-white-75 small">Total Applicants</div>
                                            <div class="text-lg font-weight-bold">
                                                <?php
                                                $totalApplicants = $conn->query("SELECT * FROM application");
                                                echo $totalApplicants->num_rows;
                                                ?>
                                            </div>
                                        </div>
                                        <i class="fa fa-user-tie"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="mr-3">
                                            <div class="text-white-75 small">Total Passed Applicants</div>
                                            <div class="text-lg font-weight-bold">
                                                <?php
                                                $newApplicants = $conn->query("SELECT * FROM application WHERE process_id = 2 OR process_id = 5");
                                                echo $newApplicants->num_rows;
                                                ?>
                                            </div>
                                        </div>
                                        <i class="fa fa-user-tie"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="mr-3">
                                            <div class="text-white-75 small">Total Failed Applicants</div>
                                            <div class="text-lg font-weight-bold">
                                                <?php
                                                $vacancies = $conn->query("SELECT * FROM application WHERE process_id = 6 OR process_id = 3");
                                                echo $vacancies->num_rows;
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container ml-2 mr-2">
                        <div class="chart-item">
                            <canvas id="applicantChart"></canvas>
                        </div>
                        <div class="chart-item">
                            <canvas id="totalApplicantsPieChart"></canvas>
                        </div>
                        <div class="chart-item">
                            <canvas id="applicantPieChart"></canvas>
                        </div>
                        <div class="chart-item">
                            <canvas id="genderChart"></canvas>
                        </div>
                        <div class="chart-item">
                            <canvas id="applicantLineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        var ctx = document.getElementById('applicantChart').getContext('2d');
        var chartData = {
            labels: ['Total Applicants', 'Total Passed Applicants', 'Total Failed Applicants'],
            datasets: [{
                label: 'Data Chart',
                data: [
                    <?php echo $totalApplicants->num_rows; ?>,
                    <?php echo $newApplicants->num_rows; ?>,
                    <?php echo $vacancies->num_rows; ?>
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(40, 167, 69, 0.2)',
                    'rgba(220, 53, 69, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        };
        var applicantChart = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        var totalApplicantsPieCtx = document.getElementById('totalApplicantsPieChart').getContext('2d');
        var totalApplicantsPieData = {
            labels: ['Total Passed Applicants', 'Total Failed Applicants'],
            datasets: [{
                data: [
                    <?php echo $newApplicants->num_rows; ?>,
                    <?php echo $vacancies->num_rows; ?>
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.2)',
                    'rgba(220, 53, 69, 0.2)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        };
        var totalApplicantsPieChart = new Chart(totalApplicantsPieCtx, {
            type: 'pie',
            data: totalApplicantsPieData,
            options: {
                responsive: true
            }
        });

        var pieCtx = document.getElementById('applicantPieChart').getContext('2d');
var pieData = {
    labels: ['Total Applicants'],
    datasets: [{
        data: [
            <?php echo $totalApplicants->num_rows; ?>
        ],
        backgroundColor: [
            'rgba(75, 192, 192, 0.2)'
        ],
        borderColor: [
            'rgba(75, 192, 192, 1)'
        ],
        borderWidth: 1
    }]


        };
        var applicantPieChart = new Chart(pieCtx, {
            type: 'pie',
            data: pieData,
            options: {
                responsive: true
            }
        });

        // Get female and male applicants
        var femaleApplicants = <?php echo $conn->query("SELECT COUNT(*) as total FROM application WHERE gender = 'Female'")->fetch_assoc()['total']; ?>;
        var maleApplicants = <?php echo $conn->query("SELECT COUNT(*) as total FROM application WHERE gender = 'Male'")->fetch_assoc()['total']; ?>;

        var genderCtx = document.getElementById('genderChart').getContext('2d');
        var genderData = {
            labels: ['Female', 'Male'],
            datasets: [{
                label: 'Gender',
                data: [femaleApplicants, maleApplicants],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        };
        var genderChart = new Chart(genderCtx, {
            type: 'bar',
            data: genderData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        var lineCtx = document.getElementById('applicantLineChart').getContext('2d');
        var lineData = {
            labels: [
                <?php
                $dateQuery = $conn->query("SELECT DISTINCT DATE_FORMAT(date_created, '%Y-%m-%d') as date FROM application ORDER BY date_created ASC");
                while ($row = $dateQuery->fetch_assoc()) {
                    echo "'" . $row['date'] . "',";
                }
                ?>
            ],
            datasets: [{
                label: 'Date of Applications',
                data: [
                    <?php
                    $dateQuery->data_seek(0); // Reset the result set pointer
                    while ($row = $dateQuery->fetch_assoc()) {
                        $date = $row['date'];
                        $countQuery = $conn->query("SELECT COUNT(*) as total FROM application WHERE DATE_FORMAT(date_created, '%Y-%m-%d') = '$date'");
                        $count = $countQuery->fetch_assoc()['total'];
                        echo $count . ",";
                    }
                    ?>
                ],
                fill: false,
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };
        var applicantLineChart = new Chart(lineCtx, {
            type: 'line',
            data: lineData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>