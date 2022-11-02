<?php require 'dbcon.php' ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="John Emil, Mawuli King">
  <meta name="generator" content="Hugo 0.101.0">
  <title>Upload and Increase money</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container">
      <?php
        if (isset($_POST['AddInvestment'])) {

          if (empty($_POST['name_field']) || empty($_POST['emailInput']) || empty($_POST['amount']) || empty($_POST['percentage']) || empty($_POST['days'])) {
            echo "Please feel in all input areas";
          }else {
            $name_field = $_POST['name_field'];
            $emailInput = $_POST['emailInput'];
            $amount = $_POST['amount'];
            $percentage = $_POST['percentage'];
            $days = $_POST['days'];


            $sql = "INSERT INTO `investments`(`name_field`, `email`, `amount`, `percentage`, `days`) VALUES (?,?,?,?,?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sssss', $name_field, $emailInput, $amount, $percentage, $days);

            if (mysqli_stmt_execute($stmt)) {
              echo "<script>alert('Investment added Successfully')</script>";
              echo "<script>window.open('index.php','_self')</script>";
            }else {
              echo "<script>alert('There was an error updating investments, please try again later')</script>";
              echo "<script>window.open('index.php','_self')</script>";
            }
          }
        }
       ?>

    <form class="mt-5" action="" method="post">

      <div class="form-floating mb-3">
        <input type="text" class="form-control" name="name_field" id="name_field" placeholder="John Doe">
        <label for="name_field">Full Name</label>
      </div>

      <div class="form-floating mb-3">
        <input type="email" class="form-control" name="emailInput" id="emailInput" placeholder="johndoe@gmail.com" required>
        <label for="emailInput">Email address</label>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="form-floating mb-3">
            <input type="number" class="form-control" name="amount" id="amount" placeholder="GHS 10.00" min="1" required>
            <label for="amount">Amount</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating mb-3">
            <input type="number" class="form-control" name="percentage" id="percentage" placeholder="johndoe@gmail.com" min="1" max="100" required>
            <label for="amount">Percentage</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating mb-3">
            <input type="number" class="form-control" name="days" id="days" placeholder="johndoe@gmail.com" min="1" max="100" required>
            <label for="amount">Number of Days</label>
          </div>
        </div>
        <div class="col-md-12 text-center">
          <button class="btn btn-primary" type="submit" name="AddInvestment">Add Investment</button>
        </div>
      </div>
    </form>

<hr><br><br><br><hr>
    <div class="table-responsive">
      <table class="table table-hover table-striped">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Initial Deposit</th>
            <th>Percentage</th>
            <th>Number of days</th>
            <th>Days Since Investment</th>
            <th>Investment Return</th>
          </tr>
        </thead>

        <tbody>
          <?php
          $getItems = mysqli_query($conn, "SELECT * FROM investments");
          while ($row = mysqli_fetch_array($getItems)) {

            $rate = $row['percentage']/100;
            $principal = $row['amount'];
            $timeFrame = $row['days'];

            $dateReg = new DateTime($row['systemdate']);
            $now = new DateTime(date('Y-m-d'));

            // Use this area to test the dates to see if it is changing on the page
            // $now = new DateTime(date('2022-10-26'));
            $interval = $dateReg->diff($now)->format("%d");
            $totalReturn = '';

            if ($interval == 0 ) {
              $totalReturn = $row['amount'];
            }elseif ($interval >= 1 && $interval < $timeFrame) {
              $totalReturn = $principal + ($rate * $principal * $interval);
            }elseif ($interval >= $timeFrame ) {
              $totalReturn = $principal + ($rate * $principal * $timeFrame);
            }else {
              $totalReturn = $row['amount'];
            }

            $updateCurrentInvestment = mysqli_query($conn, "UPDATE `investments` SET `setudate`='$totalReturn' WHERE id = '".$row['id']."';");


            echo "<tr>
              <td>".$row['name_field']."</td>
              <td>".$row['email']."</td>
              <td>".$row['amount']."</td>
              <td>".$row['percentage']."%</td>
              <td>".$row['days']."</td>
              <td>$interval</td>
              <td>".number_format($totalReturn,2)."</td>
            </tr>";
          }
           ?>

        </tbody>
      </table>
    </div>

  </div>
</body>

</html>
