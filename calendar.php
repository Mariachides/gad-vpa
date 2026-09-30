<?php
include 'config.php';

// Kunin ang lahat ng events mula sa database, naka-sort mula sa pinaka-maaga
$query = "SELECT event_name AS title, event_date, description FROM calendar ORDER BY event_date ASC";
$result = mysqli_query($conn, $query);
$events = [];

if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
}

// I-convert ang PHP array papuntang JSON para sa JavaScript Calendar Grid
$events_json = json_encode($events);

$activePage = 'calendar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar of Events | GAD VPA</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-purple: #6f42c1;
            --gradient-purple: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
            --bg-light: #f8f7fa;
        }

        body { 
            background-color: var(--bg-light); 
            color: #2d3436; 
            font-family: 'Inter', sans-serif; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }
        
        .compact-header {
            background: var(--gradient-purple);
            padding: 15px 0;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: white !important;
            text-decoration: none;
        }

        main { flex: 1; }

        .calendar-container {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 40px;
        }

        .calendar-header-bg {
            background-color: #fff;
            color: var(--primary-purple);
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .day-name {
            background-color: #fcfcfc;
            color: #888;
            font-weight: 700;
            padding: 12px 0;
            font-size: 0.75rem;
            letter-spacing: 1px;
            border-bottom: 1px solid #f0f0f0;
        }

        .calendar-day {
            flex: 0 0 14.2857%;
            max-width: 14.2857%;
            height: 110px;
            border: 0.5px solid #f8f9fa;
            padding: 10px;
            background-color: #fff;
            cursor: pointer;
            transition: 0.2s;
        }

        .calendar-day:hover { background-color: #f3effb; }

        .day-number { font-weight: 600; font-size: 0.9rem; }
        .weekend .day-number { color: #e74c3c; }

        .today { background-color: #f3effb !important; }
        .today .day-number {
            color: var(--primary-purple);
            background: #e9ecef;
            width: 28px; height: 28px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
        }

        .event-card {
            margin-top: 4px;
            padding: 3px 8px;
            background: var(--gradient-purple);
            color: #fff;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-nav {
            background: #f3effb;
            color: var(--primary-purple);
            border: none;
            width: 35px; height: 35px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }

        .info-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            border-left: 5px solid var(--primary-purple);
        }

        .section-title {
            font-weight: 700;
            color: var(--primary-purple);
            margin-bottom: 15px;
            display: flex; align-items: center;
        }

        .activity-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #eee;
        }

        .date-badge {
            background: #f3effb;
            color: var(--primary-purple);
            min-width: 45px; height: 45px;
            border-radius: 10px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700;
            margin-right: 15px;
        }

        .announcement-box {
            background: #fff9db;
            border: 1px solid #ffe066;
            border-radius: 10px;
            padding: 12px;
            font-size: 0.85rem;
            margin-bottom: 10px;
        }

        footer { background: #6f42c1; color: white; padding: 20px 0; margin-top: auto; }
    </style>
</head>

<body>

<?php include 'includes/user_nav.php'; ?>

<main class="main-content">
    <div class="container my-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="calendar-container">
                <div class="calendar-header-bg d-flex justify-content-between align-items-center">
                    <button class="btn-nav" onclick="changeMonth(-1)"><i class="bi bi-chevron-left"></i></button>
                    <h4 id="monthTitle" class="mb-0 fw-bold"></h4>
                    <button class="btn-nav" onclick="changeMonth(1)"><i class="bi bi-chevron-right"></i></button>
                </div>

                <div class="row g-0 text-center day-name">
                    <div class="col">SUN</div><div class="col">MON</div><div class="col">TUE</div>
                    <div class="col">WED</div><div class="col">THU</div><div class="col">FRI</div>
                    <div class="col">SAT</div>
                </div>

                <div id="calendarGrid"></div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="info-card">
                <div class="section-title"><i class="bi bi-megaphone-fill me-2"></i> Announcements</div>
                <div class="announcement-box shadow-sm">
                    <strong>Notice:</strong> Please be informed that the GAD Office will be closed on Friday for the Faculty Meeting.
                </div>
            </div>

            <div class="info-card">
                <div class="section-title border-bottom pb-2 mb-3">
                    <i class="bi bi-calendar-event-fill me-2"></i> Upcoming Activities
                </div>
                
                <div id="upcoming-list">
                    <?php 
                    $today = date('Y-m-d');
                    $has_upcoming = false;
                    $count = 0;

                    foreach($events as $event): 
                        if($event['event_date'] >= $today && $count < 5): // Ipakita ang next 5 upcoming
                            $has_upcoming = true;
                            $count++;
                            $event_ts = strtotime($event['event_date']);
                            $month_name = strtoupper(date('M', $event_ts));
                            $day_num = date('d', $event_ts);
                    ?>
                        <div class="activity-item">
                            <div class="date-badge">
                                <span><?php echo $month_name; ?></span>
                                <span><?php echo $day_num; ?></span>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold small"><?php echo htmlspecialchars($event['title']); ?></h6>
                                <small class="text-muted"><?php echo date('l', $event_ts); ?></small>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 

                    if(!$has_upcoming):
                        echo '<p class="text-muted small text-center">No upcoming activities found.</p>';
                    endif;
                    ?>
                </div>
                <button class="btn btn-outline-primary btn-sm w-100 mt-2 rounded-pill">Refresh List</button>
            </div>
        </div>
    </div>
    </div>

    <footer class="text-center mt-4">
        <div class="container"><span>© 2025 GAD VPA | Cavite State University - Naic Campus</span></div>
    </footer>
</main>

<script>
const dbEvents = <?php echo $events_json; ?>;
const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
let now = new Date();
let currentMonth = now.getMonth();
let currentYear = now.getFullYear();

function renderCalendar() {
    document.getElementById("monthTitle").innerText = months[currentMonth] + " " + currentYear;
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const grid = document.getElementById("calendarGrid");
    grid.innerHTML = "";

    let row = document.createElement("div");
    row.className = "row g-0";

    for (let i = 0; i < firstDay; i++) row.appendChild(createDay("", i));

    for (let day = 1; day <= daysInMonth; day++) {
        const dayOfWeek = (firstDay + day - 1) % 7;
        row.appendChild(createDay(day, dayOfWeek));
        if ((firstDay + day) % 7 === 0) {
            grid.appendChild(row);
            row = document.createElement("div");
            row.className = "row g-0";
        }
    }

    let remainingDays = (firstDay + daysInMonth) % 7;
    if (remainingDays !== 0) {
        for (let i = remainingDays; i < 7; i++) row.appendChild(createDay("", i));
        grid.appendChild(row);
    }
}

function createDay(content, dayOfWeek) {
    const col = document.createElement("div");
    col.className = "col calendar-day";
    if (dayOfWeek === 0 || dayOfWeek === 6) col.classList.add("weekend");

    let checkDate = new Date();
    if (content == checkDate.getDate() && currentMonth == checkDate.getMonth() && currentYear == checkDate.getFullYear()) {
        col.classList.add("today");
    }

    col.innerHTML = `<span class="day-number">${content}</span>`;

    if (content !== "") {
        let m = (currentMonth + 1).toString().padStart(2, '0');
        let d = content.toString().padStart(2, '0');
        let dateString = `${currentYear}-${m}-${d}`;

        dbEvents.forEach(event => {
            if (event.event_date === dateString) {
                col.innerHTML += `<div class="event-card" title="${event.title}">${event.title}</div>`;
            }
        });
    }
    return col;
}

function changeMonth(step) {
    currentMonth += step;
    if (currentMonth < 0) { currentMonth = 11; currentYear--; }
    else if (currentMonth > 11) { currentMonth = 0; currentYear++; }
    renderCalendar();
}

renderCalendar();
</script>
</body>
</html>