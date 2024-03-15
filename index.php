
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gantt Chart Generator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <style>
        .chart-container {
            overflow-x: auto;
        }
        .dates-container {
            white-space: nowrap;
        }
        svg {
            display: block;
        }
        .task {
            fill: #007bff;
        }
        .dependency {
            stroke: #007bff;
            stroke-width: 2;
        }
        text {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .month {
            font-weight: bold;
            overflow-x: scroll;
        }
        .tasks-column {
            width: 200px;
            float: left;
        }

        .action-link {
            position: relative;
            display: inline-block;
        }

        .action-text {
            position: absolute;
            top: -50px; 
            left: 60%;
            transform: translateX(-50%);
            background-color: black;
            color: white;
            padding: 2px 2px;
            border-radius: 3px;
            font-size: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .action-link:hover .action-text {
            opacity: 1;
        }
    
        button.submit:hover {
            background-color: #28a746;
        }

        .glossy-container {
            text-align: center;
            background: linear-gradient(to right, #ffffff, #e6e6e6); 
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            display: inline-block;
            align-items: center;
            margin-left: 600px;
        }
        h2 {
            margin: 0;
            color: #007bff;
        }
        .task-form{
            margin-left: 50px;
        }
    </style>
</head>
<body>
    <div class="glossy-container">
        <h2 style="text-align:center">Gantt Chart Generator</h2>
    </div>

    <div class="content">
        <div class="tasks-column" style="margin-left: 250px;">
            <h3>Tasks</h3>
            <div style="display: flex;" class="task-form">
                <form method="post" style="display: flex;">
                    <label for="task_name">Task Name:</label>
                    <input type="text" id="task_name" name="task_name" required style="margin-right: 10px;" >
                    <label for="start_date" style="margin-right: 10px;">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required  style="width: 150px;" required style="margin-right: 10px;">
                    <label for="end_date" style="margin-left: 10px;">End Date:</label>
                    <input type="date" id="end_date" name="end_date" required  style="width: 120px;" style="margin-right: 10px;">
                    <label for="dependency" style="margin-left: 10px;">Dependency:</label>
                    <select id="dependency" name="dependency" style="margin-right: 10px;">
                        <option value="" style="margin-right: 10px;">None</option>
                        <?php
                        session_start();
                        //sample tasks
                        $sample_tasks = array(
                            array("name" => "Sample Task 1", "start" => "2024-03-15", "end" => "2024-03-20", "dependency" => ""),
                            array("name" => "Sample Task 2", "start" => "2024-03-22", "end" => "2024-03-25", "dependency" => ""),
                            array("name" => "Sample Task 3", "start" => "2024-03-28", "end" => "2024-03-31", "dependency" => ""),
                            array("name" => "Sample Task 4", "start" => "2024-04-02", "end" => "2024-04-05", "dependency" => "")
                        );
                        foreach ($sample_tasks as $sample_task) {
                            echo "<option value='{$sample_task['name']}'>{$sample_task['name']}</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" class="submit" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold;">Add Task</button>
                </form>
            </div>
            
            <ul style="margin-top: 100px; ">
                <?php
                // session_start();
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Process form submission and add task to the list
                    $task_name = $_POST["task_name"];
                    $start_date = $_POST["start_date"];
                    $end_date = $_POST["end_date"];
                    $dependency = $_POST["dependency"];

                    // Add new task to the list
                    $_SESSION["tasks"][] = ["name" => $task_name, "start" => $start_date, "end" => $end_date, "dependency" => $dependency];

                    // Remove sample tasks
                    unset($_SESSION["sample_tasks"]);
                }

                if (isset($_SESSION["tasks"]) && !empty($_SESSION["tasks"])) {
                    foreach ($_SESSION["tasks"] as $key => $task) {
                        echo "<li style='margin-bottom: 12px;'>{$task['name']} <a href='?action=delete&task_id=$key' title='Delete' class='action-link'><i class='fas fa-trash-alt'></i><span class='action-text'>Delete</span></a> <a href='?action=complete&task_id=$key' title='Mark as Completed' class='action-link'><i class='far fa-check-square'></i><span class='action-text'>Mark as Completed</span></a></li>";
                    }
                }
                ?>
            </ul>
        </div>
        <div class="chart-container" style="margin-bottom: 2px;">
            <div class="dates-container" style="width: 800px;"> <!-- Set a fixed width for the dates container -->
                <svg id="gantt-chart">
                    <!-- Chart will be drawn here -->
                </svg>
            </div>
        </div>
    </div>
    

    <?php
    // Handle task actions (delete, mark as completed)
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"]) && isset($_GET["task_id"])) {
        $action = $_GET["action"];
        $task_id = $_GET["task_id"];
        if ($action === "delete" && isset($_SESSION["tasks"][$task_id])) {
            unset($_SESSION["tasks"][$task_id]);
        } elseif ($action === "complete" && isset($_SESSION["tasks"][$task_id])) {
            // Add logic to mark task as completed
        }
    }

    // Generate Gantt chart
    if (isset($_SESSION["tasks"]) && !empty($_SESSION["tasks"])) {
        $tasks = $_SESSION["tasks"];
        $chart_height = 30 * count($tasks) + 50; // Height of the chart area
        $chart_width = max(800, count($tasks) * 120); // Width of the chart area, adjust based on the number of tasks
        $task_height = 20; // Height of each task bar
        $y = 50; // Initial y-coordinate for tasks

        // Start the scrollable container
        echo "<div style='width: 50%; overflow-x: auto; overflow-y: hidden;'>";

        // Start SVG
        echo "<svg id='gantt-chart' width='$chart_width' height='$chart_height'>";
        
        // Draw month names and dates
        $current_month = null;
        $x = 200; // Initial x-coordinate for dates
        $dates = [];
        foreach (new DatePeriod(new DateTime(min(array_column($tasks, 'start'))), new DateInterval('P1D'), new DateTime(max(array_column($tasks, 'end')))) as $date) {
            $day = $date->format('j');
            $month = $date->format('F');
            if ($day % 4=== 0) {
                $dates[] = "<text x='$x' y='20'>$month</text>";
                $dates[] = "<text x='$x' y='40'>$day</text>";
            }
            $x += 20; // Increment x-coordinate for next date
        }
        echo "<g class='dates'>" . implode('', $dates) . "</g>";

        // Draw tasks
        foreach ($tasks as $task) {
            $task_name = $task["name"];
            $start_date = new DateTime($task["start"]);
            $end_date = new DateTime($task["end"]);
            $start_x = ($start_date->diff(new DateTime(min(array_column($tasks, 'start'))))->days) * 20 + 200; // X-coordinate for start date
            $duration = $start_date->diff($end_date)->days + 1; // Duration of the task in days
            $width = $duration * 20; // Width of the task bar
            $dependency = $task["dependency"];

            // Assign a random color for each task
            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));

            // Draw task bar with color
            echo "<rect class='task' x='$start_x' y='$y' width='$width' height='$task_height' fill='$color'/>";
            echo "<text x='" . ($start_x + $width / 2) . "' y='" . ($y + $task_height / 2 + 4) . "' text-anchor='middle' fill='white'>$task_name</text>";

            // Draw dependency line
            if (!empty($dependency)) {
                foreach ($tasks as $dep_task) {
                    if ($dep_task["name"] == $dependency) {
                        $dep_start_date = new DateTime($dep_task["start"]);
                        $dep_start_x = ($dep_start_date->diff($start_date)->days) * 20 + 200; // X-coordinate for dependency line start
                        echo "<line class='dependency' x1='$dep_start_x' y1='" . ($y + $task_height / 2) . "' x2='$start_x' y2='" . ($y + $task_height / 2) . "' stroke='$color'/>";
                        break;
                    }
                }
            }

            $y += 30; // Increment y-coordinate for next task
        }

        // End SVG
        echo "</svg>";
        echo "</div>";
    }
    ?>

</body>
</html>
